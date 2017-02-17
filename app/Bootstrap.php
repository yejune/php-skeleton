<?php
namespace App;

class Bootstrap extends \Peanut\Bootstrap\Yaml
{
    public $debug = false;
    /**
     * @param $config
     */
    public function initialize(\Phalcon\Mvc\Micro $app)
    {
        $this->initDatabase();
        $this->initSession();
        $this->initTemplate();
        $this->initAuth();
        $this->initDebug();
        $this->initCache();

        $app->notFound(
            function () use ($app) {
                throw new \App\Exception('404 Page or File Not Found!', 404);
            }
        );
    }

    public function initCache()
    {
        $this->di->setShared('cache', function () {

            // Cache data for 2 days
            $frontCache = new \Phalcon\Cache\Frontend\Data([
                'lifetime' => 3600,
            ]);

            // Create the Cache setting redis connection options
            $cache = new \Phalcon\Cache\Backend\Redis($frontCache, [
                'host'       => getenv('REDIS_URL'),
                'port'       => 6379,
                'persistent' => false,
                'index'      => 0,
            ]);

            return $cache;
        });
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
                ->setStatusCode(200, 'OK')
                ->send();
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
                    $tpl->compileCheck = false;
                    break;
                default:
                    $tpl->compileCheck = 'dev';
            }

            $tpl->compileRoot  = __BASE__.DIRECTORY_SEPARATOR.'.template';
            $tpl->templateRoot = __BASE__.DIRECTORY_SEPARATOR.'app'.DIRECTORY_SEPARATOR.'Views';

            return $tpl;
        });
    }

    public function initAuth()
    {
        $this->di->setShared('auth', function () {
            $jwt = new \Firebase\JWT\JWT;

            return $jwt;
        });
    }

    /**
     * @return array
     */
    public function initDatabase()
    {
        $debug     = $this->debug;

        $this->setDiDbConnect('master', getenv('MASTER_DATABASE_URL'));
        $this->setDiDbConnect('slave1', getenv('SLAVE1_DATABASE_URL'));

        if (true === $debug) {
            $this->dbProfiler();
        }
    }

    public function setDiDbConnect($name, $dsn)
    {
        $this->di->setShared(
            $name,
            function () use ($name, $dsn) {
                return \Peanut\Phalcon\Db::connect($name, $dsn);
            }
        );
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
            include_once __BASE__.'/app/Helpers/Debug.php';
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

        $this->di['master']->setEventsManager($eventsManager);
        $this->di['slave1']->setEventsManager($eventsManager);
    }

    protected function initRouter()
    {
        $routes              = $this->getDomainRoutes();
        //$routes['before']    = '\App\Middlewares\Validator->handle';
        $this->di->setShared('router', function () use ($routes) {
            $router = new \Peanut\Phalcon\Mvc\Router\Rules\Hash();
            $router->group($routes);

            $router->setUriSource(
                \Phalcon\Mvc\Router::URI_SOURCE_SERVER_REQUEST_URI
            );

            return $router;
        });
        /*
        $swagger          = decode_file(__BASE__.'/app/Specs/Gateway/V1/swagger.json');
        pr(memory_get_usage(false));

        $this->di->setShared('validator', function () use ($swagger) {
            $validator = new \Peanut\Validator($this);
            $validator->mode = 'strict';
            $validator->setSpec($swagger);

            return $validator;
        });
        */
    }

    private function getDomainRoutes()
    {
        $filename        = __BASE__.'/app/Bootstrap/Routes/global.yml';
        $global          = [];
        if (true === file_exists($filename)) {
            $global = decode_file($filename);
        } else {
            die($filename);
        }

        $version         = $this->getDi('request')->getSegment(0);
        $domainPrefix    = $this->getDi('request')->getSubDomain();
        $filename        = __BASE__.'/app/Bootstrap/Routes/'.$domainPrefix.'/'.$version.'.yml';
        $subDomain       = [];
        if (true === file_exists($filename)) {
            $subDomain = decode_file($filename);
        }

        return array_merge($global, $subDomain);
    }
}
