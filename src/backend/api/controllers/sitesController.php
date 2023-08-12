<?php

// sites crud
// get sites

function getSites($username, $password, $siteRid, $customerId, $siteId, $pageNumber, $pageSize)
{
  # validing admin
  // $credentials = validateAdmin($username, $password);
  // if (!$credentials) {
  //     http_response_code(404);
  //     return responseHandler(2, 'getSites', array());
  // }

  $sites = selectSites($siteRid, $customerId, $siteId, $pageNumber, $pageSize);
  $sitesData = $sites['data'];

  if (!$sitesData) {
    http_response_code(404);
    return responseHandler(1, 'getSites', array());
  }

  $config = array(
    'itemsCount' => $sites['itemsCount'],
  );

  return responseHandler(0, 'getSites', $sitesData, $config);
}

// create sites
function createSite($username, $password, $site)
{
  # validating admin
  $credentials = validateAdmin($username, $password);
  if (!$credentials) {
    http_response_code(404);
    return responseHandler(2, 'createSite', array());
  }

  # validating body of the request
  if (!$site) {
    http_response_code(404);
    return responseHandler(3, 'createSite', array());
  }

  $site = (array) $site;
  $errors = validatingSite($site);

  //print_r($errors);

  if ($errors) {
    http_response_code(404);
    return responseHandler(5, 'createSite', $errors);
  }

  $site['installedAt'] = unixToDate($site['installedAt']);

  if (!insertUpdateSite(0, null, $site, $credentials[0]['credentialId'])) {
    http_response_code(404);
    return responseHandler(4, 'createSite', array());
  }

  return responseHandler(0, 'createSite', $site);
}

// update sites
function updateSite($username, $password, $siteRid, $site)
{
  # validating admin
  $credentials = validateAdmin($username, $password);
  if (!$credentials) {
    http_response_code(404);
    return responseHandler(2, 'updateSite', array());
  }

  # validating id and body of the request
  if (!trim($siteRid) || !$site) {
    http_response_code(404);
    return responseHandler(3, 'updateSite', array());
  }

  # validating data by id
  $sites = selectSites($siteRid, null, null);
  if (!$sites['data']) {
    http_response_code(404);
    return responseHandler(3, 'updateSite', array());
  }

  $site = (array) $site;
  unset($site['siteRid']);
  $errors = validatingSite($site);

  if ($errors) {
    http_response_code(404);
    return responseHandler(5, 'updateSite', $errors);
  }

  $site['installedAt'] = unixToDate($site['installedAt']);

  if (!insertUpdateSite(1, $siteRid, $site, $credentials[0]['credentialId'])) {
    http_response_code(404);
    return responseHandler(4, 'updateSite', array());
  }

  $site['installedAt'] = strtotime($site['installedAt']);

  return responseHandler(0, 'updateSite', $site);
}

// delete sites
function deleteSite($username, $password, $siteRid)
{
  # validating admin
  $credentials = validateAdmin($username, $password);
  if (!$credentials) {
    http_response_code(404);
    return responseHandler(2, 'deleteSite', array());
  }

  # validating id
  if (!trim($siteRid)) {
    http_response_code(404);
    return responseHandler(3, 'deleteSite', array());
  }

  # validating data by id
  $sites = selectSites($siteRid, null, null, 1, 1);

  if (!$sites['data']) {
    http_response_code(404);
    return responseHandler(3, 'deleteSite', array());
  }

  if (!insertUpdateSite(2, $siteRid, null, $credentials[0]['credentialId'])) {
    http_response_code(404);
    return responseHandler(4, 'deleteSite', array());
  }

  return responseHandler(0, 'deleteSite', array());
}

function validatingSite($body)
{
  extract($body);

  $errors = array();
  if (isnull($customerId)) {
    $errors[] = 'customerId';
  }

  if (isnull($siteId)) {
    $errors[] = 'siteId';
  }

  if (isnull($siteName)) {
    $errors[] = 'siteName';
  }

  if (isnull($mobileNumber)) {
    $errors[] = 'mobileNumber';
  }

  if (isnull($address)) {
    $errors[] = 'address';
  }

  if (isnull($batteryAH)) {
    $errors[] = 'batteryAH';
  }

  if (isnull($batteryCapacity)) {
    $errors[] = 'batteryCapacity';
  }

  if (isnull($batteryEnergy)) {
    $errors[] = 'batteryEnergy';
  }

  // if (isnull($batteryAH1)) {
  //     $errors[] = 'batteryAH1';
  // }

  // if (isnull($batteryCapacity1)) {
  //     $errors[] = 'batteryCapacity1';
  // }

  // if (isnull($batteryEnergy1)) {
  //     $errors[] = 'batteryEnergy1';
  // }

  // if (isnull($batteryAH2)) {
  //     $errors[] = 'batteryAH2';
  // }

  // if (isnull($batteryCapacity2)) {
  //     $errors[] = 'batteryCapacity2';
  // }

  // if (isnull($batteryEnergy2)) {
  //     $errors[] = 'batteryEnergy2';
  // }

  if (isnull($l1Name)) {
    $errors[] = 'l1Name';
  }

  if (isnull($l1Number)) {
    $errors[] = 'l1Number';
  }

  // if (isnull($l1Email)) {
  //     $errors[] = 'l1Email';
  // }

  if (isnull($l2Name)) {
    $errors[] = 'l2Name';
  }

  if (isnull($l2Number)) {
    $errors[] = 'l2Number';
  }

  // if (isnull($l2Email)) {
  //     $errors[] = 'l2Email';
  // }

  if (isnull($l3Name)) {
    $errors[] = 'l3Name';
  }

  if (isnull($l3Number)) {
    $errors[] = 'l3Number';
  }

  // if (isnull($l3Email)) {
  //     $errors[] = 'l3Email';
  // }

  // if (isnull($longitude)) {
  //     $errors[] = 'longitude';
  // }

  // if (isnull($latitude)) {
  //     $errors[] = 'latitude';
  // }

  if (isnull($installedAt)) {
    $errors[] = 'installedAt';
  }

  if (isnull($operatorFlags)) {
    $errors[] = 'operatorFlags';
  }

  if (isnull($circleName)) {
    $errors[] = 'circleName';
  }

  return $errors;
}
