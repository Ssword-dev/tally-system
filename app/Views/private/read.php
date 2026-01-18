<?php
include(__DIR__ . '/connect.php');

$templateQuery = '
    SELECT 
        *
    FROM `customers`
';

// $statement = $connection->prepare($templateQuery);

// if (!$statement) {
//     echo 'Failed to prepare statement.';
// }

// $statement->execute();
// $result = $statement->get_result();
// $customers = $result->fetch_all(MYSQLI_ASSOC);

$result = mysqli_query($connection, $templateQuery);

if (!$result) {
    echo 'Failed to query.';
    die(1);
}

echo '<table>';

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
        echo "<tr>";

        foreach ($row as $column) {
            echo "<td>" . $column . "</td>";
        }

        echo "</tr>";
    }
}

echo '</table>';