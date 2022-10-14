<?php

function sendmail($frombody)
{
  $request = (array) $frombody;

  $email_to = $request["to"];
  $email_cc = $request["cc"];
  $email_subject = $request["subject"];
  $email_message = $request["body"];

  if (!$email_to) if (!$email_cc) {
    http_response_code(400);
    return ["status" => false, "message" => "to/cc is required"];
  }

  $status = CommSendMailHtml(
    $email_to,
    $email_cc,
    "iprotect@sisrtd.com",
    "iprotect@sisrtd.com",
    $email_subject,
    $email_message
  );

  if ($status) return ["status" => true, "message" => "Mail sent successfully."];

  http_response_code(500);
  return ["status" => false, "message" => "Failed to send mail."];
}
