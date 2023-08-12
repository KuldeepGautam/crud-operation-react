
<?php

require_once $db;
require_once $pathGeneralMethods;

function selectSiteRidsByUser($userId, $searchText = null)
{
  $searchText = !isnull($searchText) ? "'%$searchText%'" : "'%%'";

  $sql =
    "SELECT siteUsers.siteRid 
    FROM siteUsers 
    JOIN sites ON sites.siteRid = siteUsers.siteRid
    WHERE isDeleted = 0 
    AND userId = $userId
    AND (sites.siteId LIKE $searchText OR sites.siteName LIKE $searchText)";

  $mysqli = dbContext();
  $result = $mysqli->query($sql);

  if (!$result) {
    echo ("Error selectSitesByUser: " . $mysqli->error);
    return [];
  }

  $data = [];
  while ($row = $result->fetch_row()) $data[] = $row[0];

  $mysqli->close();
  return $data;
}

function selectUsersBySite($siteRid)
{
  $siteRid = !isnull($siteRid) ? $siteRid : 'NULL'; //number
  $limit = 1000;

  $sql =
    "SELECT
            siteUsers.userId,
            customerId,
            users.position,
            name,
            mobileNumber,
            access
        FROM siteUsers
        JOIN users ON users.userId = siteUsers.userId
        WHERE isDeleted = 0
            AND siteRid = $siteRid
        ORDER BY userId DESC";

  $mysqli = dbContext();
  $result = $mysqli->query($sql);

  $dataTable = array();

  if (!$result) {
    echo ("Error selectUsersBySite: " . $mysqli->error);
    return $dataTable;
  }

  if ($result) {
    while ($row = $result->fetch_row()) {
      $dataRow = array(
        'userId' => $row[0],
        'customerId' => $row[1],
        'position' => $row[2],
        'name' => $row[3],
        'mobileNumber' => $row[4],
        'access' => $row[5],
      );

      array_push($dataTable, $dataRow);
    }
  }

  $mysqli->close();
  return $dataTable;
}

function selectSitesByUser($userId, $pageNumber = 1, $pageSize = 10)
{
  $userId = !isnull($userId) ? $userId : 'NULL'; //number

  $pageNumber = !isnull($pageNumber) ? $pageNumber : 1; //number
  $pageSize = !isnull($pageSize) ? $pageSize : 10; //number
  $offset = $pageSize * ($pageNumber - 1);

  $sql =
    "SELECT
            siteUsers.siteRid,
            siteId,
            customerId,
            siteName,
            mobileNumber,
            address,
            batteryAH,
            batteryCapacity,
            batteryEnergy,
            batteryAH1,
            batteryCapacity1,
            batteryEnergy1,
            batteryAH2,
            batteryCapacity2,
            batteryEnergy2,
            l1Name,
            l1Number,
            l2Name,
            l2Number,
            collectedAt,
            installedAt
        FROM siteUsers
        JOIN sites ON sites.siteRid = siteUsers.siteRid
        WHERE isDeleted = 0
            AND userId = $userId
        ORDER BY siteRid DESC
        LIMIT $offset, $pageSize ";

  $sqlCount = "SELECT
                    COUNT(siteUsers.siteRid)
                FROM siteUsers
                JOIN sites ON sites.siteRid = siteUsers.siteRid
                WHERE isDeleted = 0
                    AND userId = $userId";

  $mysqli = dbContext();
  $result = $mysqli->query($sql);
  $resultCount = $mysqli->query($sqlCount);

  $dataTable = array();

  if (!$result) {
    echo ("Error selectSitesByUser: " . $mysqli->error);
    return $dataTable;
  }

  if (!$resultCount) {
    // echo ("Error selectIProtect: " . $mysqli->error);
    return $dataTable;
  }

  $count = $resultCount->fetch_row();

  if ($result) {
    while ($row = $result->fetch_row()) {
      $dataRow = array(
        'siteRid' => $row[0],
        'siteId' => $row[1],
        'customerId' => $row[2],
        'siteName' => $row[3],
        'mobileNumber' => $row[4],
        'address' => $row[5],
        'batteryAH' => $row[6],
        'batteryCapacity' => $row[7],
        'batteryEnergy' => $row[8],
        'batteryAH1' => $row[9],
        'batteryCapacity1' => $row[10],
        'batteryEnergy1' => $row[11],
        'batteryAH2' => $row[12],
        'batteryCapacity2' => $row[13],
        'batteryEnergy2' => $row[14],
        'l1Name' => $row[15],
        'l1Number' => $row[16],
        'l2Name' => $row[17],
        'l2Number' => $row[18],
        'collectedAt' => $row[19] ? (string) strtotime($row[19]) : null,
        'serverTimestamp' => $row[19] ? (string) strtotime($row[19]) : null,
        'installedAt' => $row[20] ? (string) strtotime($row[20]) : null,
        'iProtectFlags' => null,
      );

      array_push($dataTable, $dataRow);
    }
  }

  $mysqli->close();
  return array(
    'data' => $dataTable,
    'itemsCount' => intval($count[0]),
  );
}

