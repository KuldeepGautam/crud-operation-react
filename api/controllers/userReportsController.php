<?php

function getAnalyticReportByUser($username, $password, $startDate, $endDate, $searchText, $pageNumber, $pageSize)
{
  $users = validateUser($username, $password);
  if (!$users) {
    return responseHandler(2, "getAnalyticReportByUser", array());
  }

  $sites = selectSitesForReportByUser($users[0]["userId"], $searchText, $pageNumber, $pageSize);
  $sitesData = $sites['data'];
  if (!$sitesData) {
    return responseHandler(1, "getAnalyticReportByUser", array());
  }

  $config = array(
    'itemsCount' => $sites['itemsCount'],
  );

  // $result = [];
  // foreach ($sitesData as $record) {
  //   extract($record);

  //   // calculate battery health
  //   $batteryHealthCH = $batteryCapacity / ($batteryAH * 3600) * 100;
  //   $batteryHealthCH1 = $batteryCapacity1 / ($batteryAH1 * 3600) * 100;

  //   $batteryHealthCH = round($batteryHealthCH, 2);
  //   $batteryHealthCH1 = round($batteryHealthCH1, 2);

  //   if (is_nan($batteryHealthCH)) $batteryHealthCH = 0;
  //   if (is_nan($batteryHealthCH1)) $batteryHealthCH1 = 0;

  //   $item = [
  //     'siteRid' => $record['siteRid'],
  //     'siteId' => $record['siteId'],
  //     'siteName' => $record['siteName'],
  //     'batteryHealthCH' =>  $batteryHealthCH,
  //     'batteryHealthCH1' => $batteryHealthCH1,
  //     'batteryHealthCHPercentage' =>  100,
  //     'batteryHealthCH1Percentage' => 100,

  //   ];

  //   $result[] = $item;
  // }

  return responseHandler(0, 'getAnalyticReportByUser', $sitesData, $config);
}

function getNonCommReportByUser($username, $password, $startDate, $endDate, $searchText, $pageNumber, $pageSize)
{
  $users = validateUser($username, $password);
  if (!$users) {
    return responseHandler(2, "getNonCommReportByUser", array());
  }

  $sites = selectSitesForNonCommReportByUser($users[0]["userId"], $searchText, $pageNumber, $pageSize);
  $sitesData = $sites['data'];
  if (!$sitesData) {
    return responseHandler(1, "getNonCommReportByUser", array());
  }

  $config = array(
    'itemsCount' => $sites['itemsCount'],
  );

  return responseHandler(0, 'getNonCommReportByUser', $sitesData, $config);
}

function getDashboardByUser($username, $password)
{
  $users = validateUser($username, $password);
  if (!$users) {
    return responseHandler(2, "getDashboardByUser", array());
  }

  $sites = selectSitesForDashboardByUser($users[0]["userId"]);
  if (!$sites) {
    return responseHandler(1, "getDashboardByUser", array());
  }

  return responseHandler(0, 'getDashboardByUser', $sites);
}

function getDashboardAlarmAlertsCallByUser($username, $password, $startDate, $endDate, $pageNumber = 1, $pageSize = 10)
{
  $users = validateUser($username, $password);
  if (!$users) {
    return responseHandler(2, "getDashboardAlarmAlertsCallByUser", []);
  }

  $details = selectAlarmAlertsCallByUser($users[0]["userId"], unixToDate($startDate), unixToDate($endDate), $pageNumber, $pageSize);
  if (!$details) return responseHandler(1, "getDashboardAlarmAlertsCallByUser", []);

  $data = $details['data'];
  if (!$data) return responseHandler(1, "getDashboardAlarmAlertsCallByUser", []);

  $config = ['itemsCount' => $details['itemsCount']];
  return responseHandler(0, 'getDashboardAlarmAlertsCallByUser', $data, $config);
}

function getGraphSitesByUser($username, $password, $searchText, $pageNumber, $pageSize)
{
  // Validation
  // Auth
  if (!$username || !$password) return responseHandler(2, "getGraphSitesByUser", array());
  $users = validateUser($username, $password);
  if (!$users) return responseHandler(2, "getCurrentGraph", array());

  $sites = selectSitesByUserForGraph($users[0]["userId"], $searchText, $pageNumber, $pageSize);
  $sitesData = $sites['data'];
  if (!$sitesData) return responseHandler(1, "getGraphSitesByUser", array());

  $config = array(
    'itemsCount' => $sites['itemsCount'],
  );

  return responseHandler(0, 'getGraphSitesByUser', $sitesData, $config);
}

