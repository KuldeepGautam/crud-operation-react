<?php

require_once $pathPushNotifications;

function postIProtect($q, $settings, $orion)
{
  $protocol = protocolsVerification($q);
  // print_r($protocol);

  extract($protocol);

  if (!$isValid) {
    // protocol verification error.
    insertDump($q);
    $return = array(
      'errorMessage' => $message,
      'data' => $q,
    );

    return $return;
  }

  $temperatureSites = [
    'STIPLTN024',
    'STIPLTN019',
    'STIPLTN004',
    'STIPLMUM46',
    'STIPLDEL60',
    'STIPLDEL19',
    'STIPLD1255',
    'STIPLD1512',
    'STIPLD1327',
    'STIPLDL062',
    'STIPLD1176',
    'STIPLDL589',
    'STIPLD1609',
    'STIPLD1604',
  ];

  if (in_array($data['siteId'], $temperatureSites)) $data['temperature'] = 300;

  # database information
  $sites = selectSites(null, null, $data['siteId']);
  $sites = $sites['data'];
  $iProtect = selectIProtect(null, $data['siteId'], 1, 1);
  $iProtect = $iProtect['data'];

  // print_r($sites);
  if ($sites) $data['siteName'] = $sites[0]['siteName'];

  $iProtect[0] = count($iProtect) > 0 ? $iProtect[0] : null;

  # handling device data
  $flags = handleFlags($protocolId, $sites[0]['siteRid'], $data, $iProtect);
  $iProtectNew = handleIProtect($protocolId, $flags, $data, $orion, $q);
  // print_r($iProtectNew);

  $iProtectId = insertUpdateIProtect($iProtectNew);

  if (!$iProtectId) {
    insertDump($q);
    return 'An unexpected error occured';
  }

  $iProtectNew['iProtectId'] = $iProtectId;

  if (!$sites) {
    return "invalid site id";
  }

  // $conditionSettings = false;
  // $responseSettings = null;

  // if ($conditionSettings) {
  //     # handling settings data
  //     $responseSettings = handleSettings($sites, $settings);
  // }

  # handling battery energy data.
  handleEnergyData($iProtectNew, $iProtect, $sites, $flags);

  # handling alerts
  $isAlarm = handleAlarms($iProtect, $iProtectNew);

  //print_r($sites);
  handleNotification($isAlarm, $flags, $iProtectNew, $sites);

  postIProtectDemo($q, $settings, $orion);
  return handleResponse($iProtectNew, $sites);
}

function postIProtectDemo($q, $settings, $orion)
{
  // Add site IDs to replace & duplicate
  $q = str_replace('CWNR230000', 'IPROTECT01', $q);
  $q = str_replace('CWNR040000', 'IPROTECT02', $q);
  $q = str_replace('GSH0070000', 'IPROTECT03', $q);
  $q = str_replace('AGR0400000', 'IPROTECT04', $q);
  $q = str_replace('AGR0020000', 'IPROTECT05', $q);
  $q = str_replace('AGR0060000', 'IPROTECT06', $q);
  $q = str_replace('AGR0130000', 'IPROTECT07', $q);
  $q = str_replace('AGR0180000', 'IPROTECT08', $q);
  $q = str_replace('RVDPBB0000', 'IPROTECT09', $q);
  $q = str_replace('KNDHW10000', 'IPROTECT10', $q);
  $q = str_replace('KNDHW20000', 'IPROTECT11', $q);
  $q = str_replace('DHAR010000', 'IPROTECT12', $q);

  $protocol = protocolsVerification($q);
  // print_r($protocol);

  extract($protocol);

  if (!$isValid) {
    // protocol verification error.
    insertDump($q);
    $return = array(
      'errorMessage' => $message,
      'data' => $q,
    );

    return $return;
  }

  $temperatureSites = [
    'STIPLTN024',
    'STIPLTN019',
    'STIPLTN004',
    'STIPLMUM46',
    'STIPLDEL60',
    'STIPLDEL19',
    'STIPLD1255',
    'STIPLD1512',
    'STIPLD1327',
    'STIPLDL062',
    'STIPLD1176',
    'STIPLDL589',
    'STIPLD1609',
    'STIPLD1604',
  ];

  if (in_array($data['siteId'], $temperatureSites)) $data['temperature'] = 300;

  # database information
  $sites = selectSites(null, null, $data['siteId']);
  $sites = $sites['data'];
  $iProtect = selectIProtect(null, $data['siteId'], 1, 1);
  $iProtect = $iProtect['data'];

  // print_r($sites);
  if ($sites) $data['siteName'] = $sites[0]['siteName'];

  $iProtect[0] = count($iProtect) > 0 ? $iProtect[0] : null;

  # handling device data
  $flags = handleFlags($protocolId, $sites[0]['siteRid'], $data, $iProtect);
  $iProtectNew = handleIProtect($protocolId, $flags, $data, $orion, $q);
  // print_r($iProtectNew);

  $iProtectId = insertUpdateIProtect($iProtectNew);

  if (!$iProtectId) {
    insertDump($q);
    return 'An unexpected error occured';
  }

  $iProtectNew['iProtectId'] = $iProtectId;

  if (!$sites) {
    return "invalid site id";
  }

  // $conditionSettings = false;
  // $responseSettings = null;

  // if ($conditionSettings) {
  //     # handling settings data
  //     $responseSettings = handleSettings($sites, $settings);
  // }

  # handling battery energy data.
  handleEnergyData($iProtectNew, $iProtect, $sites, $flags);

  # handling alerts
  $isAlarm = handleAlarms($iProtect, $iProtectNew);

  //print_r($sites);
  handleNotification($isAlarm, $flags, $iProtectNew, $sites);

  return handleResponse($iProtectNew, $sites);
}