function selectSitesForMailByUser($userId, $startDate = '1970-01-01', $endDate = null)
{
  $time = time();
  $time = unixToDate($time);

  $startDate = !isnull($startDate) ? "'$startDate'" : "'1970-01-01'"; //string
  $endDate = !isnull($endDate) ? "'$endDate'" : "'$time'"; //string

  $userId = !isnull($userId) ? $userId : 'NULL'; //number
  // $customerId = !isnull($customerId) ? $customerId : 'NULL'; //number
  // $siteId = !isnull($siteId) ? "'$siteId'" : 'NULL'; //string

  $sql =
    "SELECT
            siteUsers.siteRid,
            siteId,
            siteName, 
            collectedAt as serverTimestamp,
            batteryAH,
            batteryCapacity,
            batteryAH1,
            batteryCapacity1,
            operatorFlags,
            circleName

        FROM siteUsers
        JOIN sites ON sites.siteRid = siteUsers.siteRid
        WHERE isDeleted = 0
        AND userId = $userId
        AND IFNULL(collectedAt, '1970-01-01') >= $startDate
        ORDER BY siteId DESC";

  // echo $sql . "\r\n";
  $mysqli = dbContext();
  $result = $mysqli->query($sql);

  if (!$result) {
    echo ("Error selectSites: " . $mysqli->error);
    return [];
  }

  $data = [];
  while ($row = $result->fetch_row()) {
    $data[] = [
      'siteRid' => $row[0],
      'siteId' => $row[1],
      'siteName' => $row[2],
      'serverTimestamp' => $row[3],
      'batteryAH' => $row[4],
      'batteryCapacity' => $row[5],
      'batteryAH1' => $row[6],
      'batteryCapacity1' => $row[7],
      'operatorFlags' => $row[8],
      'circleName' => $row[9],
    ];
  }

  $mysqli->close();
  return $data;
}

