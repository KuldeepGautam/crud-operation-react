
<?php

require_once $db;
require_once $pathGeneralMethods;

function selectAppSettings($siteRid)
{
    $siteRid = !isnull($siteRid) ? $siteRid : 'NULL'; //number

    $sql =
        "SELECT
            siteRid,
            impedanceMin,
            impedanceMax,
            insertedAt,
            updatedAt
        FROM AppSettingsUpdate
        WHERE siteRid = $siteRid";

    $mysqli = dbContext();
    $result = $mysqli->query($sql);

    $dataTable = array();

    if (!$result) {
        echo ("Error selectAppSettings: " . $mysqli->error);
        return $dataTable;
    }

    while ($row = $result->fetch_row()) {
        $dataRow = array(
            'siteRid' => $row[0],
            'impedanceMin' => $row[1],
            'impedanceMax' => $row[2],
            'insertedAt' => (string) strtotime($row[3]),
            'updatedAt' => (string) strtotime($row[4]),
        );

        array_push($dataTable, $dataRow);
    }

    $mysqli->close();
    return $dataTable;
}

function insertUpdateAppSettings($inType, $siteRid, $setting, $executedBy)
{
    /**
     * $inType = 0: insert
     * $inType = 1: update
     */
    extract($setting);

    $sql = '';
    if ($inType === 0) {
        $sql =
            "INSERT appSettingsUpdate
            SET    siteRid = $siteRid,
                   impedanceMin = $impedanceMin,
                   impedanceMax = $impedanceMax,
                   insertedAt = NOW()";
    } else if ($inType === 1) {
        $sql =
            "UPDATE appSettingsUpdate
            SET    impedanceMin = $impedanceMin,
                   impedanceMax = $impedanceMax,
                   updatedAt = NOW()
            WHERE siteRid = $siteRid";
    }

    //echo $sql;
    $mysqli = dbContext();
    $result = $mysqli->query($sql);

    if (!$result) {
        echo ("Error insertUpdateAppSettings: " . $mysqli->error);
        return false;
    }

    $mysqli->close();
    return true;
}

