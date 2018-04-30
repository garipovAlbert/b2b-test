<?php

namespace testframework;

use Exception;
use ReflectionMethod;
use testframework\auth\User;

/**
 * @author albert
 * 
 * @property Db $db
 */
class App extends Object
{

    private $_c = [];
    private static $_singletone;

    /**
     * @var string
     */
    public $rootDir;

    /**
     * @var boolean
     */
    public $debug = false;

    /**
     * @var string
     */
    public $controllerNamespace;

    /**
     * Default components configuration.
     * @var array
     */
    protected $default = [
        'router' => [
            'class' => Router::class,
        ],
        'db' => [
            'class' => Db::class,
        ],
        'user' => [
            'class' => User::class,
        ],
        'request' => [
            'class' => Request::class,
        ],
    ];

    /**
     * @inheritdoc
     */
    public function init()
    {
        if (self::$_singletone === null) {
            self::$_singletone = $this;
        } else {
            throw new Exception('Only one instance of application should exist (singleton pattern).');
        }
    }

    /**
     * @return static
     */
    public static function get()
    {
        return self::$_singletone;
    }

    protected function setComponent($name, $config)
    {
        $this->_c[$name] = $config;
    }

    protected function getComponent($name)
    {
        if (!isset($this->_c[$name]) || !is_object($this->_c[$name])) {
            $config = array_merge($this->default[$name], $this->_c[$name] ?? []);
            $this->_c[$name] = Helper::create($config);
        }
        return $this->_c[$name];
    }

    /**
     * @param mixed $router
     */
    public function setRouter($router)
    {
        $this->setComponent('router', $router);
    }

    /**
     * @return Router
     */
    public function getRouter()
    {
        return $this->getComponent('router');
    }

    /**
     * @param mixed $db
     */
    public function setDb($db)
    {
        $this->setComponent('db', $db);
    }

    /**
     * @return Db
     */
    public function getDb()
    {
        return $this->getComponent('db');
    }

    /**
     * @param mixed $user
     */
    public function setUser($user)
    {
        $this->setComponent('user', $user);
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->getComponent('user');
    }

    /**
     * @param mixed $request
     */
    public function setRequest($request)
    {
        $this->setComponent('request', $request);
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->getComponent('request');
    }

    public function run()
    {
        $response = new Response;

        try {
            list($statusCode, $object) = $this->processRequest();
            $response->send($statusCode, $object);
        } catch (HttpException $e) {
            $response->send($e->statusCode, $e);
        }
    }

    public function processRequest()
    {
        list($route, $params) = $this->getRouter()->getRoute();
        if ($route === NULL) {
            throw new HttpException(404, "Not Found");
        }

        list($controllerId, $actionId) = explode('/', $route);

        $controllerClass = $this->controllerNamespace . '\\' . Helper::dashesToCamel($controllerId) . 'Controller';
        $actionMethod = 'action' . Helper::dashesToCamel($actionId);

        $controller = Helper::create([
            'class' => $controllerClass,
            'actionId' => $actionId,
        ]);

        $actionArguments = [];
        $r = new ReflectionMethod($controller, $actionMethod);
        foreach ($r->getParameters() as $p) {
            if (array_key_exists($p->getName(), $params)) {
                $actionArguments[] = $params[$p->getName()];
            } else {
                $actionArguments[] = $p->getDefaultValue();
            }
        }

        $result = call_user_func_array([$controller, $actionMethod], $actionArguments);

        return $result;
    }

}