<?php if ($_SERVER['REQUEST_METHOD'] === 'GET'): ?>
    <?php
    include __DIR__ . '/connect.php';

    $formFields = [
        ['label' => 'Id', 'name' => 'id', 'type' => 'text', 'placeholder' => 'Enter your requested id', 'required' => 'required'],
        ['label' => 'Customer Name', 'name' => 'customerName', 'type' => 'text', 'placeholder' => 'Enter your name', 'required' => 'required'],
        ['label' => 'Contact Name', 'name' => 'contactName', 'type' => 'text', 'placeholder' => 'Enter your Contact Name', 'required' => 'required'],
        ['label' => 'Address', 'name' => 'address', 'type' => 'text', 'placeholder' => 'Enter your Address', 'required' => 'required'],
        ['label' => 'City', 'name' => 'city', 'type' => 'text', 'placeholder' => 'Enter your Home City', 'required' => 'required'],
        ['label' => 'Postal Code', 'name' => 'postalCode', 'type' => 'text', 'placeholder' => 'Enter your Postal Code', 'required' => 'required'],
        ['label' => 'Country', 'name' => 'country', 'type' => 'text', 'placeholder' => 'Enter your Home Country', 'required' => 'required'],
    ];

    // $paginatedFormFields = array_chunk($formFields, 3);
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Sign Up</title>
        <link href="../../../static/css/shared.css" rel="stylesheet">
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"
            integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo="
            crossorigin="anonymous"></script>
    </head>
    <body class="flex flex-col items-center justify-center min-h-screen bg-background">
        <form
            action=""
            id="signup-form"
            method="POST"
            class="
                flex flex-col w-full max-w-md p-6 bg-background/110
                outline-text outline-1 outline-solid
                rounded-xl shadow-md space-y-4
            "
        >
            <h1 class="text-2xl font-semibold text-center">Sign Up</h1>

            <div class="flex flex-col paginated-form translate-x-[calc(-100%_*_var(--page))] overflow-hidden" id="form-page-carousel" style="translate: 0% 0;">
                <?php foreach ($formFields as $field): ?>
                    <div class="flex flex-col relative">
                        <label for="<?= htmlspecialchars($field['name']) ?>" class="mb-1 font-medium text-muted-foreground">
                            <?= htmlspecialchars($field['label']) ?>
                        </label>
                        <input
                            id="<?= htmlspecialchars($field['name']) ?>"
                            name="<?= htmlspecialchars($field['name']) ?>"
                            class="px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-accent"
                            <?php foreach ($field as $key => $value):
                                if (in_array($key, ['label', 'name'])) continue;
                                echo htmlspecialchars("$key=\"$value\" ");
                            endforeach; ?>
                        >
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="" id="form-action-tray">
                <!-- javascript populated -->
                <button
                    type="submit"
                    class="px-4 py-2 w-full rounded-md bg-green-600 text-white text-sm
                           transition hover:bg-green-700"
                    id="submit-btn"
                >
                    submit
                </button>
            </div>

            <p class="text-center text-muted-foreground text-sm">
                Already have an account? <a href="./login" class="text-primary hover:underline">Log in</a>
            </p>

            <div id="templates" class="hidden">
                <template id="form-error-template">
                    <div class=" p-3 bg-red-500/20 border border-red-500 text-red-700 rounded-md text-sm" id="form-error">
                        <span id="form-error-message"></span>
                    </div>
                </template>
                <template id="loading-spinner-icon">
                    <!-- taken from https://tailwindcss.com/docs/animation#adding-a-spin-animation -->
                    <svg class="mr-3 -ml-1 size-5 animate-spin text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                        
                        </circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </template>

                <!-- Navigation Buttons -->
                <template id="prev-button-template">
                    <button
                        type="button"
                        class="px-4 py-2 rounded-md border text-sm text-muted-foreground
                               transition hover:bg-muted disabled:opacity-50"
                        id="prev-btn"
                        disabled
                    >
                        previous
                    </button>
                </template>

                <!-- next button template -->
                <template id="next-button-template">
                    <button
                        type="button"
                        class="px-4 py-2 rounded-md bg-primary text-text text-sm
                               transition hover:bg-accent"
                        id="next-btn"
                    >
                        next
                    </button>
                </template>

                <!-- submit button template -->
                <template id="submit-button-template">
                    <button
                        type="submit"
                        class="px-4 py-2 rounded-md bg-green-600 text-white text-sm
                               transition hover:bg-green-70"
                        id="submit-btn"
                    >
                        submit
                    </button>
                </template>
            </div>
        </form>
    </body>
    </html>
<?php else: ?>
    <?php
    include __DIR__ . '/connect.php';

    $templateQuery = '
        INSERT INTO `customers` (
            `Id`,
            `CustomerName`,
            `ContactName`,
            `Address`,
            `City`,
            `PostalCode`,
            `Country`
        ) VALUES (
            ?,
            ?,
            ?,
            ?,
            ?,
            ?,
            ?
        );
    ';
    
    $statement = $connection->prepare($templateQuery);

    if (!$statement) {
        echo 'Failed to prepare statement.';
    }

    $statement->bind_param(
        'issssss',
        $_POST['id'],
        $_POST['customerName'],
        $_POST['contactName'],
        $_POST['address'],
        $_POST['city'],
        $_POST['postalCode'],
        $_POST['country']
    );
    
    $statement->execute();
    // $result = $statement->get_result();
    // $customers = $result->fetch_all(MYSQLI_ASSOC);
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Document</title>
    </head>
    <body>
        <p>Created Customer!</p>
    </body>
    </html>
<?php endif; ?>