function handleSettings($sites, $settings)
{
  $settingsValidation = settingsValidation($settings);
  //print_r($settingsValidation);
  $settingsIsValid = $settingsValidation['isValid'];
  $settingsMessage = $settingsValidation['message'];
  $settingsData = $settingsValidation['settingsData'];
  $noUpdate = $settingsValidation['noUpdate'];

  if (!$settingsIsValid) {
    // invalid data string

    $errorMessage = "settings=$settingsMessage";
    insertDeviceSettingsDump($settings, $errorMessage);
    return $errorMessage;
  }

  $siteRid = $sites[0]['siteRid'];

  $deviceSettingsDefault = selectDeviceSettings(0, deviceSettingsDefault, $siteRid);
  //site first time communication for settings.
  if (count($deviceSettingsDefault) === 0) {
    insertUpdateDeviceSettings(0, deviceSettingsDefault, $siteRid, $settingsData);
    insertUpdateDeviceSettings(0, deviceSettingsCurrentDevice, $siteRid, $settingsData);
    insertUpdateDeviceSettings(0, deviceSettingsCurrentServer, $siteRid, $settingsData);
    return $noUpdate;
  }

  $deviceSettingsUpdate = selectDeviceSettings(0, deviceSettingsUpdate, $siteRid);
  if (count($deviceSettingsUpdate) > 0) {
    $isEqual = $deviceSettingsUpdate[0]['settings'] === $settings;
    $return = $isEqual ? $noUpdate : $deviceSettingsUpdate[0]['settings'];

    if ($isEqual) {
      insertUpdateDeviceSettings(1, deviceSettingsCurrentServer, $siteRid, $settingsData);
      insertUpdateDeviceSettings(1, deviceSettingsCurrentDevice, $siteRid, $settingsData);
      //deleteSettings(deviceSettingsUpdate, $siteRid);
    }
    //echo "debugging\r\n";
    return $return;
  }

  $deviceSettingsCurrentDevice = selectDeviceSettings(0, deviceSettingsCurrentDevice, $siteRid);
  if (count($deviceSettingsCurrentDevice) > 0) {
    $isEqual = $deviceSettingsCurrentDevice[0]['settings'] === $settings;

    if (!$isEqual) {
      insertUpdateDeviceSettings(1, deviceSettingsCurrentDevice, $siteRid, $settingsData);
    }
  } else {
    insertUpdateDeviceSettings(0, deviceSettingsCurrentDevice, $siteRid, $settingsData);
  }

  return $noUpdate;
}