function getGraphByUser($username, $password, $siteId, $startDate, $endDate)
{
  // Validation
  // Auth
  if (!$username || !$password) return responseHandler(2, "getGraph", array());
  $users = validateUser($username, $password);
  if (!$users) return responseHandler(2, "getGraph", array());

  // Params
  if (!$siteId) return responseHandler(3, "getGraph", ["SiteId is required"]);
  if (!$startDate || !$endDate) return responseHandler(3, "getGraph", ["startDate & endDate are required"]);

  if (!validateSiteByUser($users[0]["userId"], $siteId)) return responseHandler(3, "getGraph", ["User does not have access to this site."]);

  $sites = selectGraph($siteId, unixToDate($startDate), unixToDate($endDate));
  if (!$sites) return responseHandler(1, "getGraph", array());

  return responseHandler(0, 'getGraph', $sites);
}

function getOutageSitesByUser($username, $password, $startDate, $endDate, $searchText, $pageNumber, $pageSize)
{
  // Validation
  // Auth
  if (!$username || !$password) return responseHandler(2, "getOutageSitesByUser", []);
  $users = validateUser($username, $password);
  if (!$users) return responseHandler(2, "getOutageSitesByUser", []);
  if (!$startDate || !$endDate) return responseHandler(3, "getOutageSitesByUser", ["startDate & endDate are required"]);

  $siteRids = selectSiteRidsByUser($users[0]["userId"], $searchText);
  if (!$siteRids) return responseHandler(1, "getOutageSitesByUser", []);

  $sites = selectSitesForOutage(implode(",", $siteRids), unixToDate($startDate), unixToDate($endDate), $pageNumber, $pageSize);
  $sitesData = $sites['data'];
  if (!$sitesData) return responseHandler(1, "getOutageSitesByUser", []);

  $outageSites = selectOutage(implode(",", $sitesData), unixToDate($startDate), unixToDate($endDate), $pageNumber, $pageSize);

  $config = array(
    'itemsCount' => $sites['itemsCount'],
  );

  return responseHandler(0, 'getOutageSitesByUser', $outageSites, $config);
}

function getOutageGraphByUser($username, $password, $siteId, $startDate, $endDate, $pageNumber = 1, $pageSize = 10)
{
  // Validation
  // Auth
  if (!$username || !$password) return responseHandler(2, "getOutageGraph", []);
  $users = validateUser($username, $password);
  if (!$users) return responseHandler(2, "getOutageGraph", []);

  // Params
  if (!$siteId) return responseHandler(3, "getOutageGraph", ["SiteId is required"]);
  if (!$startDate || !$endDate) return responseHandler(3, "getOutageGraph", ["startDate & endDate are required"]);

  if (!validateSiteByUser($users[0]["userId"], $siteId)) return responseHandler(3, "getOutageGraph", ["Invalid siteId."]);

  $outage = selectOutageGraph($siteId, unixToDate($startDate), unixToDate($endDate), $pageNumber, $pageSize);
  if (!$outage) return responseHandler(1, "getOutageGraph", []);

  return responseHandler(0, 'getOutageGraph', $outage);
}

function getCallingSitesByUser($username, $password, $searchText, $pageNumber, $pageSize)
{
  // Validation
  // Auth
  if (!$username || !$password) return responseHandler(2, "getCallingSitesByUser", array());
  $users = validateUser($username, $password);
  if (!$users) return responseHandler(2, "getCurrentGraph", array());

  $sites = selectSitesByUserForCalling($users[0]["userId"], $searchText, $pageNumber, $pageSize);
  $sitesData = $sites['data'];
  if (!$sitesData) return responseHandler(1, "getCallingSitesByUser", array());

  $config = array(
    'itemsCount' => $sites['itemsCount'],
  );

  return responseHandler(0, 'getCallingSitesByUser', $sitesData, $config);
}

function getCallingByUser($username, $password, $siteId, $searchText, $startDate, $endDate, $pageNumber, $pageSize)
{
  // Validation
  // Auth
  if (!$username || !$password) return responseHandler(2, "getCallingByUser", array());
  $users = validateUser($username, $password);
  if (!$users) return responseHandler(2, "getCallingByUser", array());

  // Params
  if (!$siteId) return responseHandler(3, "getCallingByUser", ["SiteId is required"]);
  if (!$startDate || !$endDate) return responseHandler(3, "getCallingByUser", ["startDate & endDate are required"]);
  if (!$pageNumber || !$pageSize) return responseHandler(3, "getCallingByUser", ["pageNumber & pageSize are required"]);

  if (!validateSiteByUser($users[0]["userId"], $siteId)) return responseHandler(3, "getCallingByUser", ["User does not have access to this site."]);

  $calls = selectCalling($siteId, $searchText, unixToDate($startDate), unixToDate($endDate), $pageNumber, $pageSize);
  $callsData = $calls['data'];
  if (!$callsData) return responseHandler(1, "getCallingByUser", array());

  $config = array(
    'itemsCount' => $calls['itemsCount'],
  );

  return responseHandler(0, 'getCallingByUser', $callsData, $config);
}
