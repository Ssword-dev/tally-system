<?php

use App\SimplePage;
use App\Repositories\ClassRepository;
use App\Repositories\StudentRepository;
use App\Router\Request;
use App\Router\Response;

return function (Request $request, Response $response): Response {
    // Check if user is logged in
    if (!auth()) {
        return $response
            ->setStatusCode(302)
            ->setHeader('Location', '/student-database-system');
    }

    $classRepo = ClassRepository::getInstance();
    $studentRepo = StudentRepository::getInstance();
    $message = null;
    $messageType = null;

    // Handle form submissions
    if ($request->method === 'POST') {
        $postData = $request->request->all();
        if (isset($postData['action'])) {
            if ($postData['action'] === 'create') {
                try {
                    $name = $postData['name'] ?? null;
                    $courseName = $postData['course_name'] ?? null;
                    $schoolYear = $postData['school_year'] ?? null;

                    if (!$name || !$courseName || !$schoolYear) {
                        throw new Exception('All fields are required');
                    }

                    $classRepo->create([
                        'teacherId' => auth()->id,
                        'createdBy' => auth()->id,
                        'name' => $name,
                        'courseName' => $courseName,
                        'schoolYear' => $schoolYear
                    ]);
                    $message = 'Class created successfully';
                    $messageType = 'success';
                } catch (Exception $e) {
                    $message = 'Error: ' . $e->getMessage();
                    $messageType = 'error';
                }
            } elseif ($postData['action'] === 'delete') {
                try {
                    $classId = $postData['class_id'] ?? null;
                    if (!$classId) {
                        throw new Exception('Class ID is required');
                    }
                    $classRepo->delete($classId);
                    $message = 'Class deleted successfully';
                    $messageType = 'success';
                } catch (Exception $e) {
                    $message = 'Error: ' . $e->getMessage();
                    $messageType = 'error';
                }
            }
        }
    }

    // Get all classes for this teacher
    $classes = $classRepo->findByTeacherId(auth()->id);

    // Get all students to count per class
    $allStudents = $studentRepo->findAll();
    $studentCountByClass = array_reduce($allStudents, function ($counts, $student) {
        $classId = $student->class_id;
        $counts[$classId] = ($counts[$classId] ?? 0) + 1;
        return $counts;
    }, []);

    // Build classes table rows
    $classRows = '';
    if ($classes) {
        foreach ($classes as $class) {
            $studentCount = $studentCountByClass[$class->id] ?? 0;
            $classRows .= <<<HTML
            <tr class="border-b border-muted hover:bg-background/80">
                <td class="px-4 py-3">{$class->name}</td>
                <td class="px-4 py-3">{$class->course_name}</td>
                <td class="px-4 py-3">{$class->school_year}</td>
                <td class="px-4 py-3">$studentCount</td>
                <td class="px-4 py-3">
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="class_id" value="{$class->id}">
                        <button type="submit" class="text-red-500 hover:text-red-700 text-sm" onclick="return confirm('Are you sure?')">
                            Delete
                        </button>
                    </form>
                </td>
            </tr>
        HTML;
        }
    } else {
        $classRows = '<tr><td colspan="5" class="px-4 py-3 text-center text-muted-foreground">No classes found</td></tr>';
    }

    $messageAlert = '';
    if ($message) {
        $bgColor = $messageType === 'success' ? 'bg-green-500/20 border-green-500 text-green-700' : 'bg-red-500/20 border-red-500 text-red-700';
        $messageAlert = <<<HTML
        <div class="p-3 $bgColor border rounded-md text-sm mb-4">
            $message
        </div>
    HTML;
    }

    $html = SimplePage::page([
        "attributes" => [
            "app_name" => $_ENV["APP_NAME"],
            "display_name" => auth()->firstName . " " . auth()->lastName,
            "page_title" => "Classes",
            "nav_links" => [
                ["href" => "./home", "label" => "Home"],
                ["href" => "./students", "label" => "Students"],
                ["href" => "./sections", "label" => "Classes", "active" => true],
                ["href" => "./activities", "label" => "Activities"],
                ["href" => "./grades", "label" => "Grades"],
                ["href" => "./teachers", "label" => "Teachers"],
            ]
        ],

        "content" => <<<HTML
            $messageAlert

            <div class="mb-8">
                <h2 class="text-xl font-semibold mb-4">Create New Class</h2>
                <form method="POST" class="p-6 rounded-xl outline-1 outline-text bg-background/110 space-y-4">
                    <input type="hidden" name="action" value="create">
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-muted-foreground text-sm mb-2">Class Name</label>
                            <input type="text" name="name" required class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-accent" placeholder="e.g., Grade 10 - Section A">
                        </div>
                        <div>
                            <label class="block text-muted-foreground text-sm mb-2">Course Name</label>
                            <input type="text" name="course_name" required class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-accent" placeholder="e.g., Mathematics">
                        </div>
                        <div class="col-span-2">
                            <label class="block text-muted-foreground text-sm mb-2">School Year</label>
                            <input type="text" name="school_year" required class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-accent" placeholder="e.g., 2024-2025">
                        </div>
                    </div>
                    
                    <button type="submit" class="w-full py-2 bg-primary text-text font-semibold rounded-md hover:bg-accent transition">
                        Create Class
                    </button>
                </form>
            </div>

            <div>
                <h2 class="text-xl font-semibold mb-4">My Classes</h2>
                <div class="overflow-x-auto rounded-xl outline-1 outline-text bg-background/110">
                    <table class="w-full">
                        <thead class="bg-muted/40">
                            <tr>
                                <th class="px-4 py-3 text-left font-semibold">Class Name</th>
                                <th class="px-4 py-3 text-left font-semibold">Course</th>
                                <th class="px-4 py-3 text-left font-semibold">School Year</th>
                                <th class="px-4 py-3 text-left font-semibold">Students</th>
                                <th class="px-4 py-3 text-left font-semibold">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            $classRows
                        </tbody>
                    </table>
                </div>
            </div>
        HTML
    ])->html();

    return $response->setContent($html)->setContentType('text/html');
};