<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Startie\Router;

final class RouterTest extends TestCase
{
    const SIMPLE_PARSED_ROUTE_CONFIG = [
        'url' => 'blog',
        'urlParts' => ['blog'],
        'urlPartsCount' => 1,

        'type' => null,
        'title' => null,
        'layout' => null,
        'middles' => null,
        'controller' => 'Blog::index',
    ];

    const PARSED_ROUTE_CONFIG_WITH_PARAMS = [
        'url' => 'users/$userId:int/posts/$posts:int',
        'urlParts' => ['users', '$userId:int', 'posts', '$posts:int'],
        'urlPartsCount' => 4,

        'type' => null,
        'title' => null,
        'layout' => null,
        'middles' => null,
        'controller' => 'UserPosts::show',
    ];

    public function testGetPathParts(): void
    {
        $parts = Router::getPathParts("users/10/edit");

        $this->assertSame($parts, [
            "users",
            "10",
            "edit"
        ]);
    }

    public function testParseRoutesWithoutParams()
    {
        $routes = [
            'blog' => [
                'controller' => 'Blog::index',
            ],
        ];

        $reality = Router::parseRoutes($routes);

        $expectation = [
            self::SIMPLE_PARSED_ROUTE_CONFIG
        ];

        $this->assertSame($expectation, $reality);
    }

    public function testParseRoutesWithParams()
    {
        $routes = [
            'users/$userId:int/posts/$posts:int' => [
                'controller' => 'UserPosts::show',
            ],
        ];

        $reality = Router::parseRoutes($routes);

        $expectation = [
            self::PARSED_ROUTE_CONFIG_WITH_PARAMS
        ];

        $this->assertSame($expectation, $reality);
    }

    public function testFindOne()
    {
        $parsedRoutes = [
            self::SIMPLE_PARSED_ROUTE_CONFIG,
            self::PARSED_ROUTE_CONFIG_WITH_PARAMS,
        ];

        $findResult = Router::findOne($parsedRoutes, [
            'users',
            '10',
            'posts',
            '200'
        ]);

        $expectation = [
            'hasFound' => true,
            'findedRouteConfig' => self::PARSED_ROUTE_CONFIG_WITH_PARAMS,
            'controllerParams' => [
                'userId' => '10',
                'posts' => '200',
            ],
        ];

        $this->assertSame($expectation, $findResult);
    }

    public function testPartHasVariable()
    {
        $part = '$userId:int';

        $expectation = true;

        $reality = Router::partHasVariable($part);

        $this->assertSame($expectation, $reality);
    }

    public function testPartHasNotVariable()
    {
        $part = 'blog';

        $expectation = false;

        $reality = Router::partHasVariable($part);

        $this->assertSame($expectation, $reality);
    }

    public function testGetVariableNameForInteger()
    {
        $currentUrlPart = "2";
        $currentRoutePart = '$userId:int';

        $expectation = "userId";

        $reality = Router::getVariableName($currentUrlPart, $currentRoutePart);

        $this->assertSame($expectation, $reality);
    }

    public function testGetVariableNameForString()
    {
        $currentUrlPart = "john";
        $currentRoutePart = '$name:str';

        $expectation = "name";

        $reality = Router::getVariableName($currentUrlPart, $currentRoutePart);

        $this->assertSame($expectation, $reality);
    }

    public function testGetVariableNameWithFailure()
    {
        $currentUrlPart = "php";
        $currentRoutePart = '$userId:int';

        $expectation = null;

        $reality = Router::getVariableName($currentUrlPart, $currentRoutePart);

        $this->assertSame($expectation, $reality);
    }
}