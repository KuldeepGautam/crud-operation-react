
<?php

function selectIProtect($iProtectId, $siteId, $pageNumber = 1, $pageSize = 5)
{
  // echo "pageSize: $pageSize\r\n";

  $iProtectId = !isnull($iProtectId) ? $iProtectId : 'NULL'; //number
  $siteId = !isnull($siteId) ? "'$siteId'" : 'NULL'; //string

  $pageNumber = !isnull($pageNumber) ? $pageNumber : 1; //number
  $pageSize = !isnull($pageSize) ? $pageSize : 5; //number
  $offset = $pageSize * ($pageNumber - 1);

  $joinAndIProtectId = $iProtectId === 'NULL' ? "iProtect.iProtectId" : $iProtectId;
  $joinAndSiteId = $siteId === 'NULL' ? "iProtect.siteId" : $siteId;

  $sql = "SELECT
                iProtect.iProtectId,
                deviceId,
                protocolId,
                iProtect.siteId,
                iProtect.datetime,
                iProtectFlags,
                auxiliaryPower,
                secondaryPower,
                secondaryPower1,
                secondaryPower2,
                secondaryPower3reserved,
                mainPower,
                temperature,
                impedance,
                orion,
                lvd,
                lvd1,
                lvd2,
                lvd3,
                lvd4,
                lvd5,
                lvd6,
                lvd7,
                mainsFrequency,
                dgFrequency,
                mainsRunHours,
                batteryRunHours,
                dgRunHours,
                mainsDailyRunHours,
                batteryEnergy,
                batteryEnergy1,
                iProtect.insertedAt,
                (SELECT versionCode FROM version ORDER BY versionCode DESC limit 1),
                quantum

            FROM iProtect
            LEFT OUTER JOIN sitesEnergyDetails ON sitesEnergyDetails.iProtectId = iProtect.iProtectId
            WHERE iProtect.isDeleted = 0
                AND iProtect.iProtectId = $joinAndIProtectId
                AND iProtect.siteId = $joinAndSiteId
            ORDER BY iProtect.iProtectId DESC
            LIMIT $offset, $pageSize ";

  $sqlCount = "SELECT
                    COUNT(iProtectId)
                FROM iProtect
                WHERE iProtect.isDeleted = 0
                AND iProtect.iProtectId = $joinAndIProtectId
                AND iProtect.siteId = $joinAndSiteId";

  // echo "sql: $sql\r\n";
  // echo "sql: $sqlCount\r\n";
  $mysqli = dbContext();
  $result = $mysqli->query($sql);
  // $resultCount = $mysqli->query($sqlCount);

  $dataTable = array();

  if (!$result) {
    // echo ("Error selectIProtect: " . $mysqli->error);
    return $dataTable;
  }

  // if (!$resultCount) {
  //     // echo ("Error selectIProtect: " . $mysqli->error);
  //     return $dataTable;
  // }

  // $count = $resultCount->fetch_row();

  while ($row = $result->fetch_row()) {
    $dataRow = array(
      'iProtectId' => $row[0],
      'deviceId' => $row[1],
      'protocolId' => $row[2],
      'siteId' => $row[3],
      'deviceTimestamp' => $row[4] ? (string) strtotime($row[4]) : null,
      #'datetime' => strtotime($row[4]),
      'iProtectFlags' => $row[5],
      'auxiliaryPower' => $row[6],
      'secondaryPower' => $row[7],
      'secondaryPower1' => $row[8],
      'secondaryPower2' => $row[9],
      'secondaryPower3reserved' => $row[10],
      'mainPower' => $row[11],
      'temperature' => $row[12],
      'impedance' => $row[13],
      'orion' => $row[14],
      'lvd' => $row[15],
      'lvd1' => $row[16],
      'lvd2' => $row[17],
      'lvd3' => $row[18],
      'lvd4' => $row[19],
      'lvd5' => $row[20],
      'lvd6' => $row[21],
      'lvd7' => $row[22],
      'mainsFrequency' => $row[23],
      'dgFrequency' => $row[24],
      'mainsRunHours' => $row[25],
      'batteryRunHours' => $row[26],
      'dgRunHours' => $row[27],
      'mainsDailyRunHours' => $row[28],
      'batteryEnergyLog' => $row[29],
      'batteryEnergy1Log' => $row[30],
      'timestamp' => (string) strtotime($row[31]),
      'serverTimestamp' => (string) strtotime($row[31]),
      'versionCode' => $row[32],
      'quantum' => $row[33],
    );

    array_push($dataTable, $dataRow);
  }

  $mysqli->close();
  return array(
    'data' => $dataTable,
    // 'itemsCount' => intval($count[0]),
    'itemsCount' => 5000,
  );
}

