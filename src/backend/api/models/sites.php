
<?php

require_once $db;
require_once $pathGeneralMethods;

function selectSites($siteRid, $customerId, $siteId, $pageNumber = 1, $pageSize = 5)
{
  $siteRid = !isnull($siteRid) ? $siteRid : 'NULL'; //number
  $customerId = !isnull($customerId) ? $customerId : 'NULL'; //number
  $siteId = !isnull($siteId) ? "'$siteId'" : 'NULL'; //string

  $pageNumber = !isnull($pageNumber) ? $pageNumber : 1; //number
  $pageSize = !isnull($pageSize) ? $pageSize : 5; //number
  $offset = $pageSize * ($pageNumber - 1);

  $sql =
    "SELECT
            siteRid,
            customerId,
            siteId,
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
            l1Email,
            l2Name,
            l2Number,
            l2Email,
            l3Name,
            l3Number,
            l3Email,
            longitude,
            latitude,
            installedAt,
            collectedAt,
            operatorFlags,
            circleName

        FROM sites
        WHERE isDeleted = 0
        AND siteRid = IFNULL($siteRid, siteRid)
        AND siteId = IFNULL($siteId, siteId)
        AND customerId = IFNULL($customerId, customerId)
        ORDER BY siteId DESC
        LIMIT $offset, $pageSize ";

  $sqlCount = "SELECT
                    COUNT(siteRid)
                FROM sites
                WHERE isDeleted = 0
                AND siteRid = IFNULL($siteRid, siteRid)
                AND siteId = IFNULL($siteId, siteId)
                AND customerId = IFNULL($customerId, customerId)";

  // echo "sql $sql \r\n\r\n";
  $mysqli = dbContext();
  $result = $mysqli->query($sql);
  $resultCount = $mysqli->query($sqlCount);

  $dataTable = array();

  if (!$result) {
    // echo ("Error selectSites: " . $mysqli->error);
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
      'customerId' => $row[1],
      'siteId' => $row[2],
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
      'l1Email' => $row[17],
      'l2Name' => $row[18],
      'l2Number' => $row[19],
      'l2Email' => $row[20],
      'l3Name' => $row[21],
      'l3Number' => $row[22],
      'l3Email' => $row[23],
      'longitude' => $row[24],
      'latitude' => $row[25],
      'installedAt' => $row[26] ? (string) strtotime($row[26]) : null,
      'collectedAt' => $row[27] ? (string) strtotime($row[27]) : null,
      'serverTimestamp' => $row[27] ? (string) strtotime($row[27]) : null,
      'operatorFlags' => $row[28],
      'circleName' => $row[29],
    );

    array_push($dataTable, $dataRow);
  }

  $mysqli->close();
  return array(
    'data' => $dataTable,
    'itemsCount' => intval($count[0]),
  );
}