function selectDeviceSettings($inType, $tableName, $siteId)
{
    // echo $inType;
    // echo $tableName;
    // echo $siteId;

    $siteId = isset($siteId) ? $siteId : 'NULL'; //number

    $mysqli = dbContext();

    /*
    inType: 0 = settings raw data
    inType: 1 = parsed settings
    inType: 2 = ALL DATA
     */

    $whereClause = " WHERE $tableName.siteId = IFNULL($siteId, $tableName.siteId) ";

    switch ($inType) {
        case 0:
            $sql = "SELECT
                        siteId,
                        settings
                    FROM $tableName";
            break;
        case 1:
            $sql = "SELECT
                        siteId,
                        runningMode,
                        battSupllyBypas,
                        roomTempHighAlarm,
                        mainsHighCutoffVolt,
                        mainsLowCutoffVolt,
                        mainsOverLoad,
                        lcuHighCutOff,
                        lcuLowCutOff,
                        dgHighCutOff,
                        dgLowCutOff,
                        dgFrequencyHigh,
                        dgFrequencyLow,
                        dgCrankTempHigh,
                        dgOverLoad,
                        dg1CrankHoldTime,
                        dg2CrankHoldTime,
                        startRetries,
                        stopRetries,
                        dg1CtRatio,
                        dg2CtRatio,
                        llopEnable,
                        dg1StopTime,
                        dg2StopTime,
                        btsBatterylow,
                        battCurrValue,
                        dg1Scheduling,
                        dg2Scheduling,
                        dg1AutoRunTime,
                        dg2AutoRunTime,
                        dg1StartHHMM,
                        dg1EndHHMM,
                        dg2StartHHMM,
                        dg2EndHHMM,
                        lvdVoltMainMin,
                        lvdVolt1Min,
                        lvdVolt2Min,
                        lvdVolt3Min,
                        lvdVolt4Min,
                        insertedAt,
                        updatedAt
                    FROM $tableName";
            break;
        case 2:
            $sql = "SELECT
                        siteId,
                        runningMode,
                        battSupllyBypas,
                        roomTempHighAlarm,
                        mainsHighCutoffVolt,
                        mainsLowCutoffVolt,
                        mainsOverLoad,
                        lcuHighCutOff,
                        lcuLowCutOff,
                        dgHighCutOff,
                        dgLowCutOff,
                        dgFrequencyHigh,
                        dgFrequencyLow,
                        dgCrankTempHigh,
                        dgOverLoad,
                        dg1CrankHoldTime,
                        dg2CrankHoldTime,
                        startRetries,
                        stopRetries,
                        dg1CtRatio,
                        dg2CtRatio,
                        llopEnable,
                        dg1StopTime,
                        dg2StopTime,
                        btsBatterylow,
                        battCurrValue,
                        dg1Scheduling,
                        dg2Scheduling,
                        dg1AutoRunTime,
                        dg2AutoRunTime,
                        dg1StartHHMM,
                        dg1EndHHMM,
                        dg2StartHHMM,
                        dg2EndHHMM,
                        lvdVoltMainMin,
                        lvdVolt1Min,
                        lvdVolt2Min,
                        lvdVolt3Min,
                        lvdVolt4Min,
                        settings,
                        insertedAt,
                        updatedAt
                    FROM $tableName";
            break;
    }

    $sql .= $whereClause;

    //echo $sql;

    $dataTable = array();
    if (!isnull($sql)) {
        $result = $mysqli->query($sql);
        if ($result) {
            while ($row = $result->fetch_row()) {

                switch ($inType) {
                    case 0:
                        $dataRow = array(
                            'siteId' => $row[0],
                            'settings' => $row[1],
                        );
                        break;
                    case 1:
                        $dataRow = array(
                            'siteId' => $row[0],
                            'runningMode' => $row[1],
                            'battSupllyBypas' => $row[2],
                            'roomTempHighAlarm' => $row[3],
                            'mainsHighCutoffVolt' => $row[4],
                            'mainsLowCutoffVolt' => $row[5],
                            'mainsOverLoad' => $row[6],
                            'lcuHighCutOff' => $row[7],
                            'lcuLowCutOff' => $row[8],
                            'dgHighCutOff' => $row[9],
                            'dgLowCutOff' => $row[10],
                            'dgFrequencyHigh' => $row[11],
                            'dgFrequencyLow' => $row[12],
                            'dgCrankTempHigh' => $row[13],
                            'dgOverLoad' => $row[14],
                            'dg1CrankHoldTime' => $row[15],
                            'dg2CrankHoldTime' => $row[16],
                            'startRetries' => $row[17],
                            'stopRetries' => $row[18],
                            'dg1CtRatio' => $row[19],
                            'dg2CtRatio' => $row[20],
                            'llopEnable' => $row[21],
                            'dg1StopTime' => $row[22],
                            'dg2StopTime' => $row[23],
                            'btsBatterylow' => $row[24],
                            'battCurrValue' => $row[25],
                            'dg1Scheduling' => $row[26],
                            'dg2Scheduling' => $row[27],
                            'dg1AutoRunTime' => $row[28],
                            'dg2AutoRunTime' => $row[29],
                            'dg1StartHHMM' => $row[30],
                            'dg1EndHHMM' => $row[31],
                            'dg2StartHHMM' => $row[32],
                            'dg2EndHHMM' => $row[33],
                            'lvdVoltMainMin' => $row[34],
                            'lvdVolt1Min' => $row[35],
                            'lvdVolt2Min' => $row[36],
                            'lvdVolt3Min' => $row[37],
                            'lvdVolt4Min' => $row[38],
                            'insertedAt' => (string) strtotime($row[39]),
                            'updatedAt' => (string) strtotime($row[40]),
                        );
                        break;
                    case 2:
                        $dataRow = array(
                            'siteId' => $row[0],
                            'runningMode' => $row[1],
                            'battSupllyBypas' => $row[2],
                            'roomTempHighAlarm' => $row[3],
                            'mainsHighCutoffVolt' => $row[4],
                            'mainsLowCutoffVolt' => $row[5],
                            'mainsOverLoad' => $row[6],
                            'lcuHighCutOff' => $row[7],
                            'lcuLowCutOff' => $row[8],
                            'dgHighCutOff' => $row[9],
                            'dgLowCutOff' => $row[10],
                            'dgFrequencyHigh' => $row[11],
                            'dgFrequencyLow' => $row[12],
                            'dgCrankTempHigh' => $row[13],
                            'dgOverLoad' => $row[14],
                            'dg1CrankHoldTime' => $row[15],
                            'dg2CrankHoldTime' => $row[16],
                            'startRetries' => $row[17],
                            'stopRetries' => $row[18],
                            'dg1CtRatio' => $row[19],
                            'dg2CtRatio' => $row[20],
                            'llopEnable' => $row[21],
                            'dg1StopTime' => $row[22],
                            'dg2StopTime' => $row[23],
                            'btsBatterylow' => $row[24],
                            'battCurrValue' => $row[25],
                            'dg1Scheduling' => $row[26],
                            'dg2Scheduling' => $row[27],
                            'dg1AutoRunTime' => $row[28],
                            'dg2AutoRunTime' => $row[29],
                            'dg1StartHHMM' => $row[30],
                            'dg1EndHHMM' => $row[31],
                            'dg2StartHHMM' => $row[32],
                            'dg2EndHHMM' => $row[33],
                            'lvdVoltMainMin' => $row[34],
                            'lvdVolt1Min' => $row[35],
                            'lvdVolt2Min' => $row[36],
                            'lvdVolt3Min' => $row[37],
                            'lvdVolt4Min' => $row[38],
                            'settings' => $row[39],
                            'insertedAt' => (string) strtotime($row[40]),
                            'updatedAt' => (string) strtotime($row[41]),
                        );
                        break;
                }

                array_push($dataTable, $dataRow);
            }
        }
    }

    $mysqli->close();
    //print_r($dataTable);
    return $dataTable;
}

