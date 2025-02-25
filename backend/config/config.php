<?php
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();
$hostname = $_ENV['HOST_NAME'];
$username =  $_ENV['USER_NAME'];
$password = $_ENV['PASS_WORD'];
$dbName = $_ENV['DB_NAME'];

$APP_KEY = $_ENV['PUSHER_APP_KEY'];
$APP_SECRET = $_ENV['PUSHER_APP_SECRET'];
$APP_ID = $_ENV['PUSHER_APP_ID'];
$CLUSTER = $_ENV['PUSHER_APP_CLUSTER'];

$options = array(
  'cluster' => $CLUSTER,
  'useTLS' => true
);

$pusher = new Pusher\Pusher(
  $APP_KEY,
  $APP_SECRET,
  $APP_ID,
  $options
);

$conn = new mysqli($hostname, $username, $password, $dbName) or die("Connection Failed!!!");
