<?php

require_once $pathGeneralMethods;

function protocolsVerification($string)
{
  // Action web($q);
  // api/controllers/iProtect/add.php?q=*001,01,SISPL00001,1,0,0,1,1,0000,0000,0000,0000$

  //$q = "*001,01,SISPL00001,1,0,0,0,0,0000,0000,1,0000,0000$";

  /*
    1.        Start of String SOS       x               1           *
    2.        Device ID                 xxx             3           Packet
    3.        Protocol ID               xx              2           Packet
    4.        Site ID                   xxxxxxxxxx      10          Packet
    5.        Timestamp                                             Current Timestamp
    6.        Device Status             x               1           Packet
    7.        Siren Status              x               1           Packet
    8.        PIR                       x               1           Packet
    9.        Door                      x               1           Packet
    10.       Smoke                     x               1           Packet
    11.       Auxiliary Power           xxxx            4           Packet  / 10
    12.       Secondary Power           xxxx            4           Packet  / 10
    13.       Current Direction         x               1           Packet
    14.       Main Power                xxxx            4           Packet  / 10
    15.       Temperature               xxxx            4           Packet  / 10
    16.       End of String EOS         x               1           $
     */

  $message = '';
  $start = '*';
  $end = '$';
  $delimiter = ',';

  if (isnull($string)) $message .= 'Empty String. ';
  if (!stringStartValidation($start, $string)) $message .= 'Invalid start: "*". ';
  if (!stringEndValidation($end, $string)) $message .= 'Invalid end: "$". ';

  //clipping start and end of the string.
  $data = substr($string, 1, strlen($string) - 2);
  //var_dump($data);

  $alnum = str_replace($delimiter, '', $data);
  if (!ctype_alnum($alnum)) $message .= 'Contains invalid character. ';

  $explode = explode(',', $data);
  $protocolId = intval($explode[1]);
  // $length = protocolLength($protocolId);

  $count = protocolCount($protocolId);

  // if (!stringLengthValidation($length, $string)) {
  //   $message .= 'String Length Invalid.';
  // }

  if (!dataCountValidation($count, $explode)) $message .= 'Invalid count. ';

  $isValid = strlen($message) > 0 ? false : true;
  $protocol = $isValid ? protocolSelection($protocolId, $explode) : null;

  return array(
    'protocolId' => $protocolId,
    'message' => $message,
    'isValid' => $isValid,
    'data' => $protocol,
  );
}

function protocolLength($protocolId)
{
  switch ($protocolId) {
    case 0:
      return 49;
    case 1:
      return 51;
    case 2:
      return 53;
    case 3:
      return 115;
    case 4:
      return 191;
    case 5:
      return 143;
    case 6:
      return 143;
    case 7:
      return 148;
    case 8:
      return 158;
    case 10:
      return 158;
  }
}

function protocolCount($protocolId)
{
  switch ($protocolId) {
    case 0:
      return 12;
    case 1:
      return 13;
    case 2:
      return 14;
    case 3:
      return 31;
    case 4:
      return 41;
    case 5:
      return 33;
    case 6:
      return 33;
    case 7:
      return 34;
    case 8:
      return 36;
    case 10:
      return 36;
  }
}

function makeDateTime($datetime)
{
  $hour = intval(substr($datetime, 0, 2));
  $minute = intval(substr($datetime, 2, 2));
  $second = intval(substr($datetime, 4, 2));
  $day = intval(substr($datetime, 6, 2));
  $month = intval(substr($datetime, 8, 2));
  $year = intval('20' . substr($datetime, 10, 2));

  return date('Y-m-d H:i:s', mktime($hour, $minute, $second, $month, $day, $year));
}

