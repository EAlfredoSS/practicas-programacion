<?php
require_once('files/bd.php');

if (!$link) {
    die("Connection failed: " . mysqli_connect_error());
}

// Check if 'users' table exists
$query = "SHOW TABLES LIKE 'users'";
$result = mysqli_query($link, $query);

if (mysqli_num_rows($result) > 0) {
    echo "Table 'users' exists.\n";
    
    // Get columns
    $query = "SHOW COLUMNS FROM users";
    $result = mysqli_query($link, $query);
    if ($result) {
        echo "Columns in 'users':\n";
        while ($row = mysqli_fetch_assoc($result)) {
            echo $row['Field'] . " - " . $row['Type'] . "\n";
        }
    } else {
        echo "Error getting columns: " . mysqli_error($link) . "\n";
    }
    
    // Check for common columns with mentor2009
    echo "\nChecking for common data...\n";
    $query = "SELECT * FROM users LIMIT 1";
    $result = mysqli_query($link, $query);
    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        print_r($user);
    }
    
} else {
    echo "Table 'users' does NOT exist.\n";
    
    // List all tables to see if it has a different name
    $query = "SHOW TABLES";
    $result = mysqli_query($link, $query);
    echo "Available tables:\n";
    while ($row = mysqli_fetch_row($result)) {
        echo $row[0] . "\n";
    }
}

mysqli_close($link);
?>