function selectSitesForReportByUser(
  $userId,
  $searchText,
  $pageNumber = 1,
  $pageSize = 5
) {
  $userId = !isnull($userId) ? $userId : 'NULL'; //number
  $searchText = !isnull($searchText) ? "'%$searchText%'" : "'%%'"; //string
  $pageNumber = !isnull($pageNumber) ? $pageNumber : 1; //number
  $pageSize = !isnull($pageSize) ? $pageSize : 5; //number
  $offset = $pageSize * ($pageNumber - 1);

  $sql =
    "SELECT
            siteUsers.siteRid,
            siteId,
            siteName,
            batteryAH,
            batteryCapacity,
            batteryAH1,
            batteryCapacity1

        FROM siteUsers
        JOIN sites ON sites.siteRid = siteUsers.siteRid
        WHERE isDeleted = 0
        AND userId = $userId
        AND (sites.siteId LIKE $searchText OR sites.siteName LIKE $searchText)
        ORDER BY siteId DESC
        LIMIT $offset, $pageSize ";

  $sqlCount = "SELECT
                    COUNT(siteUsers.siteRid)
                  FROM siteUsers
                  JOIN sites ON sites.siteRid = siteUsers.siteRid
                  WHERE isDeleted = 0
                  AND userId = $userId
                  AND (sites.siteId LIKE $searchText OR sites.siteName LIKE $searchText)";

  // echo $sql . "\r\n";
  $mysqli = dbContext();
  $result = $mysqli->query($sql);
  $resultCount = $mysqli->query($sqlCount);

  $dataTable = array();

  if (!$result) {
    echo ("Error selectSites: " . $mysqli->error);
    return $dataTable;
  }

  if (!$resultCount) {
    // echo ("Error selectIProtect: " . $mysqli->error);
    return $dataTable;
  }

  $count = $resultCount->fetch_row();

  while ($row = $result->fetch_row()) {
    $dataRow = array(
      'siteRid' => $row[0],
      'siteId' => $row[1],
      'siteName' => $row[2],
      'batteryAH' => $row[3],
      'batteryCapacity' => $row[4],
      'batteryAH1' => $row[5],
      'batteryCapacity1' => $row[6]
    );

    array_push($dataTable, $dataRow);
  }

  $mysqli->close();
  return array(
    'data' => $dataTable,
    'itemsCount' => intval($count[0]),
  );
}

function selectSitesForNonCommReportByUser(
  $userId,
  $searchText,
  $pageNumber = 1,
  $pageSize = 5
) {
  $time = time() - 3600;
  $time = unixToDate($time);
  $time = "'$time'";

  $userId = !isnull($userId) ? $userId : 'NULL'; //number
  $searchText = !isnull($searchText) ? "'%$searchText%'" : "'%%'"; //string
  $pageNumber = !isnull($pageNumber) ? $pageNumber : 1; //number
  $pageSize = !isnull($pageSize) ? $pageSize : 5; //number
  $offset = $pageSize * ($pageNumber - 1);

  $sql =
    "SELECT
            siteUsers.siteRid,
            siteId,
            siteName,
            collectedAt

        FROM siteUsers
        JOIN sites ON sites.siteRid = siteUsers.siteRid
        WHERE isDeleted = 0
        AND userId = $userId
        AND (sites.siteId LIKE $searchText OR sites.siteName LIKE $searchText)
        AND IFNULL(collectedAt, '1970-01-01') <= $time
        ORDER BY siteId DESC
        LIMIT $offset, $pageSize ";

  $sqlCount = "SELECT
                    COUNT(siteUsers.siteRid)
                  FROM siteUsers
                  JOIN sites ON sites.siteRid = siteUsers.siteRid
                  WHERE isDeleted = 0
                  AND userId = $userId
                  AND (sites.siteId LIKE $searchText OR sites.siteName LIKE $searchText)
                  AND IFNULL(collectedAt, '1970-01-01') <= $time";

  // echo $sql . "\r\n";
  $mysqli = dbContext();
  $result = $mysqli->query($sql);
  $resultCount = $mysqli->query($sqlCount);

  $dataTable = array();

  if (!$result) {
    echo ("Error selectSites: " . $mysqli->error);
    return $dataTable;
  }

  if (!$resultCount) {
    // echo ("Error selectIProtect: " . $mysqli->error);
    return $dataTable;
  }

  $count = $resultCount->fetch_row();

  while ($row = $result->fetch_row()) {
    $dataRow = array(
      'siteRid' => $row[0],
      'siteId' => $row[1],
      'siteName' => $row[2],
      'collectedAt' => $row[3] ? (string) strtotime($row[3]) : null,
    );

    array_push($dataTable, $dataRow);
  }

  $mysqli->close();
  return array(
    'data' => $dataTable,
    'itemsCount' => intval($count[0]),
  );
}

