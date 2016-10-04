<?php
require_once __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$connection = new AMQPStreamConnection('localhost', 5672, 'admin', 'asdf');
$channel = $connection->channel();
$channel->queue_declare('rpc_queue', false, false, false, false);

function fib($JsonCred) {
/**	if ($n == 0)
		return 0;
	if ($n == 1)
		return 1;
	return fib($n-1) + fib($n-2);
**/
$json=json_decode($JsonCred,true);
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


 $con =new  mysqli("localhost","root","toor");

        if ($con->connect_error) {

        die("<html>Connection failed:".$con->connect_error."</html>");

}
mysqli_select_db($con,"UserInfo");


$sql = "INSERT INTO RegistrationPage VALUES('$username','$FirstName','$LastName','$Email','$password');";

//$sql1=mysqli_query($con,$sql);

	if(mysqli_query($con,$sql)){

		return true/**"<html>

<link rel='stylesheet' href='registrationsuccesspage.css'/>
<body>

<div id=login>
<a href='registration.html'>Logout</a>
</div>

<br><br>
<div id=home>
<a href='index.html'>
<img alt='home' src='logo.jpg' width='150' height='100'>
</a>
</div>

<div id =zlatan>
<a href='zlatan.jpeg'>
<img alt='zlat' src='zlatan.jpeg' width='350' height='200'>
</a>
</div>

<center><h1> Registration Successful!!!</h1></center>


<br><br>
<div id=ligue1>
<h1> Ligue 1</h1>
<a href='ligue.html'>
<img alt='french' src='frenchligue.jpg' width='300' height='300'>

</a>
</div>

<div id=premier>
<h1> Premier League</h1>
<a href='premier.html'>
<img alt='england' src='premier.jpg' width='300' height='300'>

</a>
</div>

<br><br>
<br><br>
<div id=spain>
<h1> La Liga</h1>
<a href='laliga.html'>
<img alt='spain' src='spain.png' width='300' height='300'>

</a>
</div>

<div id=germany>
<h1> BundesLiga</h1>
<a href='german.html'>
<img alt='german' src='german.png' width='300' height='300'>

</a>
</div>

<br><br>
<br><br>
<div id=champion>
<h1> Champion League</h1>
<a href='champion.html'>
<img alt='champion' src='champion.jpg' width='300' height='300'>

</a>
</div>

<div id=italian>
<h1> Serie A</h1>
<a href='italian.html'>
<img alt='italian' src='italian-league.jpg' width='300' height='300'>

</a>
</div>
</p>


</body>


 </html>"**/;
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
	$credentials = json_decode($req->body);

//	echo " [.] recieved(",(string) fib($n), ")\n";
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
$channel->basic_consume('rpc_queue', '', false, false, false, false, $callback);
while(count($channel->callbacks)) {
    $channel->wait();
}
$channel->close();
$connection->close();
?>