function handleFlags($protocolId, $siteRid, $data, $iProtect)
{

  /*
    array(
    [0] => 'device' => 0x80000000,
    [1] => 'siren' => 0x40000000,
    [2] => 'pir' => 0x20000000,
    [3] => 'door' => 0x10000000,
    [4] => 'smoke' => 0x08000000,
    [5] => 'bbActive' => 0x04000000,
    [6] => 'bb1Active' => 0x02000000,
    [7] => 'bb2Active' => 0x01000000,
    [8] => 'bb3Active' => 0x00800000,
    [9] => 'currentDirection' => 0x00400000,
    [10] => 'currentDirection1' => 0x00200000,
    [11] => 'currentDirection2' => 0x00100000,
    [12] => 'currentDirection3reserved' => 0x00080000,
    [13] => 'currentDirection4reserved' => 0x00040000,
    [14] => 'theft' => 0x00020000,
    [15] => 'theft1' => 0x00010000,
    [16] => 'theft2' => 0x00008000,
    [17] => 'theft3reserved' => 0x00004000,
    [18] => 'bts' => 0x00002000,
    [19] => 'bts' => 0x00001000,
    );
     */
  if (!$protocolId || !$data || !$iProtect) {
    return;
  }

  $iProtectNew = $data;
  $iProtectOld = $iProtect[0];

  $flags = $data['flags'];
  // echo "flags before: $flags\r\n";

  # orion connected
  $flags[16] = $flags[14];
  # input mains relay
  $flags[17] = $flags[15];

  $parameterTheft = array(
    'protocolId' => $protocolId,
    'siteRid' => $siteRid,
    'mainPower' => $iProtectNew['mainPower'] / 10,
    'secondaryPower1' => $iProtectNew['secondaryPower1'],
    'secondaryPower2' => $iProtectNew['secondaryPower2'],
    'secondaryPower3reserved' => $iProtectNew['secondaryPower3reserved'],
    'secondaryPower1Old' => $iProtectOld['secondaryPower1'] * 10,
    'secondaryPower2Old' => $iProtectOld['secondaryPower2'] * 10,
    'secondaryPower3reservedOld' => $iProtectOld['secondaryPower3reserved'] * 10,
  );

  // print_r($parameterTheft);

  $status = calculateTheft($parameterTheft);
  // print_r($status);

  # theft
  $flags[14] = $status['theft'];

  # wire
  $flags[15] = $status['theft1'];

  # bts
  $flags[18] = $status['bts'];

  # battery
  $flags[19] = $status['battery'];
  // echo "flags after: $flags\r\n";
  return $flags;
}

function handleIProtect($protocolId, $processedFlags, $data, $orion, $q)
{
  $lvd = null;
  $lvd = null;
  $lvd1 = null;
  $lvd2 = null;
  $lvd3 = null;
  $lvd4 = null;
  $lvd5 = null;
  $lvd6 = null;
  $lvd7 = null;

  $mainsFrequency = null;
  $dgFrequency = null;
  $mainsRunHours = null;

  extract($data);

  // echo "processedFlags: $processedFlags\r\n";

  $iProtectFlags = packBinaryFlags($processedFlags);

  return array(
    'deviceId' => $deviceId,
    'protocolId' => $protocolId,
    'siteId' => $siteId,
    'siteName' => $siteName,
    'datetime' => $datetime,
    'iProtectFlags' => $iProtectFlags,
    'temperature' => $temperature / 10,
    'auxiliaryPower' => $auxiliaryPower / 10,
    'mainPower' => $mainPower / 10,
    'secondaryPower' => $secondaryPower / 10,
    'secondaryPower1' => $secondaryPower1 / 10,
    'secondaryPower2' => $secondaryPower2 / 10,
    'secondaryPower3reserved' => $secondaryPower3reserved / 10,
    'impedance' => $impedance,
    'orion' => $orion,
    'lvd' => $lvd * 100,
    'lvd1' => $lvd1 * 100,
    'lvd2' => $lvd2 * 100,
    'lvd3' => $lvd3 * 100,
    'lvd4' => $lvd4 * 100,
    'lvd5' => $lvd5 * 100,
    'lvd6' => $lvd6 * 100,
    'lvd7' => $lvd7 * 100,
    'mainsFrequency' => $mainsFrequency,
    'dgFrequency' => $dgFrequency,
    'mainsRunHours' => $mainsRunHours,
    'batteryRunHours' => $batteryRunHours,
    'dgRunHours' => $dgRunHours,
    'mainsDailyRunHours' => $mainsDailyRunHours,
    'quantum' => $q,
  );
}

