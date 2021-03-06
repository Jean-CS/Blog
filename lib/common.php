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
    $pdo = new PDO(getDsn());

    // Foreign key constraint need to be enabled manually in SQLite
    $result = $pdo->query('PRAGMA foreign_keys = ON');
    if ($result === false) {
        throw new Exception("Could not turn on foreign key constraints");
    }

    return $pdo;
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

function getSqlDateForNow() {
    return date('Y-m-d');
}

/**
 * We need to test for a minimum version of PHP, because earlier versions have bugs that affect security
 */
function checkPHPVersion() {
    if (version_compare(PHP_VERSION, '5.3.7') < 0) {
        throw new Exception("This system needs PHP 5.5 or later");
    }
    return true;
}

/**
 * Gets a list of posts in reverse order
 * @param  PDO    $pdo
 * @return array
 */
function getAllPosts(PDO $pdo) {
    $stmt = $pdo->query(
        'SELECT
            id, title, created_at, body,
            (SELECT COUNT(*) FROM comment WHERE comment.post_id = post_id) comment_count
        FROM
            post
        ORDER BY
            created_at DESC'
    );

    if ($stmt === false) {
        throw new Exception("There was a problem running this query");
    }

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Returns all the comments for the specified post
 * @param  PDO $pdo
 * @param  integer $postId
 * @return array
 */
function getCommentsForPost(PDO $pdo, $postId) {
    $sql = "
        SELECT
            id, name, text, created_at, website
        FROM
            comment
        WHERE
            post_id = :post_id
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute(
        array('post_id' => $postId, )
    );

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


function verifyNewUser(PDO $pdo, $username, $email) {
    try {
        $sql = "
            SELECT
                username, email
            FROM
                user
            WHERE
                username = :username
                OR email = :email
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute(
            array(
                'username' => $username,
                'email' => $email,
            )
        );
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row['username'] === $username || $row['email'] === $email) {
            return false;
        }

        return true;

    } catch(PDOException $e) {
        echo $e->getMessage();
    }
}


function tryRegister(PDO $pdo, $username, $email, $password) {

    if(!verifyNewUser($pdo, $username, $email)) {
        return false;
    }

    $sql = "
        INSERT INTO
            user
            (username, email, password, created_at, is_enabled)
            VALUES
            (:username, :email, :password, :created_at, :is_enabled)
    ";

    $hash = password_hash($password, PASSWORD_DEFAULT);

    if ($hash === false) {
        throw new Exception("Password hashing failed");
    }

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute(
            array(
                'username' => $username,
                'email' => $email,
                'password' => $hash,
                'created_at' => getSqlDateForNow(),
                'is_enabled' => 1,
            )
        );

        if ($stmt === false) {
    		throw new Exception('Could not run post insert query');
    	}

        return true;

    } catch (PDOException $e) {
        echo $e->getMessage();
    }
}


function tryLogin(PDO $pdo, $email, $password) {
    $sql = "
        SELECT
            username, password
        FROM
            user
        WHERE
            email = :email
            AND is_enabled = 1
    ";

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute(
            array('email' => $email, )
        );

        // Get the hash from this row, and use the thid-party hashing library to check it
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (password_verify($password, $user['password'])) {
            $_SESSION['logged_in_username'] = $user['username'];
            return true;
        } else {
            return false;
        }

    } catch (PDOException $e) {
        echo $e->getMessage();
    }
}

/**
 * Logs in the user
 *
 * For safety, we ask PHP to regenerate the cookie, so if a user logs onto a site that a cracker
 * has prepared for him/her (e.g. on a public computer) the cracker's copy of the cookie ID will be
 * useless.
 *
 * @param  string $username
 */
function login($email) {
    session_regenerate_id();

    $_SESSION['logged_in_email'] = $email;
}

function logout() {
    session_destroy();
    unset($_SESSION['logged_in_email']);
}

function getAuthUser() {
    return isLoggedIn() ? $_SESSION['logged_in_username'] : null;
}

function isLoggedIn() {
    return isset($_SESSION['logged_in_email']);
}

function getAuthUserId(PDO $pdo) {
    // Reply with null if there is no logged-in user
    if (!isLoggedIn()) {
        return null;
    }

    $sql = "
        SELECT
            id
        FROM
            user
        WHERE
            email = :email
            AND is_enabled = 1
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute(
        array(
            'email' => getAuthUser()
        )
    );

    return $stmt->fetchColumn();
}

/**
 * Converts unsafe text to safe, paragraphed HTML
 * @param  string $text
 * @return string
 */
function convertNewLinesToParagraphs($text) {
    $escaped = htmlEscape($text);

    return '<p>' . str_replace("\n", "</p><p>", $escaped . '</p>');
}

function redirectAndExit($script) {
    // Get the domain-relative URL (e.g. /blog/wtv.php or /wtv.php) and work
    // out the folder (e.g. /blog/ or /)
    $relativeUrl = $_SERVER['PHP_SELF'];
    $urlFolder = substr($relativeUrl, 0, strrpos($relativeUrl, '/') + 1);

    // Redirect to the full URL (http:/myhost/blog/script.php)
    $host = $_SERVER['HTTP_HOST'];
    $fullUrl = 'http://' . $host . $urlFolder . $script;
    header('Location: ' . $fullUrl);
    exit();
}

 ?>
