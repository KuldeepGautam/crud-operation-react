<?php

require_once $pathCommunications;
require_once $pathIProtect;
require_once $pathCalculations;

function analysis()
{
  $users = selectUsersForMail();
  if (!$users) return 'User not found';

  $result = [];
  $dateDuration = getDateDuration();
  extract($dateDuration);

  foreach ($users as $user) {
    extract($user);

    $status = false;
    $email_to = "";
    $email_cc = "";
    if ($emailTo) $email_to .= "$emailTo,";
    if ($emailCC) $email_cc .= "$emailCC,";

    if ($email_to) {
      $sites = selectSitesForMailByUser($userId, $startDate, $endDate, 1, 200);
      if (!$sites) continue;

      $email_message = includingAlarms($sites);
      $email_to = trim($email_to, ",");
      $default_cc = "dushyant@sysinfra.in,ram.sharma@sysinfra.in,erp.sysinfra@gmail.com,kuldeep.gautam@sisrtd.com";
      if ($email_cc) {
        $email_cc = trim($email_cc, ",");
        $email_cc = "$email_cc,$default_cc";
      } else $email_cc = $default_cc;

      // echo "email_to: $email_to <br />";
      // echo "email_cc: $email_cc <br />";

      //TODO: test mail
      // $email_to = "xenon.9210@gmail.com";
      // $email_cc = "erp.sysinfra@gmail.com";

      $apis = getAPIs();
      $servicesApi = $apis["services"];
      $status = curlService("POST", "$servicesApi/email", [
        "to" => $email_to,
        "cc" => $email_cc,
        "subject" => "I-Protect Daily Analysis Report",
        "body" => $email_message,
      ]);

      // echo $email_message;
    }


    $result[] = ['userName' => $name, 'status' => $status];
  }

  // echo json_encode($data) . "\r\n";

  return $result;
}

function getDateDuration()
{
  # One day analysis report
  $endDate = time();
  $startDate = $endDate - (3600 * 24 * 1); // day
  $endDate--;

  $startDateUnixToDate = unixToDate($startDate);
  $endDateUnixToDate = unixToDate($endDate);

  return [
    'startDate' => $startDateUnixToDate,
    'endDate' => $endDateUnixToDate
  ];
}

function getPercentageStyle($value)
{
  if ($value >= 99.99) return "<td style=\"color: #00b050; font-weight: bold;\">$value%</td>";
  return "<td>$value%</td>";
}

function siteOperator($name, $operators, $color)
{
  if ($operators[$name]) return "<td style=\"background-color: $color; color: $color; font-weight: bold;\">OPCODE</td>";
  else return "<td></td>";
}

function getBatteryHealth($value)
{
  if ($value == 0) return "<td style=\"color: #999; font-weight: bold;\">NA</td>";
  if ($value > 90) return "<td style=\"color: #00b050; font-weight: bold;\">Good</td>";
  if ($value >= 80 && $value <= 90) return "<td style=\"color: #ffc000; font-weight: bold;\">Moderate</td>";
  if ($value < 80) return "<td style=\"color: #ff0000; font-weight: bold;\">Service Required</td>";
}

