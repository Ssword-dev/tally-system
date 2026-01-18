<?php
require __DIR__ . '/bootstrap/app.php';

use App\Router\Request;
use App\Router\Response;
use App\Router\Router;

$router = Router::getInstance();

try {
    // Create a request from global variables
    $request = Request::fromGlobals();

    // Handle the request through the router
    $response = $router->handle($request);

    // Send the response to the client
    if (!$response->hasBeenSent()) {
        $response->send();
    }
} catch (\Throwable $e) {
    // Handle uncaught exceptions
    $response = Response::error('Internal Server Error: ' . $e->getMessage(), 500);
    $response->setContentType('text/html');
    $response->send();

    if ($_ENV['APP_ENV'] !== 'production') {
        echo "\n\nDebug Information:\n";
        echo "Error: " . $e->getMessage() . "\n";
        echo "File: " . $e->getFile() . "\n";
        echo "Line: " . $e->getLine() . "\n";
        echo "Trace:\n" . $e->getTraceAsString() . "\n";
    }
}
