
<?php

require_once $db;

function validateCredentials($username, $password)
{
    $sql =
        "SELECT
            credentialId
        FROM credentials
        WHERE username = '$username'
            AND password = '$password'";

    //echo $sql;
    // prepare query statement

    $mysqli = dbContext();
    $result = $mysqli->query($sql);

    $dataTable = array();

    if (!$result) {
        // echo ("Error validateCredentials: " . $mysqli->error);
        return $dataTable;
    }

    while ($row = $result->fetch_row()) {
        $dataRow = array(
            'credentialId' => $row[0],
        );

        array_push($dataTable, $dataRow);
    }

    $mysqli->close();
    return $dataTable;
}

function validateAdmin($username, $password)
{
    return validateCredentials($username, $password);
}
