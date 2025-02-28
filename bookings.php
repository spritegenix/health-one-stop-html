<?php
// Import PHPMailer classes
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Dotenv\Dotenv;

// Require Composer's autoload file
require 'vendor/autoload.php';

// Load environment variables
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = strip_tags(trim($_POST["name"]));
    $name = str_replace(array("\r", "\n"), array(" ", " "), $name);
    $email = filter_var(trim($_POST["email"]), FILTER_SANITIZE_EMAIL);
    // $message = trim($_POST["message"]);
    $serviceTitle = $_POST['service_title'];
    $serviceImage = $_POST['service_image'];
    $mobile = trim($_POST["phone"]);

    // Validate fields
    if (empty($name) || empty($mobile)    || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo "Please complete the form and try again.";
        exit;
    }

    // Send email via PHPMailer
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = $_ENV['SMTP_HOST'];
        $mail->SMTPAuth = true;
        $mail->Username = $_ENV['SMTP_USERNAME'];
        $mail->Password = $_ENV['SMTP_PASSWORD'];
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = $_ENV['SMTP_PORT'];

        $mail->setFrom($_ENV['SMTP_FROM_EMAIL'], $_ENV['SMTP_FROM_NAME']);
        $mail->addAddress($_ENV['SMTP_FROM_EMAIL'], 'Admin');
        $mail->addReplyTo($email, $name);
        $mail->Subject = "New Booking for " . $serviceTitle;
        $mail->Body = '<html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; color: #333; }
                .container { width: 100%; padding: 10px; }
                .label { font-weight: bold; color: #555; }
                .value { color: #222; }
            </style>
        </head>
        <body>
            <div class="container">
                <p><span class="label">Name:</span> <span class="value">' . $name . '</span></p>
                  <p>
                   <p><span class="label">Title:</span> <span class="value">' . $serviceTitle . '</span></p>
                  <p>
    <span class="label">Image:</span> 
    <img style="width: 150px; height: auto;" src="' . $serviceImage . '" alt="Service Image" />
</p>

                <p><span class="label">Email:</span> <span class="value">' . $email . '</span></p>
                <p><span class="label">Mobile:</span> <span class="value">' . $mobile . '</span></p>
           
            </div>
        </body>
        </html>';

        $mail->isHTML(true); // Ensure HTML format is enabled

        $mail->send();
    } catch (Exception $e) {
        http_response_code(500);
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        exit;
    }
} else {
    http_response_code(403);
    echo "There was a problem with your submission, please try again.";
}
