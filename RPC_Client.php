<?php
require_once __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;




class FibonacciRpcClient {
	private $connection;
	private $channel;
	private $callback_queue;
	private $response;
	private $corr_id;

	public function __construct() {

		$this->connection = new AMQPStreamConnection(
			'192.168.1.178', 5672, 'admin', 'asdf');

		$this->channel = $this->connection->channel();

		list($this->callback_queue, ,) = $this->channel->queue_declare(
			"", false, false, true, false);

		$this->channel->basic_consume(
			$this->callback_queue, '', false, false, false, false,
			array($this, 'on_response'));
	}

	public function on_response($rep) {
		if($rep->get('correlation_id') == $this->corr_id) {
			$this->response = $rep->body;
		}
	}




	public function call($n,$n1,$n2,$n3,$n4) {
		$this->response = null;

		$this->corr_id = uniqid();

		$k=$n." ".$n1." ".$n2." ".$n3." ".$n4;	

$UserName=$_GET["UserName"];
$FirstName=$_GET["FirstName"];
$LastName=$_GET["LastName"];
$Email=$_GET["Email"];
$Password=$_GET["Password"];

	$Creds= Array("Username"=>$UserName,"Password"=>$Password,"FirstName"=>$FirstName,"LastName"=>$LastName,"Email"=>$Email);

	$JsonCreds=json_encode($Creds);	
		$msg = new AMQPMessage(
			(string)$JsonCreds ,
			array('correlation_id' => $this->corr_id,
			      'reply_to' => $this->callback_queue)
			);

		$this->channel->basic_publish($msg, '', 'rpc_queue');

		while(!$this->response) {
			$this->channel->wait();
		}
		return ($this->response);
	}
};
$fibonacci_rpc = new FibonacciRpcClient();

$response = $fibonacci_rpc->call($UserName,$Password,$FirstName,$LastName,$Email);
//$response = $fibonacci_rpc->call($FirstName);
//$response = $fibonacci_rpc->call($LastName);
//$response = $fibonacci_rpc->call($Email);
//$response = $fibonacci_rpc->call($Password);

//echo "<html><br> [.] Got ", $response, "<br><br></html>\n";
if ($response == 1){
//echo $_SERVER['REQUEST_URI'];
header("location:registrationsuccesspage.html");
}

?>
