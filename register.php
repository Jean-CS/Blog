<?php

require_once 'lib/common.php';

checkPHPVersion();

session_start();

// If we're already logged in, go back home
if (isLoggedIn()) {
    redirectAndExit('index.php');
}

// Handle the form posting
$username = $email = '';

$errors = array();
if ($_POST) {
    // Validate these first
    $username = $_POST['username'];
    if (!$username) {
        $errors[] = 'You must enter an username';
    }

    $email = $_POST['email'];
    if (!$email) {
        $errors[] = 'You must enter an email';
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'You must enter a valid email';
    }

    $password = $_POST['password'];
    if (!$password) {
        $errors[] = 'You must set a password';
    } else {
        if (strlen($password) < 6) {
            $errors[] = 'Password must be at least 6 characters';
        } else {
            $confirmPassword = $_POST['confirm-password'];
            if (!$confirmPassword) {
                $errors[] = 'You must confirm your password';
            } else {
                if (!$password === $confirmPassword) {
                    $errors[] = 'Passwords must match';
                }
            }
        }
    }

    if (!$errors) {
        // Init database
        $pdo = getPDO();
        $ok = tryRegister($pdo, $username, $email, $password);

        if ($ok) {
            redirectAndExit('index.php');
        }
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>A blog application | Register</title>
    <?php require 'templates/head.php' ?>
</head>
<body>
    <?php require 'templates/title.php'; ?>

    <?php if ($errors): ?>
        <div class="error box">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?php echo $error ?></li>
                <?php endforeach ?>
            </ul>
        </div>
    <?php endif ?>

    <p>Register here:</p>

    <form class="user-form" method="post">
        <div>
            <label for="username">User:</label>
            <input id="username" type="text" name="username" value="<?php echo htmlEscape($username); ?>">
        </div>
        <div>
            <label for="email">Email:</label>
            <input id="email" type="text" name="email" value="<?php echo htmlEscape($email); ?>">
        </div>
        <div>
            <label for="password">Password:</label>
            <input id="password" type="password" name="password">
        </div>
        <div>
            <label for="confirm-password">Confirm Password:</label>
            <input id="confirm-password" type="password" name="confirm-password">
        </div>
        <input type="submit" name="submit" value="Register">
    </form>

</body>
</html>