function selectSitesForDashboardByUser($userId)
{
  $userId = !isnull($userId) ? $userId : 'NULL'; //number

  $sql =
    "SELECT
            siteUsers.siteRid,
            siteId,
            siteName,
            collectedAt,
            iProtectFlags,
            batteryAH,
            batteryCapacity,
            batteryAH1,
            batteryCapacity1,
            secondaryPower,
            secondaryPower1,
            secondaryPower2,
            secondaryPower3reserved,
            mainPower,
            temperature

        FROM siteUsers
        JOIN sites ON sites.siteRid = siteUsers.siteRid
        WHERE isDeleted = 0 AND collectedAt IS NOT NULL
        AND userId = $userId";

  // echo $sql . "\r\n";
  $mysqli = dbContext();
  $result = $mysqli->query($sql);

  $dataTable = array();

  if (!$result) {
    echo ("Error selectSitesForDashboardByUser: " . $mysqli->error);
    return $dataTable;
  }

  while ($row = $result->fetch_row()) {
    $dataRow = array(
      'siteRid' => $row[0],
      'siteId' => $row[1],
      'siteName' => $row[2],
      'collectedAt' => $row[3] ? (string) strtotime($row[3]) : null,
      'iProtectFlags' => versionFlags($row[4]),
      'batteryAH' => $row[5],
      'batteryCapacity' => $row[6],
      'batteryAH1' => $row[7],
      'batteryCapacity1' => $row[8],
      'secondaryPower' => $row[9],
      'secondaryPower1' => $row[10],
      'secondaryPower2' => $row[11],
      'secondaryPower3reserved' => $row[12],
      'mainPower' => $row[13],
      'temperature' => $row[14]
    );

    array_push($dataTable, $dataRow);
  }

  $mysqli->close();
  return $dataTable;
}

function selectAlarmAlertsCallByUser($userId, $startDate, $endDate, $pageNumber = 1, $pageSize = 10)
{
  $userId = !isnull($userId) ? $userId : 'NULL'; //number
  $pageNumber = !isnull($pageNumber) ? $pageNumber : 1; //number
  $pageSize = !isnull($pageSize) ? $pageSize : 10; //number
  $offset = $pageSize * ($pageNumber - 1);

  $sql =
    "SELECT
    alarmsAlerts.alarmsAlertId,
    alarmsAlerts.statusString

  FROM siteUsers
  JOIN sites ON sites.siteRid = siteUsers.siteRid
  JOIN alarms ON alarms.siteRid = siteUsers.siteRid
  JOIN alarmsAlerts ON alarms.alarmId = alarmsAlerts.alarmId
  WHERE sites.isDeleted = 0
  AND alertType = 'call'
  AND siteUsers.userId = $userId
  AND alarmsAlerts.date BETWEEN '$startDate' AND '$endDate'
  ORDER BY alarmsAlerts.alarmsAlertId DESC
  LIMIT $offset, $pageSize";

  $sqlCount =
    "SELECT COUNT(alarmsAlerts.alarmId)
  FROM siteUsers
  JOIN sites ON sites.siteRid = siteUsers.siteRid
  JOIN alarms ON alarms.siteRid = siteUsers.siteRid
  JOIN alarmsAlerts ON alarms.alarmId = alarmsAlerts.alarmId
  WHERE sites.isDeleted = 0
  AND alertType = 'call'
  AND siteUsers.userId = $userId
  AND alarmsAlerts.date BETWEEN '$startDate' AND '$endDate'";

  // echo $sql . "\r\n";
  $mysqli = dbContext();
  $result = $mysqli->query($sql);
  $resultCount = $mysqli->query($sqlCount);

  if (!$result) {
    echo ("Error selectCallingDataForDashboardByUser: " . $mysqli->error);
    return [];
  }

  if (!$resultCount) {
    echo ("Error selectCallingDataForDashboardByUser: " . $mysqli->error);
    return [];
  }

  $count = $resultCount->fetch_row();

  $data = [];
  while ($row = $result->fetch_row())
    $data[] = [
      "alarmsAlertId" => $row[0],
      "statusString" => $row[1],
    ];

  $mysqli->close();
  return [
    'data' => $data,
    'itemsCount' => intval($count[0]),
  ];
}

