
<?php

// response constants
define('success', 0);
define('noDataError', 1);
define('authError', 2);
define('parameterError', 3);
define('dbError', 4);
define('requiredError', 5);

function responseHandler($responseStatus, $action, $data, $config = null)
{
    $responseDescription = 'none';
    switch ($responseStatus) {
        case 0:
            $responseDescription = 'success';
            break;
        case 1:
            $responseDescription = 'no data';
            break;
        case 2:
            $responseDescription = 'credentials do not match';
            break;
        case 3:
            $responseDescription = 'invalid parameter';
            break;
        case 4:
            $responseDescription = 'database error';
            break;
        case 5:
            $responseDescription = 'required data';
            break;

        default:
            break;
    }

    $response = array(
        'response' => array(
            'responseStatus' => (string) $responseStatus,
            'responseDescription' => $responseDescription,
            'action' => $action,
            'data' => $data,
            'config' => $config,
        ),
    );

    if (!$response['response']['config']) {
        unset($response['response']['config']);
    }

    // var_dump($response);

    return $response;
}
?>