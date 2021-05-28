<?php

namespace App\Service;

use \PDO;

class DatabaseService
{
    /**
     * Create database handler and return it.
     *
     * @return Instance
     */
    public function connect()
    {
        $db_username = "xxxxx";
        $db_password = "xxxxx";
        $dsn = "mysql:host=xxxxx;dbname=xxxxx;";
        $dbh = new PDO($dsn, $db_username, $db_password);
        return $dbh;
    }
}