function protocolSelection($protocolId, $explode)
{
  //print_r($explode);
  // common in all protocols.
  $array = array(
    'deviceId' => $explode[0],
    'protocolId' => $explode[1],
    'siteId' => $explode[2],
    'siteName' => null,
    'datetime' => null,
  );

  if ($protocolId == 0) {

    $device = $explode[3];
    $siren = $explode[4];
    $pir = $explode[5];
    $door = $explode[6];
    $smoke = $explode[7];

    $bbActive = 0;
    $bb1Active = 1;
    $bb2Active = 0;
    $bb3Active = 0;

    $currentDirection = 0;
    $currentDirection1 = 0;
    $currentDirection2 = 0;
    $currentDirection3reserved = 0;
    $currentDirection4reserved = 0;
    $theft = 0;
    $theft1 = 0;
    $theft2 = 0;
    $theft3reserved = 0;

    $flags = $device
      . $siren
      . $pir
      . $door
      . $smoke;

    $array['flags'] = $flags;
    //$array['iProtectFlags'] = packBinaryFlags($flags);

    $array['auxiliaryPower'] = $explode[8];
    $array['mainPower'] = $explode[10];
    $array['temperature'] = $explode[11];

    $array['secondaryPower'] = $explode[9];
    $array['secondaryPower1'] = 0;
    $array['secondaryPower2'] = 0;
    $array['secondaryPower3reserved'] = 0;

    $array['impedance'] = 0;
    $array['lvd'] = 0;

    $array['lvd1'] = 0;
    $array['lvd2'] = 0;
    $array['lvd3'] = 0;
    $array['lvd4'] = 0;
    $array['lvd5'] = 0;
    $array['lvd6'] = 0;
    $array['lvd7'] = 0;
    $array['mainsRunHours'] = 0;
    $array['batteryRunHours'] = 0;
    $array['dgRunHours'] = 0;
    $array['mainsDailyRunHours'] = 0;
  }

  if ($protocolId == 1) {
    $device = $explode[3];
    $siren = $explode[4];
    $pir = $explode[5];
    $door = $explode[6];
    $smoke = $explode[7];

    $bbActive = 0;
    $bb1Active = 1;
    $bb2Active = 0;
    $bb3Active = 0;

    $currentDirection = $explode[10];
    $currentDirection1 = 0;
    $currentDirection2 = 0;
    $currentDirection3reserved = 0;
    $currentDirection4reserved = 0;
    $theft = 0;
    $theft1 = 0;
    $theft2 = 0;
    $theft3reserved = 0;

    $flags = $device
      . $siren
      . $pir
      . $door
      . $smoke
      . $bbActive
      . $bb1Active
      . $bb2Active
      . $bb3Active
      . $currentDirection;

    $array['flags'] = $flags;
    //$array['iProtectFlags'] = packBinaryFlags($flags);

    $array['auxiliaryPower'] = $explode[8];
    $array['mainPower'] = $explode[11];
    $array['temperature'] = $explode[12];

    $array['secondaryPower'] = $explode[9];
    $array['secondaryPower1'] = 0;
    $array['secondaryPower2'] = 0;
    $array['secondaryPower3reserved'] = 0;

    $array['impedance'] = 0;
    $array['lvd'] = 0;
    $array['lvd1'] = 0;
    $array['lvd2'] = 0;
    $array['lvd3'] = 0;
    $array['lvd4'] = 0;
    $array['lvd5'] = 0;
    $array['lvd6'] = 0;
    $array['lvd7'] = 0;
    $array['mainsRunHours'] = 0;
    $array['batteryRunHours'] = 0;
    $array['dgRunHours'] = 0;
    $array['mainsDailyRunHours'] = 0;
  }

  if ($protocolId == 2) {
    $device = $explode[3];
    $siren = $explode[4];
    $pir = $explode[5];
    $door = $explode[6];
    $smoke = $explode[7];

    $bbActive = 0;
    $bb1Active = 1;
    $bb2Active = 0;
    $bb3Active = 0;

    $currentDirection = $explode[10];
    $currentDirection1 = 0;
    $currentDirection2 = 0;
    $currentDirection3reserved = 0;
    $currentDirection4reserved = 0;
    $theft = $explode[13];
    $theft1 = 0;
    $theft2 = 0;
    $theft3reserved = 0;

    $flags = $device
      . $siren
      . $pir
      . $door
      . $smoke
      . $bbActive
      . $bb1Active
      . $bb2Active
      . $bb3Active
      . $currentDirection
      . $currentDirection1
      . $currentDirection2
      . $currentDirection3reserved
      . $currentDirection4reserved
      . $theft;

    $array['flags'] = $flags;
    //$array['iProtectFlags'] = packBinaryFlags($flags);

    $array['auxiliaryPower'] = $explode[8];
    $array['mainPower'] = $explode[11];
    $array['temperature'] = $explode[12];

    $array['secondaryPower'] = $explode[9];
    $array['secondaryPower1'] = 0;
    $array['secondaryPower2'] = 0;
    $array['secondaryPower3reserved'] = 0;

    $array['impedance'] = 0;
    $array['lvd'] = 0;
    $array['lvd1'] = 0;
    $array['lvd2'] = 0;
    $array['lvd3'] = 0;
    $array['lvd4'] = 0;
    $array['lvd5'] = 0;
    $array['lvd6'] = 0;
    $array['lvd7'] = 0;
    $array['mainsRunHours'] = 0;
    $array['batteryRunHours'] = 0;
    $array['dgRunHours'] = 0;
    $array['mainsDailyRunHours'] = 0;
  }

  if ($protocolId == 3) {
    $device = $explode[4];
    $siren = $explode[5];
    $pir = $explode[6];
    $door = $explode[7];
    $smoke = $explode[8];

    $bbActive = $explode[9];
    $bb1Active = $explode[10];
    $bb2Active = $explode[11];
    $bb3Active = $explode[12];

    $currentDirection = $explode[13];
    $currentDirection1 = $explode[14];
    $currentDirection2 = $explode[15];
    $currentDirection3reserved = $explode[16];
    $currentDirection4reserved = $explode[17];
    $theft = $explode[18];
    $theft1 = $explode[19];
    $theft2 = $explode[20];
    $theft3reserved = $explode[21];

    //echo $explode[17];

    $flags = $device
      . $siren
      . $pir
      . $door
      . $smoke
      . $bbActive
      . $bb1Active
      . $bb2Active
      . $bb3Active
      . $currentDirection
      . $currentDirection1
      . $currentDirection2
      . $currentDirection3reserved
      . $currentDirection4reserved
      . $theft
      . $theft1
      . $theft2
      . $theft3reserved;

    $array['flags'] = $flags;
    //$array['iProtectFlags'] = packBinaryFlags($flags);

    $array['temperature'] = $explode[22];
    $array['auxiliaryPower'] = $explode[23];
    $array['mainPower'] = $explode[24];

    $array['secondaryPower'] = $explode[25];
    $array['secondaryPower1'] = $explode[26];
    $array['secondaryPower2'] = $explode[27];
    $array['secondaryPower3reserved'] = $explode[28];

    $array['impedance'] = $explode[29];
    $array['lvd'] = $explode[30];
    $array['lvd1'] = 0;
    $array['lvd2'] = 0;
    $array['lvd3'] = 0;
    $array['lvd4'] = 0;
    $array['lvd5'] = 0;
    $array['lvd6'] = 0;
    $array['lvd7'] = 0;
    $array['mainsRunHours'] = 0;
    $array['batteryRunHours'] = 0;
    $array['dgRunHours'] = 0;
    $array['mainsDailyRunHours'] = 0;
  }

  if ($protocolId == 4) {
    $device = $explode[4];
    $siren = $explode[5];
    $pir = $explode[6];
    $door = $explode[7];
    $smoke = $explode[8];

    $bbActive = $explode[9];
    $bb1Active = $explode[10];
    $bb2Active = $explode[11];
    $bb3Active = $explode[12];

    $currentDirection = $explode[13];
    $currentDirection1 = $explode[14];
    $currentDirection2 = $explode[15];
    $currentDirection3reserved = $explode[16];
    $currentDirection4reserved = $explode[17];
    $theft = $explode[18];
    $theft1 = $explode[19];
    $theft2 = $explode[20];
    $theft3reserved = $explode[21];

    //echo $explode[17];

    $flags = $device
      . $siren
      . $pir
      . $door
      . $smoke
      . $bbActive
      . $bb1Active
      . $bb2Active
      . $bb3Active
      . $currentDirection
      . $currentDirection1
      . $currentDirection2
      . $currentDirection3reserved
      . $currentDirection4reserved
      . $theft
      . $theft1
      . $theft2
      . $theft3reserved;

    $array['flags'] = $flags;
    //$array['iProtectFlags'] = packBinaryFlags($flags);

    $array['temperature'] = $explode[22];
    $array['auxiliaryPower'] = $explode[23];
    $array['mainPower'] = $explode[24];

    $array['secondaryPower'] = $explode[25];
    $array['secondaryPower1'] = $explode[26];
    $array['secondaryPower2'] = $explode[27];
    $array['secondaryPower3reserved'] = $explode[28];

    $array['impedance'] = $explode[29];

    $array['lvd'] = $explode[30]; //lvd0
    $array['lvd1'] = $explode[31];
    $array['lvd2'] = $explode[32];
    $array['lvd3'] = $explode[33];
    $array['lvd4'] = $explode[34];
    $array['lvd5'] = $explode[35];
    $array['lvd6'] = $explode[36];
    $array['lvd7'] = $explode[37];
    $array['mainsRunHours'] = $explode[38];
    $array['batteryRunHours'] = $explode[39];
    $array['dgRunHours'] = $explode[40];
    $array['mainsDailyRunHours'] = 0;
  }

  if ($protocolId == 5 || $protocolId == 6) {
    $device = $explode[4];
    $siren = $explode[5];
    $pir = $explode[6];
    $door = $explode[7];
    $smoke = $explode[8];

    $bbActive = $explode[9];
    $bb1Active = $explode[10];
    $bb2Active = $explode[11];
    $bb3Active = $explode[12];

    $currentDirection = $explode[13];
    $currentDirection1 = $explode[14];
    $currentDirection2 = $explode[15];
    $currentDirection3reserved = $explode[16];
    $currentDirection4reserved = $explode[17];
    $theft = $explode[18];
    $theft1 = $explode[19];
    $theft2 = $explode[20];
    $theft3reserved = $explode[21];

    //echo $explode[17];

    $flags = $device
      . $siren
      . $pir
      . $door
      . $smoke
      . $bbActive
      . $bb1Active
      . $bb2Active
      . $bb3Active
      . $currentDirection
      . $currentDirection1
      . $currentDirection2
      . $currentDirection3reserved
      . $currentDirection4reserved
      . $theft
      . $theft1
      . $theft2
      . $theft3reserved;

    $array['flags'] = $flags;
    //$array['iProtectFlags'] = packBinaryFlags($flags);

    $array['temperature'] = $explode[22];
    $array['auxiliaryPower'] = $explode[23];
    $array['mainPower'] = $explode[24];

    $array['secondaryPower'] = $explode[25];
    $array['secondaryPower1'] = $explode[26];
    $array['secondaryPower2'] = $explode[27];
    $array['secondaryPower3reserved'] = $explode[28];
    $array['impedance'] = $explode[29];
    $array['mainsRunHours'] = $explode[30];
    $array['batteryRunHours'] = $explode[31];
    $array['dgRunHours'] = $explode[32];
    $array['mainsDailyRunHours'] = 0;
  }

  if ($protocolId == 7) {
    $array['datetime'] = makeDateTime($explode[3]);
    $device = $explode[4];
    $siren = $explode[5];
    $pir = $explode[6];
    $door = $explode[7];
    $smoke = $explode[8];

    $bbActive = $explode[9];
    $bb1Active = $explode[10];
    $bb2Active = $explode[11];
    $bb3Active = $explode[12];

    $currentDirection = $explode[13];
    $currentDirection1 = $explode[14];
    $currentDirection2 = $explode[15];
    $currentDirection3reserved = $explode[16];
    $currentDirection4reserved = $explode[17];
    $theft = $explode[18];
    $theft1 = $explode[19];
    $theft2 = $explode[20];
    $theft3reserved = $explode[21];

    //echo $explode[17];

    $flags = $device
      . $siren
      . $pir
      . $door
      . $smoke
      . $bbActive
      . $bb1Active
      . $bb2Active
      . $bb3Active
      . $currentDirection
      . $currentDirection1
      . $currentDirection2
      . $currentDirection3reserved
      . $currentDirection4reserved
      . $theft
      . $theft1
      . $theft2
      . $theft3reserved;

    $array['flags'] = $flags;
    //$array['iProtectFlags'] = packBinaryFlags($flags);

    $array['temperature'] = $explode[22];
    $array['auxiliaryPower'] = $explode[23];
    $array['mainPower'] = $explode[24];

    $array['secondaryPower'] = $explode[25];
    $array['secondaryPower1'] = $explode[26];
    $array['secondaryPower2'] = $explode[27];
    $array['secondaryPower3reserved'] = $explode[28];
    $array['impedance'] = $explode[29];
    $array['mainsRunHours'] = $explode[30];
    $array['batteryRunHours'] = $explode[31];
    $array['dgRunHours'] = $explode[32];
    $array['mainsDailyRunHours'] = $explode[33];
  }

  if ($protocolId == 8 || $protocolId == 10) {
    $array['datetime'] = makeDateTime($explode[3]);
    $device = $explode[4];
    $siren = $explode[5];
    $pir = $explode[6];
    $door = $explode[7];
    $smoke = $explode[8];

    $bbActive = $explode[9];
    $bb1Active = $explode[10];
    $bb2Active = $explode[11];
    $bb3Active = $explode[12];

    $currentDirection = $explode[13];
    $currentDirection1 = $explode[14];
    $currentDirection2 = $explode[15];
    $currentDirection3reserved = $explode[16];
    $currentDirection4reserved = $explode[17];
    $theft = $explode[18];
    $theft1 = $explode[19];
    $theft2 = $explode[20];
    $theft3reserved = $explode[21];

    //echo $explode[17];

    $flags = $device
      . $siren
      . $pir
      . $door
      . $smoke
      . $bbActive
      . $bb1Active
      . $bb2Active
      . $bb3Active
      . $currentDirection
      . $currentDirection1
      . $currentDirection2
      . $currentDirection3reserved
      . $currentDirection4reserved
      . $theft
      . $theft1
      . $theft2
      . $theft3reserved;

    $array['flags'] = $flags;
    //$array['iProtectFlags'] = packBinaryFlags($flags);

    $array['temperature'] = $explode[22];
    $array['auxiliaryPower'] = $explode[23];
    $array['mainPower'] = $explode[24];

    $array['secondaryPower'] = $explode[25];
    $array['secondaryPower1'] = $explode[26];
    $array['secondaryPower2'] = $explode[27];
    $array['secondaryPower3reserved'] = $explode[28];
    $array['impedance'] = $explode[29];

    $array['mainsFrequency'] = $explode[30];
    $array['dgFrequency'] = $explode[31];

    $array['mainsRunHours'] = $explode[32];
    $array['batteryRunHours'] = $explode[33];
    $array['dgRunHours'] = $explode[34];
    $array['mainsDailyRunHours'] = $explode[35];
  }

  //print_r($array);

  return $array;
}