function handleEnergyData($iProtectNew, $iProtect, $sites, $flags)
{
  if (!$iProtect || !$sites) {
    return;
  }

  extract($sites[0]);

  $iProtectOld = $iProtect[0];
  $currentDirection = $flags[9];
  $currentDirection1 = $flags[10];
  $currentDirection2 = $flags[11];

  $batteryBank = objectBatteryBank($batteryAH, $batteryCapacity, $batteryEnergy);
  $batteryBank1 = objectBatteryBank($batteryAH1, $batteryCapacity1, $batteryEnergy1);
  $batteryBank2 = objectBatteryBank($batteryAH2, $batteryCapacity2, $batteryEnergy2);

  // $bank = array(
  //     'batteryBank' => $batteryBank,
  //     'batteryBank1' => $batteryBank1,
  //     'batteryBank2' => $batteryBank2,
  // );

  $current = objectCurrent(floatval($iProtectOld['secondaryPower']), floatval($iProtectNew['secondaryPower']), $currentDirection);
  $current1 = objectCurrent(floatval($iProtectOld['secondaryPower1']), floatval($iProtectNew['secondaryPower1']), $currentDirection1);
  $current2 = objectCurrent(floatval($iProtectOld['secondaryPower2']), floatval($iProtectNew['secondaryPower2']), $currentDirection2);

  //print_r($current);
  $voltage = array(
    'voltageOld' => floatval($iProtectOld['mainPower']),
    'voltageNew' => floatval($iProtectNew['mainPower']),
  );

  // print_r($voltage);
  // print_r($collectedAt);
  // echo "\r\n";

  $batteryBank = batteryBankCalculation($current, $voltage, $batteryBank, $iProtectOld['timestamp']);
  $batteryBank1 = batteryBankCalculation($current1, $voltage, $batteryBank1, $iProtectOld['timestamp']);
  $batteryBank2 = batteryBankCalculation($current2, $voltage, $batteryBank2, $iProtectOld['timestamp']);

  $batteryBank = array(
    'batteryBank' => $batteryBank,
    'batteryBank1' => $batteryBank1,
    'batteryBank2' => $batteryBank2,
  );

  // echo "new battery bank \r\n";
  // print_r($batteryBank);
  // echo "\r\n";

  // update site last collection
  updateSiteCollectedAtLast($siteId, $siteRid, $iProtectNew, $batteryBank);
}

function handleAlarms($dataOld, $dataNew)
{
  // check for breach...
  return isAlarm($dataOld[0], $dataNew);
}

