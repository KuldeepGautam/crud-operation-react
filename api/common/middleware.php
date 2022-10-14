<?php

require_once $pathProtocols;

// post iprotect
function objectCurrent($currentOld, $currentNew, $currentDirection)
{
  return array(
    'currentOld' => $currentOld,
    'currentNew' => $currentNew,
    'currentDirection' => $currentDirection,
  );
}

function objectBatteryBank($batteryAH, $batteryCapacity, $batteryEnergy)
{
  return array(
    'batteryAH' => $batteryAH,
    'batteryCapacity' => $batteryCapacity,
    'batteryEnergy' => $batteryEnergy,
  );
}

function batteryBankCalculation($current, $voltage, $batteryBank, $timestamp)
{
  // print_r($current);
  // print_r($voltage);
  // print_r($batteryBank);
  // print_r($timestamp);
  // echo "\r\n";

  $currentNew = $current['currentNew'];
  $currentOld = $current['currentOld'];
  $currentDirection = $current['currentDirection'];

  $voltageNew = $voltage['voltageNew'];
  $voltageOld = $voltage['voltageOld'];

  $batteryCapacity = $batteryBank['batteryCapacity'];
  $batteryEnergy = $batteryBank['batteryEnergy'];

  $batteryEnergyMax = $batteryBank['batteryAH'] * 3600;
  //$batteryEnergyMin = 0;
  $batteryEnergyMin = $batteryEnergyMax / 2;

  /*
    Max = batteryAH
    Min = Max / 2
    time gap >
     */

  //if ($voltageNew >= 54.5 && $currentNew < 5) {
  if ($currentNew <= 3 && $currentOld < 3) {
    // the voltage or main power is greater than 54.5 v => battery full charged
    // battery capacity should be equal to battery energy.
    $batteryCapacity = setRange($batteryEnergy, $batteryEnergyMin, $batteryEnergyMax);
  } else {
    if ($currentOld > 0) {
      $timeDisplacement = timeDisplacement($timestamp, $currentOld);

      //echo "timeDisplacement: $timeDisplacement\r\n";
      //echo "batteryEnergy: $batteryEnergy\r\n";
      $charging = $batteryEnergy + $timeDisplacement;
      $discharging = $batteryEnergy - $timeDisplacement;

      $batteryEnergy = $currentDirection ? $discharging : $charging;
      $batteryEnergy = setRange($batteryEnergy, $batteryEnergyMin, $batteryEnergyMax);
    }
  }

  // calculating KWH
  $AHBB = $voltageNew * $currentNew;
  $timeHr = timeDisplacement($timestamp, 1) / 60 / 60;
  $batteryKwh = $AHBB * $timeHr / 1000;
  $batteryKwh = $currentDirection ? -$batteryKwh : $batteryKwh;

  return array(
    'batteryEnergy' => $batteryEnergy,
    'batteryCapacity' => $batteryCapacity,
    'batteryKwh' => $batteryKwh,
  );
}

function timeDisplacement($time, $parameter)
{
  $seconds = 600;
  $timeSpan = abs(time() - $time);
  return ($timeSpan > $seconds ? 0 : $timeSpan) * $parameter;
}

function setRange($value, $value1, $value2)
{
  if ($value1 === $value2) {
    return $value1;
  }

  $min = $value1 < $value2 ? $value1 : $value2;
  $max = $value1 > $value2 ? $value1 : $value2;

  $value = $value < $min ? $min : $value;
  $value = $value > $max ? $max : $value;

  return intval($value);
}

