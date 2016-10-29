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
			'192.168.43.167', 5672, 'admin', 'knp33');
		$this->channel = $this->connection->channel();
		list($this->callback_queue, ,) = $this->channel->queue_declare(
			"", false, false, true, false);
		$this->channel->basic_consume(
			$this->callback_queue, '', false, false, false, false,
			array($this, 'on_response'));
 
        //$fixtures = json_decode($response,TRUE)

	}

	public function on_response($rep) {
		if($rep->get('correlation_id') == $this->corr_id) {
			$this->response = $rep->body;
		}
	}


	public function call($n) {
                //echo $n;
		$this->response = null;
		$this->corr_id = uniqid();
		$msg = new AMQPMessage(
			(string) $n,
			array('correlation_id' => $this->corr_id,
			      'reply_to' => $this->callback_queue)
			);
		$this->channel->basic_publish($msg, '', 'rpc_queue');
		while(!$this->response) {
			$this->channel->wait();
		}
		return intval($this->response);
	}

	public function APIdata0(){

         $uri = 'http://api.football-data.org/v1/competitions/426/leagueTable';
         $reqPrefs['http']['method'] = 'GET';
         $reqPrefs['http']['header'] = 'X-Auth-Token: d65835349a984abcb4d4e66cb66af5f1';
         $stream_context = stream_context_create($reqPrefs);
	 
          $content= file_get_contents($uri, false, $stream_context);
          json_encode($content);
         return $content;
}

	public function APIdata1(){


         $uri = 'http://api.football-data.org/v1/competitions/430/leagueTable';
         $reqPrefs['http']['method'] = 'GET';
         $reqPrefs['http']['header'] = 'X-Auth-Token: d65835349a984abcb4d4e66cb66af5f1';
         $stream_context = stream_context_create($reqPrefs);
          
          $content= file_get_contents($uri, false, $stream_context);
          json_encode($content);
         return $content;
	
}


public function APIdata2(){


         $uri = 'http://api.football-data.org/v1/competitions/434/leagueTable';
         $reqPrefs['http']['method'] = 'GET';
         $reqPrefs['http']['header'] = 'X-Auth-Token: d65835349a984abcb4d4e66cb66af5f1';
         $stream_context = stream_context_create($reqPrefs);
	
          $content = file_get_contents($uri, false, $stream_context);
          json_encode($content);
         return $content;

        
}



public function APIdata3(){


         $uri = 'http://api.football-data.org/v1/competitions/436/leagueTable';
         $reqPrefs['http']['method'] = 'GET';
         $reqPrefs['http']['header'] = 'X-Auth-Token: d65835349a984abcb4d4e66cb66af5f1';
         $stream_context = stream_context_create($reqPrefs);	
          $content = file_get_contents($uri, false, $stream_context);
          json_encode($content);
         return  $content;

        
}


public function APIdata4(){


         $uri = 'http://api.football-data.org/v1/competitions/438/leagueTable';
         $reqPrefs['http']['method'] = 'GET';
         $reqPrefs['http']['header'] = 'X-Auth-Token: d65835349a984abcb4d4e66cb66af5f1';
         $stream_context = stream_context_create($reqPrefs);
	  
          $content = file_get_contents($uri, false, $stream_context);
          json_encode($content);
         return $content;

        
}

public function APIdataFixture0(){
 	
 

}

}


//echo "Hello";
$fibonacci_rpc = new FibonacciRpcClient();

$PLdata= $fibonacci_rpc->APIdata0();
$response1 = $fibonacci_rpc->call($PLdata);



if ($response1 == 1 ) {

echo "1st one";
$fibonacci_rpc1 = new FibonacciRpcClient();
$PLdata1= $fibonacci_rpc1->APIdata1();
$response2 = $fibonacci_rpc1->call($PLdata1);

      if ($response2==1){

 	$fibonacci_rpc2 = new FibonacciRpcClient();
	$PLdata2= $fibonacci_rpc2->APIdata2();
	$response3 = $fibonacci_rpc2->call($PLdata2);  


		if ($response3 == 1){

		
			$fibonacci_rpc3 = new FibonacciRpcClient();
			$PLdata3=$fibonacci_rpc3->APIdata3();
			$response4 = $fibonacci_rpc3->call($PLdata3);

			
			if ($response4 == 1){

				
					$fibonacci_rpc4 = new FibonacciRpcClient();
					$PLdata4= $fibonacci_rpc4->APIdata4();
					$response5 = $fibonacci_rpc4->call($PLdata4);

			if($response5 == 1){

 					echo "ALL DONE!!!";
}

}

}   
}


}

else {
echo "PROBLEM";

}

























echo " [.] Got ". $response1."\n";






?>