function handleNotification($isAlarm, $flags, $iProtectNew, $sites)
{
  // echo "handleNotification\r\n";
  // var_dump($isAlarm);
  if (!$isAlarm['isAlarm'] || !$sites) {
    return;
  }

  extract($sites[0]);

  // echo "flags: true: $flags\r\n";

  $pir = $flags[2];
  $door = $flags[3];
  $smoke = $flags[4];
  $theft = $flags[14];
  $theft1 = $flags[15];
  $bts = $flags[18];
  $battery = $flags[19];

  // echo $iProtectNew['secondaryPower2'] . ": $battery" . "\r\n";
  # notification process
  $body = null;
  $body .= ', ' . ($bts ? 'bts' : null);
  $body .= ', ' . ($battery ? 'battery' : null);
  $body .= ', ' . ($pir ? 'pir warning' : null);
  $body .= ', ' . ($door ? 'door open' : null);
  $body .= ', ' . ($smoke ? 'smoke warning' : null);
  $body .= ', ' . ($theft ? 'theft' : null);
  $body .= ', ' . ($theft1 ? 'theft1' : null);
  $body .= ', ';
  $body .= ',' . intval($iProtectNew['mainPower']);
  $body .= ',' . $siteName;

  // echo "body: $body\r\n";

  // $calling =
  // strpos($body, 'smoke') !== false ||
  // strpos($body, 'theft') !== false ||
  // strpos($body, 'door') !== false ||
  // strpos($body, 'theft1') !== false;

  # night motion should be considered. 6pm to 6am.

  $alarms = '';
  $alarms .= strpos($body, 'door') !== false ? 'door,' : null;
  $alarms .= strpos($body, 'smoke') !== false ? 'smoke,' : null;
  $alarms .= strpos($body, 'bts') !== false ? 'bts,' : null;
  $alarms .= strpos($body, 'theft') !== false ? 'theft,' : null;
  $alarms .= strpos($body, 'theft1') !== false ? 'wire,' : null;
  $alarms .= strpos($body, 'battery') !== false ? 'battery,' : null;

  // print_r($iProtectNew);

  $alarms = trim($alarms, ',');

  // echo "alarms: $alarms\r\n";
  if (!$isAlarm['isBattery'] && $alarms === 'battery') $alarms = null;

  if (!isnull($alarms)) {
    # saving the alarms into database
    $iProtectNew['breach'] = $alarms;
    $alarmId = insertAlarm($siteRid, $iProtectNew);
    // callingQuery = "username=admin&password=system&mobiles=9625191665&siteId=SITEIDTEST&siteName=Noida%20Sector%205&alarms=smoke";

    $siteName = urlencode($siteName);

    $callQuery = "server=IPROTECT&username=admin&password=system&alarmid=$alarmId&l1=$l1Number&l2=$l2Number&l3=$l3Number&siteId=$siteId&siteName=$siteName&alarms=$alarms";
    // echo "callQuery: $callQuery\r\n";
    $callQuery = urlencode($callQuery);

    $apis = getAPIs();
    $servicesApi = $apis["services"];
    $callingApi = "$servicesApi/call?$callQuery";
    // echo "callingApi: $callingApi \r\n";
    curlService("GET", $callingApi);
  }

  // echo "Result: $callResult\r\n";
  $tokens = selectTokens($siteRid);
  // print_r($tokens) . "\r\n";

  if ($tokens) {
    // TODO: use array_map function instead.
    $allTokens = [];
    foreach ($tokens as $token) {
      if ($token['token']) $allTokens[] = $token['token'];
    }
    // sendNotification($allTokens, $iProtectNew['siteId'], $body);

    $notificationData = [
      "tokens" => $allTokens,
      "title" => $iProtectNew['siteId'],
      "body" => $body
    ];

    // echo json_encode($notificationData);
    $notificationResult = curlService("POST", "$servicesApi/notification", $notificationData);
    // echo "notificationResult: $notificationResult \r\n";

    // emitting real time data
    //realTimeData($iProtectId, $siteRid);
  }
}

function handleResponse($iProtectNew, $sites)
{
  // abs(serverTime - deviceTime) > 600 seconds than isRTC = 1 otherwise 0.
  $isRTC = abs(time() - strtotime($iProtectNew['datetime'])) > 600 ? 1 : 0;

  $dateParams = currentDateParams();
  $hour = $dateParams['hour'];
  $minute = $dateParams['minute'];
  $second = $dateParams['second'];
  $year = $dateParams['year'];
  $month = $dateParams['month'];
  $day = $dateParams['day'];

  $isMainsCurrentDailyRunHour = 0;
  $mainsCurrentDailyRunHour = 3;

  $mainsTotalRunHour = 12;

  // $responseString = '**';
  // $responseString .= $hour;
  // $responseString .= '**';
  // $responseString .= '**';
  // $responseString .= '**';
  // $responseString .= '**';
  // $responseString .= '**';
  // $responseString .= '**';
  // $responseString .= '**';
  // $responseString .= '**';
  #"**1,13,46,28,01,20,0,3,1#"

  return "**$isRTC,$hour,$minute,$day,$month,$year,$isMainsCurrentDailyRunHour,$mainsCurrentDailyRunHour,$mainsTotalRunHour#";
}

function realTimeData($iProtectId, $siteRid)
{
  // Emitting real time data on sockets
  $iProtectRealTime = getIProtect(null, $iProtectId, 1, 1, 1);

  $siteUsers = selectUsersBySite($siteRid);

  if ($siteUsers) {
    foreach ($siteUsers as $user) {
      socketEmit($user['mobileNumber'], $iProtectRealTime);
    }
  }

  // $siteUser = new SiteUsers();
  // $siteUser->siteRid = $siteRid;

  // $statement = $siteUser->selectUsersBySite(null);
  // if ($statement->rowCount() > 0) {
  //     while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
  //         extract($row);
  //         socketEmit($mobileNumber, $iProtectRealTime);
  //     }
  // }
}

