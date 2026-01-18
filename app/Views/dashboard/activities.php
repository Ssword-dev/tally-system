<?php

use App\SimplePage;
use App\Repositories\ClassRepository;
use App\Repositories\ActivityTypeRepository;
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

    $classRepo = ClassRepository::getInstance();
    $typeRepo = ActivityTypeRepository::getInstance();
    $activityRepo = ActivityRepository::getInstance();
    $message = null;
    $messageType = null;

    // Handle form submissions
    if ($request->method === 'POST') {
        $postData = $request->request->all();
        if (isset($postData['action'])) {
            if ($postData['action'] === 'create_type') {
                try {
                    $classId = $postData['class_id'] ?? null;
                    $name = $postData['type_name'] ?? null;
                    $weight = $postData['weight'] ?? null;

                    if (!$classId || !$name || !$weight) {
                        throw new Exception('Class, Type Name, and Weight are required');
                    }

                    $typeRepo->create([
                        'classId' => $classId,
                        'createdBy' => auth()->id,
                        'name' => $name,
                        'weight' => $weight
                    ]);
                    $message = 'Activity type created successfully';
                    $messageType = 'success';
                } catch (Exception $e) {
                    $message = 'Error: ' . $e->getMessage();
                    $messageType = 'error';
                }
            } elseif ($postData['action'] === 'create_activity') {
                try {
                    $typeId = $postData['type_id'] ?? null;
                    $name = $postData['activity_name'] ?? null;
                    $maxScore = $postData['maximum_score'] ?? null;

                    if (!$typeId || !$name || !$maxScore) {
                        throw new Exception('Type, Activity Name, and Maximum Score are required');
                    }

                    // Get the activity type to get class_id
                    $type = $typeRepo->findById($typeId);
                    if (!$type) {
                        throw new Exception('Activity type not found');
                    }

                    $activityRepo->create([
                        'classId' => $type->classId,
                        'typeId' => $typeId,
                        'createdBy' => auth()->id,
                        'name' => $name,
                        'maximumScore' => $maxScore
                    ]);
                    $message = 'Activity created successfully';
                    $messageType = 'success';
                } catch (Exception $e) {
                    $message = 'Error: ' . $e->getMessage();
                    $messageType = 'error';
                }
            }
        }
    }

    // Get teacher's classes
    $classes = $classRepo->findByTeacherId(auth()->id);

    // Build classes dropdown
    $classesOptions = '';
    if ($classes) {
        foreach ($classes as $class) {
            $classesOptions .= "<option value=\"{$class->id}\">{$class->name}</option>";
        }
    }

    // Get activity types for current teacher's classes
    $allActivityTypes = $typeRepo->findAll();
    $classIds = array_map(fn($c) => $c->id, $classes);
    $activityTypes = array_filter($allActivityTypes, fn($t) => in_array($t->class_id, $classIds));

    // Create class name map
    $classNameMap = array_reduce($classes, function ($map, $class) {
        $map[$class->id] = $class->name;
        return $map;
    }, []);

    // Get activities for current teacher's classes
    $allActivities = $activityRepo->findAll();
    $activities = array_filter($allActivities, fn($a) => in_array($a->class_id, $classIds));

    // Create activity type name map
    $typeNameMap = array_reduce($activityTypes, function ($map, $type) {
        $map[$type->id] = $type->name;
        return $map;
    }, []);

    // Build activity types table rows
    $typeRows = '';
    if ($activityTypes) {
        foreach ($activityTypes as $type) {
            $className = htmlspecialchars($classNameMap[$type->class_id] ?? 'Unknown');
            $typeRows .= <<<HTML
            <tr class="border-b border-muted hover:bg-background/80">
                <td class="px-4 py-3">$className</td>
                <td class="px-4 py-3">{$type->name}</td>
                <td class="px-4 py-3">{$type->weight}%</td>
            </tr>
        HTML;
        }
    } else {
        $typeRows = '<tr><td colspan="3" class="px-4 py-3 text-center text-muted-foreground">No activity types found</td></tr>';
    }

    // Build activities table rows
    $activityRows = '';
    if ($activities) {
        foreach ($activities as $activity) {
            $className = htmlspecialchars($classNameMap[$activity->class_id] ?? 'Unknown');
            $typeName = htmlspecialchars($typeNameMap[$activity->type_id] ?? 'Unknown');
            $activityRows .= <<<HTML
            <tr class="border-b border-muted hover:bg-background/80">
                <td class="px-4 py-3">$className</td>
                <td class="px-4 py-3">{$activity->name}</td>
                <td class="px-4 py-3">$typeName</td>
                <td class="px-4 py-3">{$activity->maximum_score}</td>
            </tr>
        HTML;
        }
    } else {
        $activityRows = '<tr><td colspan="4" class="px-4 py-3 text-center text-muted-foreground">No activities found</td></tr>';
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

    // Build activity types dropdown options
    $activityTypesOptions = '';
    if ($activityTypes) {
        foreach ($activityTypes as $type) {
            $className = $classNameMap[$type->class_id] ?? 'Unknown';
            $displayName = htmlspecialchars($className . ' - ' . $type->name);
            $activityTypesOptions .= "<option value=\"{$type->id}\">$displayName</option>";
        }
    }

    $html = SimplePage::page([
        "attributes" => [
            "app_name" => $_ENV["APP_NAME"],
            "display_name" => auth()->firstName . " " . auth()->lastName,
            "page_title" => "Activities",
            "nav_links" => [
                ["href" => "./home", "label" => "Home"],
                ["href" => "./students", "label" => "Students"],
                ["href" => "./sections", "label" => "Classes"],
                ["href" => "./activities", "label" => "Activities", "active" => true],
                ["href" => "./grades", "label" => "Grades"],
                ["href" => "./teachers", "label" => "Teachers"],
            ]
        ],

        "content" => <<<HTML
            $messageAlert

            <div class="mb-8">
                <h2 class="text-xl font-semibold mb-4">Create Activity Type</h2>
                <form method="POST" class="p-6 rounded-xl outline-1 outline-text bg-background/110 space-y-4">
                    <input type="hidden" name="action" value="create_type">
                    
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label class="block text-muted-foreground text-sm mb-2">Select Class *</label>
                            <select name="class_id" required class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-accent">
                                <option value="">Choose a class</option>
                                $classesOptions
                            </select>
                        </div>
                        <div>
                            <label class="block text-muted-foreground text-sm mb-2">Type Name *</label>
                            <input type="text" name="type_name" required class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-accent" placeholder="e.g., Quiz, Exam, Project">
                        </div>
                        <div>
                            <label class="block text-muted-foreground text-sm mb-2">Weight (%) *</label>
                            <input type="number" name="weight" min="0" max="100" required class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-accent" placeholder="e.g., 30">
                        </div>
                    </div>
                    
                    <button type="submit" class="w-full py-2 bg-primary text-text font-semibold rounded-md hover:bg-accent transition">
                        Create Activity Type
                    </button>
                </form>
            </div>

            <div class="mb-8">
                <h2 class="text-xl font-semibold mb-4">Create Activity</h2>
                <form method="POST" class="p-6 rounded-xl outline-1 outline-text bg-background/110 space-y-4">
                    <input type="hidden" name="action" value="create_activity">
                    
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label class="block text-muted-foreground text-sm mb-2">Select Activity Type *</label>
                            <select name="type_id" required class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-accent">
                                <option value="">Choose an activity type</option>
                                $activityTypesOptions
                            </select>
                        </div>
                        <div>
                            <label class="block text-muted-foreground text-sm mb-2">Activity Name *</label>
                            <input type="text" name="activity_name" required class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-accent" placeholder="e.g., Quiz 1, Midterm Exam">
                        </div>
                        <div>
                            <label class="block text-muted-foreground text-sm mb-2">Maximum Score *</label>
                            <input type="number" name="maximum_score" min="1" required class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-accent" placeholder="e.g., 100">
                        </div>
                    </div>
                    
                    <button type="submit" class="w-full py-2 bg-primary text-text font-semibold rounded-md hover:bg-accent transition">
                        Create Activity
                    </button>
                </form>
            </div>

            <div class="mb-8">
                <h2 class="text-xl font-semibold mb-4">Activity Types</h2>
                <div class="overflow-x-auto rounded-xl outline-1 outline-text bg-background/110">
                    <table class="w-full">
                        <thead class="bg-muted/40">
                            <tr>
                                <th class="px-4 py-3 text-left font-semibold">Class</th>
                                <th class="px-4 py-3 text-left font-semibold">Type Name</th>
                                <th class="px-4 py-3 text-left font-semibold">Weight</th>
                            </tr>
                        </thead>
                        <tbody>
                            $typeRows
                        </tbody>
                    </table>
                </div>
            </div>

            <div>
                <h2 class="text-xl font-semibold mb-4">Activities</h2>
                <div class="overflow-x-auto rounded-xl outline-1 outline-text bg-background/110">
                    <table class="w-full">
                        <thead class="bg-muted/40">
                            <tr>
                                <th class="px-4 py-3 text-left font-semibold">Class</th>
                                <th class="px-4 py-3 text-left font-semibold">Activity Name</th>
                                <th class="px-4 py-3 text-left font-semibold">Type</th>
                                <th class="px-4 py-3 text-left font-semibold">Max Score</th>
                            </tr>
                        </thead>
                        <tbody>
                            $activityRows
                        </tbody>
                    </table>
                </div>
            </div>
        HTML
    ])->html();

    return $response->setContent($html)->setContentType('text/html');
};