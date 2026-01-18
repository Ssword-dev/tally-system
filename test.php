<?php
require __DIR__ . "/src/bootstrap.php";

use App\Router\RoutePattern;

$pattern = RoutePattern::compile("/users/[id]");
$tokens = RoutePattern::tokenize("/users/[id]");
$samples = [
    "/users/123",
    "/users/456",
    "/users/789"
];

foreach ($samples as $sample) {
    $match = $pattern->match($sample);
    echo "Pattern matches $sample:" . ($match ? 'yes' : 'no') . PHP_EOL;
    var_dump($match);
}