<?php
$host = "localhost";
$user = "root";
$pass = "";          
$db   = "users";    

$mysqli = new mysqli($host, $user, $pass, $db);

if ($mysqli->connect_error) {
    die("Erro de conexão: " . $mysqli->connect_error);
}