function includingAlarms($sites)
{
  $duration = getDateDuration();
  extract($duration);

  $header = "<tr style=\"background-color: #44546a; color: #ffffff;\">
                <th>Sr. No.</th>
                <th>Circle</th>
                <th>Date</th>
                <th>Start Time (HHMM)</th>
                <th>End Time (HHMM)</th>
                <th>Site ID</th>
                <th>Site Name</th>
                <th>Airtel</th>
                <th>Jio</th>
                <th>VIL</th>
                <th>MTNL</th>
                <th>BSNL</th>
                <th>Activity</th>
                <th>Motion</th>
                <th>Door</th>
                <th>Smoke</th>
                <th>EB Runhour</th>
                <th>DG Runhour</th>
                <th>BB Run Hour</th>
                <th>Outage in minutes (less than 47V)</th>
                <th>Uptime</th>
                <th>%Site on EB</th>
                <th>%Site on DG</th>
                <th>%Site on Battery</th>
                <th>%Low Voltage (Less Than 47V)</th>
                <th>Battery Health (CH1)</th>
                <th>Battery Health (CH2)</th>
                <th>% of UP-Time</th>
              </tr>";

  $data = '';

  foreach ($sites as $key => $item) {
    extract($item);

    $alarms = selectAlarmsByDate($siteRid, null, $startDate, $endDate);
    $powerSupply = GetSiteStatsFromIdStartEnd($siteId, $startDate, $endDate);

    $alarmsCount = alarmsCount($alarms, $startDate, $endDate);

    $motionCount = $alarmsCount['motionCount'];
    $doorCount = $alarmsCount['doorCount'];
    $smokeCount = $alarmsCount['smokeCount'];
    $btsCount = $alarmsCount['btsCount'];
    $upTime = $alarmsCount['upTime'];
    $outage = $alarmsCount['outage'];

    // calculate activity count
    $alarmsTotal = $motionCount + $doorCount + $smokeCount;

    // calculate run hours
    $mainsRunHours = $powerSupply['mainsRunHours'] / 60;
    $dgRunHours = $powerSupply['dgRunHours']  / 60;
    $batteryRunHours = $powerSupply['batteryRunHours'] / 60;

    $mainsRunHours = round($mainsRunHours, 2);
    $dgRunHours = round($dgRunHours, 2);
    $batteryRunHours = round($batteryRunHours, 2);

    $totalRunHours = $mainsRunHours + $batteryRunHours + $dgRunHours;

    $mainsPercentage = $mainsRunHours / $totalRunHours * 100;
    $dgPercentage = $dgRunHours / $totalRunHours * 100;
    $batteryPercentage = $batteryRunHours / $totalRunHours * 100;

    $mainsPercentage = round($mainsPercentage, 2);
    $dgPercentage = round($dgPercentage, 2);
    $batteryPercentage = round($batteryPercentage, 2);

    if (is_nan($mainsPercentage)) $mainsPercentage = 0;
    if (is_nan($dgPercentage)) $dgPercentage = 0;
    if (is_nan($batteryPercentage)) $batteryPercentage = 0;

    // format date and time
    $startDateUI = unixToDate(strtotime($startDate) + 19800);
    $endDateUI = unixToDate(strtotime($endDate) + 19800);

    $date = date_format(date_create($endDateUI), "d-M-y");
    $startTime = date_format(date_create($startDateUI), "H.i.s");
    $endTime = date_format(date_create($endDateUI), "H.i.s");

    // calculate outage and uptime
    $totalTime = $upTime + $outage;
    $upTimePercentage = $upTime / $totalTime * 100;
    $outagePercentage = $outage / $totalTime * 100;

    $upTimePercentage = round($upTimePercentage, 2);
    $outagePercentage = round($outagePercentage, 2);

    if (is_nan($upTimePercentage)) $upTimePercentage = 0;
    if (is_nan($outagePercentage)) $outagePercentage = 0;

    $upTime = date_format(new DateTime("@$upTime"), 'H.i');
    $outage = date_format(new DateTime("@$outage"), 'H.i');

    // calculate battery health
    $batteryHealthCH = $batteryCapacity / ($batteryAH * 3600) * 100;
    $batteryHealthCH1 = $batteryCapacity1 / ($batteryAH1 * 3600) * 100;

    $batteryHealthCH = round($batteryHealthCH, 2);
    $batteryHealthCH1 = round($batteryHealthCH1, 2);

    if (is_nan($batteryHealthCH)) $batteryHealthCH = 0;
    if (is_nan($batteryHealthCH1)) $batteryHealthCH1 = 0;

    $operators = getSiteOperator($operatorFlags);

    // print_r($alarms);
    $serialNumber = $key + 1;
    $data .= "<tr>";
    $data .= "<td>$serialNumber</td>";
    $data .= "<td>$circleName</td>";
    $data .= "<td nowrap>$date</td>";
    $data .= "<td>$startTime</td>";
    $data .= "<td>$endTime</td>";
    $data .= "<td>$siteId</td>";
    $data .= "<td>$siteName</td>";
    $data .= siteOperator('airtel', $operators, "#ff0e0e");
    $data .= siteOperator('jio', $operators, "#0073c6");
    $data .= siteOperator('vil', $operators, "#ffff4a");
    $data .= siteOperator('mtnl', $operators, "#426331");
    $data .= siteOperator('bsnl', $operators, "#9cc6e7");
    $data .= "<td>$alarmsTotal</td>";
    $data .= "<td>$motionCount</td>";
    $data .= "<td>$doorCount</td>";
    $data .= "<td>$smokeCount</td>";
    $data .= "<td>$mainsRunHours</td>";
    $data .= "<td>$dgRunHours</td>";
    $data .= "<td>$batteryRunHours</td>";
    $data .= "<td>$outage</td>";
    $data .= "<td>$upTime</td>";
    $data .= getPercentageStyle($mainsPercentage);
    $data .= getPercentageStyle($dgPercentage);
    $data .= getPercentageStyle($batteryPercentage);
    $data .= getPercentageStyle($outagePercentage);
    $data .= getBatteryHealth($batteryHealthCH);
    $data .= getBatteryHealth($batteryHealthCH1);
    $data .= getPercentageStyle($upTimePercentage);

    $data .= "</tr>";
  }

  return "<html><table border=\"1\" rules=\"all\" style=\"border-color: #000; font-family: calibri; font-size: small;\" cellpadding=\"5\">$header$data</table></html>";
}