function selectSitesByUserForHomePage($userId, $searchText, $pageNumber = 1, $pageSize = 10)
{
  $userId = !isnull($userId) ? $userId : 'NULL'; //number
  $searchText = !isnull($searchText) ? "'%$searchText%'" : "'%%'"; //string

  $pageNumber = !isnull($pageNumber) ? $pageNumber : 1; //number
  $pageSize = !isnull($pageSize) ? $pageSize : 10; //number
  $offset = $pageSize * ($pageNumber - 1);

  $sql =
    "SELECT
            siteUsers.siteRid,
            siteId,
            customerId,
            siteName,
            mobileNumber,
            address,
            batteryAH,
            batteryCapacity,
            batteryEnergy,
            batteryAH1,
            batteryCapacity1,
            batteryEnergy1,
            batteryAH2,
            batteryCapacity2,
            batteryEnergy2,
            l1Name,
            l1Number,
            l2Name,
            l2Number,
            collectedAt,
            installedAt,
            iProtectFlags,
            l3Name,
            l3Number,
            mainPower,
            temperature,
            secondaryPower,
            secondaryPower1,
            secondaryPower2,
            secondaryPower3reserved
        FROM siteUsers
        JOIN sites ON sites.siteRid = siteUsers.siteRid
        WHERE isDeleted = 0
            AND userId = $userId
            AND (siteId LIKE $searchText OR siteName LIKE $searchText)
            AND collectedAt IS NOT NULL
        ORDER BY siteRid DESC
        LIMIT $offset, $pageSize ";

  $sqlCount = "SELECT
                    COUNT(siteUsers.siteRid)
                FROM siteUsers
                JOIN sites ON sites.siteRid = siteUsers.siteRid
                WHERE isDeleted = 0
                    AND userId = $userId
                    AND (siteId LIKE $searchText OR siteName LIKE $searchText)";

  //  echo "SQL: $sql\r\n";
  $mysqli = dbContext();
  $result = $mysqli->query($sql);
  $resultCount = $mysqli->query($sqlCount);

  $dataTable = array();

  if (!$result) {
    echo ("Error selectSitesByUser: " . $mysqli->error);
    return $dataTable;
  }

  if (!$resultCount) {
    // echo ("Error selectIProtect: " . $mysqli->error);
    return $dataTable;
  }

  $count = $resultCount->fetch_row();

  if ($result) {
    while ($row = $result->fetch_row()) {
      $dataRow = array(
        'siteRid' => $row[0],
        'siteId' => $row[1],
        'customerId' => $row[2],
        'siteName' => $row[3],
        'mobileNumber' => $row[4],
        'address' => $row[5],
        'batteryAH' => $row[6],
        'batteryCapacity' => $row[7],
        'batteryEnergy' => $row[8],
        'batteryAH1' => $row[9],
        'batteryCapacity1' => $row[10],
        'batteryEnergy1' => $row[11],
        'batteryAH2' => $row[12],
        'batteryCapacity2' => $row[13],
        'batteryEnergy2' => $row[14],
        'l1Name' => $row[15],
        'l1Number' => $row[16],
        'l2Name' => $row[17],
        'l2Number' => $row[18],
        'collectedAt' => $row[19] ? (string) strtotime($row[19]) : null,
        'serverTimestamp' => $row[19] ? (string) strtotime($row[19]) : null,
        'installedAt' => $row[20] ? (string) strtotime($row[20]) : null,
        'iProtectFlags' => $row[21],
        'l3Name' => $row[22],
        'l3Number' => $row[23],
        'mainPower' => $row[24],
        'temperature' => $row[25],
        'secondaryPower' => $row[26],
        'secondaryPower1' => $row[27],
        'secondaryPower2' => $row[28],
        'secondaryPower3reserved' => $row[29]
      );

      array_push($dataTable, $dataRow);
    }
  }

  $mysqli->close();
  return array(
    'data' => $dataTable,
    'itemsCount' => intval($count[0]),
    // 'itemsCount' => 50,
  );
}

