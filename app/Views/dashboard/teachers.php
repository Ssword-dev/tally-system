<?php

use App\SimplePage;
use App\Repositories\TeacherRepository;
use App\Repositories\ClassRepository;
use App\Router\Request;
use App\Router\Response;

return function (Request $request, Response $response): Response {
    // Check if user is logged in
    if (!auth()) {
        return $response
            ->setStatusCode(302)
            ->setHeader('Location', '/student-database-system');
    }

    $teacherRepo = TeacherRepository::getInstance();
    $classRepo = ClassRepository::getInstance();
    $message = null;
    $messageType = null;

    // Handle form submissions
    if ($request->method === 'POST') {
        $postData = $request->request->all();
        if (isset($postData['action'])) {
            if ($postData['action'] === 'delete') {
                try {
                    $teacherId = $postData['teacher_id'] ?? null;
                    if (!$teacherId) {
                        throw new Exception('Teacher ID is required');
                    }
                    $teacherRepo->delete($teacherId);
                    $message = 'Teacher deleted successfully';
                    $messageType = 'success';
                } catch (Exception $e) {
                    $message = 'Error: ' . $e->getMessage();
                    $messageType = 'error';
                }
            }
        }
    }

    // Get all teachers
    $teachers = $teacherRepo->findAll();

    // Get all classes for counting
    $allClasses = $classRepo->findAll();
    $classCountByTeacher = array_reduce($allClasses, function ($counts, $class) {
        $teacherId = $class->teacher_id;
        $counts[$teacherId] = ($counts[$teacherId] ?? 0) + 1;
        return $counts;
    }, []);

    // Build teachers table rows
    $teacherRows = '';
    if ($teachers) {
        foreach ($teachers as $teacher) {
            $fullName = htmlspecialchars($teacher->first_name . ' ' . $teacher->last_name);
            $email = htmlspecialchars($teacher->email);
            $contact = htmlspecialchars($teacher->contact_number ?? '-');
            $classCount = $classCountByTeacher[$teacher->id] ?? 0;
            $teacherId = $teacher->id;

            $teacherRows .= <<<HTML
            <tr class="border-b border-muted hover:bg-background/80">
                <td class="px-4 py-3">$fullName</td>
                <td class="px-4 py-3">$email</td>
                <td class="px-4 py-3">$contact</td>
                <td class="px-4 py-3">$classCount</td>
                <td class="px-4 py-3">
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="teacher_id" value="$teacherId">
                        <button type="submit" class="text-red-500 hover:text-red-700 text-sm" onclick="return confirm('Are you sure?')">
                            Delete
                        </button>
                    </form>
                </td>
            </tr>
        HTML;
        }
    } else {
        $teacherRows = '<tr><td colspan="5" class="px-4 py-3 text-center text-muted-foreground">No teachers found</td></tr>';
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
            "page_title" => "Teachers",
            "nav_links" => [
                ["href" => "./home", "label" => "Home"],
                ["href" => "./students", "label" => "Students"],
                ["href" => "./sections", "label" => "Classes"],
                ["href" => "./activities", "label" => "Activities"],
                ["href" => "./grades", "label" => "Grades"],
                ["href" => "./teachers", "label" => "Teachers", "active" => true],
            ]
        ],

        "content" => <<<HTML
            $messageAlert

            <div>
                <h2 class="text-xl font-semibold mb-4">All Teachers</h2>
                <div class="overflow-x-auto rounded-xl outline-1 outline-text bg-background/110">
                    <table class="w-full">
                        <thead class="bg-muted/40">
                            <tr>
                                <th class="px-4 py-3 text-left font-semibold">Name</th>
                                <th class="px-4 py-3 text-left font-semibold">Email</th>
                                <th class="px-4 py-3 text-left font-semibold">Contact</th>
                                <th class="px-4 py-3 text-left font-semibold">Classes</th>
                                <th class="px-4 py-3 text-left font-semibold">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            $teacherRows
                        </tbody>
                    </table>
                </div>
            </div>
        HTML
    ])->html();

    return $response->setContent($html)->setContentType('text/html');
};