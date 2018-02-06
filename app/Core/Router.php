<?php
namespace Dcms\Core;

use \App\App\App;

class Router{
    public static $routes = [];
    public static $route;


    public static function get($pattern, array $options)
    {
        self::add($pattern, $options, 'GET');
    }
    public static function post($pattern, array $options)
    {
        self::add($pattern, $options, 'POST');
    }

    private static function add(string $pattern, array $options, string $method = 'GET')
    {
        $group = $options['group'] ?? 0;
        if ($group > App::user()->group) {
            return;
        }
        if (isset($options['onlyGroup']) && $options['onlyGroup'] !== App::user()->group) {
            return;
        }
        $pattern = AVAILABLE_LANG . $pattern . '/?(\?page\=[0-9]+)?';
        self::$routes[] = ['pattern' => $pattern, 'run' => $options['use'], 'method' => $method, 'name' => $options['name']];
    }
    private static function matchRoute()
    {
        //$routes_default = App::config('routes', true);
        //$routes = array_merge($routes_default, self::$routes);
        foreach (self::$routes as $route) {
            # сравниваем метод передачи данных
            if (strpos($route['method'], $_SERVER['REQUEST_METHOD']) === false) {
                continue;
            }
            if (preg_match('#^' . $route['pattern'] . '$#i', App::getURI(), $matches)) {
                $run = explode('@', $route['run']);
                $route['controller'] = $run[0];
                $route['action'] = $run[1];
                /*
                * планировалось что нужные параметры будут с указанными строчными ключами
                * но не срослось...
                $arr = array_filter($matches, function($v, $k){
                    if (is_string($k)) {
                        return $v;
                    }
                }, ARRAY_FILTER_USE_BOTH);*/
                unset($route['method']); // метод передачи данных, убираем его
                unset($route['run']); // контроллер/экшен, убираем его
                unset($route['pattern']); // паттерн не нужен, убираем его
                unset($matches[0]); // тут строка в которой найдено совпадение, тоже убираем
                unset($matches[1]); // тут локализация в параметре, она не нужна
                unset($route['name']); // тут локализация в параметре, она не нужна
                self::$route = array_merge($route, $matches);
                return true;
            }
        }
        return false;
    }
    public static function dispatch()
    {
        if (self::matchRoute()) {
            $controller = '\Dcms\Http\Controllers\\' . ucfirst(array_shift(self::$route)) . 'Controller';
            $action = array_shift(self::$route);
            $obj = new $controller;
            call_user_func_array([$obj, $action], self::$route);
            global $dcms;
        } else {
            //http_response_code(404);
            echo '=(';
        }
    }
    public static function getRoutes()
    {
        return self::$routes;
    }
}