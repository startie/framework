<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Startie\Router;

final class RouterTest extends TestCase
{
    public function test_gets_path_parts(): void
    {
        $parts = Router::getPathParts("users/10/edit");

        $this->assertSame($parts, [
            "users",
            "10",
            "edit"
        ]);
    }
}