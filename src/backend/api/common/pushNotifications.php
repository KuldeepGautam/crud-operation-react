
<?php

function sendNotification($tokens, $title, $body)
{
  define(
    'API_ACCESS_KEY',
    'AAAAosM1N1c:APA91bEi6S_yfaNPHh_oLxe-5464OynYMxoky7wj0XSK_CE3UugpRzx0CUBtRxfVk1ZEJSwY7OzzupEnF0W_qPrAZhemW6DKuVN1h5Rst7sawTtKgR603cNdQB_69179MrCbLN8KLC7p'
  );

  $message = array(
    'title' => $title,
    'message' => $body,
    'timestamp' => timeIST(),
  );

  $fields = array(
    'registration_ids' => $tokens,
    'priority' => 'high',
    'data' => $message,
  );

  $headers = array(
    'Authorization: key=' . API_ACCESS_KEY,
    'Content-Type: application/json',
  );
  #Send Reponse To FireBase Server
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
  curl_setopt($ch, CURLOPT_POST, true);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
  $result = curl_exec($ch);
  curl_close($ch);
  return $result;
}
?>