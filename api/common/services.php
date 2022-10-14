<?php

function getAPIs()
{
  // $ubuntu1 = "10.0.1.78";
  // $ubuntu2 = "10.0.2.36";
  $localhost = "localhost";

  // $ubuntu2 = "iprotect-dev.sisrtd.com";
  // $localhost = "iprotect-dev.sisrtd.com";

  // $ubuntu2 = "iprotect.com";
  // $localhost = "iprotect.com";

  return [
    "services" => "http://$localhost/api/services",
    "spawn" => "http://$localhost/api/spawn",
    // "calling" => "http://203.122.34.206/api/calling",
    // "calling" => "http://203.122.34.206:20000/function001",
    "calling" => "http://203.122.34.206:20000/function002",
    "notification" => "https://fcm.googleapis.com/fcm/send",
    "email" => "https://email.sisrtd.com/api/sendmail"
  ];
}

function curlService($method, $url, $data = null, $headers = null)
{
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  if (strtolower($method) === "post") curl_setopt($ch, CURLOPT_POST, true);
  if ($headers) curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  if ($data) curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

  $output = curl_exec($ch);
  curl_close($ch);

  return $output;
}

function wgetService($method, $url, $data = null, $headers = null)
{
  if (strtolower($method) === "get")
    return exec("wget $url > /dev/null &");

  if (strtolower($method) === "post" && $data) {
    $data = json_encode($data);
    return exec("wget -O- --post-data='$data' $url > /dev/null &");
  }

  // --output-document=/var/www/html/logs/notification3.log
  return false;
}