function selectSitesForMail(
  $siteRid,
  $customerId,
  $siteId,
  $startDate = '1970-01-01',
  $endDate = null,
  $pageNumber = 1,
  $pageSize = 5
) {
  $time = time();
  $time = unixToDate($time);

  $startDate = !isnull($startDate) ? "'$startDate'" : "'1970-01-01'"; //string
  $endDate = !isnull($endDate) ? "'$endDate'" : "'$time'"; //string

  $siteRid = !isnull($siteRid) ? $siteRid : 'NULL'; //number
  $customerId = !isnull($customerId) ? $customerId : 'NULL'; //number
  $siteId = !isnull($siteId) ? "'$siteId'" : 'NULL'; //string

  $pageNumber = !isnull($pageNumber) ? $pageNumber : 1; //number
  $pageSize = !isnull($pageSize) ? $pageSize : 5; //number
  $offset = $pageSize * ($pageNumber - 1);

  $sql =
    "SELECT
            siteRid,
            siteId,
            siteName, 
            collectedAt as serverTimestamp,
            batteryAH,
            batteryCapacity,
            batteryAH1,
            batteryCapacity1,
            operatorFlags,
            circleName

        FROM sites
        WHERE isDeleted = 0
        AND siteRid = IFNULL($siteRid, siteRid)
        AND siteId = IFNULL($siteId, siteId)
        AND customerId = IFNULL($customerId, customerId)
        AND IFNULL(collectedAt, '1970-01-01') BETWEEN $startDate AND $endDate 
        ORDER BY siteId DESC
        LIMIT $offset, $pageSize ";

  $sqlCount = "SELECT
                    COUNT(siteRid)
                FROM sites
                WHERE isDeleted = 0
                AND siteRid = IFNULL($siteRid, siteRid)
                AND siteId = IFNULL($siteId, siteId)
                AND customerId = IFNULL($customerId, customerId)
                AND IFNULL(collectedAt, '1970-01-01') BETWEEN $startDate AND $endDate ";

  // echo $sql;
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
      'serverTimestamp' => $row[3],
      'batteryAH' => $row[4],
      'batteryCapacity' => $row[5],
      'batteryAH1' => $row[6],
      'batteryCapacity1' => $row[7],
      'operatorFlags' => $row[8],
      'circleName' => $row[9],
    );

    array_push($dataTable, $dataRow);
  }

  $mysqli->close();
  return array(
    'data' => $dataTable,
    'itemsCount' => intval($count[0]),
  );
}

function insertUpdateSite($inType, $siteRid, $site, $executedBy)
{
  $site = $site ? handleNull($site) : $site;

  extract($site);
  //print_r($site);

  $batteryAH1 = !isnull($batteryAH1) ? $batteryAH1 : "NULL";
  $batteryCapacity1 = !isnull($batteryCapacity1) ? $batteryCapacity1 : "NULL";
  $batteryEnergy1 = !isnull($batteryEnergy1) ? $batteryEnergy1 : "NULL";
  $batteryAH2 = !isnull($batteryAH2) ? $batteryAH2 : "NULL";
  $batteryCapacity2 = !isnull($batteryCapacity2) ? $batteryCapacity2 : "NULL";
  $batteryEnergy2 = !isnull($batteryEnergy2) ? $batteryEnergy2 : "NULL";

  $sql = null;
  switch ($inType) {
    case 0:
      // insert
      $sql =
        "INSERT INTO sites
                SET customerId = $customerId,
                    siteId = $siteId,
                    siteName = $siteName,
                    mobileNumber = $mobileNumber,
                    address = $address,
                    circleName = $circleName,
                    batteryAH = $batteryAH,
                    batteryCapacity = $batteryCapacity,
                    batteryEnergy = $batteryEnergy,
                    batteryAH1 = $batteryAH1,
                    batteryCapacity1 = $batteryCapacity1,
                    batteryEnergy1 = $batteryEnergy1,
                    batteryAH2 = $batteryAH2,
                    batteryCapacity2 = $batteryCapacity2,
                    batteryEnergy2 = $batteryEnergy2,
                    l1Name = $l1Name,
                    l1Number = $l1Number,
                    l1Email = $l1Email,
                    l2Name = $l2Name,
                    l2Number = $l2Number,
                    l2Email = $l2Email,
                    l3Name = $l3Name,
                    l3Number = $l3Number,
                    l3Email = $l3Email,
                    operatorFlags = $operatorFlags,
                    longitude = $longitude,
                    latitude = $latitude,
                    installedAt = $installedAt,
                    insertedBy = $executedBy,
                    insertedAt = NOW()";
      break;
    case 1:
      // update
      $sql =
        "UPDATE sites
                SET customerId = $customerId,
                    siteId = $siteId,
                    siteName = $siteName,
                    mobileNumber = $mobileNumber,
                    address = $address,
                    circleName = $circleName,

                    batteryAH = $batteryAH,
                    batteryCapacity = $batteryCapacity,
                    batteryEnergy = $batteryEnergy,
                    batteryAH1 = $batteryAH1,
                    batteryCapacity1 = $batteryCapacity1,
                    batteryEnergy1 = $batteryEnergy1,
                    batteryAH2 = $batteryAH2,
                    batteryCapacity2 = $batteryCapacity2,
                    batteryEnergy2 = $batteryEnergy2,

                    l1Name = $l1Name,
                    l1Number = $l1Number,
                    l1Email = $l1Email,
                    l2Name = $l2Name,
                    l2Number = $l2Number,
                    l2Email = $l2Email,
                    l3Name = $l3Name,
                    l3Number = $l3Number,
                    l3Email = $l3Email,

                    operatorFlags = $operatorFlags,
                    longitude = $longitude,
                    latitude = $latitude,
                    installedAt = $installedAt,
                    updatedBy = $executedBy,
                    updatedAt = NOW()
                WHERE siteRid = $siteRid";
      break;
    case 2:
      //delete: set the isDeleted flag
      $sql =
        "UPDATE sites
                SET isDeleted = 1,
                    updatedBy = $executedBy,
                    updatedAt = NOW()
                WHERE siteRid = $siteRid";
      break;
    default:
  }

  // echo "sql: $sql\r\n";
  $mysqli = dbContext();
  $result = $mysqli->query($sql);

  if (!$result) {
    echo ("Error insertUpdateSite: " . $mysqli->error);
    return false;
  }

  $mysqli->close();
  return true;
}

