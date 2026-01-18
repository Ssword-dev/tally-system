<?php

use App\SimplePage;
use App\Repositories\StudentRepository;
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

    $studentRepo = StudentRepository::getInstance();
    $classRepo = ClassRepository::getInstance();
    $message = null;
    $messageType = null;

    // Handle form submissions
    if ($request->method === 'POST') {
        $postData = $request->request->all();
        if (isset($postData['action'])) {
            if ($postData['action'] === 'create') {
                try {
                    $classId = $postData['class_id'] ?? null;
                    $lrn = $postData['lrn'] ?? null;
                    $firstName = $postData['first_name'] ?? null;
                    $lastName = $postData['last_name'] ?? null;

                    if (!$classId || !$lrn || !$firstName || !$lastName) {
                        throw new Exception('Required fields: Class, LRN, First Name, Last Name');
                    }

                    $studentRepo->create([
                        'classId' => $classId,
                        'createdBy' => auth()->id,
                        'lrn' => $lrn,
                        'firstName' => $firstName,
                        'lastName' => $lastName,
                        'email' => $postData['email'] ?? null,
                        'contactNumber' => $postData['contact_number'] ?? null,
                        'guardian' => $postData['guardian'] ?? null,
                        'guardianContactNumber' => $postData['guardian_contact_number'] ?? null
                    ]);
                    $message = 'Student created successfully';
                    $messageType = 'success';
                } catch (Exception $e) {
                    $message = 'Error: ' . $e->getMessage();
                    $messageType = 'error';
                }
            } elseif ($postData['action'] === 'delete') {
                try {
                    $studentId = $postData['student_id'] ?? null;
                    if (!$studentId) {
                        throw new Exception('Student ID is required');
                    }
                    $studentRepo->delete($studentId);
                    $message = 'Student deleted successfully';
                    $messageType = 'success';
                } catch (Exception $e) {
                    $message = 'Error: ' . $e->getMessage();
                    $messageType = 'error';
                }
            }
        }
    }

    // Get teacher's classes
    $classModels = $classRepo->findByTeacherId(auth()->id);

    // Get all students created by this teacher
    $studentModels = $studentRepo->findAll();
    // Filter to only students in teacher's classes
    $classIds = array_map(fn($c) => $c->id, $classModels);
    $students = array_filter($studentModels, fn($s) => in_array($s->class_id, $classIds));

    // Create a mapping of class IDs to class names for lookup
    $classNameMap = array_reduce($classModels, function ($map, $class) {
        $map[$class->id] = $class->name;
        return $map;
    }, []);

    // Build classes dropdown
    $classesOptions = '';
    if ($classModels) {
        foreach ($classModels as $class) {
            $classesOptions .= "<option value=\"{$class->id}\">{$class->name}</option>";
        }
    }

    // Build students table rows
    $studentRows = '';
    if ($students) {
        foreach ($students as $student) {
            $fullName = htmlspecialchars($student->first_name . ' ' . $student->last_name);
            $lrn = htmlspecialchars($student->lrn);
            $email = htmlspecialchars($student->email ?? '-');
            $contact = htmlspecialchars($student->contact_number ?? '-');
            $className = htmlspecialchars($classNameMap[$student->class_id] ?? 'Unknown');
            $studentId = $student->id;

            $studentRows .= <<<HTML
            <tr class="border-b border-muted hover:bg-background/80">
                <td class="px-4 py-3">$lrn</td>
                <td class="px-4 py-3">$fullName</td>
                <td class="px-4 py-3">$email</td>
                <td class="px-4 py-3">$contact</td>
                <td class="px-4 py-3">$className</td>
                <td class="px-4 py-3">
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="student_id" value="$studentId">
                        <button type="submit" class="text-red-500 hover:text-red-700 text-sm" onclick="return confirm('Are you sure?')">
                            Delete
                        </button>
                    </form>
                </td>
            </tr>
        HTML;
        }
    } else {
        $studentRows = '<tr><td colspan="6" class="px-4 py-3 text-center text-muted-foreground">No students found</td></tr>';
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
            "page_title" => "Students",
            "nav_links" => [
                ["href" => "./home", "label" => "Home"],
                ["href" => "./students", "label" => "Students", "active" => true],
                ["href" => "./sections", "label" => "Classes"],
                ["href" => "./activities", "label" => "Activities"],
                ["href" => "./grades", "label" => "Grades"],
                ["href" => "./teachers", "label" => "Teachers"],
            ]
        ],

        "content" => <<<HTML
            $messageAlert

            <div class="mb-8">
                <h2 class="text-xl font-semibold mb-4">Add New Student</h2>
                <form method="POST" class="p-6 rounded-xl outline-1 outline-text bg-background/110 space-y-4">
                    <input type="hidden" name="action" value="create">
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-muted-foreground text-sm mb-2">Select Class *</label>
                            <select name="class_id" required class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-accent">
                                <option value="">Choose a class</option>
                                $classesOptions
                            </select>
                        </div>
                        <div>
                            <label class="block text-muted-foreground text-sm mb-2">LRN (Learner Reference Number) *</label>
                            <input type="text" name="lrn" required class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-accent" placeholder="e.g., 123456789">
                        </div>
                        <div>
                            <label class="block text-muted-foreground text-sm mb-2">First Name *</label>
                            <input type="text" name="first_name" required class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-accent" placeholder="Enter first name">
                        </div>
                        <div>
                            <label class="block text-muted-foreground text-sm mb-2">Last Name *</label>
                            <input type="text" name="last_name" required class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-accent" placeholder="Enter last name">
                        </div>
                        <div>
                            <label class="block text-muted-foreground text-sm mb-2">Email</label>
                            <input type="email" name="email" class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-accent" placeholder="student@example.com">
                        </div>
                        <div>
                            <label class="block text-muted-foreground text-sm mb-2">Contact Number</label>
                            <input type="tel" name="contact_number" class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-accent" placeholder="+63 9XX XXX XXXX">
                        </div>
                        <div>
                            <label class="block text-muted-foreground text-sm mb-2">Guardian Name</label>
                            <input type="text" name="guardian" class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-accent" placeholder="Guardian's full name">
                        </div>
                        <div>
                            <label class="block text-muted-foreground text-sm mb-2">Guardian Contact</label>
                            <input type="tel" name="guardian_contact_number" class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-accent" placeholder="+63 9XX XXX XXXX">
                        </div>
                    </div>
                    
                    <button type="submit" class="w-full py-2 bg-primary text-text font-semibold rounded-md hover:bg-accent transition">
                        Add Student
                    </button>
                </form>
            </div>

            <div>
                <h2 class="text-xl font-semibold mb-4">My Students</h2>
                <div class="overflow-x-auto rounded-xl outline-1 outline-text bg-background/110">
                    <table class="w-full">
                        <thead class="bg-muted/40">
                            <tr>
                                <th class="px-4 py-3 text-left font-semibold">LRN</th>
                                <th class="px-4 py-3 text-left font-semibold">Full Name</th>
                                <th class="px-4 py-3 text-left font-semibold">Email</th>
                                <th class="px-4 py-3 text-left font-semibold">Contact</th>
                                <th class="px-4 py-3 text-left font-semibold">Class</th>
                                <th class="px-4 py-3 text-left font-semibold">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            $studentRows
                        </tbody>
                    </table>
                </div>
            </div>
        HTML
    ])->html();

    return $response->setContent($html)->setContentType('text/html');
};