function selectIProtectByUser(
  $iProtectId,
  $siteId,
  $userId,
  $searchText,
  $startDate = '1970-01-01',
  $endDate = null,
  $pageNumber = 1,
  $pageSize = 5
) {
  // echo "pageSize: $pageSize\r\n";
  $time = time();
  $time = unixToDate($time);

  $startDate = !isnull($startDate) ? "'$startDate'" : "'1970-01-01'"; //string
  $endDate = !isnull($endDate) ? "'$endDate'" : "'$time'"; //string
  $searchText = !isnull($searchText) ? "'%$searchText%'" : "'%%'"; //string
  $userId = !isnull($userId) ? $userId : 'NULL'; //number


  $iProtectId = !isnull($iProtectId) ? $iProtectId : 'NULL'; //number
  $siteId = !isnull($siteId) ? "'$siteId'" : 'NULL'; //string

  $pageNumber = !isnull($pageNumber) ? $pageNumber : 1; //number
  $pageSize = !isnull($pageSize) ? $pageSize : 5; //number
  $offset = $pageSize * ($pageNumber - 1);

  $joinAndIProtectId = $iProtectId === 'NULL' ? "iProtect.iProtectId" : $iProtectId;
  $joinAndSiteId = $siteId === 'NULL' ? "iProtect.siteId" : $siteId;

  $sitesSql = "SELECT siteId 
              FROM siteUsers
              JOIN sites ON sites.siteRid = siteUsers.siteRid
              WHERE userId = $userId AND (sites.siteId LIKE $searchText OR sites.siteName LIKE $searchText)";

  $mysqli = dbContext();
  $sitesResult = $mysqli->query($sitesSql);

  $sites = "";
  while ($row = $sitesResult->fetch_row()) {
    $sites .= "'$row[0]',";
  }

  $sites = trim($sites, ",");
  // echo "sites: $sites\r\n\r\n";
  $sql = "SELECT
                iProtect.iProtectId,
                protocolId,
                iProtect.datetime,
                iProtect.insertedAt,
                iProtect.iProtectFlags,
                auxiliaryPower,
                secondaryPower,
                secondaryPower1,
                secondaryPower2,
                secondaryPower3reserved,
                mainPower,
                temperature,
                mainsRunHours,
                dgRunHours,
                batteryRunHours,
                orion,
                quantum,
                siteId,
                siteName

            FROM iProtect
            LEFT OUTER JOIN sitesEnergyDetails ON sitesEnergyDetails.iProtectId = iProtect.iProtectId
            WHERE iProtect.isDeleted = 0
                AND siteId IN ($sites)
                AND iProtect.iProtectId = $joinAndIProtectId
                AND IFNULL(iProtect.insertedAt, '1970-01-01') BETWEEN $startDate AND $endDate
            ORDER BY iProtect.iProtectId DESC
            LIMIT $offset, $pageSize ";

  $sqlCount = "SELECT
                    COUNT(iProtectId)
                    FROM siteUsers
                JOIN sites ON sites.siteRid = siteUsers.siteRid
                JOIN iProtect ON iProtect.siteId = sites.siteId
                WHERE iProtect.isDeleted = 0
                AND userId = $userId
                AND iProtect.iProtectId = $joinAndIProtectId
                AND iProtect.siteId = $joinAndSiteId
                AND IFNULL(iProtect.insertedAt, '1970-01-01') BETWEEN $startDate AND $endDate
                AND (sites.siteId LIKE $searchText OR sites.siteName LIKE $searchText)";

  // echo "sql: $sql\r\n\r\n";
  // echo "sql: $sqlCount\r\n";
  $result = $mysqli->query($sql);
  // $resultCount = $mysqli->query($sqlCount);

  $dataTable = array();

  if (!$result) {
    // echo ("Error selectIProtect: " . $mysqli->error);
    return $dataTable;
  }

  // if (!$resultCount) {
  //     // echo ("Error selectIProtect: " . $mysqli->error);
  //     return $dataTable;
  // }

  // $count = $resultCount->fetch_row();

  while ($row = $result->fetch_row()) {
    $dataRow = array(
      'iProtectId' => $row[0],
      'protocolId' => $row[1],
      'deviceTimestamp' => $row[2] ? (string) strtotime($row[2]) : null,
      'serverTimestamp' => (string) strtotime($row[3]),
      'iProtectFlags' => $row[4],
      'auxiliaryPower' => $row[5],
      'secondaryPower' => $row[6],
      'secondaryPower1' => $row[7],
      'secondaryPower2' => $row[8],
      'secondaryPower3reserved' => $row[9],
      'mainPower' => $row[10],
      'temperature' => $row[11],
      'mainsRunHours' => $row[12],
      'dgRunHours' => $row[13],
      'batteryRunHours' => $row[14],
      'orion' => $row[15],
      'quantum' => $row[16],
      'siteId' => $row[17],
      'siteName' => $row[18],
    );

    array_push($dataTable, $dataRow);
  }

  $mysqli->close();
  return array(
    'data' => $dataTable,
    // 'itemsCount' => intval($count[0]),
    'itemsCount' => 5000,
  );
}

