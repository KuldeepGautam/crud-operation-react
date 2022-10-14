
<?php
function isnull($string)
{
  return (trim($string) === "" || !isset($string));
}

function isBinary($string)
{
  return preg_match("/^[01]+$/m", $string) === 1;
}

function stringLengthValidation($length, $string)
{
  return strlen($string) == $length ? true : false;
}

function dataCountValidation($count, $data)
{
  return count($data) == $count ? true : false;
}

function stringStartValidation($start, $string)
{
  return substr($string, 0, 1) == $start ? true : false;
}

function stringEndValidation($end, $string)
{
  return substr($string, strlen($string) - 1, 1) == $end ? true : false;
}

//32-bit binary bits from text 10101010 flag
function packBinaryFlags($flags)
{
  //var_dump($flags);
  $flags_binary = "0000";

  $stats = unpack("C*", $flags_binary);
  $stats[1] = 0;
  $stats[2] = 0;
  $stats[3] = 0;
  $stats[4] = 0;

  //Byte 1
  if (substr($flags, 0, 1) == "1") {
    $stats[1] = $stats[1] | 0x80;
  }

  if (substr($flags, 1, 1) == "1") {
    $stats[1] = $stats[1] | 0x40;
  }

  if (substr($flags, 2, 1) == "1") {
    $stats[1] = $stats[1] | 0x20;
  }

  if (substr($flags, 3, 1) == "1") {
    $stats[1] = $stats[1] | 0x10;
  }

  if (substr($flags, 4, 1) == "1") {
    $stats[1] = $stats[1] | 0x08;
  }

  if (substr($flags, 5, 1) == "1") {
    $stats[1] = $stats[1] | 0x04;
  }

  if (substr($flags, 6, 1) == "1") {
    $stats[1] = $stats[1] | 0x02;
  }

  if (substr($flags, 7, 1) == "1") {
    $stats[1] = $stats[1] | 0x01;
  }

  //Byte 2
  if (substr($flags, 8, 1) == "1") {
    $stats[2] = $stats[2] | 0x80;
  }

  if (substr($flags, 9, 1) == "1") {
    $stats[2] = $stats[2] | 0x40;
  }

  if (substr($flags, 10, 1) == "1") {
    $stats[2] = $stats[2] | 0x20;
  }

  if (substr($flags, 11, 1) == "1") {
    $stats[2] = $stats[2] | 0x10;
  }

  if (substr($flags, 12, 1) == "1") {
    $stats[2] = $stats[2] | 0x08;
  }

  if (substr($flags, 13, 1) == "1") {
    $stats[2] = $stats[2] | 0x04;
  }

  if (substr($flags, 14, 1) == "1") {
    $stats[2] = $stats[2] | 0x02;
  }

  if (substr($flags, 15, 1) == "1") {
    $stats[2] = $stats[2] | 0x01;
  }

  //Byte 3
  if (substr($flags, 16, 1) == "1") {
    $stats[3] = $stats[3] | 0x80;
  }

  if (substr($flags, 17, 1) == "1") {
    $stats[3] = $stats[3] | 0x40;
  }

  if (substr($flags, 18, 1) == "1") {
    $stats[3] = $stats[3] | 0x20;
  }

  if (substr($flags, 19, 1) == "1") {
    $stats[3] = $stats[3] | 0x10;
  }

  if (substr($flags, 20, 1) == "1") {
    $stats[3] = $stats[3] | 0x08;
  }

  if (substr($flags, 21, 1) == "1") {
    $stats[3] = $stats[3] | 0x04;
  }

  if (substr($flags, 22, 1) == "1") {
    $stats[3] = $stats[3] | 0x02;
  }

  if (substr($flags, 23, 1) == "1") {
    $stats[3] = $stats[3] | 0x01;
  }

  //Byte 4
  if (substr($flags, 24, 1) == "1") {
    $stats[4] = $stats[4] | 0x80;
  }

  if (substr($flags, 25, 1) == "1") {
    $stats[4] = $stats[4] | 0x40;
  }

  if (substr($flags, 26, 1) == "1") {
    $stats[4] = $stats[4] | 0x20;
  }

  if (substr($flags, 27, 1) == "1") {
    $stats[4] = $stats[4] | 0x10;
  }

  if (substr($flags, 28, 1) == "1") {
    $stats[4] = $stats[4] | 0x08;
  }

  if (substr($flags, 29, 1) == "1") {
    $stats[4] = $stats[4] | 0x04;
  }

  if (substr($flags, 30, 1) == "1") {
    $stats[4] = $stats[4] | 0x02;
  }

  if (substr($flags, 31, 1) == "1") {
    $stats[4] = $stats[4] | 0x01;
  }

  $output = pack("CCCC", $stats[1], $stats[2], $stats[3], $stats[4]);

  return $output;
}

