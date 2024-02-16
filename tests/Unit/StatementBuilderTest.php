<?php

declare(strict_types=1);

use Startie\StatementBuilder;
use PHPUnit\Framework\TestCase;

class StatementBuilderTest extends TestCase
{
    /** @test */
    public function it_generates_select()
    {
        $select = [
            'name',
            'city',
        ];

        $sql = "";

        StatementBuilder::select($sql, $select);

        $expectation = " SELECT\n\t name, \n\t city,\n\n ";

        $this->assertEquals($sql, $expectation);
    }

    /** @test */
    public function it_generates_from()
    {
        $from = "users";

        $sql = "";

        StatementBuilder::from($sql, $from);

        $expectation = " FROM users\n\n ";

        $this->assertEquals($sql, $expectation);
    }

    /** @test */
    public function it_generates_join_with_1_join_with_2_arguments()
    {
        $join = [
            'members' => ['members.project_id', 'accounts.project_id'],
        ];

        $sql = "";
        StatementBuilder::join($sql, $join);

        $expectation = " INNER JOIN members ON"
            . " members.project_id = accounts.project_id\n"
            . "\n"
            . "\n"
            . " ";

        $this->assertEquals($sql, $expectation);
    }

    /** @test */
    public function it_generates_join_with_1_join_with_3_arguments()
    {
        $join = [
            'members' => [
                'members.project_id',
                'accounts.project_id',
                'left'
            ],
        ];

        $sql = "";
        StatementBuilder::join($sql, $join);

        $expectation = " LEFT JOIN members ON"
            . " members.project_id = accounts.project_id\n"
            . "\n"
            . "\n"
            . " ";

        $this->assertEquals($sql, $expectation);
    }

    /** @test */
    public function it_generates_join_with_2_join_with_3_arguments()
    {
        $join = [
            'members' => [
                'members.project_id',
                'accounts.project_id',
                'left'
            ],
            'projects' => [
                'projects.id', 'members.project_id'
            ],
        ];

        $sql = "";
        StatementBuilder::join($sql, $join);

        $expectation = " "
            . "LEFT JOIN members ON"
            . " members.project_id = accounts.project_id\n"
            . "INNER JOIN projects ON"
            . " projects.id = members.project_id\n"
            . "\n"
            . "\n"
            . " ";

        $this->assertEquals($sql, $expectation);
    }

    // /** @test */
    // public function it_generates_clause()
    // {
    // }
}