<?php

namespace Startie;

use Startie\App;

class Route
{
    public string $type;
    public string $middles;
    public string $title;
    public string $layout;
    public string $class;
    public string $method;
    public string $classMethodExecution;
    public string $classFull;
    public string $controllerNamespacedClass;
    public string $controllerFilePath;

    public function __construct(array $config)
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
        $this->classMethodExecution
            = "{$this->class}_Controller::{$this->method}";

        $this->classFull = "{$this->class}_Controller";

        $this->controllerNamespacedClass
            = "\Controllers\\{$this->classFull}";

        $this->controllerFilePath
            = App::path("backend/Controllers/{$this->classFull}.php");
    }
}