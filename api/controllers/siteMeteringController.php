<?php

require_once $pathSiteUsers;

function getSiteMetering($username, $password, $startDate, $endDate, $searchText, $pageNumber, $pageSize)
{
  // return [$username, $password, $startDate, $endDate, $pageNumber, $pageSize];
  $users = validateUser($username, $password);
  if (!$users) {
    return responseHandler(2, "getSiteMetering", array());
  }

  $sitesUser = selectSitesByUserForSiteMetering($users[0]['userId'], $searchText, $pageNumber, $pageSize);
  $sitesUserData = $sitesUser['data'];
  if (!$sitesUserData) {
    return responseHandler(1, 'getSiteMetering', array());
  }

  // foreach ($sitesUserData as $key => $site) {
  //   $powerSupply = GetSiteStatsFromIdStartEnd($site["siteId"], unixToDate($startDate), unixToDate($endDate));
  //   $sitesUserData[$key]["mainsRunHours"] = $powerSupply["mainsRunHours"];
  //   $sitesUserData[$key]["batteryRunHours"] = $powerSupply["batteryRunHours"];
  //   $sitesUserData[$key]["dgRunHours"] = $powerSupply["dgRunHours"];
  // }

  $config = array(
    'itemsCount' => $sitesUser['itemsCount'],
  );

  return responseHandler(0, 'getSiteMetering', $sitesUserData, $config);
}
