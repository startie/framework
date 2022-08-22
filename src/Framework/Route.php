<?php

namespace Startie;

use Startie\App;

class Route
{
    public function __construct($config)
    {
        $this->type = $config['type'] ?? "";
        $this->middles = $config['middles'] ?? "";
        $this->title = $config['title'] ?? "";
        $this->layout = $config['layout'] ?? "";

        $signature = $config['controller'];
        $this->class = explode("::", $signature)[0];
        $this->method = ucfirst(
            explode("::", $signature)[1]
        );

        $this->classMethodExecution = "{$this->class}_Controller::" . strtolower($this->method);
        $this->classFull = "{$this->class}_Controller";
        $this->controllerNamespacedClass = "\Controllers\\{$this->classFull}";

        $this->controllerFilePath = App::path("backend/Controllers/{$this->classFull}.php");
    }

    // public static function extractMethodFromRoute($params)
    // {
    //     #Dump::made($params);
    //     extract($params);
    //     $routeClassMethodPieces = explode("::", $routeExpression);

    //     # Get class name and make changes
    //     $routeClass = $routeClassMethodPieces[0];
    //     #$routeClass = strtolower($routeClass); #why?
    //     $routeClass = $routeClass . "_Controller";
    //     #$routeClass = ucfirst($routeClass);

    //     # Get method name and delete parentheses
    //     $routeMethod = $routeClassMethodPieces[1];
    //     $routeMethod = str_replace("()", "", $routeMethod);
    //     $routeMethod = strtolower($routeMethod);

    //     # Form possible execution expression
    //     return $routeClass . '::' . $routeMethod;
    // }
}
