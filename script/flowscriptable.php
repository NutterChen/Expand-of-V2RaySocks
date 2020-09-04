<?php
    $subscriptionurl = 'https://'.$_SERVER['HTTP_HOST']."/modules/servers/V2raySocks/subscribe/flowscriptable.php?sid={$_GET['sid']}&token={$_GET['token']}";
    $flow_scriptable = file_get_contents(__DIR__ . "/flow.scriptable");
    $flow_scriptable = str_replace('subscriptionurl', $subscriptionurl, $flow_scriptable);
    $flow_scriptable = str_replace('airportname', $_SERVER['HTTP_HOST'], $flow_scriptable);
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="flow.scriptable"');
    echo $flow_scriptable; 
?>