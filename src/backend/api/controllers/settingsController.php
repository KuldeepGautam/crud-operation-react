<?php

require_once $pathGeneralMethods;
require_once $pathCredentials;
require_once $pathSites;
require_once $pathSettings;

function getSettings($username, $password, $siteRid)
{
    $credentials = validateAdmin($username, $password);
    if (!$credentials) {
        return responseHandler(2, 'getSettings', array());
    }

    $settings = selectAppSettings($siteRid);
    if (!$settings) {
        return responseHandler(1, 'getSettings', array());
    }

    extract($settings[0]);

    $data = array(
        'siteRid' => $siteRid,
        'impedanceMin' => $impedanceMin,
        'impedanceMax' => $impedanceMax,
    );

    return responseHandler(0, 'getSettings', $data);
}

function postSetting($username, $password, $siteRid, $setting)
{
    # validating admin
    $credentials = validateAdmin($username, $password);
    if (!$credentials) {
        return responseHandler(2, 'postSetting', array());
    }

    $credentialId = $credentials[0]['credentialId'];

    # validating id and body of the request
    if (!trim($siteRid) || !$setting) {
        return responseHandler(3, 'postSetting', array());
    }

    $setting = (array) $setting;
    $errors = validatingSettings($setting);

    if ($errors) {
        return responseHandler(5, 'postSetting', $errors);
    }

    $settings = selectAppSettings($siteRid);
    if (!$settings) {
        if (!insertUpdateAppSettings(0, $siteRid, $setting, $credentialId)) {
            return responseHandler(4, 'postSettings', array());
        }
    } else {
        if (!insertUpdateAppSettings(1, $siteRid, $setting, $credentialId)) {
            return responseHandler(4, 'postSettings', array());
        }
    }

    return responseHandler(0, 'postSetting', $setting);
}

function validatingSettings($body)
{
    extract($body);

    $errors = array();
    if (isnull($impedanceMin)) {
        $errors[] = 'impedanceMin';
    }

    if (isnull($impedanceMax)) {
        $errors[] = 'impedanceMax';
    }

    return $errors;
}
