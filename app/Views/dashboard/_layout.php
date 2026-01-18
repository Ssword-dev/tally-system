<?php
/** @var string $app_name */
/** @var string $display_name */
/** @var string $page_title */
/** @var array|string $nav_links */
/** @var string $content */

$app_name = $app_name ?? 'Student Database';
$display_name = $display_name ?? 'Guest';
$page_title = $page_title ?? 'Dashboard';
$nav_links = $nav_links ?? [];
$content = $content ?? '';

// Build navigation HTML if nav_links is an array
$navHtml = '';
if (is_array($nav_links)) {
    foreach ($nav_links as $link) {
        $href = $link['href'] ?? '#';
        $label = $link['label'] ?? 'Link';
        $active = isset($link['active']) && $link['active'] ? ' data-active' : '';
        $navHtml .= "<a href=\"$href\"$active>$label</a>\n";
    }
} else {
    $navHtml = $nav_links; // Assume it's already HTML
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title) ?> - <?= htmlspecialchars($app_name) ?></title>
    <link href="../static/css/shared.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
</head>

<body class="min-h-screen min-w-screen bg-background text-text flex">
    <!-- Sidebar -->
    <aside
        class="min-h-screen w-(--sidebar-width) p-6 bg-background/110 outline-1 outline-text transition-all duration-75"
        id="sidebar" style="--sidebar-width: 250px">
        <div class="flex flex-row text-xl font-semibold mb-10 truncate">
            <?= htmlspecialchars($app_name) ?>
        </div>
        <nav class="flex flex-col gap-2 *:truncate [&>a[data-active]]:bg-accent">
            // TODO: use foreach loop to auto-detect visited routes.
            <?= $navHtml ?>
        </nav>
    </aside>

    <!-- Sidebar Separator -->
    <div class="w-2 h-screen -ml-1 touch-none cursor-col-resize bg-muted" id="sidebar-separator"></div>

    <!-- Main Content -->
    <main class="flex-1 p-8 overflow-y-auto">
        <!-- Top Bar -->
        <div class="flex items-center justify-between mb-8">
            <h1 class="text-2xl font-semibold"><?= htmlspecialchars($page_title) ?></h1>
            <div class="flex items-center gap-4">
                <span class="text-muted-foreground"><?= htmlspecialchars($display_name) ?></span>
                <form action="../auth/logout" method="POST" style="display: inline;">
                    <button type="submit"
                        class="px-4 py-2 text-sm rounded-md border border-outline hover:bg-background/80 transition">
                        log out
                    </button>
                </form>
            </div>
        </div>

        <!-- Page Content -->
        <section class="space-y-6 pb-8">

        </section>
    </main>

    <script src="../static/js/dashboard/layout.js"></script>
</body>

</html>