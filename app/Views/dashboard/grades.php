<?php

use App\SimplePage;
use App\Repositories\ClassRepository;
use App\Repositories\ActivityRepository;
use App\Repositories\StudentRepository;
use App\Repositories\ScoreRepository;
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
    $activityRepo = ActivityRepository::getInstance();
    $studentRepo = StudentRepository::getInstance();
    $scoreRepo = ScoreRepository::getInstance();
    $message = null;
    $messageType = null;

    // Handle score submission
    if ($request->method === 'POST') {
        $postData = $request->request->all();
        if (isset($postData['action'])) {
            if ($postData['action'] === 'add_score') {
                try {
                    $activityId = $postData['activity_id'] ?? null;
                    $studentId = $postData['student_id'] ?? null;
                    $score = $postData['score'] ?? null;

                    if (!$activityId || !$studentId || $score === null || $score === '') {
                        throw new Exception('Activity, Student, and Score are required');
                    }

                    // Check if score already exists
                    $allScores = $scoreRepo->findAll();
                    $existing = array_filter($allScores, function ($s) use ($activityId, $studentId) {
                        return $s->activity_id == $activityId && $s->student_id == $studentId;
                    });

                    if ($existing) {
                        // Get the first match and update it
                        $existingScore = reset($existing);
                        $scoreRepo->update($existingScore->id, [
                            'activityId' => $activityId,
                            'studentId' => $studentId,
                            'createdBy' => auth()->id,
                            'score' => $score
                        ]);
                        $message = 'Score updated successfully';
                    } else {
                        // Insert new score
                        $scoreRepo->create([
                            'activityId' => $activityId,
                            'studentId' => $studentId,
                            'createdBy' => auth()->id,
                            'score' => $score
                        ]);
                        $message = 'Score recorded successfully';
                    }
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

    $selectedClassId = $request->query->get('class_id') ?? null;
    $activities = [];
    $studentsWithScores = [];

    if ($selectedClassId) {
        // Get activities for selected class
        $activities = $activityRepo->findByClassId($selectedClassId);

        // Get students in selected class
        $studentsWithScores = $studentRepo->findByClassId($selectedClassId);
    }

    // Build classes dropdown
    $classesOptions = '';
    if ($classes) {
        foreach ($classes as $class) {
            $selected = $selectedClassId == $class->id ? 'selected' : '';
            $classesOptions .= "<option value=\"{$class->id}\" $selected>{$class->name}</option>";
        }
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

    // Build grading table
    $gradingTable = '';
    if ($selectedClassId && $activities && $studentsWithScores) {
        $gradingTable = '<div class="overflow-x-auto rounded-xl outline-1 outline-text bg-background/110"><table class="w-full"><thead class="bg-muted/40"><tr><th class="px-4 py-3 text-left font-semibold">Student</th>';

        foreach ($activities as $activity) {
            $actName = htmlspecialchars($activity->name);
            $maxScore = $activity->maximum_score;
            $gradingTable .= "<th class=\"px-4 py-3 text-left font-semibold\">$actName<br><span class=\"text-xs text-muted-foreground\">/ $maxScore</span></th>";
        }

        $gradingTable .= '</tr></thead><tbody>';

        // Get all scores at once for efficiency
        $allScores = $scoreRepo->findAll();
        $scoreMap = [];
        foreach ($allScores as $scoreObj) {
            $key = $scoreObj->activity_id . '_' . $scoreObj->student_id;
            $scoreMap[$key] = $scoreObj->score;
        }

        foreach ($studentsWithScores as $student) {
            $studentName = htmlspecialchars($student->first_name . ' ' . $student->last_name);
            $gradingTable .= "<tr class=\"border-b border-muted hover:bg-background/80\"><td class=\"px-4 py-3\">$studentName</td>";

            foreach ($activities as $activity) {
                $scoreKey = $activity->id . '_' . $student->id;
                $scoreValue = $scoreMap[$scoreKey] ?? '';

                $gradingTable .= <<<HTML
                <td class="px-4 py-3">
                    <form method="POST" style="display: inline;" class="inline-form">
                        <input type="hidden" name="action" value="add_score">
                        <input type="hidden" name="activity_id" value="{$activity->id}">
                        <input type="hidden" name="student_id" value="{$student->id}">
                        <input type="number" name="score" min="0" max="{$activity->maximum_score}" value="$scoreValue" class="w-16 px-2 py-1 border rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-accent" placeholder="-">
                    </form>
                </td>
            HTML;
            }

            $gradingTable .= '</tr>';
        }

        $gradingTable .= '</tbody></table></div>';
    }

    ob_start();
    ?>
    <?=
        SimplePage::page([
            "attributes" => [
                "app_name" => $_ENV["APP_NAME"],
                "display_name" => auth()->firstName . " " . auth()->lastName,
                "page_title" => "Grades",
                "nav_links" => [
                    ["href" => "./home", "label" => "Home"],
                    ["href" => "./students", "label" => "Students"],
                    ["href" => "./sections", "label" => "Classes"],
                    ["href" => "./activities", "label" => "Activities"],
                    ["href" => "./grades", "label" => "Grades", "active" => true],
                    ["href" => "./teachers", "label" => "Teachers"],
                ]
            ],

            "content" => <<<HTML
            $messageAlert

            <div class="mb-8">
                <h2 class="text-xl font-semibold mb-4">Select Class to Grade</h2>
                <form method="GET" class="p-6 rounded-xl outline-1 outline-text bg-background/110">
                    <div class="flex gap-4">
                        <select name="class_id" onchange="this.form.submit()" class="flex-1 px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-accent">
                            <option value="">Choose a class</option>
                            $classesOptions
                        </select>
                    </div>
                </form>
            </div>

            <?php if ($selectedClassId && !$activities): ?>
                <div class="p-6 rounded-xl outline-1 outline-text bg-background/110 text-center text-muted-foreground">
                    No activities found for this class. Create activities first in the Activities page.
                </div>
            <?php elseif ($selectedClassId && !$studentsWithScores): ?>
                <div class="p-6 rounded-xl outline-1 outline-text bg-background/110 text-center text-muted-foreground">
                    No students found in this class. Add students first in the Students page.
                </div>
            <?php elseif ($selectedClassId): ?>
                <div>
                    <h2 class="text-xl font-semibold mb-4">Grading Sheet</h2>
                    $gradingTable
                    <p class="mt-4 text-sm text-muted-foreground">
                        <small>Note: Scores are auto-saved when you change them.</small>
                    </p>
                </div>
            <?php endif; ?>
        HTML
        ])->html()
        ?>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Auto-save scores on input change
            const forms = document.querySelectorAll('.inline-form');
            forms.forEach(form => {
                const input = form.querySelector('input[name="score"]');
                if (input) {
                    input.addEventListener('change', function () {
                        form.submit();
                    });
                }
            });
        });
    </script>
    <?php

    $html = ob_get_clean();
    return $response->setContent($html)->setContentType('text/html');
};