<?php
require 'functions.php';

if (isset($_GET['email']) && isset($_GET['code'])) {
    $email = $_GET['email'];
    $input_code = $_GET['code'];
    $saved_code = trim(@file_get_contents(__DIR__ . "/unsubscribe_codes/$email.txt"));
    if ($input_code === $saved_code) {
        unsubscribeEmail($email);
        echo "You have been unsubscribed.";
    } else {
        echo "Invalid unsubscription code.";
    }
}
?>