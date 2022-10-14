<?php

require_once $db;

/************ */

/*
SELECT
iProtectId,
insertedAt
FROM
iProtect
WHERE
siteId='SISPLTD001'
ORDER BY
(ABS(TIMESTAMPDIFF(SECOND, insertedAt, ('2019-11-16 23:17:20')))) ASC

SELECT
iProtectId,
insertedAt
FROM
iProtect
WHERE
siteId='SISPLTD001'
ORDER BY
(ABS(TIMESTAMPDIFF(SECOND, insertedAt, ('2019-11-28 23:17:20')))) ASC

 */

// Power Supply
function GetSiteStatsFromIdStartEnd($siteId, $start_date_time, $end_date_time)
{
  // echo $start_date_time . "\r\n";
  // echo $end_date_time . "\r\n";

  $mysqli = dbContext();

  if ($start_date_time == null) {
    return null;
  }

  if ($end_date_time == null) {
    return null;
  }

  $sql_sdt = "\"" . $start_date_time . "\"";
  $sql_edt = "\"" . $end_date_time . "\"";

  //Get first Id
  // $sql = "SELECT
  // 				iProtectId,
  // 				insertedAt
  // 			FROM
  // 			  iProtect
  // 			WHERE
  // 				siteId=\"$siteId\"
  // 			ORDER BY
  // 			  (ABS(TIMESTAMPDIFF(SECOND, insertedAt, ($sql_edt)))) ASC
  // 			LIMIT 1";

  $sql = "SELECT iProtectId, insertedAt
  			FROM iProtect
        WHERE siteId = '$siteId' AND insertedAt BETWEEN $sql_sdt AND $sql_edt 
  			ORDER BY insertedAt ASC
  			LIMIT 1";

  // echo "$sql \r\n";

  $result = $mysqli->query($sql);

  if ($result !== false) {
    $data = $result->fetch_row();
    $id1 = $data[0]; //First Id
    //echo "\r\nID1: $id1";

    $result->close();

    //Get 2nd Id
    // $sql = "SELECT
    //                     iProtectId
    //                 FROM
    //                   iProtect
    //                 WHERE
    //                     siteId=\"$siteId\"
    //                 ORDER BY
    //                   (ABS(TIMESTAMPDIFF(SECOND, insertedAt, ($sql_sdt)))) ASC
    //                 LIMIT 2";

    $sql = "SELECT iProtectId, insertedAt
  			FROM iProtect
        WHERE siteId = '$siteId' AND insertedAt BETWEEN $sql_sdt AND $sql_edt 
  			ORDER BY insertedAt DESC
  			LIMIT 1";

    // echo "$sql \r\n";

    $result = $mysqli->query($sql);
    if ($result !== false) {

      if ($result->num_rows === 0) return;

      $data1 = $result->fetch_row();
      $id2 = $data1[0]; //Second Id
      //echo "\r\nID2: $id2";

      // if ($id1 == $id2) {
      //   //echo "\r\nSame Id $id1 and $id2, We have results more than given time interval apart\r\n";
      //   //Find the next record

      //   $data1 = $result->fetch_row();
      //   if ($data1 !== false) {
      //     $id2 = $data1[0]; //Second Id
      //   } else {
      //     //We have not enough records
      //     $result->close();
      //     mysqli_close($mysqli);
      //     echo "Not Enough Records";
      //     return null;
      //   }
      // }

      $result->close();

      // echo "\r\nComparison between $id1 and $id2\r\n";
      $array = GetComparisonFromIds($id1, $id2);
      // print_r($array);

      mysqli_close($mysqli);
      return $array;
    }
  }

  mysqli_close($mysqli);
  echo "NULL";

  return null;
}

function GetComparisonFromIds($id1, $id2)
{
  $arr1 = GetDeviceRecordFromId($id1); //Old
  $arr2 = GetDeviceRecordFromId($id2); //New
  // var_dump($arr1);
  // var_dump($arr2);

  // var_dump($arr2['mainsRunHours']);

  $load = "battery";
  if ($arr2["secondaryPower2"] > 90) $load = "mains";
  else if ($arr2["secondaryPower3reserved"] > 90) $load = "dg";

  // $load = "mains";
  // if ($arr2["secondaryPower2"] < 90) {
  //   $load = "battery";
  // } else {
  //   if ($arr2["secondaryPower3reserved"] < 90) {
  //     $load = "dg";
  //   } else {
  //     $load = "mains";
  //   }
  // }

  $mainsRunHours = abs($arr2['mainsRunHours'] - $arr1['mainsRunHours']);
  $batteryRunHours = abs($arr2['batteryRunHours'] - $arr1['batteryRunHours']);
  $dgRunHours = abs($arr2['dgRunHours'] - $arr1['dgRunHours']);
  $mainsDailyRunHours = abs($arr2['mainsDailyRunHours'] - $arr1['mainsDailyRunHours']);

  $startDate = strtotime($arr1['insertedAt']);
  $endDate = strtotime($arr2['insertedAt']);

  $powerSupply = array(
    'startDate' => (string) $startDate,
    'endDate' => (string) $endDate,
    'mainsRunHours' => (string) $mainsRunHours,
    'batteryRunHours' => (string) $batteryRunHours,
    'dgRunHours' => (string) $dgRunHours,
    'mainsDailyRunHours' => (string) $mainsDailyRunHours,
  );

  // print_r($powerSupply);
  return settingOffset($powerSupply, $load);
  // return $powerSupply;
}

