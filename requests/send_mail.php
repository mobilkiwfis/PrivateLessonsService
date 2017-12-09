<?php

require_once "../configuration.php";

function send_mail_with_activation_link($email, $activation_key) {
    // Subject
    $subject = "Private Lessons - Account activation";

    // Message
    $message = "
    <html>
    <head>
        <title>$subject</title>
    </head>
    
    <body>
        <h1>$subject</h1>

        <p>
            Your account can be activated by clicking the link below:

            <a href='http://mobilki.cba.pl/PrivateLessonsService/requests/activate_account.php?key=$activation_key'>
                http://mobilki.cba.pl/PrivateLessonsService/requests/activate_account.php?key=$activation_key
            </a>
        </p>
    </body>
    </html>
    ";

    // To send HTML mail, the Content-type header must be set
    $headers[] = "MIME-Version: 1.0";
    $headers[] = "Content-type: text/html; charset=utf-8";

    // Additional headers
    $headers[] = "From: Private Lessons Service <contact@privatelessons.com>";

    // Mail it
    mail($email, $subject, $message, implode("\r\n", $headers));
}

?>