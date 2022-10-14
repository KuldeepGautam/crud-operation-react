<?php

// /api/services/call
function callService()
{
  $apis = getAPIs();
  $spawnApi = $apis["spawn"];
  $urlParsed = parse_url($_SERVER["REQUEST_URI"]);
  $query = $urlParsed["query"];
  wgetService("GET", "$spawnApi/call?$query");
  return true;
}

// /api/services/notification
function notificationService($frombody)
{
  $apis = getAPIs();
  $spawnApi = $apis["spawn"];
  echo wgetService("POST", "$spawnApi/notification", $frombody);
  return true;
}

function emailService($frombody)
{
  $apis = getAPIs();
  $spawnApi = $apis["spawn"];
  echo wgetService("POST", "$spawnApi/email", $frombody);
  return true;
}

function smsService()
{

  return true;
}
