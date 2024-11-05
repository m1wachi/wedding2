<?php
 $host = 'localhost';
 $port = 3307;
 $dbName = 'user_auth';
 $user = 'root';
 $password = '';

 $dsn = "mysql:host={$host};port={$port};dbname={$dbName}; charset=utf8";

 try{
    $pdo = new PDO($dsn, $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo 'Database connected...';
 } catch(PDOException $e){
    echo 'Connection failed...'. $e->getMessage();
 }

?> 