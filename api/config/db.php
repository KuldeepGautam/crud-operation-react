<?php

function dbContext()
{
  $mysql_hostname = "sisrtd.com";
  $mysql_user = "rtdnetin_crud_db";
  $mysql_password = "crud_db";
  $mysql_database = "rtdnetin_crud_db";

  $mysqli = new mysqli("$mysql_hostname", "$mysql_user", "$mysql_password", "$mysql_database");
  if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
  }

  return $mysqli;
}
