<?php
require_once __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$connection = new AMQPStreamConnection('localhost', 5672, 'admin', 'asdf');
$channel = $connection->channel();

$channel->queue_declare('notifications', false, false, false, false);



function  DeleteNotifications($userName,$gameID){
echo "UserName is $userName".PHP_EOL;
$con =new  mysqli("192.168.84.102","vatsal","toor");
        if ($con->connect_error) {
        die("Connection failed:".$con->connect_error);
}
mysqli_select_db($con,"Soccer");

$DeleteGames="UPDATE NOTIFICATIONS SET Acknowledged='Yes' WHERE UserName='$userName' AND NotificationID='$gameID'";

$r=mysqli_query($con,$DeleteGames) or die ("Error78: ".mysqli_error($con));


return true;
}

function notifications($userName){
echo "UserName is $userName".PHP_EOL;
$con =new  mysqli("192.168.84.102","vatsal","toor");
        if ($con->connect_error) {
        die("Connection failed:".$con->connect_error);
}
mysqli_select_db($con,"Soccer");

$GetGames="SELECT NotificationID,GameDate,GameStatus,homeTeamName,awayTeamName  FROM NOTIFICATIONS WHERE UserName= '$userName' AND Acknowledged='No'";

$r=mysqli_query($con,$GetGames) or die ("Error78: ".mysqli_error($con));

while ($row=mysqli_fetch_assoc($r)){
$temp[]=$row;
}


$cont=json_encode($temp);
return $cont;
}

echo " [x] Awaiting RPC requests\n";
$callback = function($req) {
	$n = ($req->body);
	
$outputTable;

$fin=json_decode($n);

if (count($fin) == 2){

var_dump($fin);
$outputTable=notifications($fin[1]);

}
else {

var_dump($fin);
$outputTable=DeleteNotifications($fin[1],$fin[2]);

}


	$msg = new AMQPMessage(
		(string) $outputTable,
		array('correlation_id' => $req->get('correlation_id'))
		);
	$req->delivery_info['channel']->basic_publish(
		$msg, '', $req->get('reply_to'));
	$req->delivery_info['channel']->basic_ack(
		$req->delivery_info['delivery_tag']);
};
$channel->basic_qos(null, 1, null);
$channel->basic_consume('notifications', '', false, false, false, false, $callback);
while(count($channel->callbacks)) {
    $channel->wait();
}
$channel->close();
$connection->close();
?>

?>
