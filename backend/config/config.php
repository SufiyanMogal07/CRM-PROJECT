<?php
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__.'/..');
$dotenv->load();
$hostname = $_ENV['HOST_NAME'];
$username =  $_ENV['USER_NAME'];
$password = $_ENV['PASS_WORD'];
$dbName = $_ENV['DB_NAME'];

$conn = new mysqli($hostname,$username,$password,$dbName) or die("Connection Failed!!!");