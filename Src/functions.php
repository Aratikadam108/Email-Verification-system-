<?php
function generateVerificationCode() {
    return str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
}

function registerEmail($email) {
    $file = __DIR__ . '/registered_emails.txt';
    $emails = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if (!in_array($email, $emails)) {
        file_put_contents($file, $email . PHP_EOL, FILE_APPEND);
    }
}

function unsubscribeEmail($email) {
    $file = __DIR__ . '/registered_emails.txt';
    $emails = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $emails = array_filter($emails, fn($e) => trim($e) !== trim($email));
    file_put_contents($file, implode(PHP_EOL, $emails) . PHP_EOL);
}

function sendVerificationEmail($email, $code) {
    $subject = "Your Verification Code";
    $message = "Your verification code is: <b>$code</b>";
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: no-reply@example.com";
    mail($email, $subject, $message, $headers);
}

function fetchGitHubTimeline() {
    $opts = ['http'=>['method'=>"GET", 'header'=>"User-Agent: PHP"]];
    $context = stream_context_create($opts);
    $json = file_get_contents("https://api.github.com/events", false, $context);
    return json_decode($json, true);
}

function formatGitHubData($data) {
    $html = "<h3>GitHub Timeline</h3><ul>";
    foreach (array_slice($data, 0, 5) as $event) {
        $type = $event['type'];
        $repo = $event['repo']['name'];
        $html .= "<li><b>$type</b> at <i>$repo</i></li>";
    }
    $html .= "</ul>";
    return $html;
}

function sendGitHubUpdatesToSubscribers() {
    $file = __DIR__ . '/registered_emails.txt';
    if (!file_exists($file)) return;

    $emails = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $data = fetchGitHubTimeline();
    $message = formatGitHubData($data);

    foreach ($emails as $email) {
        $unsubscribe_code = generateVerificationCode();
        file_put_contents(__DIR__ . "/unsubscribe_codes/$email.txt", $unsubscribe_code);
        $unsubscribe_link = "http://localhost/src/unsubscribe.php?email=" . urlencode($email) . "&code=$unsubscribe_code";
        $full_message = $message . "<br><br><a href='$unsubscribe_link'>Unsubscribe</a>";
        $subject = "GitHub Timeline Updates";
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= "From: updates@example.com";
        mail($email, $subject, $full_message, $headers);
    }
}
?>