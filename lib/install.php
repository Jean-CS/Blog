<?php

/**
 * Blog install function
 * @return array(count array, error string)
 */
function installBlog() {
    // Get the PDO DSN string
    $root = getRootPath();
    $db = getDatabasePath();

    $error = '';

    // A security measure, to avoid anyone resetting the database if it already exists
    if (is_readable($db) && filesize($db) > 0) {
        $error = 'Please delete the existing database manually before installing it afresh';
    }

    // Create an empty file for the database
    if (!$error) {
        $createdOk = @touch($db);
        if (!$createdOk) {
            $error = sprintf(
                'Could not create the database, please allow the server to create new files in \'%s\'',
                dirname($db)
            );
        }
    }

    // Grab the SQL commands we want to run on the database
    if (!$error) {
        $sql = file_get_contents($root . '/data/init.sql');
        if ($sql === false) {
            $error = 'Cannot find SQL file';
        }
    }

    // Connect to the new database and try to run the SQL commands
    if (!$error) {
        $pdo = getPDO();
        $result = $pdo->exec($sql);
        if ($result === false) {
            $error = 'Could not run SQL: ' . print_r($pdo->errorInfo(), true);
        }
    }

    // See how many rows we created, if any
    $count = array();

    foreach (array('post', 'comment') as $tableName) {

        if (!$error) {
            $sql = "SELECT COUNT(*) AS c FROM " . $tableName;
            $stmt = $pdo->query($sql);
            if ($stmt) {
                // We sotre each count in an associative array
                $count[$tableName] = $stmt->fetchColumn();
            }
        }
    }

    return array($count, $error);
}

?>
