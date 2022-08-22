<?php

    namespace App\Models;

    use \PDO;

    class DB
    {
        private $host = 'dbserver';
        private $port = '3306';
        private $user = 'root';
        private $pass = 'root';
        private $dbname = 'meubebe';

        public function connect()
        {
            // $conn_str = "mysql:host=$this->host;dbname=$this->dbname";
            $dsn = "mysql:host=$this->host;port=$this->port;dbname=$this->dbname";
            $conn = new PDO($dsn, $this->user, $this->pass);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            return $conn;
        }
    }