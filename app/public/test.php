<?php

    class Database
    {
        private $host = 'localhost';
        private $port = '3307';
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

    use App\Models\BabyModel;

    require __DIR__ . '/../bootstrap.php';

    $baby = new BabyModel();
    $baby->uuid = UUID_v4();
    $baby->name = 'Ricardo Cardozo Silveira';
    $baby->description = 'Lorem ipsum dolor sit, amet consectetur adipisicing elit.';
    $baby->gender = 'M';
    $baby->born_at = '2022-09-05';
    $baby->created_at = (new DateTime('now'))->format('Y-m-d H:i:s'); 

    