function updateSiteCollectedAtLast($siteId, $siteRid, $iProtectNew, $batteryBank)
{
  //print_r($batteryBank);
  extract($batteryBank);

  $collect = array(
    'batteryEnergy' => $batteryBank['batteryEnergy'],
    'batteryCapacity' => $batteryBank['batteryCapacity'],
    'batteryKwh' => $batteryBank['batteryKwh'],

    'batteryEnergy1' => $batteryBank1['batteryEnergy'],
    'batteryCapacity1' => $batteryBank1['batteryCapacity'],
    'batteryKwh1' => $batteryBank1['batteryKwh'],

    'batteryEnergy2' => $batteryBank2['batteryEnergy'],
    'batteryCapacity2' => $batteryBank2['batteryCapacity'],
    'batteryKwh2' => $batteryBank2['batteryKwh'],

    'iProtectFlags' => $iProtectNew['iProtectFlags'],
    'auxiliaryPower' => $iProtectNew['auxiliaryPower'],
    'secondaryPower' => $iProtectNew['secondaryPower'],
    'secondaryPower1' => $iProtectNew['secondaryPower1'],
    'secondaryPower2' => $iProtectNew['secondaryPower2'],
    'secondaryPower3reserved' => $iProtectNew['secondaryPower3reserved'],
    'mainPower' => $iProtectNew['mainPower'],
    'temperature' => $iProtectNew['temperature'],
  );

  updateSiteCollectedAt($collect, $siteId);
  insertSitesEnergyDetails($collect, $siteRid, $iProtectNew['iProtectId']);
}

function postAlarmsAlertsCall($username, $password, $frombody)
{
  $credentials = validateAdmin($username, $password);
  if (!$credentials) return responseHandler(2, 'postAlarmsAlertsCall', []);
  if (!$frombody) return responseHandler(3, 'postAlarmsAlertsCall', []);

  $alerts = (array) $frombody;
  if (!$alerts) return responseHandler(3, 'postAlarmsAlertsCall', []);

  $sql = [];
  $alarmIds = [];
  foreach ($alerts as $index => $alert) {
    $alarmAlert = (array) $alert;
    extract($alarmAlert);

    $errors = validatingCallData($alarmAlert);
    if ($errors) return responseHandler(5, 'postAlarmsAlertsCall', json_encode(["At Index: $index", $errors]));
    if (!in_array($alarmId, $alarmIds)) $alarmIds[] = $alarmId;

    $date = unixToDate(round($date / 1000));
    $exitDate = unixToDate(round($exitDate / 1000));
    $sql[] = "INSERT INTO alarmsAlerts SET alarmId = '$alarmId', alertType = 'call', siteId = '$siteId', mobile = '$mobile', alarms = '$alarms', date = '$date', callString = '$callString', status = '$status', statusString = '$statusString', callDurationSeconds = $callDurationSeconds, exitDate = '$exitDate', comments = '$comments';";
  }

  $alarmIdsInDb = validateAlarmIds($alarmIds);
  $invalidAlarmIds = array_values(array_filter($alarmIds, function ($alarmId) use ($alarmIdsInDb) {
    return !in_array($alarmId, $alarmIdsInDb);
  }));

  if ($invalidAlarmIds) return responseHandler(3, 'postAlarmsAlertsCall', json_encode(["Invalid alarmId(s)", $invalidAlarmIds]));

  // insert data into db
  if (!insertAlarmsAlerts(implode("", $sql))) return responseHandler(4, 'postAlarmsAlertsCall', []);

  // return responseHandler(0, 'postAlarmsAlertsCall', []);
  return responseHandler(0, 'postAlarmsAlertsCall', $sql); //DEBUG SQL
}

