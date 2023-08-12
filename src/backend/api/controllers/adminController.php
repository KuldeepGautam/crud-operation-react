<?php

// admin login
function verifyAdmin($username, $password)
{
    $credentials = validateAdmin($username, $password);
    if (!$credentials) {
        http_response_code(400);
        return 'Invalid username or password';
        // return responseHandler(2, "verifyAdmin", array());
    }

    return responseHandler(0, "verifyAdmin", $credentials[0]);
}
