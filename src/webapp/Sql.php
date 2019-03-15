<?php

namespace ttm4135\webapp;

class Sql
{
    static $pdo;

    function __construct()
    {
    }

    /**
     * Create tables.
     */
    static function up() {
        $q1 = "CREATE TABLE users (id INTEGER PRIMARY KEY, username VARCHAR(50), password VARCHAR(50), email varchar(50),  bio varhar(50), isadmin INTEGER);";

        self::$pdo->exec($q1);

        print "[ttm4135] Done creating all SQL tables.".PHP_EOL;

        self::insertDummyUsers();
    }

    static function insertDummyUsers() {
        $query = self::$pdo->prepare("INSERT INTO users(username, password, isadmin) VALUES (:username, :password, :isadmin)");
        $query->bindParam(':username', $username);
        $query->bindParam(':password', $password);
        $query->bindParam(':isadmin',  $isadmin);

        $username = 'admin';
        $password = password_hash('admin', PASSWORD_BCRYPT);
        $isadmin = 1;
        $query->execute();

        $username = 'bob';
        $password = password_hash('bob', PASSWORD_BCRYPT);
        $isadmin = 0;
        $query->execute();

        print "[ttm4135] Done inserting dummy users.".PHP_EOL;
    }

    static function down() {
        $q1 = "DROP TABLE users";

        self::$pdo->exec($q1);

        print "[ttm4135] Done deleting all SQL tables.".PHP_EOL;
    }

}
try {
    // Create (connect to) SQLite database in file
    Sql::$pdo = new \PDO('sqlite:/home/grp38/apache/htdocs/site/app.db');
    // Set errormode to exceptions
    Sql::$pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
} catch(\PDOException $e) {
    echo $e->getMessage();
    exit();
}
