<?php

function spawnCall()
{
  $apis = getAPIs();
  $callingApi = $apis["calling"];
  $urlParsed = parse_url($_SERVER['REQUEST_URI']);
  $query = urldecode($urlParsed["query"]);

  curlService("GET", "$callingApi?$query");
  // curlService("GET", "http://iprotect.com/api/controllers/spawnTask.php?$query");
  // curlService("GET", "http://rtd.net.in/api/calling?username=admin&password=system&mobiles=9996895534&siteName=New%20Delhi&siteId=SITEIDTEST&alarms=door,smoke,pir,theft,wire");

  return "Call executed";
}

function spawnNotification($frombody)
{
  $apis = getAPIs();
  $notificationApi = $apis["notification"];
  $request = (array) $frombody;

  $tokens = $request["tokens"];
  $title = $request["title"];
  $body = $request["body"];


  $message = array(
    'title' => $title,
    'message' => $body,
    'timestamp' => timeIST(),
  );

  $data = array(
    'registration_ids' => $tokens,
    'priority' => 'high',
    'data' => $message,
  );

  $notification = [
    'registration_ids' => $tokens,
    'notification' => [
      'title' => $title,
      'body' => $body,
    ]
  ];

  // echo json_encode($notification) . "\r\n";
  $headers = array(
    'Authorization: key=AAAAosM1N1c:APA91bEi6S_yfaNPHh_oLxe-5464OynYMxoky7wj0XSK_CE3UugpRzx0CUBtRxfVk1ZEJSwY7OzzupEnF0W_qPrAZhemW6DKuVN1h5Rst7sawTtKgR603cNdQB_69179MrCbLN8KLC7p',
    'Content-Type: application/json',
  );

  // return json_decode(curlService("POST", $notificationApi, $data, $headers));
  return json_decode(curlService("POST", $notificationApi, $notification, $headers));
}

function spawnEmail($frombody)
{
  $apis = getAPIs();
  $emailApi = $apis["email"];
  return json_decode(curlService("POST", "$emailApi", $frombody));
}