function insertUpdateDeviceSettings($inType, $tableName, $siteId, $settings)
{
    /*
    inType: 0 = insert
    inType: 1 = update
     */

    // $siteId = isset($siteId) ? $siteId : 'NULL'; //number

    $mysqli = dbContext();

    $whereClause = "WHERE siteId = $siteId ";

    switch ($inType) {
        case 0:
            $sql = "INSERT INTO $tableName
                    SET siteId = $siteId,
                        runningMode = $settings[0],
                        battSupllyBypas = $settings[1],
                        roomTempHighAlarm = $settings[2],
                        mainsHighCutoffVolt = $settings[3],
                        mainsLowCutoffVolt = $settings[4],
                        mainsOverLoad = $settings[5],
                        lcuHighCutOff = $settings[6],
                        lcuLowCutOff = $settings[7],
                        dgHighCutOff = $settings[8],
                        dgLowCutOff = $settings[9],
                        dgFrequencyHigh = $settings[10],
                        dgFrequencyLow = $settings[11],
                        dgCrankTempHigh = $settings[12],
                        dgOverLoad = $settings[13],
                        dg1CrankHoldTime = $settings[14],
                        dg2CrankHoldTime = $settings[15],
                        startRetries = $settings[16],
                        stopRetries = $settings[17],
                        dg1CtRatio = $settings[18],
                        dg2CtRatio = $settings[19],
                        llopEnable = $settings[20],
                        dg1StopTime = $settings[21],
                        dg2StopTime = $settings[22],
                        btsBatterylow = $settings[23],
                        battCurrValue = $settings[24],
                        dg1Scheduling = $settings[25],
                        dg2Scheduling = $settings[26],
                        dg1AutoRunTime = $settings[27],
                        dg2AutoRunTime = $settings[28],
                        dg1StartHHMM = $settings[29],
                        dg1EndHHMM = $settings[30],
                        dg2StartHHMM = $settings[31],
                        dg2EndHHMM = $settings[32],
                        lvdVoltMainMin = $settings[33],
                        lvdVolt1Min = $settings[34],
                        lvdVolt2Min = $settings[35],
                        lvdVolt3Min = $settings[36],
                        lvdVolt4Min = $settings[37],
                        settings = '$settings[38]'";
            break;
        case 1:
            $sql = "UPDATE $tableName
                    SET runningMode = $settings[0],
                        battSupllyBypas = $settings[1],
                        roomTempHighAlarm = $settings[2],
                        mainsHighCutoffVolt = $settings[3],
                        mainsLowCutoffVolt = $settings[4],
                        mainsOverLoad = $settings[5],
                        lcuHighCutOff = $settings[6],
                        lcuLowCutOff = $settings[7],
                        dgHighCutOff = $settings[8],
                        dgLowCutOff = $settings[9],
                        dgFrequencyHigh = $settings[10],
                        dgFrequencyLow = $settings[11],
                        dgCrankTempHigh = $settings[12],
                        dgOverLoad = $settings[13],
                        dg1CrankHoldTime = $settings[14],
                        dg2CrankHoldTime = $settings[15],
                        startRetries = $settings[16],
                        stopRetries = $settings[17],
                        dg1CtRatio = $settings[18],
                        dg2CtRatio = $settings[19],
                        llopEnable = $settings[20],
                        dg1StopTime = $settings[21],
                        dg2StopTime = $settings[22],
                        btsBatterylow = $settings[23],
                        battCurrValue = $settings[24],
                        dg1Scheduling = $settings[25],
                        dg2Scheduling = $settings[26],
                        dg1AutoRunTime = $settings[27],
                        dg2AutoRunTime = $settings[28],
                        dg1StartHHMM = $settings[29],
                        dg1EndHHMM = $settings[30],
                        dg2StartHHMM = $settings[31],
                        dg2EndHHMM = $settings[32],
                        lvdVoltMainMin = $settings[33],
                        lvdVolt1Min = $settings[34],
                        lvdVolt2Min = $settings[35],
                        lvdVolt3Min = $settings[36],
                        lvdVolt4Min = $settings[37],
                        settings = '$settings[38]',
                        updatedAt = now()
                    $whereClause";
            break;
    }

    //echo $sql;

    $result = $mysqli->query($sql);

    if (!$result) {
        echo $mysqli->error;
    }

    $mysqli->close();

    return $result;
}

function deleteDeviceSettings($tableName, $siteId)
{
    /*
    inType: 0 = delete
     */

    // $siteId = isset($siteId) ? $siteId : 'NULL'; //number
    $mysqli = dbContext();

    $whereClause = "WHERE siteId = $siteId ";
    $sql = "DELETE FROM $tableName $whereClause";
    $result = $mysqli->query($sql);

    if (!$result) {
        echo $mysqli->error;
    }

    //var_dump($result);
    $mysqli->close();
    //print_r($dataTable[0]);
    return $result;
}

function insertDeviceSettingsDump($data, $errorMessage)
{
    $data = !isnull($data) ? "'$data'" : 'NULL'; //string
    $errorMessage = !isnull($errorMessage) ? "'$errorMessage'" : 'NULL'; //string
    // query to insert record
    $sql = "INSERT INTO deviceSettingsDump
            SET data = $data,
                errorMessage = $errorMessage,
                insertedAt = NOW()";

    //echo $sql;
    $mysqli = dbContext();
    $result = $mysqli->query($sql);

    if (!$result) {
        echo ("Error insertDeviceSettingsDump: " . $mysqli->error);
        return false;
    }

    $mysqli->close();
    return true;
}
