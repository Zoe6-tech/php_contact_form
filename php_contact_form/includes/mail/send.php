<?php
// ini_set('display_errors', 1);
// TODO: takes care of the form submission

// 4. Return the proper info in JSON format.
//  a. What is AJAX?
//      AJAX is a way for the browser to send data without reloading the page.
//  b. What is JSON (in PHP)
//      JSON is a filetype that works natively with JS. It took over XML 
//  c. How to build JSON (in PHP)
header('Access-Control-Allow-Origin*'); 
header('Content-Type: application/json; charset=UTF-8'); // allows the browser to parse the data in JSON format.
$results = [];
$visitor_name = '';
$visitor_email = '';
$visitor_message = '';

// 1. Check the submission and validate the data [is there non-mailable items?]
// $_POST['firstname']

if(isset($_POST['firstname'])) {
    $visitor_name = filter_var($_POST['firstname'], FILTER_SANITIZE_STRING);
}

if(isset($_POST['lastname'])) {
    $visitor_name .= ' '.filter_var($_POST['lastname'], FILTER_SANITIZE_STRING);
}

if(isset($_POST['email'])) {
    $visitor_email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
}

if(isset($_POST['message'])) {
    $visitor_message = filter_var(htmlspecialchars($_POST['message']), FILTER_SANITIZE_STRING);
}

$results['name'] = $visitor_name;
$results['message'] = $visitor_message;

// 2. Prepare the email [Prepare it in a certain format.]
$email_subject = 'Inquiry From Portfolio Site';
$email_recipient = 'test@mydomain.com';
$email_message = sprintf('Name: %s, Email: %s, Message: %s,', $visitor_name, $visitor_email, $visitor_message);
// Make sure you run the code in PHP 7.4 or above otherwise $email_headers needs to be a string.
$email_headers = array(
    // best practice, but it may need to you have a mail setup in noreply@yourdomain.ca
    // 'From'=>'noreply@yourdomain.ca',
    'From'=>$visitor_email
    // will still work, but will probably be flagged by e-mail client as spam since the DNS server email address differs from the actual address.
    // 'Reply-To'=>$visitor_email,
);

// 3. Send out the email.
$email_result = mail($email_recipient, $email_subject, $email_message, $email_headers);
if($email_result) {
    $results['message'] = sprintf('Thank you for contacting us, %s! You should receive a reply within 24 hours', $visitor_name);
} else {
    $results['message'] = sprintf('%s, we are sorry, but the email did not send.', $visitor_name);
}

echo json_encode($results);