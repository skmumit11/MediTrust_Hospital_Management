<?php
// setup_db11_15.php

$host = '127.0.0.1';
$user = 'root';
$pass = '';

// 1. Connect to MySQL server (no database selected yet)
$conn = new mysqli($host, $user, $pass);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 2. Read the SQL file
$sqlFile = 'db_schema_update.sql';
if (!file_exists($sqlFile)) {
    die("Error: SQL file '$sqlFile' not found.");
}

$sqlContent = file_get_contents($sqlFile);

// 3. Execute Multi Query
if ($conn->multi_query($sqlContent)) {
    echo "<h1>Database Setup Successful!</h1>";
    echo "<p>Database <code>test_db</code> created and tables imported.</p>";
    echo "<p>You can now <a href='index.php'>Go to Login</a>.</p>";
    
    // Process results to clear buffer
    do {
        if ($res = $conn->store_result()) {
            $res->free();
        }
    } while ($conn->more_results() && $conn->next_result());
    
} else {
    echo "<h1>Error setting up database:</h1>";
    echo "<p>" . $conn->error . "</p>";
}

$conn->close();
?>
