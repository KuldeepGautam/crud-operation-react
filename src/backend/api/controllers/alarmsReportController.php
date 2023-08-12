<?php

function getAlarmActivityByUser($username, $password, $startDate, $endDate, $searchText, $pageNumber, $pageSize)
{
  $users = validateUser($username, $password);
  if (!$users) {
    return responseHandler(2, "getAlarmActivityByUser", array());
  }

  $sites = selectSitesForReportByUser($users[0]["userId"], $searchText, $pageNumber, $pageSize);
  $sitesData = $sites['data'];
  if (!$sitesData) {
    return responseHandler(1, "getAlarmActivityByUser", array());
  }

  $config = array(
    'itemsCount' => $sites['itemsCount'],
  );

  $siteRids = "";
  foreach ($sitesData as $record) {
    $siteRid = $record['siteRid'];
    $siteRids .= "$siteRid,";
  }

  $siteRids = trim($siteRids, ",");
  $alarms = selectAlarmsByUser($siteRids, unixToDate($startDate), unixToDate($endDate));
  // print_r($alarms);

  $result = [];
  foreach ($sitesData as $record) {

    $siteAlarms = [];
    foreach ($alarms as $alarm) {
      if ($alarm['siteRid'] === $record['siteRid']) $siteAlarms[] = $alarm;
    }

    // if (!$siteAlarms) continue;

    $alarmsCount = alarmsCount($siteAlarms, $startDate, $endDate);

    $doorCount = $alarmsCount['doorCount'];
    $smokeCount = $alarmsCount['smokeCount'];
    $motionCount = $alarmsCount['motionCount'];
    $btsCount = $alarmsCount['btsCount'];
    // $upTime = $alarmsCount['upTime'];
    // $outage = $alarmsCount['outage'];

    $alarmsTotal = $doorCount + $smokeCount + $motionCount + $btsCount;

    $result[] = [
      'siteRid' => $record['siteRid'],
      'siteId' => $record['siteId'],
      'siteName' => $record['siteName'],
      'alarmsCount' =>  $alarmsTotal,
      'doorCount' => $doorCount,
      'smokeCount' => $smokeCount,
      'motionCount' => $motionCount,
      'btsCount' => $btsCount
    ];
  }

  return responseHandler(0, 'getAlarmActivityByUser', $result, $config);
}
