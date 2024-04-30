<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Startie\QueryBinder;

final class QueryBinderTest extends TestCase
{
    /*
        fixType
    */

    public function test_fix_str_uppercase_type(): void
    {
        $rawType = "STR";

        $fixedType = QueryBinder::fixType($rawType);

        $this->assertSame($fixedType, "STR");
    }

    public function test_fix_str_type(): void
    {
        $rawType = "str";

        $fixedType = QueryBinder::fixType($rawType);

        $this->assertSame($fixedType, "STR");
    }

    public function test_fix_integer_type(): void
    {
        $rawType = "integer";

        $fixedType = QueryBinder::fixType($rawType);

        $this->assertSame($fixedType, "INT");
    }

    public function test_fix_unknown_type(): void
    {
        $rawType = "unknown";

        $fixedType = QueryBinder::fixType($rawType);

        $this->assertSame($fixedType, "UNKNOWN");
    }

    /*
        isValidType
    */

    public function test_str_is_valid_type(): void
    {
        $rawType = "STR";

        $validationResult = QueryBinder::isValidType($rawType);

        $this->assertSame($validationResult, true);
    }
    
    public function test_string_is_valid_type(): void
    {
        $rawType = "STRING";

        $validationResult = QueryBinder::isValidType($rawType);

        $this->assertSame($validationResult, false);
    }

    public function test_int_is_valid_type(): void
    {
        $rawType = "INT";

        $validationResult = QueryBinder::isValidType($rawType);

        $this->assertSame($validationResult, true);
    }

    public function test_null_is_valid_type(): void
    {
        $rawType = "NULL";

        $validationResult = QueryBinder::isValidType($rawType);

        $this->assertSame($validationResult, true);
    }

    /*
        validateType
    */

    public function test_fix_validate_str_uppercase_type(): void
    {
        $column = "id";
        $rawType = "STR";

        $validatedType = QueryBinder::validateType($rawType, $column);

        $this->assertSame($validatedType, "STR");
    }

    public function test_fix_validate_str_type(): void
    {
        $column = "id";
        $rawType = "str";

        $validatedType = QueryBinder::validateType($rawType, $column);

        $this->assertSame($validatedType, "STR");
    }

    public function test_fix_validate_unknown_type(): void
    {
        $column = "id";
        $this->expectException(\Startie\Exception::class);
        $rawType = "unknown";
        
        QueryBinder::validateType($rawType, $column);
    }
}