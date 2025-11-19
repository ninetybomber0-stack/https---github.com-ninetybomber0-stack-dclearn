<?php
header('Content-Type: text/plain; charset=utf-8');

// --- 1. Connect to Database ---
require_once __DIR__ . '/config/connect.php';

if (!isset($mysqli)) {
    die("❌ Database connection failed. Please check 'config/connect.php'.");
}

echo "✅ Successfully connected to the database.\n\n";

// --- 2. Read the SQL Update File ---
$sql_file = __DIR__ . '/page/update_schema.sql';
if (!file_exists($sql_file)) {
    die("❌ SQL update file not found at: " . $sql_file);
}
$sql_commands = file_get_contents($sql_file);
echo "✅ Successfully read SQL commands from 'page/update_schema.sql'.\n\n";

// --- 3. Execute the SQL Commands ---
// Use multi_query to execute all commands in the file
if ($mysqli->multi_query($sql_commands)) {
    // Clear results from each query
    do {
        if ($result = $mysqli->store_result()) {
            $result->free();
        }
    } while ($mysqli->more_results() && $mysqli->next_result());

    echo "✅ Database schema updated successfully!\n";
    echo "All necessary tables and columns should now be correctly configured.\n\n";
} else {
    // If the first query fails
    echo "❌ An error occurred while updating the database:\n";
    echo "Error: " . $mysqli->error . "\n\n";
    die("Database update failed. Please check the SQL script and your database permissions.");
}

// --- 4. Finalize ---
$mysqli->close();

echo "🎉 Setup process complete. You can now delete this file ('run_update.php').\n";

?>