function selectIProtectFlags($iProtectId, $siteId, $pageNumber = 1, $pageSize = 5)
{
  // echo "pageSize: $pageSize\r\n";

  $iProtectId = !isnull($iProtectId) ? $iProtectId : 'NULL'; //number
  $siteId = !isnull($siteId) ? "'$siteId'" : 'NULL'; //string

  $pageNumber = !isnull($pageNumber) ? $pageNumber : 1; //number
  $pageSize = !isnull($pageSize) ? $pageSize : 5; //number
  $offset = $pageSize * ($pageNumber - 1);

  $sql = "SELECT
                iProtect.iProtectId,
                iProtectFlags

            FROM iProtect
            WHERE iProtect.isDeleted = 0
                AND iProtect.iProtectId = IFNULL($iProtectId, iProtect.iProtectId)
                AND iProtect.siteId = IFNULL($siteId, iProtect.siteId)
            ORDER BY iProtect.iProtectId DESC
            LIMIT $offset, $pageSize ";

  $sqlCount = "SELECT
                    COUNT(iProtectId)
                FROM iProtect
                WHERE iProtect.isDeleted = 0
                AND iProtect.iProtectId = IFNULL($iProtectId, iProtect.iProtectId)
                AND iProtect.siteId = IFNULL($siteId, iProtect.siteId)";

  // echo "sql: $sql\r\n";
  $mysqli = dbContext();
  $result = $mysqli->query($sql);
  $resultCount = $mysqli->query($sqlCount);

  $dataTable = array();

  if (!$result) {
    // echo ("Error selectIProtect: " . $mysqli->error);
    return $dataTable;
  }

  if (!$resultCount) {
    // echo ("Error selectIProtect: " . $mysqli->error);
    return $dataTable;
  }

  $count = $resultCount->fetch_row();

  while ($row = $result->fetch_row()) {
    $dataRow = array(
      'iProtectId' => $row[0],
      'iProtectFlags' => $row[5],
    );

    array_push($dataTable, $dataRow);
  }

  $mysqli->close();
  return array(
    'data' => $dataTable,
    'itemsCount' => intval($count[0]),
  );
}

