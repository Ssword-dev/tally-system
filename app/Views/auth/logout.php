<?php

use App\Auth;
use App\Router\Request;
use App\Router\Response;

return function (Request $request, Response $response): Response {
    Auth::logout('/student-database-system/');
    return $response;
};
?>