function isAlarm($dataOld, $dataNew)
{
  // print_r($dataOld);
  // print_r($dataNew);
  if (!$dataOld) {
    return true;
  }

  // $protocolOld = protocolsVerification($dataOld);
  // $protocolNew = protocolsVerification($dataNew);

  // $protocolDataOld = $protocolOld['data'];
  // $protocolDataNew = $protocolNew['data'];

  // calculate breach....
  $breachOld = breachState($dataOld);
  $breachNew = breachState($dataNew);

  // print_r($breachOld);
  // print_r($breachNew);

  $arrayDiff = array_diff_assoc($breachOld, $breachNew);

  // print_r($arrayDiff);

  return ['isAlarm' => count($arrayDiff) > 0, 'isBattery' => $breachOld['battery'] !== $breachNew['battery']];
}

function breachState($data)
{
  // print_r($data);
  $flags = versionFlags($data['iProtectFlags']);

  // print_r($data);

  // echo "breachState flags: $flags\r\n";

  // $mainPowerMin = 49;
  // $flags = $data['flags'];
  // $mainPower = ($data['mainPower'] / 10) < $mainPowerMin;

  return array(
    'motion' => $flags[2],
    'door' => $flags[3],
    'smoke' => $flags[4],
    'theft' => $flags[6],
    'wire' => $flags[7],
    // 'isOrionConnected' => $flags[8],
    'bts' => $data['mainPower'] < 49,
    'battery' => intval($data['secondaryPower2']) < 90 && intval($data['secondaryPower3reserved']) < 90
  );
}

// get iprotect
function versionFlags($iProtectFlags)
{
  $getiProtectflags = getIProtectFlags();

  // print_r($getiProtectflags);

  $device = unpackBinaryFlag($iProtectFlags, $getiProtectflags['device']) ? '1' : '0';
  $siren = '0';
  $pir = unpackBinaryFlag($iProtectFlags, $getiProtectflags['pir']) ? '1' : '0';
  $door = unpackBinaryFlag($iProtectFlags, $getiProtectflags['door']) ? '1' : '0';
  $smoke = unpackBinaryFlag($iProtectFlags, $getiProtectflags['smoke']) ? '1' : '0';
  $bbActive = unpackBinaryFlag($iProtectFlags, $getiProtectflags['bbActive']) ? '1' : '0';
  $bb1Active = unpackBinaryFlag($iProtectFlags, $getiProtectflags['bb1Active']) ? '1' : '0';
  $bb2Active = unpackBinaryFlag($iProtectFlags, $getiProtectflags['bb2Active']) ? '1' : '0';
  $bb3Active = unpackBinaryFlag($iProtectFlags, $getiProtectflags['bb3Active']) ? '1' : '0';
  $currentDirection = unpackBinaryFlag($iProtectFlags, $getiProtectflags['currentDirection']) ? '1' : '0';
  $currentDirection1 = unpackBinaryFlag($iProtectFlags, $getiProtectflags['currentDirection1']) ? '1' : '0';
  $currentDirection2 = unpackBinaryFlag($iProtectFlags, $getiProtectflags['currentDirection2']) ? '1' : '0';
  $currentDirection3reserved = unpackBinaryFlag($iProtectFlags, $getiProtectflags['currentDirection3reserved']) ? '1' : '0';
  $currentDirection4reserved = unpackBinaryFlag($iProtectFlags, $getiProtectflags['currentDirection4reserved']) ? '1' : '0';
  $theft = unpackBinaryFlag($iProtectFlags, $getiProtectflags['theft']) ? '1' : '0';
  $theft1 = unpackBinaryFlag($iProtectFlags, $getiProtectflags['theft1']) ? '1' : '0';
  $theft2 = unpackBinaryFlag($iProtectFlags, $getiProtectflags['theft2']) ? '1' : '0';
  $theft3reserved = unpackBinaryFlag($iProtectFlags, $getiProtectflags['theft3reserved']) ? '1' : '0';
  $bts = unpackBinaryFlag($iProtectFlags, $getiProtectflags['bts']) ? '1' : '0';
  $battery = unpackBinaryFlag($iProtectFlags, $getiProtectflags['battery']) ? '1' : '0';

  $flags =
    $device // 0
    . $siren // 1
    . $pir // 2
    . $door // 3
    . $smoke // 4
    . $currentDirection // 5
    . $theft // 6
    . $theft1 // 7
    . $theft2 // 8
    . $theft3reserved // 9
    . $currentDirection1 // 10
    . $currentDirection2 // 11
    . $currentDirection3reserved // 12
    . $currentDirection4reserved // 13
    . $bbActive // 14
    . $bb1Active // 15
    . $bb2Active // 16
    . $bb3Active // 17
    . $bts // 18
    . $battery; // 19

  return $flags;
}