function settingOffset($powerSupply, $load)
{
  // print_r($powerSupply);
  extract($powerSupply);

  if (intval($startDate) === 0 || intval($endDate) === 0)
    return $powerSupply;

  $timespan = intval($startDate) - intval($endDate);
  // echo "timespan: $timespan\r\n";

  $totalMinutes = abs(round($timespan / 60));

  // echo "totalMinutes: $totalMinutes\r\n";

  $mainsRunHours = intval($mainsRunHours);
  $dgRunHours = intval($dgRunHours);
  $batteryRunHours = intval($batteryRunHours);

  $totalCalculated = $mainsRunHours + $dgRunHours + $batteryRunHours;
  // echo "totalCalculated: $totalCalculated\r\n";

  $offset = $totalMinutes - $totalCalculated;
  // echo "offset: $offset, load: $load\r\n";

  if ($offset === 0) {
    return $powerSupply;
  }

  if ($offset > 0) {
    # positive case
    // if ($mainsRunHours > 0) {
    //   $mainsRunHours += $offset;
    // } else if ($batteryRunHours > 0) {
    //   $batteryRunHours += $offset;
    // } else {
    //   $dgRunHours += $offset;
    // }

    if ($load === "mains") {
      $mainsRunHours += $offset;
    } else if ($load === "battery") {
      $batteryRunHours += $offset;
    } else {
      $dgRunHours += $offset;
    }
  } else {
    # negative case
    $isSet = false;

    // if ($isSet && $mainsRunHours > 0) {
    //   if ($mainsRunHours + $offset > 0) {
    //     $mainsRunHours += $offset;
    //     $isSet = true;
    //   }
    // }

    // if ($isSet && $batteryRunHours > 0) {
    //   if ($batteryRunHours + $offset > 0) {
    //     $batteryRunHours += $offset;
    //     $isSet = true;
    //   }
    // }

    // if ($isSet && $dgRunHours > 0) {
    //   if ($dgRunHours + $offset > 0) {
    //     $dgRunHours += $offset;
    //     $isSet = true;
    //   }
    // }

    // if ($isSet && $load === "mains") {
    //   if ($mainsRunHours + $offset > 0) {
    //     $mainsRunHours += $offset;
    //     $isSet = true;
    //   }
    // }

    // if ($isSet && $load === "battery") {
    //   if ($batteryRunHours + $offset > 0) {
    //     $batteryRunHours += $offset;
    //     $isSet = true;
    //   }
    // }

    // if ($isSet && $load === "dg") {
    //   if ($dgRunHours + $offset > 0) {
    //     $dgRunHours += $offset;
    //     $isSet = true;
    //   }
    // }

    if (!$isSet && ($load === "mains" || $load === "battery")) {
      if ($dgRunHours + $offset > 0) {
        $dgRunHours += $offset;
        $isSet = true;
      }
    }

    if (!$isSet && $load === "dg") {
      if ($mainsRunHours + $offset > 0) {
        $mainsRunHours += $offset;
        $isSet = true;
      }
    }
  }

  return array(
    'startDate' => (string) $startDate,
    'endDate' => (string) $endDate,
    'mainsRunHours' => (string) $mainsRunHours,
    'batteryRunHours' => (string) $batteryRunHours,
    'dgRunHours' => (string) $dgRunHours,
    'mainsDailyRunHours' => (string) $mainsDailyRunHours,
  );
}

function GetDeviceRecordFromId($iProtectId)
{
  if (isnull($iProtectId))
    return null;

  $mysqli = dbContext();

  $sql = "SELECT
                iProtectId,
                mainsRunHours,
                batteryRunHours,
                dgRunHours,
                mainsDailyRunHours,
                insertedAt,
                secondaryPower2,
                secondaryPower3reserved
		    FROM iProtect
            WHERE iProtectId = $iProtectId";

  // echo "sql: $sql\r\n";

  $result = $mysqli->query($sql);

  if (!$result) {
    echo ("Error GetDeviceRecordFromId: " . $mysqli->error);
    return false;
  }

  $data = $result->fetch_row();

  // var_dump($data);

  $array = array(
    'iProtectId' => $data[0],
    'mainsRunHours' => $data[1],
    'batteryRunHours' => $data[2],
    'dgRunHours' => $data[3],
    'mainsDailyRunHours' => $data[4],
    'insertedAt' => $data[5],
    'secondaryPower2' => $data[6],
    'secondaryPower3reserved' => $data[7],
  );

  $result->close();
  mysqli_close($mysqli);

  return $array;
}

function selectEnergyLoss($siteRid, $startDate, $endDate)
{
  $sql =
    "SELECT SUM(batteryKwh), SUM(batteryKwh1) 
  FROM sitesEnergyDetails 
  WHERE siteRid = '$siteRid' AND insertedAt BETWEEN '$startDate' AND '$endDate'";

  $mysqli = dbContext();
  $result = $mysqli->query($sql);

  if (!$result) {
    echo ("Error selectEnergyLoss: " . $mysqli->error);
    return false;
  }

  $data = [];
  if ($row = $result->fetch_row()) {
    $data = [
      'batteryKwh' => $row[0],
      'batteryKwh1' => $row[1],
    ];
  }

  $result->close();
  mysqli_close($mysqli);
  return $data;
}