function insertUpdateIProtect($iProtect)
{
  // print_r($iProtect);
  # data doesn't need to be handled for null
  $iProtectFlagsUnhandled = $iProtect['iProtectFlags'];

  $iProtect = $iProtect ? handleNull($iProtect) : $iProtect;
  extract($iProtect);

  // query to insert record
  $sql = "INSERT INTO iProtect
                SET     deviceId = $deviceId,
                        protocolId = $protocolId,
                        siteId = $siteId,
                        siteName = $siteName,
                        datetime = $datetime,
                        iProtectFlags = '$iProtectFlagsUnhandled',
                        temperature = $temperature,
                        auxiliaryPower = $auxiliaryPower,
                        mainPower = $mainPower,
                        secondaryPower = $secondaryPower,
                        secondaryPower1 = $secondaryPower1,
                        secondaryPower2 = $secondaryPower2,
                        secondaryPower3reserved = $secondaryPower3reserved,
                        impedance = $impedance,
                        orion = $orion,
                        lvd = $lvd,
                        lvd1 = $lvd1,
                        lvd2 = $lvd2,
                        lvd3 = $lvd3,
                        lvd4 = $lvd4,
                        lvd5 = $lvd5,
                        lvd6 = $lvd6,
                        lvd7 = $lvd7,
                        mainsFrequency = $mainsFrequency,
                        dgFrequency = $dgFrequency,
                        mainsRunHours = $mainsRunHours,
                        batteryRunHours = $batteryRunHours,
                        dgRunHours = $dgRunHours,
                        mainsDailyRunHours = $mainsDailyRunHours,
                        quantum = $quantum,
                        insertedAt = NOW()";

  // echo "sql $sql \r\n\r\n";
  $mysqli = dbContext();
  $result = $mysqli->query($sql);

  if (!$result) {
    echo ("Error insertUpdateIProtect: " . $mysqli->error);
    return false;
  }

  $insertId = $mysqli->insert_id;
  $mysqli->close();
  return $insertId;
}

function insertDump($data)
{
  $data = !isnull($data) ? "'$data'" : 'NULL'; //string
  // query to insert record
  $sql = "INSERT INTO dump
            SET data = $data,
                insertedAt = NOW()";

  //echo $sql;
  $mysqli = dbContext();
  $result = $mysqli->query($sql);

  if (!$result) {
    echo ("Error insertDump: " . $mysqli->error);
    return false;
  }

  $mysqli->close();
  return true;
}

function insertAlarm($siteRid, $alarm)
{
  //print_r($alarm);
  # data doesn't need to be handled for null
  $iProtectFlagsUnhandled = $alarm['iProtectFlags'];

  $alarm = $alarm ? handleNull($alarm) : $alarm;
  extract($alarm);

  // query to insert record
  $sql = "INSERT INTO alarms
                SET     siteRid = $siteRid,
                        siteId = $siteId,
                        iProtectId = $iProtectId,
                        protocolId = $protocolId,
                        iProtectFlags = '$iProtectFlagsUnhandled',
                        breach = $breach,
                        mainPower = $mainPower,
                        quantum = $quantum,
                        datetime = $datetime,
                        insertedAt = NOW()";

  // echo $sql;
  $mysqli = dbContext();
  $result = $mysqli->query($sql);

  if (!$result) {
    echo ("Error insertAlarm: " . $mysqli->error);
    return false;
  }

  $insertId = $mysqli->insert_id;
  $mysqli->close();
  return $insertId;
}

