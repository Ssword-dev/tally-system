<?php

$host = 'localhost';
$user = 'root';
$password = '';
$database = 'acerdb';

$connection = mysqli_connect($host, $user, $password, $database);

if (!$connection) {
    echo 'Error: ' . mysqli_connect_error();
}

// $templateQuery = 'SELECT * FROM `customers`;';
// $statement = $connection->prepare($templateQuery);

// if (!$statement) {
//     echo 'Failed to prepare statement.';
// }

// $statement->execute();
// $result = $statement->get_result();
// $customers = $result->fetch_all(MYSQLI_ASSOC);

// var_dump($customers);
