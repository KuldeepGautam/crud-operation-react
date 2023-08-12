<?php

require_once $pathSiteUsers;
require_once $pathMiddleware;

// users crud
// get site-users
// sites by user using user credentials
function getSitesByUser($username, $password, $searchText, $pageNumber, $pageSize)
{
    $users = validateUser($username, $password);
    if (!$users) {
        return responseHandler(2, "getSitesByUser", array());
    }

    $sitesUser = selectSitesByUserForHomePage($users[0]['userId'], $searchText, $pageNumber, $pageSize);
    $sitesUserData = $sitesUser['data'];

    // print_r($sitesUserData);
    if (!$sitesUserData) {
        return responseHandler(1, 'getSitesByUser', array());
    }

    foreach ($sitesUserData as $key => $site) {

        $sitesUserData[$key]['agingInDays'] = $site['installedAt'] ? (string) dateDiff($site['installedAt'], time()) : 0;
        $sitesUserData[$key]['collectedAt'] += 19800;

        //echo $site['siteId'] . "\r\n";
        // $iProtect = selectIProtectFlags(null, $site['siteId'], 1, 1);
        // $iProtectData = $iProtect['data'];
        // if (!$iProtectData) {
        //     continue;
        // }

        // $sitesUserData[$key]['iProtectFlags'] = versionFlags($iProtectData[0]['iProtectFlags']);
        // $sitesUserData[$key]['binary'] = base64_encode($iProtectData[0]['iProtectFlags']);

        $sitesUserData[$key]['iProtectFlags'] = versionFlags($site['iProtectFlags']);
        $sitesUserData[$key]['binary'] = base64_encode($site['iProtectFlags']);
    }

    $config = array(
        'itemsCount' => $sitesUser['itemsCount'],
    );

    //print_r($sitesUserData);
    return responseHandler(0, 'sitesByUser', $sitesUserData, $config);
}

// sites by user using admin credentials
function getSitesByUserId($username, $password, $userId)
{
    # validing admin
    // $credentials = validateAdmin($username, $password);
    // if (!$credentials) {
    //     return responseHandler(2, 'getSitesByUserId', array());
    // }

    $sitesUser = selectSitesByUser($userId);

    if (!$sitesUser) {
        return responseHandler(1, 'getSitesByUserId', array());
    }

    return responseHandler(0, 'siteUsers', $sitesUser);
}

function getUsersBySite($username, $password, $siteRid)
{
    # validing admin
    // $credentials = validateAdmin($username, $password);
    // if (!$credentials) {
    //     return responseHandler(2, 'getUsersBySite', array());
    // }

    $siteUser = selectUsersBySite($siteRid);
    if (!$siteUser) {
        return responseHandler(1, 'getUsersBySite', array());
    }

    return responseHandler(0, 'usersBySite', $siteUser);
}

// create user
function createSiteUser($username, $password, $userId, $sites)
{
    $body = $sites;
    $action = 'createSiteUser';

    # validing admin
    $credentials = validateAdmin($username, $password);
    if (!$credentials) {
        return responseHandler(authError, $action, array());
    }

    $credentialId = $credentials[0]['credentialId'];

    # validating userId
    $users = selectUsers($userId, null);
    if (!$users) {
        return responseHandler(parameterError, $action, array());
    }

    # validating body of the request
    if (!$sites) {
        return responseHandler(parameterError, $action, array());
    }

    $sites = (array) $sites;
    $errors = validatingSiteUser($sites);
    if ($errors) {
        return responseHandler(requiredError, $action, $errors);
    }

    # validating sites in database
    $sites = $sites['sites'];
    $invalidSites = array();
    $validSites['sites'] = array();
    foreach ($sites as $siteRid) {
        $validateSites = selectSites($siteRid, null, null);
        $validateSitesData = $validateSites['data'];

        //print_r($validateSitesData);
        if (!$validateSitesData) {
            $invalidSites[] = $siteRid;
            continue;
        }

        $site = array(
            'siteRid' => $siteRid,
            'siteId' => $validateSitesData[0]['siteId'],
        );

        array_push($validSites['sites'], $site);
    }

    if ($invalidSites) {
        $response['invalidSites'] = $invalidSites;
        return responseHandler(parameterError, $action, $response);
    }

    # getting original sites by user
    $sitesUser = selectSitesByUser($userId);
    $originalSites = array();
    if ($sitesUser) {
        foreach ($sitesUser as $site) {
            $originalSites[] = $site['siteRid'];
        }
    }

    # clearing old sites by user
    if (!insertUpdateSiteUser(2, $userId, null, $credentialId)) {
        return responseHandler(dbError, $action, array());
    }

    # setting the new relations
    $status = true;
    foreach ($sites as $siteRid) {
        if (!insertUpdateSiteUser(0, $userId, $siteRid, $credentialId)) {
            $status = false;
            break;
        }
    }

    # any issue in setting the relations then revert all the changes.
    if (!$status) {
        if (!insertUpdateSiteUser(2, $userId, null, $credentialId)) {
            return responseHandler(dbError, $action, array());
        }

        foreach ($originalSites as $siteRid) {
            insertUpdateSiteUser(0, $userId, $siteRid, $credentialId);
        }

        return responseHandler(dbError, $action, array());
    }

    return responseHandler(success, $action, $validSites);
}

function validatingSiteUser($body)
{
    extract($body);

    $errors = array();
    if (!$sites) {
        $errors[] = 'sites';
    }

    return $errors;
}
