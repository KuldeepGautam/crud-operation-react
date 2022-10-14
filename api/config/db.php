<?php

function dbContext()
{
  // TODO: change the db user while deployment.
  // $mysql_hostname = "i-protectdb.c8nfiefqxfow.ap-south-1.rds.amazonaws.com";
  // $mysql_user = "dbadmin";
  // $mysql_password = "$1protect";
  // $mysql_database = "iprotect_db";
  // $mysql_database = "iprotectdev_dialer";

  $mysql_hostname = "203.122.34.206";
  $mysql_user = "admin";
  $mysql_password = "admin123";
  $mysql_database = "react_crud";

  $mysqli = new mysqli("$mysql_hostname", "$mysql_user", "$mysql_password", "$mysql_database");
  if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
  }

  return $mysqli;
}
