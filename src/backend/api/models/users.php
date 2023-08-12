
<?php

require_once $db;
require_once $pathGeneralMethods;

function validateUser($userName, $password)
{
  $userName = !isnull($userName) ? "'$userName'" : 'NULL'; //string
  $password = !isnull($password) ? "'$password'" : 'NULL'; //string

  $sql =
    "SELECT
    userId,
    users.customerId,
    users.name,
    users.mobileNumber,
    password,
    access,
    customers.name
  FROM users
  JOIN customers ON customers.customerId = users.customerId
  WHERE mobileNumber = $userName
  AND password = $password";

  $mysqli = dbContext();
  $result = $mysqli->query($sql);

  $dataTable = array();

  if (!$result) {
    // echo ("Error validateUser: " . $mysqli->error);
    return $dataTable;
  }

  while ($row = $result->fetch_row()) {
    $dataRow = array(
      "userId" => $row[0],
      "customerId" => $row[1],
      "name" => $row[2],
      "mobileNumber" => $row[3],
      "password" => $row[4],
      "access" => $row[5],
      "customerName" => $row[6],
    );

    array_push($dataTable, $dataRow);
  }

  $mysqli->close();
  return $dataTable;
}

function insertUpdateTokens($userId, $token)
{
  //Check if token already exists.
  if (hasToken($userId, $token)) {
    return true;
  }

  if (!$token || $token == 'null') {
    return false;
  }

  //query to insert record
  $sql =
    "INSERT INTO pushNotificationsTokens
        SET userId = $userId,
            token = '$token',
            insertedAt = NOW()";

  $mysqli = dbContext();
  $result = $mysqli->query($sql);

  if (!$result) {
    // echo ("Error insertUpdateTokens: " . $mysqli->error);
    return false;
  }

  $mysqli->close();
  return true;
}

function hasToken($userId, $token)
{
  //Check if token already exists.
  $sql =
    "SELECT
            userId
        FROM    pushNotificationsTokens
        WHERE   token = '$token'
        AND     userId = $userId";

  //echo $sql;
  // prepare query statement

  $mysqli = dbContext();
  $result = $mysqli->query($sql);

  if (!$result) {
    // echo ("Error hasToken: " . $mysqli->error);
    return false;
  }

  $mysqli->close();
  return mysqli_num_rows($result) > 0;
}

function selectUsers($userId, $customerId, $pageNumber = 1, $pageSize = 5)
{

  // echo "pageNumber: $pageNumber\r\n";
  // echo "pageSize: $pageSize\r\n";
  $position = null;
  $userId = !isnull($userId) ? $userId : 'NULL'; //number
  $customerId = !isnull($customerId) ? $customerId : 'NULL'; //number
  $position = !isnull($position) ? $position : 'NULL'; //number

  $pageNumber = !isnull($pageNumber) ? $pageNumber : 1; //number
  $pageSize = !isnull($pageSize) ? $pageSize : 5; //number
  $offset = $pageSize * ($pageNumber - 1);

  $sql =
    "SELECT
            userId,
            customerId,
            name,
            mobileNumber,
            password,
            emailActivityFlags,
            dialerActivityFlags,
            smsActivityFlags,
            dialerHost,
            dialerPort,
            emailFrequency,
            scheduledTime,
            emailTo,
            emailCC,
            sendEmail
        FROM
            users

        WHERE isDeleted = 0
        AND customerId = IFNULL($customerId, customerId)
        AND userId = IFNULL($userId, userId)
        AND position = IFNULL($position, position)

        ORDER BY position
        LIMIT $offset, $pageSize ";

  $sqlCount = "SELECT
                    COUNT(userId)
                FROM
                    users
                WHERE isDeleted = 0
                AND customerId = IFNULL($customerId, customerId)
                AND userId = IFNULL($userId, userId)
                AND position = IFNULL($position, position)";

  // echo $sql . "\r\n";
  $mysqli = dbContext();
  $result = $mysqli->query($sql);
  $resultCount = $mysqli->query($sqlCount);

  $dataTable = array();

  if (!$result) {
    // echo ("Error selectUsers: " . $mysqli->error);
    return $dataTable;
  }

  if (!$resultCount) {
    // echo ("Error selectIProtect: " . $mysqli->error);
    return $dataTable;
  }

  $count = $resultCount->fetch_row();

  while ($row = $result->fetch_row()) {
    $dataRow = array(
      'userId' => $row[0],
      'customerId' => $row[1],
      'name' => $row[2],
      'mobileNumber' => $row[3],
      'password' => $row[4],
      'emailActivityFlags' => $row[5],
      'dialerActivityFlags' => $row[6],
      'smsActivityFlags' => $row[7],
      'dialerHost' => $row[8],
      'dialerPort' => $row[9],
      'emailFrequency' => $row[10],
      'scheduledTime' => $row[11],
      'emailTo' => $row[12],
      'emailCC' => $row[13],
      'sendEmail' => boolval($row[14]),
    );

    array_push($dataTable, $dataRow);
  }

  $mysqli->close();
  return array(
    'data' => $dataTable,
    'itemsCount' => intval($count[0]),
  );
}

