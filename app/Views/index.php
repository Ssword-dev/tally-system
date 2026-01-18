<?php
$features = [
    [
        'title' => 'student records',
        'subtitle' => 'store and manage student profiles, sections, and details in one place.'
    ]
];

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Database</title>

    <link href="./static/css/shared.css" rel="stylesheet">
</head>

<body class="min-h-screen bg-background text-text">
    <!-- header -->
    <header class="w-full px-6 py-4 flex items-center justify-between mx-auto">
        <div class="text-xl font-semibold">
            <?= $_ENV["APP_NAME"] ?>
        </div>

        <nav class="flex gap-4 mr-0">
            <?php if (auth()): ?>
                <a href="./dashboard" class="text-muted-foreground hover:text-text transition">
                    dashboard
                </a>
            <?php else: ?>
                <a href="./auth/signup" class="px-4 py-2 bg-primary rounded-md hover:text-text transition">
                    signup
                </a>
                <a href="./auth/login" class="px-4 py-2 bg-secondary rounded-md hover:bg-accent transition">
                    log in
                </a>
            <?php endif; ?>
        </nav>
    </header>

    <!-- hero -->
    <main class="flex flex-col items-center justify-center text-center px-6 py-24">
        <h1 class="text-4xl md:text-5xl font-bold max-w-3xl">
            manage students. stay organized. move faster.
        </h1>

        <p class="mt-6 text-lg text-muted-foreground max-w-2xl">
            a simple and secure student database system for schools, teachers,
            and administrators.
        </p>

        <div class="mt-10 flex gap-4">
            <a href="./auth/signup"
                class="px-6 py-3 bg-primary text-text font-semibold rounded-md hover:bg-accent transition">
                get started
            </a>

            <a href="./auth/login"
                class="px-6 py-3 border border-outline text-text rounded-md hover:bg-background/80 transition">
                sign in
            </a>
        </div>
    </main>

    <!-- features -->
    <section class="max-w-6xl mx-auto px-6 py-20 grid gap-8 md:grid-cols-3">
        <div class="p-6 rounded-xl outline outline-1 outline-text bg-background/110">
            <h3 class="text-xl font-semibold mb-2">student records</h3>
            <p class="text-muted-foreground">
                store and manage student profiles, sections, and details in one place.
            </p>
        </div>

        <div class="p-6 rounded-xl outline outline-1 outline-text bg-background/110">
            <h3 class="text-xl font-semibold mb-2">fast & simple</h3>
            <p class="text-muted-foreground">
                no clutter. no bloat. designed for speed and clarity.
            </p>
        </div>

        <div class="p-6 rounded-xl outline outline-1 outline-text bg-background/110">
            <h3 class="text-xl font-semibold mb-2">secure access</h3>
            <p class="text-muted-foreground">
                authentication-first design with room for role-based access.
            </p>
        </div>
    </section>

    <!-- footer -->
    <footer class="py-8 text-center text-sm text-muted-foreground">
        © <?= date("Y") ?>
        <?= $_ENV['APP_NAME'] ?>. all rights reserved.
    </footer>

</body>

</html>