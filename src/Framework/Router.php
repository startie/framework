<?php

declare(strict_types=1);

namespace Startie;

use Startie\View;
use Startie\Url;
use Startie\Route;
use Startie\Layout;
use Startie\In;
use Startie\App;

class Router
{
    /**
     * @var Route Current route
     */
    public static Route $route;

    /**
     * @var float Timestamp of router initialization
     */
    public static float $initializedAt;

    public static function init(): void
    {
        if (!$_ENV['POWER']) {
            Output::error(403);
        }

        Router::$initializedAt = microtime(true);

        $routes = Router::getRoutes();
        $location = Router::getLocation();

        $foundRoute = Router::find($routes, $location);

        [
            'hasFound' => $hasFound,
            'findedRouteConfig' => $findedRouteConfig,
            'controllerParams' => $controllerParams
        ] = $foundRoute;

        if ($hasFound) {
            Router::render($findedRouteConfig, $controllerParams);
        } else {
            Output::error(404);
        }
    }

    public static function resolveRouterConfigPath(): string|null
    {
        $filesToCheck = [
            "backend/Config/Router/*.php", // best
            "backend/Config/Router/Routs.php",
            "backend/Config/Router/Routes.php",
            "backend/Config/Router/routs.php",
            "backend/Config/Router/routes.php",
            "backend/Config/Router/All.php",
            "backend/Config/Router/all.php",
            "backend/Config/Router/Main.php",
            "backend/Config/Router/main.php",
        ];

        foreach ($filesToCheck as $fileToCheck) {
            $configPath = App::path($fileToCheck);

            if (file_exists($configPath)) {
                return $configPath;
            }
        }

        return null;
    }

    /**
     * Tries to find location of all route files
     * Each route file may contain several routes
     * With trailing slash
     */
    public static function resolveRoutesRoot(): string
    {
        $dirsToCheck = [
            "backend/routes/", // best
            "backend/Routs/",
            "backend/Routes/",
            "backend/routs/",
        ];

        $rootDir = null;

        foreach ($dirsToCheck as $dirToCheck) {
            $rootDir = App::path($dirToCheck);

            if (file_exists($rootDir)) {
                return $dirToCheck;
            }
        }

        if ($rootDir === null) {
            throw new \Startie\Exception("Routes root dir is not found");
        }
    }

    public static function getRoutes()
    {
        $routesRoot = Router::resolveRoutesRoot();
        $routerConfigPath = Router::resolveRouterConfigPath();

        $routes = [];

        if (is_null($routerConfigPath)) {
            $routes = Router::collectRoutesBasedOnRoot($routesRoot);
        } else {
            $routes = Router::collectRoutesBasedOnConfig(
                $routerConfigPath,
                $routesRoot
            );
        }

        return $routes;
    }

    public static function collectRoutesBasedOnRoot($rootPathRelative)
    {
        $routes = [];

        $rootPathAbsolute = App::path($rootPathRelative);

        $routeFiles = scandir($rootPathAbsolute);

        foreach ($routeFiles as $routeFile) {
            if ($routeFile !== "." && $routeFile !== "..") {
                $routeContent = require "{$rootPathAbsolute}{$routeFile}";

                $routes = array_merge($routeContent, $routes);
            }
        }

        return $routes;
    }

    public static function collectRoutesBasedOnConfig(
        $routerConfigPath,
        $routesRoot
    ) {
        $routes = [];

        $routerConfig = require $routerConfigPath;

        if (!is_array($routerConfig)) {
            throw new \Startie\Exception(
                "File $routerConfigPath should return an array"
            );
        } else if (is_array($routerConfig)) {
            foreach ($routerConfig as $routesFileName) {
                $routesFilePath = App::path(
                    "{$routesRoot}{$routesFileName}.php"
                );

                $routesFileContent = require $routesFilePath;
                $routes = array_merge($routesFileContent, $routes);
            }
        }

        return $routes;
    }

    /**
     * Get HTTP location, where user intented to go
     * 
     * @throws \Startie\Exception
     */
    public static function getLocation(): string
    {
        $requestedPath = In::server('REQUEST_URI', 'STR');

        // Delete domain (for sites like http://localhost:8080/startie-project/)
        $requestedPath = str_replace(In::env('DOMAIN'), "", $requestedPath);

        $location = Url::cleanFromQueryString($requestedPath);

        $location = trim($location, "/");

        if ($location === "") {
            $location = "/";
        }

        if (!$location) {
            throw new \Startie\Exception(
                "The requested location for router is not found: "
                    . PHP_EOL
                    . "- `url` query param is not set"
                    . PHP_EOL
                    . '- `REQUEST_URI` in `$_SERVER` is not set'
                    . PHP_EOL
                    . PHP_EOL
                    . "Is server running?"
            );
        }

        return $location;
    }

