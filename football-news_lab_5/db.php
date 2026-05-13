<?php
$mysqli = new mysqli("localhost", "root", "", "football_news",3307);

if ($mysqli->connect_errno !=0) {
    die("Помилка підключення до бази даних: " . $mysqli->connect_error);
}
$mysqli->set_charset("utf8mb4");
?>