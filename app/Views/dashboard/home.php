<?php

use App\SimplePage;
use App\Repositories\StudentRepository;
use App\Repositories\ClassRepository;
use App\Repositories\ActivityRepository;
use App\Router\Request;
use App\Router\Response;

return function (Request $request, Response $response): Response {
    // Check if user is logged in
    if (!auth()) {
        return $response
            ->setStatusCode(302)
            ->setHeader('Location', '/student-database-system');
    }

    // Get repositories
    $studentRepo = StudentRepository::getInstance();
    $classRepo = ClassRepository::getInstance();
    $activityRepo = ActivityRepository::getInstance();

    // Get statistics
    $allStudents = $studentRepo->findAll();
    $studentCount = count(array_filter($allStudents, fn($s) => $s->created_by == auth()->id));

    $classModels = $classRepo->findByTeacherId(auth()->id);
    $classCount = count($classModels);

    $allActivities = $activityRepo->findAll();
    $activityCount = count(array_filter($allActivities, fn($a) => $a->created_by == auth()->id));

    $html = SimplePage::page([
        "attributes" => [
            "app_name" => $_ENV["APP_NAME"],
            "display_name" => auth()->firstName . " " . auth()->lastName,
            "page_title" => "Dashboard",
            "nav_links" => [
                ["href" => "./home", "label" => "Home", "active" => true],
                ["href" => "./students", "label" => "Students"],
                ["href" => "./sections", "label" => "Classes"],
                ["href" => "./activities", "label" => "Activities"],
                ["href" => "./grades", "label" => "Grades"],
                ["href" => "./teachers", "label" => "Teachers"],
            ]
        ],

        "content" => <<<HTML
            <div class="p-6 rounded-xl outline-1 outline-text bg-background/110">
                <h3 class="text-sm text-muted-foreground">students created</h3>
                <p class="mt-2 text-3xl font-bold">$studentCount</p>
            </div>
    
            <div class="p-6 rounded-xl outline-1 outline-text bg-background/110">
                <h3 class="text-sm text-muted-foreground">my classes</h3>
                <p class="mt-2 text-3xl font-bold">$classCount</p>
            </div>
    
            <div class="p-6 rounded-xl outline-1 outline-text bg-background/110">
                <h3 class="text-sm text-muted-foreground">activities</h3>
                <p class="mt-2 text-3xl font-bold">$activityCount</p>
            </div>
        HTML
    ])->html();

    return $response->setContent($html)->setContentType('text/html');
};