function selectSitesByUserForSiteMetering($userId, $searchText, $pageNumber = 1, $pageSize = 10)
{
  $userId = !isnull($userId) ? $userId : 'NULL'; //number
  $searchText = !isnull($searchText) ? "'%$searchText%'" : "'%%'"; //string

  $pageNumber = !isnull($pageNumber) ? $pageNumber : 1; //number
  $pageSize = !isnull($pageSize) ? $pageSize : 10; //number
  $offset = $pageSize * ($pageNumber - 1);

  $sql =
    "SELECT
            siteUsers.siteRid,
            siteId,
            siteName
        FROM siteUsers
        JOIN sites ON sites.siteRid = siteUsers.siteRid
        WHERE isDeleted = 0
            AND userId = $userId
            AND (siteId LIKE $searchText OR siteName LIKE $searchText)
        ORDER BY collectedAt DESC
        LIMIT $offset, $pageSize ";

  $sqlCount = "SELECT
                    COUNT(siteUsers.siteRid)
                FROM siteUsers
                JOIN sites ON sites.siteRid = siteUsers.siteRid
                WHERE isDeleted = 0
                    AND userId = $userId
                    AND (siteId LIKE $searchText OR siteName LIKE $searchText)";

  // echo "sql: $sql \r\n";
  $mysqli = dbContext();
  $result = $mysqli->query($sql);
  $resultCount = $mysqli->query($sqlCount);

  $dataTable = array();

  if (!$result) {
    echo ("Error selectSitesByUser: " . $mysqli->error);
    return $dataTable;
  }

  if (!$resultCount) {
    // echo ("Error selectIProtect: " . $mysqli->error);
    return $dataTable;
  }

  $count = $resultCount->fetch_row();

  if ($result) {
    while ($row = $result->fetch_row()) {
      $dataRow = array(
        'siteRid' => $row[0],
        'siteId' => $row[1],
        'siteName' => $row[2],
      );

      array_push($dataTable, $dataRow);
    }
  }

  $mysqli->close();
  return array(
    'data' => $dataTable,
    'itemsCount' => intval($count[0]),
  );
}

function insertUpdateSiteUser($inType, $userId, $siteRid, $executedBy)
{
  /**
   * inType = 0: insert
   * inType = 1: update
   * inType = 2: delete by userId
   * inType = 3: delete by siteRid
   * inType = 2: delete by both userId & siteRid
   *
   */

  $position = 1;
  $siteRid = trim($siteRid) ? $siteRid : 'NULL'; //number
  $userId = trim($userId) ? $userId : 'NULL'; //number
  $position = trim($position) ? $position : 'NULL'; //number
  $executedBy = trim($executedBy) ? $executedBy : 'NULL'; //number

  $sql = null;
  switch ($inType) {
    case 0:
      // insert
      $sql =
        "INSERT INTO siteUsers
                SET siteRid = $siteRid,
                    userId = $userId,
                    position = $position,
                    insertedBy = $executedBy,
                    insertedAt = NOW()";
      break;
    case 1:
      // update
      $sql =
        "";
      break;
    case 2:
      $sql =
        "DELETE FROM
                    siteUsers
                WHERE
                    userId = $userId";
      break;
    case 3:
      $sql =
        "DELETE FROM
                    siteUsers
                WHERE
                    siteRid = $siteRid";
      break;
    case 4:
      $sql =
        "DELETE FROM
                    siteUsers
                WHERE
                    siteRid = $siteRid
                    AND userId = $userId";
      break;
    default:
      // do nothing
  }

  //echo "sql: $sql\r\n";
  $mysqli = dbContext();
  $result = $mysqli->query($sql);

  if (!$result) {
    // echo ("Error insertUpdateSiteUser: " . $mysqli->error);
    return false;
  }

  $mysqli->close();
  return true;
}