// common
function calculateTheft($parameters)
{
  // print_r($parameters);
  $status = array(
    'theft' => 0,
    'theft1' => 0,
    'bts' => 0,
    'battery' => 0,
  );

  extract($parameters);

  $theftAlert = floatval($mainPower) <= 5.00;
  $theft1Alert = false;
  $mainPowerAlert = floatval($mainPower) <= 47.00;
  // echo "secondaryPower2: $secondaryPower2: " . floatval($secondaryPower2) . "\r\n";
  $secondaryPower2Alert = floatval($secondaryPower2) < 90;

  if (intval($protocolId) === 6) {
    $theft1Alert = abs($secondaryPower2 - $secondaryPower2Old) >= 20;
    $theft1Alert = $theft1Alert || abs($secondaryPower3reserved - $secondaryPower3reservedOld) >= 20;
  }

  if (intval($protocolId) === 8) {
    $theft1Alert = abs($secondaryPower1 - $secondaryPower1Old) >= 20;
  }

  $status['theft'] = $theftAlert ? 1 : 0;
  $status['theft1'] = $theft1Alert ? 1 : 0;
  $status['bts'] = $mainPowerAlert ? 1 : 0;
  $status['battery'] = $secondaryPower2Alert ? 1 : 0;

  return $status;
}

function siteAppSettings($siteRid)
{
  $default = array(
    'impedanceMin' => 1850,
    'impedanceMax' => 1980,
  );

  $settings = selectAppSettings($siteRid);

  if (!$settings) {
    return $default;
  }

  extract($settings[0]);

  return array(
    'impedanceMin' => $impedanceMin,
    'impedanceMax' => $impedanceMax,
  );
}

function alarmsCount($alarms, $startDate, $endDate)
{
  $motion = 0;
  $door = 0;
  $smoke = 0;
  $bts = false;

  $minVoltage = 47.00;

  $motionCount = 0;
  $doorCount = 0;
  $smokeCount = 0;
  $btsCount = 0;

  $startTime = strtotime($startDate);
  $endTime = strtotime($endDate);
  $upTime = 0;
  $outage = 0;

  foreach ($alarms as $key => $alarm) {
    $timestamp = $alarm['serverTimestamp'];
    $flags = $alarms[$key]['flags'] = versionFlags($alarm['iProtectFlags']);

    $motionCount += $motion === intval($flags[2]) ? 0 : 1;
    $motion = intval($flags[2]);

    $doorCount += $door === intval($flags[3]) ? 0 : 1;
    $door = intval($flags[3]);

    $smokeCount += $smoke === intval($flags[4]) ? 0 : 1;
    $smoke = intval($flags[4]);

    $mainPower = floatval($alarms[$key]['mainPower']);
    $btsCount += $bts === $mainPower < $minVoltage ? 0 : 1;
    $bts = $mainPower < $minVoltage;

    if ($bts) $outage += $timestamp - $startTime;
    else $upTime += $timestamp - $startTime;

    $startTime = $timestamp;
  }

  if ($bts) $outage += $endTime - $startTime;
  else $upTime += $endTime - $startTime;

  return [
    'motionCount' => $motionCount,
    'doorCount' => $doorCount,
    'smokeCount' => $smokeCount,
    'btsCount' => $btsCount,
    'upTime' => $upTime,
    'outage' => $outage
  ];
}
