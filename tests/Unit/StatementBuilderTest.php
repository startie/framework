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

        $expectation = " SELECT\n\t name, \n\t city\n\n ";

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

    /** @test */
    public function is_resolves_join_type_correctly()
    {
        $resolvedJoinType = StatementBuilder::resolveJoinType("left");
        $expectation = "LEFT";

        $this->assertEquals($resolvedJoinType, $expectation);
    }

    /** @test */
    public function it_generates_clause_with_one_column_and_equal_sign()
    {
        $where = [
            'id' => [
                [1],
            ],
        ];

        $sql = "";
        StatementBuilder::clause($sql, $where, "WHERE");
        // var_dump($sql);

        $expectation = " "
            . "WHERE \t 1 = 1 "
            . "\n"
            . "\t AND ( "
            . ""
            . "id = :id0"
            . " ) "
            . "\n"
            . " ";
        // var_dump($expectation);

        $this->assertEquals($sql, $expectation);
    }

    /** @test */
    public function it_generates_clause_with_one_column_and_greater_than_sign()
    {
        $where = [
            'id' => [
                [">1"],
            ],
        ];

        $sql = "";
        StatementBuilder::clause($sql, $where, "WHERE");
        // var_dump($sql);

        $expectation = " "
            . "WHERE \t 1 = 1 "
            . "\n"
            . "\t AND ( "
            . ""
            . "id > :id0"
            . " ) "
            . "\n"
            . " ";
        // var_dump($expectation);

        $this->assertEquals($sql, $expectation);
    }

    /** @test */
    public function is_generates_raw_clauses_for_is_null()
    {
        $columnName = "age";
        $signHolder = "`IS NULL`";

        $sql = "";
        $sql .= StatementBuilder::generateRawClauses($columnName, $signHolder);

        $expectation = ""
            . "age IS NULL"
            . " OR ";

        $this->assertEquals($sql, $expectation);
    }

    /** @test */
    public function it_generates_insert_with_1_field()
    {
        $insert = [
            ['name', 'John'],
        ];
        $table = "users";

        $sql = "";
        StatementBuilder::insert($sql, $insert, $table);

        $expectation = ""
            . " INSERT INTO users "
            . " ( `name` ) "
            . " VALUES "
            . " (  :name ) ";

        $this->assertEquals($sql, $expectation);
    }

    /** @test */
    public function it_generates_insert_with_2_fields()
    {
        $insert = [
            ['name', 'John'],
            ['age', '30'],
        ];
        $table = "users";

        $sql = "";
        StatementBuilder::insert($sql, $insert, $table);

        $expectation = ""
            . " INSERT INTO users "
            . " ( `name`, `age` ) "
            . " VALUES "
            . " (  :name,  :age ) ";

        $this->assertEquals($sql, $expectation);
    }

    /** @test */
    public function it_generates_insert_with_raw()
    {
        $insert = [
            ['created_at', '`UTC_TIMESTAMP()`'],
        ];
        $table = "users";

        $sql = "";
        StatementBuilder::insert($sql, $insert, $table);

        $expectation = ""
            . " INSERT INTO users "
            . " ( "
            . "`created_at`"
            . " ) "
            . " VALUES "
            . " (  UTC_TIMESTAMP() ) ";

        $this->assertEquals($sql, $expectation);
    }

    /** @test */
    public function it_generates_update()
    {
        $table = "users";

        $sql = "";
        $sql .= StatementBuilder::update($table);

        $expectation = ""
            . " UPDATE users ";

        $this->assertEquals($sql, $expectation);
    }

    /** @test */
    public function it_generates_delete()
    {
        $sql = "";
        $sql .= StatementBuilder::delete();

        $expectation = ""
            . "\n"
            . "DELETE"
            . "\n";

        $this->assertEquals($sql, $expectation);
    }
}