function validateSiteByUser($userId, $siteId)
{
  $sql =
    "SELECT COUNT(siteUsers.siteRid)
    FROM siteUsers
    JOIN sites ON sites.siteRid = siteUsers.siteRid
    WHERE isDeleted = 0 AND userId = $userId AND siteId = '$siteId'
    LIMIT 1";

  $mysqli = dbContext();
  $result = $mysqli->query($sql);

  if (!$result) {
    // echo ("Error selectSitesByUser: " . $mysqli->error);
    return false;
  }

  $row = $result->fetch_row();
  $mysqli->close();
  return intval($row[0]) > 0;
}

function selectSitesByUserForGraph($userId, $searchText, $pageNumber = 1, $pageSize = 10)
{
  $userId = !isnull($userId) ? $userId : 'NULL'; //number
  $searchText = !isnull($searchText) ? "'%$searchText%'" : "'%%'"; //string

  $pageNumber = !isnull($pageNumber) ? $pageNumber : 1; //number
  $pageSize = !isnull($pageSize) ? $pageSize : 10; //number
  $offset = $pageSize * ($pageNumber - 1);

  $sql =
    "SELECT
            siteUsers.siteRid,
            siteId,
            siteName,
            secondaryPower,
            secondaryPower1,
            mainPower
        FROM siteUsers
        JOIN sites ON sites.siteRid = siteUsers.siteRid
        WHERE isDeleted = 0
            AND userId = $userId
            AND (siteId LIKE $searchText OR siteName LIKE $searchText)
        -- ORDER BY siteRid DESC
        LIMIT $offset, $pageSize ";

  $sqlCount = "SELECT
                    COUNT(siteUsers.siteRid)
                FROM siteUsers
                JOIN sites ON sites.siteRid = siteUsers.siteRid
                WHERE isDeleted = 0
                    AND userId = $userId
                    AND (siteId LIKE $searchText OR siteName LIKE $searchText)";

  //  echo "SQL: $sql\r\n";
  $mysqli = dbContext();
  $result = $mysqli->query($sql);
  $resultCount = $mysqli->query($sqlCount);

  $dataTable = array();

  if (!$result) {
    echo ("Error selectSitesByUser: " . $mysqli->error);
    return $dataTable;
  }

  if (!$resultCount) {
    // echo ("Error selectIProtect: " . $mysqli->error);
    return $dataTable;
  }

  $count = $resultCount->fetch_row();

  if ($result) {
    while ($row = $result->fetch_row()) {
      $dataRow = array(
        'siteRid' => $row[0],
        'siteId' => $row[1],
        'siteName' => $row[2],
        'secondaryPower' => $row[3],
        'secondaryPower1' => $row[4],
        'mainPower' => $row[5]
      );

      array_push($dataTable, $dataRow);
    }
  }

  $mysqli->close();
  return array(
    'data' => $dataTable,
    'itemsCount' => intval($count[0]),
    // 'itemsCount' => 50,
  );
}

function selectSitesForOutage($siteRids, $startDate, $endDate, $pageNumber = 1, $pageSize = 10)
{
  $pageNumber = !isnull($pageNumber) ? $pageNumber : 1; //number
  $pageSize = !isnull($pageSize) ? $pageSize : 10; //number
  $offset = $pageSize * ($pageNumber - 1);
  $outageThreshold = 46;

  $sql =
    "SELECT DISTINCT alarms.siteRid
    FROM alarms
    WHERE alarms.insertedAt BETWEEN '$startDate' AND '$endDate'
    AND alarms.siteRid IN ($siteRids)
    AND alarms.mainPower < $outageThreshold
    LIMIT $offset, $pageSize;";

  $sqlCount =
    "SELECT COUNT(DISTINCT alarms.siteRid)
    FROM alarms
    WHERE alarms.insertedAt BETWEEN '$startDate' AND '$endDate'
    AND alarms.siteRid IN ($siteRids)
    AND alarms.mainPower < $outageThreshold";

  // echo "SQL: $sql\r\n";
  // echo "SQL: $sqlCount\r\n";
  $mysqli = dbContext();
  $result = $mysqli->query($sql);
  $resultCount = $mysqli->query($sqlCount);

  if (!$result) {
    echo ("Error selectSitesByUser: " . $mysqli->error);
    return [];
  }

  if (!$resultCount) {
    echo ("Error selectIProtect: " . $mysqli->error);
    return [];
  }

  $count = $resultCount->fetch_row();

  $data = [];
  while ($row = $result->fetch_row()) $data[] = $row[0];

  $mysqli->close();
  return ['data' => $data, 'itemsCount' => intval($count[0])];
}

