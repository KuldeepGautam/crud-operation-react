<?php

function dbContext()
{
  $mysql_hostname = "sisrtd.com";
  $mysql_user = "rtdnetin_admin";
  $mysql_password = "System@321";
  $mysql_database = "rtdnetin_dbSysinfra";

  $mysqli = new mysqli("$mysql_hostname", "$mysql_user", "$mysql_password", "$mysql_database");
  if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
  }

  return $mysqli;
}

$urlParsed = parse_url($_SERVER['REQUEST_URI']);
$query = $urlParsed["query"];
$queryDecoded = urldecode($urlParsed["query"]);
$sql = "SELECT COUNT(*) FROM iProtect";

$mysqli = dbContext();
$result = $mysqli->query($sql);
$row = $result->fetch_row();
$count = $row[0];
echo "count $count\r\n";

$time = time();


$sql = "INSERT INTO dump
            SET data = '$queryDecoded',
                insertedAt = NOW()";

//echo $sql;
$mysqli = dbContext();
$result = $mysqli->query($sql);
$mysqli->close();
