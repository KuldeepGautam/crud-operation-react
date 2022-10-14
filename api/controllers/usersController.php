<?php

require_once $pathUsers;

function verifyCredentials($username, $password, $token)
{
  $users = validateUser($username, $password);
  if (!$users) {
    http_response_code(400);
    return responseHandler(2, "verifyCredentials", array());
  }

  # user has been verified successfuly.

  if ($token) {
    if (!insertUpdateTokens($users[0]['userId'], $token)) {
      $error = array(
        "errorMessage" => "an unexpected error occurred",
      );

      http_response_code(400);
      return responseHandler(4, "verifyCredentials", $error);
    }
  }

  return responseHandler(0, "verifyCredentials", $users[0]);
}

// users crud
// get users
function getUsers($username, $password, $userId, $customerId, $pageNumber, $pageSize)
{
  // $credentials = validateAdmin($username, $password);
  // if (!$credentials) {
  //     http_response_code(404);
  //     return responseHandler(2, 'getUsers', array());
  // }

  $users = selectUsers($userId, $customerId, $pageNumber, $pageSize);
  $usersData = $users['data'];

  if (!$usersData) {
    http_response_code(404);
    return responseHandler(1, 'getUsers', array());
  }

  $config = array(
    'itemsCount' => $users['itemsCount'],
  );

  return responseHandler(0, 'getUsers', $usersData, $config);
}

// create user
function createUser($username, $password, $user)
{
  # validating admin
  $credentials = validateAdmin($username, $password);
  if (!$credentials) {
    http_response_code(404);
    return responseHandler(2, 'createUser', array());
  }

  # validating body of the request
  if (!$user) {
    http_response_code(400);
    return responseHandler(3, 'createUser', array());
  }

  $user = (array) $user;
  $errors = validatingUser($user);

  if ($errors) {
    http_response_code(400);
    return responseHandler(5, 'createUser', $errors);
  }

  if (!insertUpdateUser(0, null, $user, $credentials[0]['credentialId'])) {
    http_response_code(404);
    return responseHandler(4, 'createUser', array());
  }

  return responseHandler(0, 'createUser', $user);
}

// update user
function updateUser($username, $password, $userId, $user)
{
  # validating admin
  $credentials = validateAdmin($username, $password);
  if (!$credentials) {
    http_response_code(404);
    return responseHandler(2, 'updateUser', array());
  }

  # validating id and body of the request
  if (!trim($userId) || !$user) {
    http_response_code(404);
    return responseHandler(3, 'updateUser', array());
  }

  # validating data by id
  $users = selectUsers($userId, null, 1, 1);
  if (!$users) {
    http_response_code(404);
    return responseHandler(3, 'updateUser', array());
  }

  $user = (array) $user;
  unset($user['userId']);
  $errors = validatingUser($user);

  if ($errors) {
    http_response_code(404);
    return responseHandler(5, 'updateUser', $errors);
  }

  if (!insertUpdateUser(1, $userId, $user, $credentials[0]['credentialId'])) {
    http_response_code(404);
    return responseHandler(4, 'updateUser', array());
  }

  return responseHandler(0, 'updateUser', $user);
}

// delete user
function deleteUser($username, $password, $userId)
{
  # validating admin
  $credentials = validateAdmin($username, $password);
  if (!$credentials) {
    http_response_code(404);
    return responseHandler(2, 'deleteUser', array());
  }

  # validating id
  if (!trim($userId)) {
    http_response_code(404);
    return responseHandler(3, 'deleteUser', array());
  }

  # validating data by id
  $users = selectUsers($userId, null, 1, 1);

  if (!$users) {
    http_response_code(404);
    return responseHandler(3, 'deleteUser', array());
  }

  if (!insertUpdateUser(2, $userId, null, $credentials[0]['credentialId'])) {
    http_response_code(404);
    return responseHandler(4, 'deleteUser', array());
  }

  return responseHandler(0, 'deleteUser', array());
}

function validatingUser($body)
{
  extract($body);

  $errors = array();
  if (!array_key_exists('customerId', $body)) $errors[] = 'customerId is required';
  else if (isnull($customerId)) $errors[] = 'customerId is required';

  if (!array_key_exists('name', $body)) $errors[] = 'name is required';
  else if (isnull($name)) $errors[] = 'name is required';

  if (!array_key_exists('mobileNumber', $body)) $errors[] = 'mobileNumber is required';
  else if (isnull($mobileNumber)) $errors[] = 'mobileNumber is required';

  if (!array_key_exists('password', $body)) $errors[] = 'password is required';
  else if (isnull($password)) $errors[] = 'password is required';

  if (!array_key_exists('emailTo', $body)) $errors[] = 'emailTo is required';
  else if (isnull($emailTo)) $errors[] = 'emailTo is required';

  if (!array_key_exists('emailCC', $body)) $errors[] = 'emailCC is required';
  else if (isnull($emailCC)) $errors[] = 'emailCC is required';

  if (!array_key_exists('emailActivityFlags', $body)) $errors[] = 'emailActivityFlags is required';
  else if (isnull($emailActivityFlags)) $errors[] = 'emailActivityFlags is required';
  else if (!isBinary($emailActivityFlags)) $errors[] = 'Invalid emailActivityFlags';

  if (!array_key_exists('dialerActivityFlags', $body)) $errors[] = 'dialerActivityFlags is required';
  else if (isnull($dialerActivityFlags)) $errors[] = 'dialerActivityFlags is required';
  else if (!isBinary($dialerActivityFlags)) $errors[] = 'Invalid dialerActivityFlags';

  if (!array_key_exists('smsActivityFlags', $body)) $errors[] = 'smsActivityFlags is required';
  else if (isnull($smsActivityFlags)) $errors[] = 'smsActivityFlags is required';
  else if (!isBinary($smsActivityFlags)) $errors[] = 'Invalid smsActivityFlags';

  if (!array_key_exists('dialerHost', $body)) $errors[] = 'dialerHost is required';
  else if (isnull($dialerHost)) $errors[] = 'dialerHost is required';

  if (!array_key_exists('dialerPort', $body)) $errors[] = 'dialerPort is required';
  else if (isnull($dialerPort)) $errors[] = 'dialerPort is required';
  else if (!is_numeric($dialerPort)) $errors[] = 'Invalid dialerPort';

  if (!array_key_exists('emailFrequency', $body)) $errors[] = 'emailFrequency is required';
  else if (isnull($emailFrequency)) $errors[] = 'emailFrequency is required';
  else if (isEmailFrequency($emailFrequency)) $errors[] = ['error' => 'Invalid emailFrequency', 'values' => getEmailFrequency()];

  if (!array_key_exists('sendEmail', $body)) $errors[] = 'sendEmail is required';
  else if (!is_bool($sendEmail)) $errors[] = 'Invalid sendEmail';

  if (!array_key_exists('scheduledTime', $body)) $errors[] = 'scheduledTime is required';
  else if (isnull($scheduledTime)) $errors[] = 'scheduledTime is required';
  else if (!is_numeric($scheduledTime)) $errors[] = 'Invalid scheduledTime';

  return $errors;
}
