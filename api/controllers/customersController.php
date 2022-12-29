<?php



function getCustomers($customerId, $pageNumber, $pageSize)
{
    // $credentials = validateAdmin($username, $password);
    // if (!$credentials) {
    //     http_response_code(404);
    //     return responseHandler(2, 'getCustomers', array());
    // }

    $customers = selectCustomers($customerId, $pageNumber, $pageSize);

    // print_r($customers);

    if (!$customers['data']) {
        http_response_code(404);
        return responseHandler(1, 'getCustomers', array());
    }

    $config = array(
        'itemsCount' => $customers['itemsCount'],
    );

    return responseHandler(0, 'getCustomers', $customers['data'], $config);
}

// create customer
function createCustomer($customer)
{
    # validating admin
    // $credentials = validateAdmin($username, $password);
    // if (!$credentials) {
    //     http_response_code(404);
    //     return responseHandler(2, 'createCustomer', array());
    // }

    # validating body of the request
    if (!$customer) {
        http_response_code(404);
        return responseHandler(3, 'createCustomer', array());
    }

    $customer = (array) $customer;
    $errors = validatingCustomer($customer);

    if ($errors) {
        http_response_code(404);
        return responseHandler(5, 'createCustomer', $errors);
    }

    // create a new customer
    if (!insertUpdateCustomer(0, null, $customer, 1)) {
        http_response_code(404);
        return responseHandler(4, 'createCustomer', array());
    }

    return responseHandler(0, 'createCustomer', $customer);
}

// update customer
function updateCustomer($customerId, $customer)
{
    # validating admin
    // $credentials = validateAdmin($username, $password);
    // if (!$credentials) {
    //     http_response_code(404);
    //     return responseHandler(2, 'createCustomer', array());
    // }

    # validating id and body of the request
    if (!$customerId || !$customer) {
        http_response_code(404);
        return responseHandler(3, 'createCustomer', array());
    }

    # validating data by id
    $customers = selectCustomers($customerId);

    if (!$customers['data']) {
        http_response_code(404);
        return responseHandler(3, 'createCustomer', array());
    }

    $customer = (array) $customer;
    unset($customer['customerId']);
    $errors = validatingCustomer($customer);

    if ($errors) {
        http_response_code(404);
        return responseHandler(5, 'createCustomer', $errors);
    }

    if (!insertUpdateCustomer(1, $customerId, $customer, 1)) {
        http_response_code(404);
        return responseHandler(4, 'createCustomer', array());
    }

    return responseHandler(0, 'updateCustomer', $customer);
}

// delete customer
function deleteCustomer($customerId)
{

    echo "DEBUG\r\n";
    # validating admin
    // $credentials = validateAdmin($username, $password);
    // if (!$credentials) {
    //     http_response_code(404);
    //     return responseHandler(2, 'deleteCustomer', array());
    // }

    # validating id
    if (!trim($customerId)) {
        http_response_code(404);
        return responseHandler(3, 'deleteCustomer', array());
    }

    # validating data by id
    $customers = selectCustomers($customerId);

    if (!$customers) {
        http_response_code(404);
        return responseHandler(3, 'deleteCustomer', array());
    }

    if (!insertUpdateCustomer(2, $customerId, null, 1)) {
        http_response_code(404);
        return responseHandler(4, 'deleteCustomer', array());
    }

    return responseHandler(0, 'deleteCustomer', array());
}

function validatingCustomer($body)
{
    extract($body);

    $errors = array();
    if (isnull($name)) {
        $errors[] = 'name';
    }

    if (isnull($email)) {
        $errors[] = 'email';
    }
    if (isnull($mobileNo)) {
        $errors[] = 'mobileNo';
    }

    if (isnull($address)) {
        $errors[] = 'address';
    }

    return $errors;
}