function validateAlarmIds($alarmIds)
{
  $alarmIdsString = implode(",", $alarmIds);
  $sql = "SELECT `alarmId` FROM `alarms` WHERE `alarmId` IN ($alarmIdsString)";

  // echo $sql;
  $mysqli = dbContext();
  $result = $mysqli->query($sql);

  if (!$result) {
    // echo ("Error validateAlarmIds: " . $mysqli->error);
    return [];
  }

  $dataTable = [];
  while ($row = $result->fetch_row()) $dataTable[] = $row[0];

  $mysqli->close();
  return $dataTable;
}

function insertAlarmsAlerts($sql)
{
  // echo "sql: $sql\r\n";
  $mysqli = dbContext();
  $result = $mysqli->multi_query($sql);

  if (!$result) {
    // echo ("Error insertAlarmsAlerts: " . $mysqli->error);
    return false;
  }

  $mysqli->close();
  return true;
}

function selectAlarms($alarmId, $iProtectId, $siteRid, $siteId, $pageNumber = 1, $pageSize = 5)
{
  // echo "pageSize: $pageSize\r\n";

  $alarmId = !isnull($alarmId) ? $alarmId : 'NULL'; //number
  $iProtectId = !isnull($iProtectId) ? $iProtectId : 'NULL'; //number
  $siteRid = !isnull($siteRid) ? $siteRid : 'NULL'; //number
  $siteId = !isnull($siteId) ? "'$siteId'" : 'NULL'; //string

  $pageNumber = !isnull($pageNumber) ? $pageNumber : 1; //number
  $pageSize = !isnull($pageSize) ? $pageSize : 5; //number
  $offset = $pageSize * ($pageNumber - 1);

  $sql = "SELECT
                alarmId,
                siteRid,
                siteId,
                iProtectId,
                protocolId,
                iProtectFlags,
                mainPower,
                datetime,
                insertedAt,
                quantum

            FROM alarms
            WHERE alarmId = IFNULL($alarmId, alarmId)
                AND iProtectId = IFNULL($iProtectId, iProtectId)
                AND siteRid = IFNULL($siteRid, siteRid)
                AND siteId = IFNULL($siteId, siteId)
            ORDER BY alarmId DESC
            LIMIT $offset, $pageSize ";

  $sqlCount = "SELECT
                    COUNT(alarmId)
                    FROM alarms
                WHERE alarmId = IFNULL($alarmId, alarmId)
                AND iProtectId = IFNULL($iProtectId, iProtectId)
                AND siteRid = IFNULL($siteRid, siteRid)
                AND siteId = IFNULL($siteId, siteId)";

  // echo "sql: $sql\r\n";
  $mysqli = dbContext();
  $result = $mysqli->query($sql);
  $resultCount = $mysqli->query($sqlCount);

  $dataTable = array();

  if (!$result) {
    echo ("Error selectIProtect: " . $mysqli->error);
    return $dataTable;
  }

  if (!$resultCount) {
    echo ("Error selectIProtect: " . $mysqli->error);
    return $dataTable;
  }

  $count = $resultCount->fetch_row();

  while ($row = $result->fetch_row()) {
    $dataRow = array(
      'alarmId' => $row[0],
      'siteRid' => $row[1],
      'siteId' => $row[2],
      'iProtectId' => $row[3],
      'protocolId' => $row[4],
      'iProtectFlags' => $row[5],
      'mainPower' => $row[6],
      'deviceTimestamp' => $row[7] ? strtotime($row[7]) : null,
      'serverTimestamp' => strtotime($row[8]),
      'quantum' => $row[9],
    );

    array_push($dataTable, $dataRow);
  }

  $mysqli->close();
  return array(
    'data' => $dataTable,
    'itemsCount' => intval($count[0]),
  );
}