    public static function find(
        array $routes,
        string $pathToFind
    ) {
        // Prepare and mutate data of each route for search
        $parsedRoutes = Router::parseRoutes($routes);

        $urlParts = Router::getPathParts($pathToFind);
        $result = Router::findOne($parsedRoutes, $urlParts);

        return $result;
    }

    /**
     * @tested
     */
    public static function parseRoutes($routes)
    {
        $result = [];

        foreach ($routes as $path => $routeData) {
            $routeUrlParts = Router::getPathParts($path);

            $result[] = [
                'url' => $path,
                'urlParts' => $routeUrlParts,
                'urlPartsCount' => count($routeUrlParts),

                'type' => $routeData['type'] ?? null,
                'title' => $routeData['title'] ?? null,
                'layout' => $routeData['layout'] ?? null,
                'middles' => $routeData['middles'] ?? null,
                'controller' => $routeData['controller'] ?? null,
            ];
        }

        return $result;
    }

    /**
     * @tested
     */
    public static function findOne(array $parsedRoutes, array $urlParts): array
    {
        $urlPartsCount = count($urlParts);

        $hasFound = false;
        $findedRouteConfig = [];
        $controllerParams = [];

        // Filter by parts count
        $parsedRoutes = array_filter(
            $parsedRoutes,
            fn($routeParsed) => $urlPartsCount === $routeParsed['urlPartsCount']
        );

        // Check each route
        foreach ($parsedRoutes as $routeParsed) {
            $controllerParams = [];
            if (!$hasFound) {
                $successChecks = 0;

                // Compare each part of route
                for (
                    $partIndex = 0;
                    $partIndex < $urlPartsCount;
                    $partIndex++
                ) {
                    $currentUrlPart = $urlParts[$partIndex];
                    $currentRoutePart = $routeParsed['urlParts'][$partIndex];

                    if (Router::partHasVariable($currentRoutePart)) {
                        $variableName = Router::getVariableName(
                            $currentUrlPart,
                            $currentRoutePart,
                        );

                        if (!is_null($variableName)) {
                            $successChecks++;
                            $controllerParams[$variableName] = $currentUrlPart;
                        }
                    }

                    if (!Router::partHasVariable($currentRoutePart)) {
                        if ($currentUrlPart === $currentRoutePart) {
                            $successChecks++;
                        }
                    }
                }

                // Check count of successfull checks with parts count
                if ($successChecks === $urlPartsCount) {
                    $findedRouteConfig = $routeParsed;
                    $hasFound = true;
                }
            }
        }

        $result = [
            'hasFound' => $hasFound,
            'findedRouteConfig' => $findedRouteConfig,
            'controllerParams' => $controllerParams,
        ];

        return $result;
    }

    public static function partHasVariable($part)
    {
        if (strpos($part, '$') !== false) {
            return true;
        } else {
            return false;
        }
    }

    public static function getVariableName(
        string $currentUrlPart,
        string $currentRoutePart
    ): string|null {
        // Get pieces of part for extracting 'variable name' and 'type'
        $routePartPieces = explode(":", $currentRoutePart);

        // Here is 'variable name'
        $routePartVar = $routePartPieces[0];
        $routePartVar = str_replace("$", "", $routePartVar);

        // Here is 'type' to validate
        $routePartType = $routePartPieces[1];

        // Check if is it numeric
        if (
            $routePartType == 'int'
            || $routePartType == 'integer'
            || $routePartType == 'number'
        ) {
            $routePartType = 'numeric';
        }

        // Form validation procedure
        $routePartValidateClassMethodExpression
            = "Startie\Validate::" . $routePartType;

        // Validate url part by "Validate::$routePartType()"
        $isValid = call_user_func_array(
            $routePartValidateClassMethodExpression,
            [$currentUrlPart]
        );

        if ($isValid) {
            return $routePartVar;
        } else {
            return null;
        }
    }

