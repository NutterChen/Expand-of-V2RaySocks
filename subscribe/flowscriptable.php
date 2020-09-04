<?php
require(dirname(dirname(dirname(dirname(dirname(__FILE__))))).'/init.php');
use WHMCS\Database\Capsule;
if(isset($_GET['sid']) && isset($_GET['token'])){
    $sid = $_GET['sid'];
    $token = $_GET['token'];
    $service = \WHMCS\Database\Capsule::table('tblhosting')->where('id', $sid)->where('username', $token)->first();
    if (empty($service)){
        die('Unisset or Uncorrect Token');
    }
    if ($service->domainstatus != 'Active' ) {
        die('Not Active');
    }
    $package = Capsule::table('tblproducts')->where('id', $service->packageid)->first();
    $server = Capsule::table('tblservers')->where('id', $service->server)->first();

    $dbhost = $server->ipaddress ? $server->ipaddress : 'localhost';
    $dbname = $package->configoption1;
    $dbuser = $server->username;
    $dbpass = decrypt($server->password);
    $db = new PDO('mysql:host=' . $dbhost . ';dbname=' . $dbname, $dbuser, $dbpass);
    $usage = $db->prepare('SELECT * FROM `user` WHERE `sid` = :sid');
    $usage->bindValue(':sid', $sid);
    $usage->execute();
    $usage = $usage->fetch();
    $servers = $package->configoption4;
    if($servers == ""){
        $servers = \WHMCS\Database\Capsule::table('tblservers')->where('id', $service->server)->get();
        $servers = V2raySocks_OS_QueryToArray($servers);
        $servers = $servers[0]['assignedips'];
    }
    $results = makeheader($service, $usage);
    header(makeheader($service, $usage));
    echo($results);
    }else{
    die('Invaild');
}

function makeheader($service, $usage){
    $useageleft = $usage["transfer_enable"] - $usage["u"] - $usage["d"];
    $expiredate = strtotime("$service->nextduedate");
    $SubscriptionUserinfo="Subscription-Userinfo: upload=".$usage["u"]."; download=".$usage["d"]."; total=".$usage["transfer_enable"]."; expire=".$expiredate.";";
    return $SubscriptionUserinfo;
}