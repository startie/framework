<?php

namespace Startie;

use Startie\App;

class Route
{
    public $type;
    public $middles;
    public $title;
    public $layout;
    public $class;
    public $method;
    public $classMethodExecution;
    public $classFull;
    public $controllerNamespacedClass;
    public $controllerFilePath;

    public function __construct($config)
    {
        $this->type = $config['type'] ?? "";
        $this->middles = $config['middles'] ?? "";
        $this->title = $config['title'] ?? "";
        $this->layout = $config['layout'] ?? "";

        $signature = $config['controller'];
        $this->class = explode("::", $signature)[0];

        // $this->method = ucfirst(
        //     explode("::", $signature)[1]
        // );
        $this->method = explode("::", $signature)[1];

        //$this->classMethodExecution = "{$this->class}_Controller::" . strtolower($this->method);
        $this->classMethodExecution = "{$this->class}_Controller::{$this->method}";
        $this->classFull = "{$this->class}_Controller";
        $this->controllerNamespacedClass = "\Controllers\\{$this->classFull}";

        $this->controllerFilePath = App::path("backend/Controllers/{$this->classFull}.php");
    }
}