function settingsValidation($string)
{
  //$settings = '$S1,1,56,280,180,020,280,180,280,180,55,45,45,020,120,120,3,3,002,002,1,060,060,46,003,1,1,180,180,2312,0602,1506,1301,48,46,46,46,46.';

  $stringLength = 135;
  $variableCount = 38;
  $message = '';
  $start = '$';
  $end = '.';

  if (isnull($string)) {
    $message .= 'Empty String.';
  }

  if (!stringStartValidation($start, $string)) {
    $message .= "String Does Not Start From Character: $start. ";
  }

  if (!stringEndValidation($end, $string)) {
    $message .= "String Does Not End With Character: $end. ";
  }

  // if (!stringLengthValidation($stringLength, $string)) {
  //     $message .= "String Length Invalid not '$stringLength'.";
  // }

  //clipping start and end of the string.
  $data = substr($string, 2, strlen($string) - 3);
  //echo $data;

  $explode = explode(',', $data);
  if (count($explode) !== $variableCount) {
    $message .= "No. of varialble is invalid in settings.";
  }

  $required = '';
  foreach ($explode as $key => $setting) {
    if (isnull($setting)) {
      $required .= "{key: $key, setting: required},";
    }
  }

  $required = strlen($required) > 0 ? '[' . rtrim($required, ',') . ']' : $required;
  $message .= $required;

  // inserting the setting string as it is at the end of the settings array.
  $explode[] = $string;

  return array(
    'message' => $message,
    'isValid' => strlen($message) > 0 ? false : true,
    'settingsData' => $explode,
    'noUpdate' => str_replace($data, '', $string),
  );
}