function updateSiteCollectedAt($collection, $siteId)
{
  $iProtectFlagsUnhandled = $collection['iProtectFlags'];
  $collection = handleNull($collection);
  extract($collection);

  //query to insert record
  $sql = "UPDATE sites
            SET    collectedAt = NOW(),
                   iProtectFlags = '$iProtectFlagsUnhandled',
                   batteryEnergy = IFNULL($batteryEnergy, batteryEnergy),
                   batteryCapacity = IFNULL($batteryCapacity, batteryCapacity),
                   batteryEnergy1 = IFNULL($batteryEnergy1, batteryEnergy1),
                   batteryCapacity1 = IFNULL($batteryCapacity1, batteryCapacity1),
                   batteryEnergy2 = IFNULL($batteryEnergy2, batteryEnergy2),
                   batteryCapacity2 = IFNULL($batteryCapacity2, batteryCapacity2),
                   auxiliaryPower = IFNULL($auxiliaryPower, auxiliaryPower),
                   secondaryPower = IFNULL($secondaryPower, secondaryPower),
                   secondaryPower1 = IFNULL($secondaryPower1, secondaryPower1),
                   secondaryPower2 = IFNULL($secondaryPower2, secondaryPower2),
                   secondaryPower3reserved = IFNULL($secondaryPower3reserved, secondaryPower3reserved),
                   mainPower = IFNULL($mainPower, mainPower),
                   temperature = IFNULL($temperature, temperature)
            WHERE siteId = '$siteId'";

  //echo $sql;
  $mysqli = dbContext();
  $result = $mysqli->query($sql);

  if (!$result) {
    // echo ("Error updateSiteCollectedAt: " . $mysqli->error);
    return false;
  }

  $mysqli->close();
  return true;
}

function insertSitesEnergyDetails($energy, $siteRid, $iProtectId)
{
  $energy = handleNull($energy);
  extract($energy);

  //query to insert record
  $sql = "INSERT INTO sitesEnergyDetails
                  SET   siteRid = $siteRid,
                        iProtectId = $iProtectId,
                        batteryEnergy = $batteryEnergy,
                        batteryCapacity = $batteryCapacity,
                        batteryKwh = $batteryKwh,
                        batteryEnergy1 = $batteryEnergy1,
                        batteryCapacity1 = $batteryCapacity1,
                        batteryKwh1 = $batteryKwh1,
                        batteryEnergy2 = $batteryEnergy2,
                        batteryCapacity2 = $batteryCapacity2,
                        batteryKwh2 = $batteryKwh2,
                        insertedAt = NOW()";

  // echo $sql;
  $mysqli = dbContext();
  $result = $mysqli->query($sql);

  if (!$result) {
    echo ("Error insertSitesEnergyDetails: " . $mysqli->error);
    return false;
  }

  $mysqli->close();
  return true;
}
