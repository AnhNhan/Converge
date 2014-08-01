<?php

/**
 * @author Anh Nhan Nguyen <anhnhan@outlook.com>
 */
class functions_sql_Test extends PHPUnit_Framework_TestCase
{
    public function testBasic1()
    {
        $data = [
            'str1' => SearchPrepStmt_Include,
            'str2' => SearchPrepStmt_Exclude,
            'str3' => SearchPrepStmt_Include,
        ];

        $exp = '(t = ?0 OR t = ?1) AND NOT (t = ?2)';

        self::assertEquals($exp, generate_search_prep_stmt_part($data, 't'));
    }

    public function testBasic2()
    {
        $data = [
            'str1' => SearchPrepStmt_Include,
            'str3' => SearchPrepStmt_Include,
        ];

        $exp = '(t = ?0 OR t = ?1)';

        self::assertEquals($exp, generate_search_prep_stmt_part($data, 't'));
    }
}