function selectAlarmsByDate($siteRid, $siteId, $startDate, $endDate)
{
  $siteRid = !isnull($siteRid) ? $siteRid : 'NULL'; //number
  $siteId = !isnull($siteId) ? "'$siteId'" : 'NULL'; //string
  $startDate = !isnull($startDate) ? "'$startDate'" : 'NULL'; //string
  $endDate = !isnull($endDate) ? "'$endDate'" : 'NULL'; //string

  $sql = "SELECT
                alarmId,
                siteRid,
                siteId,
                iProtectId,
                protocolId,
                iProtectFlags,
                mainPower,
                datetime,
                insertedAt,
                quantum

            FROM alarms
            WHERE siteRid = IFNULL($siteRid, siteRid)
                AND siteId = IFNULL($siteId, siteId)
                AND insertedAt BETWEEN $startDate AND $endDate
            ORDER BY alarmId";

  // echo "sql: $sql\r\n";
  $mysqli = dbContext();
  $result = $mysqli->query($sql);
  // $resultCount = $mysqli->query($sqlCount);

  $dataTable = array();

  if (!$result) {
    echo ("Error selectIProtect: " . $mysqli->error);
    return $dataTable;
  }

  while ($row = $result->fetch_row()) {
    $dataRow = array(
      'alarmId' => $row[0],
      'siteRid' => $row[1],
      'siteId' => $row[2],
      'iProtectId' => $row[3],
      'protocolId' => $row[4],
      'iProtectFlags' => $row[5],
      'mainPower' => $row[6],
      'deviceTimestamp' => $row[7] ? strtotime($row[7]) : null,
      'serverTimestamp' => strtotime($row[8]),
      'quantum' => $row[9],
    );

    array_push($dataTable, $dataRow);
  }

  $mysqli->close();
  return $dataTable;
}

function selectAlarmsByUser($siteRids, $startDate = '1970-01-01', $endDate = null)
{
  $time = time();
  $time = unixToDate($time);

  $startDate = !isnull($startDate) ? "'$startDate'" : "'1970-01-01'"; //string
  $endDate = !isnull($endDate) ? "'$endDate'" : "'$time'"; //string

  $sql = "SELECT
                alarmId,
                siteRid,
                siteId,
                iProtectId,
                protocolId,
                iProtectFlags,
                mainPower,
                datetime,
                insertedAt,
                quantum

            FROM alarms
            WHERE siteRid IN ($siteRids)
                AND insertedAt BETWEEN $startDate AND $endDate
            ORDER BY alarmId DESC";

  // echo "sql: $sql\r\n";
  $mysqli = dbContext();
  $result = $mysqli->query($sql);
  // $resultCount = $mysqli->query($sqlCount);

  $dataTable = array();

  if (!$result) {
    echo ("Error selectIProtect: " . $mysqli->error);
    return $dataTable;
  }

  while ($row = $result->fetch_row()) {
    $dataRow = array(
      'alarmId' => $row[0],
      'siteRid' => $row[1],
      'siteId' => $row[2],
      'iProtectId' => $row[3],
      'protocolId' => $row[4],
      'iProtectFlags' => $row[5],
      'mainPower' => $row[6],
      'deviceTimestamp' => $row[7] ? strtotime($row[7]) : null,
      'serverTimestamp' => strtotime($row[8]),
      'quantum' => $row[9],
    );

    array_push($dataTable, $dataRow);
  }

  $mysqli->close();
  return $dataTable;
}

function selectGraph($siteId, $startDate = '1970-01-01', $endDate = null)
{
  $time = time();
  $time = unixToDate($time);

  $startDate = !isnull($startDate) ? "'$startDate'" : "'1970-01-01'"; //string
  $endDate = !isnull($endDate) ? "'$endDate'" : "'$time'"; //string

  $sql = "SELECT
                secondaryPower,
                secondaryPower1,
                secondaryPower2,
                secondaryPower3reserved,
                mainPower,
                iProtect.insertedAt

            FROM iProtect
            WHERE iProtect.isDeleted = 0
                AND iProtect.siteId = '$siteId'
                AND insertedAt BETWEEN $startDate AND $endDate";

  // echo "sql: $sql\r\n";
  // echo "sql: $sqlCount\r\n";
  $mysqli = dbContext();
  $result = $mysqli->query($sql);

  $dataTable = array();

  if (!$result) {
    // echo ("Error selectIProtect: " . $mysqli->error);
    return $dataTable;
  }
  while ($row = $result->fetch_row()) {
    $dataRow = array(
      'secondaryPower' => floatval($row[0]),
      'secondaryPower1' => floatval($row[1]),
      'secondaryPower2' => floatval($row[2]),
      'secondaryPower3reserved' => floatval($row[3]),
      'mainPower' => floatval($row[4]),
      'serverTimestamp' => (string) strtotime($row[5]),
    );

    array_push($dataTable, $dataRow);
  }

  $mysqli->close();
  return $dataTable;
}

