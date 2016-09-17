<?php

/**
 * Gets the root path of the project
 * @return string
 */
function getRootPath() {
    return realpath(__DIR__ . '/..');
}

/**
 * Gets the full path for the database file
 * @return string
 */
function getDatabasePath() {
    return getRootPath() . '/data/data.sqlite';
}

/**
 * Gets the DSN for the SQLite connection
 * @return string
 */
function getDsn() {
    return 'sqlite:' . getDatabasePath();
}

/**
 * Gets the PDO object for database access
 * @return \PDO
 */
function getPDO() {
    return new PDO(getDSN());
}

/**
 * Escapes HTML so it is safe to output
 * @param  string $html
 * @return string
 */
function htmlEscape($html) {
    return htmlspecialchars($html, ENT_HTML5, 'UTF-8');
}

function convertSqlDate($sqlDate) {
    $date = DateTime::createFromFormat('Y-m-d', $sqlDate);

    return $date->format('d M Y');
}

/**
 * Returns the number of comments for the specified post
 * @param  integer $postId
 * @return integer
 */
function countCommentsForPost($postId) {
    $pdo = getPDO();
    $sql = "
        SELECT
            COUNT(*) c
        FROM
            comment
        WHERE
            post_id = :post_id
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'post_id' => $postId,
    ]);

    return (int) $stmt->fetchColumn();
}

 ?>
