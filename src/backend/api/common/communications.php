<?php

function CommSendSMS($receipientno, $msg)
{
    $res = null;
    $ch = curl_init();
    $user = "dushyant@sysinfra.in:sisaxs";
    $senderID = "SISAXS";
    $msgtxt = urlencode($msg);
    curl_setopt($ch, CURLOPT_URL, "https://api.mVaayoo.com/mvaayooapi/MessageCompose");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, "user=$user&senderID=$senderID&receipientno=$receipientno&msgtxt=$msgtxt");

    //print("\nSMS\n$msgtxt\n\n");

    //echo "SMS ON HOLD";
    $buffer = "SMS ON HOLD";
    //CommSendMail("root", "SMS AS MAIL - ".$receipientno, $msg);
    $buffer = curl_exec($ch);
    if (empty($buffer)) {
        $res = null;
    } else {
        $res = $buffer;
    }

    curl_close($ch);

    return $res;
}

function CommSendMail($to, $sub, $msg)
{
    $to = "root";
    // use wordwrap() if lines are longer than 70 characters
    $msg = wordwrap($msg, 70);

    // send email
    $res = mail($to, $sub, $msg);

    return $res;
}

function CommSendMailEx($email_to, $email_from, $email_reply_to, $email_subject, $email_message)
{
    $email_to = "root";
    if ($email_reply_to == null) {
        $email_reply_to = $email_from;
    }

    // create email headers
    $headers = 'From: ' . $email_from . "\r\n" .
    'Reply-To: ' . $email_reply_to . "\r\n" .
    'X-Mailer: PHP/' . phpversion();
    $result = mail($email_to, $email_subject, $email_message, $headers);

    //if ($result) echo 'Mail accepted for delivery ';
    //if (!$result) echo 'Test unsuccessful... ';

    return $result;
}

function CommSendMailHtml(
    $email_to,
    $email_cc,
    $email_from,
    $email_reply_to,
    $email_subject,
    $email_message_html
) {
    return CommSendMailHtmlWithCC($email_to, $email_cc, $email_from, $email_reply_to, $email_subject, $email_message_html);
    /*if ($email_reply_to == null)
$email_reply_to = $email_from;

// create email headers

$headers = 'From: '.$email_from."\r\n".
'Reply-To: '.$email_reply_to."\r\n" .
'X-Mailer: PHP/' . phpversion()."\r\n";
$headers .= "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

$result = mail($email_to, $email_subject, $email_message_html, $headers);

//if ($result) echo 'Mail accepted for delivery ';
//if (!$result) echo 'Test unsuccessful... ';

return $result;            */
}

function CommSendMailHtmlWithCC($email_to, $email_cc, $email_from, $email_reply_to, $email_subject, $email_message_html)
{
    // $email_to = "root";
    if ($email_reply_to == null) {
        $email_reply_to = $email_from;
    }

    // create email headers

    $headers = 'From: ' . $email_from . "\r\n" .
    'Reply-To: ' . $email_reply_to . "\r\n" .
    'X-Mailer: PHP/' . phpversion() . "\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

    if ($email_cc != null) {
        $headers .= "CC: " . $email_cc . "\r\n";
    }

    $result = mail($email_to, $email_subject, $email_message_html, $headers);

    //if ($result) echo 'Mail accepted for delivery ';
    //if (!$result) echo 'Test unsuccessful... ';

    return $result;
}

// Test Send Mail
//$message = '<html><body>';
//$message .= '<h1>Hello, World!</h1>';
//$message .= '</body></html>';
//$res = CommSendMailHtml("root", "Akhilesh@Html.com", null, "This is an html mail", $message);
//echo "<br><br>Result: $res<br>";

//Test Send SMS
/*if(is_callable('curl_init'))
{
echo "cURL OK<br>";
echo "Sending SMS<br><br>";
$res = CommSendSMS("9899280128", "ALARM All is Well TRIGGERED AT SITE Noida ON 30/07/2015 03:32:00 pm");
echo "<br><br>Result: $res<br>";
}
else
{
echo "Not Installed cURL ";
phpinfo();
}*/