function selectOutage($siteRids, $startDate, $endDate, $pageNumber = 1, $pageSize = 10)
{
  $pageNumber = !isnull($pageNumber) ? $pageNumber : 1; //number
  $pageSize = !isnull($pageSize) ? $pageSize : 10; //number
  $offset = $pageSize * ($pageNumber - 1);

  $sql =
    "SELECT 
      alarms.siteRid, 
      alarms.siteId, 
      sites.siteName, 
      COUNT(alarms.siteRid)
    FROM alarms
    JOIN sites ON sites.siteRid = alarms.siteRid
    WHERE alarms.insertedAt BETWEEN '$startDate' AND '$endDate'
    AND alarms.siteRid IN ($siteRids)
    GROUP BY alarms.siteRid HAVING COUNT(alarms.siteRid) > 1
    ORDER BY COUNT(alarms.siteRid) DESC     
    LIMIT $offset, $pageSize;";

  // echo "SQL: $sql\r\n";
  // echo "SQL: $sqlCount\r\n";
  $mysqli = dbContext();
  $result = $mysqli->query($sql);

  if (!$result) {
    echo ("Error selectSitesByUser: " . $mysqli->error);
    return [];
  }

  $data = [];
  while ($row = $result->fetch_row())
    $data[] = [
      "siteRid" => $row[0],
      "siteId" => $row[1],
      "siteName" => $row[2],
      "alarmsCount" => intval($row[3]),
    ];


  $mysqli->close();
  return $data;
}

function selectSitesByUserForCalling($userId, $searchText, $pageNumber = 1, $pageSize = 10)
{
  $userId = !isnull($userId) ? $userId : 'NULL'; //number
  $searchText = !isnull($searchText) ? "'%$searchText%'" : "'%%'"; //string

  $pageNumber = !isnull($pageNumber) ? $pageNumber : 1; //number
  $pageSize = !isnull($pageSize) ? $pageSize : 10; //number
  $offset = $pageSize * ($pageNumber - 1);

  $sql =
    "SELECT
            siteUsers.siteRid,
            siteId,
            siteName
            
        FROM siteUsers
        JOIN sites ON sites.siteRid = siteUsers.siteRid
        WHERE isDeleted = 0
            AND userId = $userId
            AND (siteId LIKE $searchText OR siteName LIKE $searchText)
        LIMIT $offset, $pageSize ";

  $sqlCount = "SELECT
                    COUNT(siteUsers.siteRid)
                FROM siteUsers
                JOIN sites ON sites.siteRid = siteUsers.siteRid
                WHERE isDeleted = 0
                    AND userId = $userId
                    AND (siteId LIKE $searchText OR siteName LIKE $searchText)";

  //  echo "SQL: $sql\r\n";
  $mysqli = dbContext();
  $result = $mysqli->query($sql);
  $resultCount = $mysqli->query($sqlCount);

  if (!$result) {
    // echo ("Error selectSitesByUserForCalling: " . $mysqli->error);
    return [];
  }

  if (!$resultCount) {
    // echo ("Error selectSitesByUserForCalling: " . $mysqli->error);
    return [];
  }

  $count = $resultCount->fetch_row();

  $data = [];
  while ($row = $result->fetch_row()) {
    $data[] = [
      'siteRid' => $row[0],
      'siteId' => $row[1],
      'siteName' => $row[2],
    ];
  }

  $mysqli->close();
  return [
    'data' => $data,
    'itemsCount' => intval($count[0]),
  ];
}