function selectOutageGraph($siteId, $startDate, $endDate, $pageNumber = 1, $pageSize = 10)
{
  $pageNumber = !isnull($pageNumber) ? $pageNumber : 1; //number
  $pageSize = !isnull($pageSize) ? $pageSize : 10; //number
  $offset = $pageSize * ($pageNumber - 1);

  $sql =
    "SELECT alarmId, mainPower, insertedAt
    FROM alarms
    WHERE siteId = '$siteId'
    AND insertedAt BETWEEN '$startDate' AND '$endDate'
    LIMIT $offset, $pageSize;";

  // echo "sql: $sql\r\n";
  $mysqli = dbContext();
  $result = $mysqli->query($sql);

  if (!$result) {
    echo ("Error selectIProtect: " . $mysqli->error);
    return [];
  }

  $data = [];
  while ($row = $result->fetch_row())
    $data[] = [
      "alarmId" => $row[0],
      "mainPower" => floatval($row[1]),
      "serverTimestamp" => (string) strtotime($row[2]),
    ];

  $mysqli->close();
  return $data;
}

function selectCalling($siteId, $searchText, $startDate, $endDate, $pageNumber = 1, $pageSize = 10)
{
  $searchText = !isnull($searchText) ? "'%$searchText%'" : "'%%'";
  // $pageNumber = !isnull($pageNumber) ? $pageNumber : 1; //number
  // $pageSize = !isnull($pageSize) ? $pageSize : 10; //number
  $offset = $pageSize * ($pageNumber - 1);

  $sql = "SELECT
                alarmsAlertId,
                mobile,
                alarms,
                statusString,
                callString,
                date

            FROM alarmsAlerts
            WHERE alarmsAlerts.alertType = 'call'
                AND alarmsAlerts.siteId = '$siteId'
                AND mobile LIKE $searchText
                AND date BETWEEN '$startDate' AND '$endDate'
            ORDER BY alarmsAlertId DESC
            LIMIT $offset, $pageSize;";

  $sqlCount = "SELECT
                COUNT(alarmsAlertId)
                FROM alarmsAlerts
            WHERE alarmsAlerts.alertType = 'call'
                AND alarmsAlerts.siteId = '$siteId'
                AND mobile LIKE $searchText
                AND date BETWEEN '$startDate' AND '$endDate'";

  // echo "sql: $sql\r\n";
  // echo "sql: $sqlCount\r\n";
  $mysqli = dbContext();
  $result = $mysqli->query($sql);
  $resultCount = $mysqli->query($sqlCount);

  if (!$result) {
    // echo ("Error selectCalling: " . $mysqli->error);
    return [];
  }

  if (!$resultCount) {
    // echo ("Error selectCalling: " . $mysqli->error);
    return [];
  }

  $count = $resultCount->fetch_row();

  $data = [];
  while ($row = $result->fetch_row()) {
    $data[] = [
      'alarmsAlertId' => $row[0],
      'mobile' => $row[1],
      'alarms' => $row[2],
      'statusString' => $row[3],
      'callString' => $row[4],
      'date' => strtotime($row[5]),
    ];
  }

  $mysqli->close();
  return [
    'data' => $data,
    'itemsCount' => intval($count[0]),
  ];
}