    public static function render($routeData, $controllerParams)
    {
        /* Route */

        $route = new Route($routeData);
        Router::$route = $route;

        /* Boostrap */

        Router::bootstrap($route);

        /* Middles */

        Router::middles($route);

        /* Title */

        if (isset($route->title)) {
            $title = "<title>$route->title</title>";
        }

        /* Load layout */

        if ($route->layout) {
            $layout = Layout::return(
                ucfirst($route->layout)
            );
        }

        /* Content */

        if (!file_exists($route->controllerFilePath)) {
            throw new \Exception(
                "File on '{$route->controllerFilePath}' doesn't exsists"
            );
        } else {
            require $route->controllerFilePath;
        }


        if (!class_exists($route->controllerNamespacedClass)) {
            throw new \Exception(
                "Class '{$route->controllerNamespacedClass}' doesn't exsists"
            );
        }
        if (!method_exists($route->controllerNamespacedClass, $route->method)) {
            throw new \Exception(
                "Controller method '{$route->classMethodExecution}' doesn't exsists"
            );
        }

        /* If we don't have params */

        if (empty($controllerParams)) {
            $controllerClassMethod = "\Controllers\\" . $route->classMethodExecution;
            $content = call_user_func($controllerClassMethod);
        }

        /* If we have params */
        if (!empty($controllerParams)) {
            $content = call_user_func_array(
                "\Controllers\\" . $route->classMethodExecution,
                [$controllerParams]
            );
        }

        /* Fill blocks */

        if ($route->layout) {
            $Config = require App::path("backend/Config/Layout/Common.php");
            $ConfigBlocks = $Config['blocks'];

            $ConfigBlocks['content'] = $content;
            $ConfigBlocks['title'] = $title;

            foreach ($ConfigBlocks as $BlockLabel => $BlockContent) {
                $layout = str_replace(
                    "{{{$BlockLabel}}}",
                    $BlockContent ?? "",
                    $layout
                );
            }
        }

        if ($route->layout) {
            echo $layout;
        } else {
            echo $content;
        }
    }


    public static function bootstrap(Route $route)
    {
        if ($route->type) {
            $RouteTypeUCFirst = ucfirst(strtolower($route->type));

            $BootstrapPath = App::path(
                "backend/Config/Bootstrap/$RouteTypeUCFirst.php"
            );
            if (file_exists($BootstrapPath)) {
                require $BootstrapPath;
            } else {
                throw new \Startie\Exception(
                    "Path '$BootstrapPath' is missing"
                );
            }
        }
    }

    public static function middles(Route $route)
    {
        if (isset($route->middles)) {
            $middlesStr = str_replace(" ", "", $route->middles);

            if ($middlesStr !== "") {
                $middlesArr = explode(',', $middlesStr);
                foreach ($middlesArr as $middle) {;
                    $MiddlePath = App::path("backend/Middles/$middle.php");
                    if (file_exists($MiddlePath)) {
                        require $MiddlePath;
                    } else {
                        throw new \Startie\Exception(
                            "Path '$MiddlePath' is missing"
                        );
                    }
                }
            }
        }
    }

    /**
     * Get array of URI's path parts based on slash
     * @tested
     */
    public static function getPathParts(string $path): array
    {
        $path = rtrim($path, '/');

        $parts = explode('/', $path);

        return $parts;
    }

    /**
     * Check if passed signature (controller::method) belongs to current url
     */
    public static function isCurrent(string $signature): bool
    {
        return Url::current() == Url::c($signature);
    }

    /**
     * Pass error code to display a certain view
     */
    public static function errorPage(int $code): void
    {
        $ConfigRouterPagesPath = App::path("backend/Config/Router/Pages.php");

        if (file_exists($ConfigRouterPagesPath)) {
            $ConfigRouterPages = require App::path(
                "backend/Config/Router/Pages.php"
            );

            if (isset($ConfigRouterPages[$code])) {
                if (isset($ConfigRouterPages[$code]['view'])) {
                    $view = $ConfigRouterPages[$code]['view'];
                } else {
                    throw new \Startie\Exception(
                        "View for '$code' error not found"
                    );
                }
            } else {
                throw new \Startie\Exception(
                    "Configuration for '$code' not found"
                );
            }

            echo View::r($view);
        } else {
            throw new \Startie\Exception(
                "Error page for code '$code' is not cofigured in 'backend/Config/Router/Pages.php'"
            );
        }
    }
}