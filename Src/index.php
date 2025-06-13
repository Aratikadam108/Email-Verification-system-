<?php
require 'functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $code = generateVerificationCode();
    file_put_contents(__DIR__ . "/codes/$email.txt", $code);
    sendVerificationEmail($email, $code);
    echo "Verification code sent!";
} elseif (isset($_GET['email']) && isset($_GET['code'])) {
    $email = $_GET['email'];
    $input_code = $_GET['code'];
    $saved_code = trim(@file_get_contents(__DIR__ . "/codes/$email.txt"));
    if ($input_code === $saved_code) {
        registerEmail($email);
        echo "Email verified and registered!";
    } else {
        echo "Invalid verification code.";
    }
}
?>
<form method="POST">
    <input type="email" name="email" required placeholder="Enter email">
    <button type="submit">Register</button>
</form>