function getIProtect($siteId, $iProtectId, $pageNumber, $pageSize, $limit)
{
  // print_r($_GET);

  $pageSize = $pageSize ? $pageSize : $limit;
  $iProtect = selectIProtect($iProtectId, $siteId, $pageNumber, $pageSize);

  $iProtectData = $iProtect['data'];

  // return $iProtectData;
  if (!$iProtectData) {
    return responseHandler(1, "getIProtect", array());
  }

  // print_r($iProtect);
  // print_r($iProtectData);

  foreach ($iProtectData as $key => $iProtectItem) {
    // echo "DEBUG $key\r\n";
    // $iProtectData[$key]['orion'] = base64_encode($iProtectItem['orion']);
    $iProtectData[$key]['timestamp'] += 19800;

    $iProtectData[$key]['iProtectFlags'] = versionFlags($iProtectItem['iProtectFlags']);
    $iProtectData[$key]['binary'] = base64_encode($iProtectItem['iProtectFlags']);

    $sites = selectSites(null, null, $iProtectItem['siteId']);
    $sites = $sites['data'];

    if (!$sites) {
      continue;
    }

    // print_r($sites);
    extract($sites[0]);

    $installedAt = intval($installedAt);
    $iProtectData[$key]['installedAt'] = $installedAt;
    // echo "installedAt: $installedAt\r\n";
    $iProtectData[$key]['agingInDays'] = (string) dateDiff($installedAt, time());
    // echo "key: $key\r\n";

    $iProtectData[$key]['siteName'] = $siteName;

    $iProtectData[$key]['batteryAH'] = $batteryAH;
    $iProtectData[$key]['batteryCapacity'] = $batteryCapacity;
    $iProtectData[$key]['batteryEnergy'] = $batteryEnergy;

    $iProtectData[$key]['batteryAH1'] = $batteryAH1;
    $iProtectData[$key]['batteryCapacity1'] = $batteryCapacity1;
    $iProtectData[$key]['batteryEnergy1'] = $batteryEnergy1;

    $iProtectData[$key]['batteryAH2'] = $batteryAH2;
    $iProtectData[$key]['batteryCapacity2'] = $batteryCapacity2;
    $iProtectData[$key]['batteryEnergy2'] = $batteryEnergy2;
  }

  $config = array(
    'itemsCount' => $iProtect['itemsCount']
  );
  //iProtectId=1163226
  // print_r($iProtectData);
  // return $iProtectData[7];

  return responseHandler(0, "getIProtect", $iProtectData, $config);
}

function getIProtectByUser($username, $password, $siteId, $iProtectId, $searchText, $startDate, $endDate, $pageNumber, $pageSize)
{
  $users = validateUser($username, $password);
  if (!$users) {
    return responseHandler(2, "getIProtectByUser", array());
  }

  // print_r($users);

  $iProtect = selectIProtectByUser($iProtectId, $siteId, $users[0]['userId'], $searchText, unixToDate($startDate), unixToDate($endDate), $pageNumber, $pageSize);
  // print_r($iProtect);

  $iProtectData = $iProtect['data'];

  // return $iProtectData;
  if (!$iProtectData) {
    return responseHandler(1, "getIProtectByUser", array());
  }

  // print_r($iProtect);
  // print_r($iProtectData);

  foreach ($iProtectData as $key => $iProtectItem) {
    $iProtectData[$key]['iProtectFlags'] = versionFlags($iProtectItem['iProtectFlags']);
    $iProtectData[$key]['binary'] = base64_encode($iProtectItem['iProtectFlags']);

    // $sites = selectSites(null, null, $iProtectItem['siteId']);
    // $sites = $sites['data'];

    // if (!$sites) {
    //   continue;
    // }

    // // print_r($sites);
    // extract($sites[0]);

    // $installedAt = intval($installedAt);
    // $iProtectData[$key]['installedAt'] = $installedAt;
    // // echo "installedAt: $installedAt\r\n";
    // $iProtectData[$key]['agingInDays'] = (string) dateDiff($installedAt, time());
    // // echo "key: $key\r\n";

    // $iProtectData[$key]['siteName'] = $siteName;

    // $iProtectData[$key]['batteryAH'] = $batteryAH;
    // $iProtectData[$key]['batteryCapacity'] = $batteryCapacity;
    // $iProtectData[$key]['batteryEnergy'] = $batteryEnergy;

    // $iProtectData[$key]['batteryAH1'] = $batteryAH1;
    // $iProtectData[$key]['batteryCapacity1'] = $batteryCapacity1;
    // $iProtectData[$key]['batteryEnergy1'] = $batteryEnergy1;

    // $iProtectData[$key]['batteryAH2'] = $batteryAH2;
    // $iProtectData[$key]['batteryCapacity2'] = $batteryCapacity2;
    // $iProtectData[$key]['batteryEnergy2'] = $batteryEnergy2;
  }

  $config = array(
    'itemsCount' => $iProtect['itemsCount']
  );

  return responseHandler(0, "getIProtectByUser", $iProtectData, $config);
}

