<?php
namespace App;

class Bootstrap extends \Peanut\Bootstrap\Yaml
{
    /**
     * @param $config
     */
    public function initialize(\Phalcon\Mvc\Micro $app)
    {
        $this->initCors();
        $this->initDatabase();
        $this->initSession();
        $this->initTemplate();
        $this->initAuth();
        //$this->initDebug();

        $app->notFound(
            function () use ($app) {
                throw new \App\Exception('404 Page or File Not Found!', 404);
            }
        );
    }

    public function initCors()
    {
        $origin = $this->getDi('request')->getHeader('ORIGIN') ?: '*';

        if (strtoupper($this->getDi('request')->getMethod()) == 'OPTIONS') {
            $this->getDi('response')
                ->setHeader('Access-Control-Allow-Origin', $origin)
                ->setHeader('Access-Control-Allow-Methods', 'GET,PUT,POST,DELETE,OPTIONS')
                ->setHeader('Access-Control-Allow-Headers', 'Origin, X-Requested-With, Content-Range, Content-Disposition, Content-Type, Authorization')
                ->setHeader('Access-Control-Allow-Credentials', 'true')
                ->setStatusCode(200, 'OK')->send();
            exit;
        }

        $this->getDi('response')
            ->setHeader('Access-Control-Allow-Origin', $origin)
            ->setHeader('Access-Control-Allow-Methods', 'GET,PUT,POST,DELETE,OPTIONS')
            ->setHeader('Access-Control-Allow-Headers', 'Origin, X-Requested-With, Content-Range, Content-Disposition, Content-Type, Authorization')
            ->setHeader('Access-Control-Allow-Credentials', 'true');
    }

    public function initEnvironment()
    {
        $stageName = getenv('STAGE_NAME');

        if (!$stageName) {
            throw new \Exception('Unable to verify stage');
        }

        $this->stageName = $stageName;
    }

    /**
     * @return \Phalcon\Session
     */
    public function initSession()
    {
        $this->di->setShared('session', function () {
            $session = new \Phalcon\Session\Adapter\Redis([
                'uniqueId'   => 'my-private-app',
                'host'       => 'redis',
                'port'       => 6379,
                'persistent' => false,
                'lifetime'   => 3600,
                'prefix'     => 'my_',
                'index'      => 1,
            ]);
            $session->start();

            return $session;
        });
    }

    /**
     * @return \Peanut\Template
     */
    public function initTemplate()
    {
        $stage = $this->getStageName();
        $this->di->setShared('template', function () use ($stage) {
            $tpl = new \Peanut\Template();
            $tpl->phpengine = true;
            $tpl->notice = false;

            switch ($stage) {
                case 'production':
                    $tpl->compileCheck = false;
                    break;
                case 'staging':
                    $tpl->compileCheck = true;
                    break;
                default:
                    $tpl->compileCheck = 'dev';
            }

            $tpl->compileRoot  = __BASE__.DIRECTORY_SEPARATOR.'.template';
            $tpl->templateRoot = __BASE__.DIRECTORY_SEPARATOR.'app'.DIRECTORY_SEPARATOR.'views';

            return $tpl;
        });
    }

    /**
     * @return array
     */
    public function initDatabase()
    {
        $stageName = $this->stageName;
        $debug     = $this->debug;
        $dbConfig  = $this->getDbConfig();

        $this->di->setShared('databases', function () use ($dbConfig) {
            return $dbConfig;
        });

        if (true === $debug) {
            $this->dbProfiler();
        }
    }

    public function initAuth()
    {
        $this->di->setShared('auth', function () {
            $jwt = new \Firebase\JWT\JWT;

            return $jwt;
        });
    }

    protected function initRouter()
    {
        $routes           = [];
        $routes['before'] = '\App\Middlewares\Validator->handle';
        $swagger          = decode_file(__BASE__.'/app/specs/swagger.json');

        foreach ($swagger['paths'] as $path => $methods) {
            foreach ($methods as $method => $info) {
                if (true === isset($info['operationId'])) {
                    $routes[$method.' '.$path] = $info['operationId'];
                } else {
                    throw new \Exception('not found operationId');
                }
            }
        }

        $this->di->setShared('router', function () use ($routes) {
            $router = new \Peanut\Phalcon\Mvc\Router\Rules\Hash();
            $router->group($routes);

            return $router;
        });

        $this->di->setShared('validator', function () use ($swagger) {
            $validator = new \Peanut\Validator($this);
            $validator->mode = 'strict';
            $validator->setSpec($swagger);

            return $validator;
        });
    }

    /**
     * @return array
     */
    private function getDbConfig()
    {
        $dbUrls = json_decode(getenv('DATABASE_URL'), true);
        if (0 === count($dbUrls)) {
            throw new \Exception('Check DB URL');
        }
        $dbConfig = [];
        foreach ($dbUrls as $server => $url) {
            $dbConfig[$server] = $this->dsnParser($url);
        }

        return $dbConfig;
    }

    /**
     * @param $url
     * @return array
     */
    private function dsnParser($url)
    {
        $dbSource = parse_url($url);
        $user     = $dbSource['user'];
        $password = $dbSource['pass'];
        $dsn      = $dbSource['scheme'].':host='.$dbSource['host'].
                    ';dbname='.trim($dbSource['path'], '/').';charset=utf8mb4';

        return [
            'dsn'      => $dsn,
            'username' => $user,
            'password' => $password,
        ];
    }

    public function initEventsManager()
    {
        $this->di->setShared('eventsManager', function () {
            return new \Phalcon\Events\Manager();
        });
    }

    public function initDbProfiler()
    {
        $this->di->setShared('dbProfiler', function () {
            return new \Phalcon\Db\Profiler();
        });
    }

    public function initDebug()
    {
        $debug = (bool)(getenv('DEBUG'));
        if ($debug) {
            $this->debug = true;
            include_once __BASE__.'/app/helpers/debug.php';
        }
    }

    public function dbProfiler()
    {
        $this->initEventsManager();
        $this->initDbProfiler();

        $eventsManager = $this->getDi('eventsManager');
        $eventsManager->attach('db', function ($event, $connection) {
            $profiler = $this->di['dbProfiler'];
            if ($event->getType() == 'beforeQuery') {
                $profiler->startProfile($connection->getSQLStatement(), $connection->getSQLVariables(), $connection->getSQLBindTypes());
            }

            if ($event->getType() == 'afterQuery') {
                $profiler->stopProfile();
            }
        });

        $dbNames = array_keys($this->getDbConfig());
        foreach ($dbNames as $name) {
            \Peanut\Phalcon\Pdo\Mysql::name($name)->setEventsManager($eventsManager);
        }
    }
}