function selectUsersForMail()
{
  $sql =
    "SELECT
            userId,
            customerId,
            name,
            mobileNumber,
            password,
            emailActivityFlags,
            dialerActivityFlags,
            smsActivityFlags,
            dialerHost,
            dialerPort,
            emailFrequency,
            scheduledTime,
            emailTo,
            emailCC
        FROM
            users

        WHERE isDeleted = 0
        AND sendEmail = 1";

  // echo $sql . "\r\n";
  $mysqli = dbContext();
  $result = $mysqli->query($sql);

  if (!$result) {
    // echo ("Error selectUsers: " . $mysqli->error);
    return [];
  }

  $data = [];
  while ($row = $result->fetch_row()) {
    $data[] = [
      'userId' => $row[0],
      'customerId' => $row[1],
      'name' => $row[2],
      'mobileNumber' => $row[3],
      'password' => $row[4],
      'emailActivityFlags' => $row[5],
      'dialerActivityFlags' => $row[6],
      'smsActivityFlags' => $row[7],
      'dialerHost' => $row[8],
      'dialerPort' => $row[9],
      'emailFrequency' => $row[10],
      'scheduledTime' => $row[11],
      'emailTo' => $row[12],
      'emailCC' => $row[13],
    ];
  }

  $mysqli->close();
  return $data;
}

function insertUpdateUser($inType, $userId, $user, $executedBy)
{
  $user = $user ? handleNull($user) : $user;
  extract($user);

  $sql = null;
  switch ($inType) {
    case 0:
      // insert
      $sql =
        "INSERT INTO users
          SET customerId = $customerId,
              position = 0,
              name = $name,
              mobileNumber = $mobileNumber,
              password = $password,
              emailTo = $emailTo,
              emailCC = $emailCC,
              access = '0',
              emailActivityFlags = $emailActivityFlags,
              dialerActivityFlags = $dialerActivityFlags,
              smsActivityFlags = $smsActivityFlags,
              dialerHost = $dialerHost,
              dialerPort = $dialerPort,
              emailFrequency = $emailFrequency,
              sendEmail = $sendEmail,
              scheduledTime = $scheduledTime,
              insertedBy = $executedBy,
              insertedAt = NOW()";
      break;
    case 1:
      // update
      $sql =
        "UPDATE users
                SET customerId = $customerId,
                    position = 0,
                    name = $name,
                    mobileNumber = $mobileNumber,
                    password = $password,
                    emailTo = $emailTo,
                    emailCC = $emailCC,
                    access = '0',
                    emailActivityFlags = $emailActivityFlags,
                    dialerActivityFlags = $dialerActivityFlags,
                    smsActivityFlags = $smsActivityFlags,
                    dialerHost = $dialerHost,
                    dialerPort = $dialerPort,
                    emailFrequency = $emailFrequency,
                    scheduledTime = $scheduledTime,
                    sendEmail = $sendEmail,
                    updatedBy = $executedBy,
                    updatedAt = NOW()
                WHERE userId = $userId";
      break;
    case 2:
      //delete: set the isDeleted flag
      $sql =
        "UPDATE users
                SET isDeleted = 1,
                    updatedBy = $executedBy,
                    updatedAt = NOW()
                WHERE userId = $userId";
      break;
    default:
      // do nothing
  }

  // echo $sql . "\r\n";
  $mysqli = dbContext();
  $result = $mysqli->query($sql);

  if (!$result) {
    echo ("Error insertUpdateUser: " . $mysqli->error);
    return false;
  }

  $mysqli->close();
  return true;
}

function selectTokens($siteRid)
{
  // Phase 1: users are connected with sites directly for rights.
  $sql =
    "SELECT
            pushNotificationsTokens.token
        FROM pushNotificationsTokens
        JOIN siteUsers ON siteUsers.userId = pushNotificationsTokens.userId
        AND siteRid = $siteRid";

  // $sql = "SELECT
  //             pushNotificationsTokens.token
  //           FROM pushNotificationsTokens
  //           JOIN siteUsers ON siteUsers.userId = pushNotificationsTokens.userId
  //           WHERE access like '%,$siteRid,%'";

  /*ORDER BY      users.position ;*/

  // Phase 2: mapping sites with rights.
  // $sql = "SELECT    pushNotificationsTokens.token
  //           FROM      pushNotificationsTokens
  //           JOIN      siteUsers ON siteUsers.userId = pushNotificationsTokens.userId";
  //AND       siteUsers.access LIKE CASE WHEN type = 0 THEN CONCAT('%', CAST(SubCompany.CircleId as varchar), '%' ";

  // Pahse 3: mapping of cluster and sites with rights.
  // $sql = "SELECT    pushNotificationsTokens.token
  //           FROM      pushNotificationsTokens
  //           JOIN      sites ON sites.siteRid = $siteRid
  //           JOIN      siteUsers ON siteUsers.userId = pushNotificationsTokens.userId
  //           AND       (
  //             IFNULL(siteUsers.siteClusterIdGroup, '') LIKE CASE WHEN isCluster = 0
  //           ) ";

  //echo $sql;
  $mysqli = dbContext();
  $result = $mysqli->query($sql);

  $dataTable = array();

  if (!$result) {
    // echo ("Error selectTokens: " . $mysqli->error);
    return $dataTable;
  }

  if ($result) {
    while ($row = $result->fetch_row()) {
      $dataRow = array(
        "token" => $row[0],
      );

      array_push($dataTable, $dataRow);
    }
  }

  $mysqli->close();
  return $dataTable;
}
