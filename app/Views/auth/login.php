<?php /** @var string|null $error */ ?>
<?php /** @var array $formFields */ ?>
<?php 

$formFields = [
    [
        'label' => 'Email',
        'name'  => 'email',
        'type'  => 'email',
        'placeholder' => 'Enter your email',
        'required' => 'required',
    ],
    [
        'label' => 'Password',
        'name'  => 'password',
        'type'  => 'password',
        'placeholder' => 'Enter your password',
        'required' => 'required',
    ],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In</title>
    <link href="../static/css/shared.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
</head>
<body class="flex flex-col items-center justify-center min-h-screen bg-background">
    <form
        id="login-form"
        method="POST"
        class="
        flex flex-col w-full max-w-md p-6 bg-background/110
        outline-text outline-1 outline-solid
        rounded-xl shadow-md space-y-4
        "
    >
        <h1 class="text-2xl font-semibold text-center">Login</h1>

        <?php if (!empty($error)): ?>
            <div class="p-3 bg-red-500/20 border border-red-500 text-red-700 rounded-md text-sm">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <?php foreach ($formFields as $field): ?>
            <div class="flex flex-col relative">
                <label for="<?= htmlspecialchars($field['name']) ?>" class="mb-1 font-medium text-muted-foreground">
                    <?= htmlspecialchars($field['label']) ?>
                </label>
                <input
                    id="<?= htmlspecialchars($field['name']) ?>"
                    name="<?= htmlspecialchars($field['name']) ?>"
                    class="px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-accent"
                    <?php foreach ($field as $key => $value) {
                        if (in_array($key, ['label', 'name'])) continue;
                        echo htmlspecialchars("$key=\"$value\" ");
                    } ?>
                >
            </div>
        <?php endforeach; ?>

        <button type="submit" class="w-full py-2 bg-primary text-text font-semibold rounded-md hover:bg-accent transition">
            Submit
        </button>

        <p class="text-center text-muted-foreground text-sm">
            Don't have an account? <a href="./signup" class="text-primary hover:underline">Sign up</a>
        </p>
    </form>
</body>
</html>