function getPowerSupply($username, $password, $siteId, $startDate, $endDate)
{
  // $users = validateUser($username, $password);
  // if (!$users) {
  //     return responseHandler(2, "powerSupply", array());
  // }

  if (!$siteId || !$startDate || !$endDate) {
    return responseHandler(3, "powerSupply", array());
  }

  $powerSupply = GetSiteStatsFromIdStartEnd($siteId, unixToDate($startDate), unixToDate($endDate));

  if ($powerSupply === null) {
    responseHandler(0, "powerSupply", $powerSupply);
  }

  return responseHandler(0, "powerSupply", $powerSupply);
}

function getEnergyLoss($username, $password, $siteRid, $startDate, $endDate)
{
  // $users = validateUser($username, $password);
  // if (!$users) {
  //     return responseHandler(2, "getEnergyLoss", array());
  // }

  if (!$siteRid || !$startDate || !$endDate) return responseHandler(3, "getEnergyLoss", []);

  $energyLoss = selectEnergyLoss($siteRid, unixToDate($startDate), unixToDate($endDate));

  if (!$energyLoss) return responseHandler(0, "getEnergyLoss", []);

  return responseHandler(0, "getEnergyLoss", $energyLoss);
}

function getAlarms($alarmId, $iProtectId, $siteRid, $siteId, $pageNumber, $pageSize)
{
  $alarms = selectAlarms($alarmId, $iProtectId, $siteRid, $siteId, $pageNumber, $pageSize);

  $alarmsData = $alarms['data'];

  if (!$alarmsData) {
    http_response_code(404);
    return responseHandler(1, "getAlarms", array());
  }

  foreach ($alarmsData as $key => $alarmsItem) {
    $alarmsData[$key]['iProtectFlags'] = versionFlags($alarmsItem['iProtectFlags']);
    $alarmsData[$key]['binary'] = base64_encode($alarmsItem['iProtectFlags']);
  }

  $config = array(
    'itemsCount' => $alarms['itemsCount'],
  );

  return responseHandler(0, "getAlarms", $alarmsData, $config);
}

function validatingCallData($body)
{
  extract($body);

  $errors = array();
  if (!array_key_exists('alarmId', $body)) $errors[] = 'alarmId is required';
  else if (isnull($alarmId)) $errors[] = 'alarmId is required';

  if (!array_key_exists('siteId', $body)) $errors[] = 'siteId is required';
  else if (isnull($siteId)) $errors[] = 'siteId is required';

  if (!array_key_exists('mobile', $body)) $errors[] = 'mobile is required';
  else if (isnull($mobile)) $errors[] = 'mobile is required';

  if (!array_key_exists('alarms', $body)) $errors[] = 'alarms is required';
  else if (isnull($alarms)) $errors[] = 'alarms is required';

  if (!array_key_exists('date', $body)) $errors[] = 'date is required';
  else if (isnull($date)) $errors[] = 'date is required';

  if (!array_key_exists('callString', $body)) $errors[] = 'callString is required';
  else if (isnull($callString)) $errors[] = 'callString is required';

  if (!array_key_exists('callString', $body)) $errors[] = 'callString is required';
  else if (isnull($callString)) $errors[] = 'callString is required';

  if (!array_key_exists('status', $body)) $errors[] = 'status is required';
  else if (isnull($status)) $errors[] = 'status is required';

  if (!array_key_exists('statusString', $body)) $errors[] = 'statusString is required';
  else if (isnull($statusString)) $errors[] = 'statusString is required';

  if (!array_key_exists('callDurationSeconds', $body)) $errors[] = 'callDurationSeconds is required';
  else if (isnull($callDurationSeconds)) $errors[] = 'callDurationSeconds is required';

  if (!array_key_exists('callDurationSeconds', $body)) $errors[] = 'callDurationSeconds is required';
  else if (isnull($callDurationSeconds)) $errors[] = 'callDurationSeconds is required';

  if (!array_key_exists('exitDate', $body)) $errors[] = 'exitDate is required';
  else if (isnull($exitDate)) $errors[] = 'exitDate is required';

  if (!array_key_exists('comments', $body)) $errors[] = 'comments is required';
  else if (isnull($comments)) $errors[] = 'comments is required';

  return $errors;
}
