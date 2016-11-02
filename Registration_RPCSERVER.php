<?php
require_once __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$connection = new AMQPStreamConnection('localhost', 5672, 'admin', 'asdf');
$channel = $connection->channel();
$channel->queue_declare('rpc_queueR', false, false, false, false);

function fib($JsonCred) {
/**	if ($n == 0)
		return 0;
	if ($n == 1)
		return 1;
	return fib($n-1) + fib($n-2);
**/

//echo "/n/n".(string)$JsonCred."/n/n";


$json=json_decode($JsonCred,true);

echo $json["Username"];
$username= $json["Username"];
$password=$json["Password"];
$FirstName=$json["FirstName"];
$LastName=$json["LastName"];
$Email=$json["Email"];

$con =new  mysqli("localhost","root","toor");

	if ($con->connect_error) {

	die("<html>Connection failed:".$con->connect_error."</html>");

}

mysqli_select_db($con,"UserInfo");

 //$con =new  mysqli("localhost","root","toor");
   //     if ($con->connect_error) {
     //   die("<html>Connection failed:".$con->connect_error."</html>");
//}
//mysqli_select_db($con,"UserLogin");
//echo $username;

$id;

$sql = "INSERT INTO RegistrationPage VALUES('$username','$FirstName','$LastName','$Email','$password');";


	if(mysqli_query($con,$sql)){
	$id=mysqli_insert_id();	
	return 1;
}
	else {

		return "<html>Error: ".$sql."<br>".mysqli_error($con)."</html>";
}

//echo $n;


if (is_array($JsonCred) || is_object($JsonCred))
{
    foreach ($JsonCred as $value)
    {
       echo "array value:\" $yarrr".PHP_EOL;
 
    }
    }

	return($JsonCred);
}
echo " [x] Awaiting RPC requests\n";
$callback = function($req) {
	$n = ($req->body);
//	$credentials = json_decode($req->body);

//        echo " [.] recieved(",(string) fib($n), ")\n";
	$msg = new AMQPMessage(
		(string)fib($n),
		array('correlation_id' => $req->get('correlation_id'))
		);	

	$req->delivery_info['channel']->basic_publish(
		$msg, '', $req->get('reply_to'));
	$req->delivery_info['channel']->basic_ack(
		$req->delivery_info['delivery_tag']);
};
$channel->basic_qos(null, 1, null);
$channel->basic_consume('rpc_queueR', '', false, false, false, false, $callback);
while(count($channel->callbacks)) {
    $channel->wait();
}
$channel->close();
$connection->close();
?>
