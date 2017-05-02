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
            $tpl->setPhpEngine(true);
            $tpl->setNotice(false);

            switch ($stage) {
                case 'production':
                    $tpl->setCompileCheck(false);
                    break;
                case 'staging':
                    $tpl->setCompileCheck(false);
                    break;
                default:
                    $tpl->setCompileCheck('dev');
            }

            $tpl->setCompileRoot(__BASE__.'/.template/app/View');
            $tpl->setTemplateRoot(__BASE__.'/app/Views');

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
        $debug = $this->debug;

        $this->setDatabaseConnection('master');
        $this->setDatabaseConnection('slave1');

        if (true === $debug) {
            $this->dbProfiler();
        }
    }

    public function setDatabaseConnection($name)
    {
        if ($dsn = getenv(strtoupper($name).'_DATABASE_URL')) {
            $this->di->setShared(
                $name,
                function () use ($dsn) {
                    $pdo = new \Peanut\Phalcon\Pdo;
                    $pdo->setDsn($dsn);
                    $pdo->setOptions([
                        \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
                        \PDO::ATTR_EMULATE_PREPARES   => false,
                        \PDO::ATTR_STRINGIFY_FETCHES  => false,
                        \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                    ]);

                    return $pdo->connect();
                }
            );
        }
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
        $this->di->setShared('router', function () {
            $router = new \Peanut\Phalcon\Mvc\Router\Rules\Hash();
            $router->setUriSource(
                \Phalcon\Mvc\Router::URI_SOURCE_SERVER_REQUEST_URI
            );

            return $router;
        });
        $routes = $this->getDomainRoutes();
        $this->getDi('router')->group($routes);
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
