
<?php

require_once $db;
require_once $pathGeneralMethods;

function selectCustomers($customerId, $pageNumber = 1, $pageSize = 100)
{
    $customerId = !isnull($customerId) ? $customerId : 'NULL'; //number

    $pageNumber = !isnull($pageNumber) ? $pageNumber : 1; //number
    $pageSize = !isnull($pageSize) ? $pageSize : 100; //number
    $offset = $pageSize * ($pageNumber - 1);

    $sql =
        "SELECT
            customerId,
            name,
            mobileNo,
            email,
            address
        FROM customers
        WHERE isDeleted = 0
        AND customerId = IFNULL($customerId, customerId)
        ORDER BY customerId DESC
        LIMIT $offset, $pageSize ";

    $sqlCount = "SELECT
                    COUNT(customerId)
                    FROM customers
        WHERE isDeleted = 0
        AND customerId = IFNULL($customerId, customerId)";

    // echo $sql;
    $mysqli = dbContext();
    $result = $mysqli->query($sql);
    $resultCount = $mysqli->query($sqlCount);

    $dataTable = array();

    if (!$result) {
        echo ("Error selectCustomers: " . $mysqli->error);
        return $dataTable;
    }

    if (!$resultCount) {
        // echo ("Error selectIProtect: " . $mysqli->error);
        return $dataTable;
    }

    $count = $resultCount->fetch_row();

    while ($row = $result->fetch_row()) {
        $dataRow = array(
            'customerId' => $row[0],
            'name' => $row[1],
            'mobileNo' => $row[2],
            'email' => $row[3],
            'address' => $row[4],
        );

        array_push($dataTable, $dataRow);
    }

    $mysqli->close();
    return array(
        'data' => $dataTable,
        'itemsCount' => intval($count[0]),
    );
}

function insertUpdateCustomer($inType, $customerId, $customer, $executedBy)
{
    //print_r($customer);
    extract($customer);

    $sql = null;
    switch ($inType) {
        case 0:
            // insert
            $sql =
                "INSERT INTO customers
                SET name = '$name',
                    mobileNo = '$mobileNo',
                    email = '$email',
                    address = '$address',
                    insertedBy = $executedBy,
                    insertedAt = NOW()";
            break;
        case 1:
            // update
            $sql =
                "UPDATE customers
                SET name = '$name',
                    mobileNo = '$mobileNo',
                    address = '$address',
                    updatedBy = $executedBy,
                    updatedAt = NOW()
                WHERE customerId = $customerId";
            break;
        case 2:
            //delete: set the isDeleted flag
            $sql =
                "UPDATE customers
                SET isDeleted = 1,
                    updatedBy = $executedBy,
                    updatedAt = NOW()
                WHERE customerId = $customerId";
            break;
        default:
            // do nothing
    }

    //echo $sql;
    //echo $sql;
    $mysqli = dbContext();
    $result = $mysqli->query($sql);

    if (!$result) {
        echo ("Error insertUpdateCustomer: " . $mysqli->error);
        return false;
    }

    $mysqli->close();
    return true;
}