function unpackBinaryFlag($flags, $flag_constant)
{
  if (is_string($flags)) {
    $tmp = unpack("N", $flags);
    $flags = $tmp[1];
    //echo(dechex($flags));
  }
  //var_dump($flags);
  $tmp = $flags & $flag_constant;
  return ($tmp != 0);
}

/*
# processing flags
'theft2' => 0x00008000,
'theft3reserved' => 0x00004000,
 */

function getIProtectFlags()
{
  return array(
    'device' => 0x80000000,
    'siren' => 0x40000000,
    'pir' => 0x20000000,
    'door' => 0x10000000,

    'smoke' => 0x08000000,
    'bbActive' => 0x04000000,
    'bb1Active' => 0x02000000,
    'bb2Active' => 0x01000000,

    'bb3Active' => 0x00800000,
    'currentDirection' => 0x00400000,
    'currentDirection1' => 0x00200000,
    'currentDirection2' => 0x00100000,

    'currentDirection3reserved' => 0x00080000,
    'currentDirection4reserved' => 0x00040000,
    'theft' => 0x00020000,
    'theft1' => 0x00010000,

    'theft2' => 0x00008000,
    'theft3reserved' => 0x00004000,
    'bts' => 0x00002000,
    'battery' => 0x00001000,
  );
}

function timeIST()
{
  return time() + 19800;
}

function strToTimeString($dateTime)
{
  return (string) localTimezone(strtotime($dateTime));
}

function strToTimeInt($dateTime)
{
  return localTimezone(strtotime($dateTime));
}

// timezone = Asia/Kolkata + 05:30 = GMT+11:00
function localTimezone($time)
{
  // GETDATE() + 11:00
  return $time + 19800;

  // GETDATE() + 05:30
  //return $time + 19800;

  // GETDATE() + unknown.
  //return $time + 140414;

  // NOW + 07:00
  //return $time + 25200;

  // GETDATE()
  //return $time;
}

function unixToDate($unix)
{
  $dateTime = new DateTime("@$unix");
  $timezone = new DateTimeZone(date_default_timezone_get());
  date_timezone_set($dateTime, $timezone);
  return date_format($dateTime, 'Y-m-d H:i:s');
}

function unixToTime($unix)
{
  $dateTime = new DateTime("@$unix");
  return date_format($dateTime, 'H:i:s');
}

function dateDiff($unix1, $unix2)
{
  $date1 = new DateTime("@$unix1");
  $date2 = new DateTime("@$unix2");

  $dateInterval = date_diff($date1, $date2);
  return date_interval_format($dateInterval, '%a');
}

function dateToUnix($date)
{
  $dateTime = new DateTime($date);
  return date_format($dateTime, 'U');
}

function currentDateParams()
{
  $time = time() + 19800;
  $dateTime = new DateTime("@$time");

  // print_r($dateTime);

  $year = date_format($dateTime, 'Y');
  $month = date_format($dateTime, 'm');
  $day = date_format($dateTime, 'd');
  $hour = date_format($dateTime, 'H');
  $minute = date_format($dateTime, 'i');
  $second = date_format($dateTime, 's');

  return array(
    'hour' => $hour,
    'minute' => $minute,
    'second' => $second,
    'month' => $month,
    'day' => $day,
    'year' => $year - 2000,
  );
}

function handleNull($object)
{
  foreach ($object as $key => $property) {
    // echo "type: " . gettype($object[$key]) . " '$key': $object[$key]\r\n";

    if (gettype($object[$key]) === "NULL") {
      $object[$key] = "NULL";
    } else if (is_numeric($object[$key])) {
      $object[$key] = !isnull($object[$key]) ? $object[$key] : "NULL";
    } else if (is_string($object[$key])) {
      $object[$key] = !isnull($object[$key]) ? "'$object[$key]'" : "NULL";
    } else if (is_bool($object[$key])) {
      $object[$key] = $object[$key] ? 1 : 0;
    }
  }

  return $object;
}

function getEmailFrequency()
{
  return ["daily", "weekly", "fortnightly", "monthly"];
}

function isEmailFrequency($emailFrequency)
{
  $emailFrequencies = getEmailFrequency();
  return !in_array($emailFrequency, $emailFrequencies);
}

function getSiteOperator($operator)
{
  return [
    'airtel' => $operator[0] ? true : false,
    'jio' => $operator[1] ? true : false,
    'vil' => $operator[2] ? true : false,
    'mtnl' => $operator[3] ? true : false,
    'bsnl' => $operator[4] ? true : false,
  ];
}

?>