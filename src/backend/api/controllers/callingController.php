<?php

// function calling($mobileNo)
// {
//     $command = 'ATD';
//     // $command = 'AT+';
//     $command .= "$mobileNo;";
//     // $command .= 'CPIN?';
//     $cmd = "echo \"$command\" > /dev/ttyS0";
//     //$cmd = "cat -v /dev/ttyS0";
//     // $cmd = "echo \"$command\" > /dev/ttyS2";
//     // echo $command;

//     // // $outputput = shell_exec('ls');
//     // //var_dump($outputput);

//     //chmod o+rw /dev/ttyS0
//     //echo shellExecute('stty -F /dev/ttyS0 9600', $output);
//     //echo shellExecute('stty -F /dev/ttyS0', $output);
//     $execute = shellExecute($cmd, $output) . "\r\n";
//     return array(
//         'command' => $cmd,
//         'output' => $output,
//         'execute' => $execute,
//     );
// }

function shellExecute($command, &$output = null)
{
    $description = array(
        1 => array("pipe", "w"),
        2 => array("pipe", "w"),
    );

    $proc = proc_open($command, $description, $pipes);

    $return = stream_get_contents($pipes[1]);
    $error = stream_get_contents($pipes[2]);

    fclose($pipes[1]);
    fclose($pipes[2]);

    $retVal = proc_close($proc);

    if (func_num_args() == 2) {
        $output = array($return, $error);
    }

    return $retVal;
}

function socketEmit($username, $body)
{
    $apiEndpoint = "https://ws.sisrtd.com/api/iProtect?username=$username";
    //$apiEndpoint = "http://192.168.0.132:5000/api/iProtect?username=$username";

    $headers = array(
        'Content-Type: application/json',
    );
    #Send Reponse To FireBase Server
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiEndpoint);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}

function calling($query)
{
    $apiEndpoint = "http://rtd.net.in/api/calling?$query";

    // echo "$apiEndpoint\r\n";

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $apiEndpoint);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    $output = curl_exec($ch);
    curl_close($ch);

    return json_decode($output);
}
