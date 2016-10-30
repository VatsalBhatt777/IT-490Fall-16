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
			'localhost', 5672, 'admin', 'asdf');
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
		$this->channel->basic_publish($msg, '', 'top-scorer');
		while(!$this->response) {
			$this->channel->wait();
		}
		return intval($this->response);
	}

	public function APIdataTS0(){

         $uri = 'https://mmsoccer.herokuapp.com/scorers/EPL';
         $reqPrefs['http']['method'] = 'GET';
//         $reqPrefs['http']['header'] = 'X-Auth-Token: d65835349a984abcb4d4e66cb66af5f1';
         $stream_context = stream_context_create($reqPrefs);
	 
          $content= file_get_contents($uri, false, $stream_context);
           $UpContent=json_decode($content,true);
           $UpContent["Type"]="EPL-TopScorer";
         $UpContent1= json_encode($UpContent);
         echo  "Encoded Json: $UpContent1 \n\n\n";
          $test=json_decode($UpContent1,true);
         echo   "\n\n\nDecoded Json: \n";
            
          var_dump($test);

//         print_r( \n\n\n$test[5]);
         return $UpContent1;
}

	public function APIdataTS1(){


         $uri = 'https://mmsoccer.herokuapp.com/scorers/SLiga';
         $reqPrefs['http']['method'] = 'GET';
         $stream_context = stream_context_create($reqPrefs);
          
          $content= file_get_contents($uri, false, $stream_context);
          $UpContent=json_decode($content,true);
          $UpContent["Type"]="LaLiga-TopScorer";
          $UpContent1 =json_encode($UpContent);
         
          json_encode($content);
         return $UpContent1;
	
}


public function APIdataTS2(){


         $uri = 'https://mmsoccer.herokuapp.com/scorers/Fliga';
         $reqPrefs['http']['method'] = 'GET';
         $stream_context = stream_context_create($reqPrefs);
	
          $content = file_get_contents($uri, false, $stream_context);
          $UpContent=json_decode($content,true);
           $UpContent['Type']="Ligue1-TopScorer";
            $UpContent1=json_encode($UpContent);
          
         return $UpContent1;

        
}



public function APIdataTS3(){


         $uri = 'https://mmsoccer.herokuapp.com/scorers/SerieA';
         $reqPrefs['http']['method'] = 'GET';
         $stream_context = stream_context_create($reqPrefs);	
          $content = file_get_contents($uri, false, $stream_context);
          $UpContent=json_decode($content,true);
          $UpContent["Type"]="SerieA-TopScorer";
          $UpContent1=json_encode($UpContent);          
         return  $UpContent1;

        
}


public function APIdataTS4(){


         $uri = 'https://mmsoccer.herokuapp.com/scorers/BLiga';
         $reqPrefs['http']['method'] = 'GET';
         $stream_context = stream_context_create($reqPrefs);
         $content = file_get_contents($uri, false, $stream_context);
         $UpContent=json_decode($content,true);
         $UpContent["Type"]="Bundesliga-TopScorer";
	 $UpContent1=json_encode($UpContent); 
         return $UpContent1;

        
}


}


//echo "Hello";
$fibonacci_rpc = new FibonacciRpcClient();

$PLdata= $fibonacci_rpc->APIdataTS0();

$response1 = $fibonacci_rpc->call($PLdata);



if ($response1 == 1 ) {

echo "1st one";
$fibonacci_rpc1 = new FibonacciRpcClient();
$PLdata1= $fibonacci_rpc1->APIdataTS1();
$response2 = $fibonacci_rpc1->call($PLdata1);

      if ($response2==1){

 	$fibonacci_rpc2 = new FibonacciRpcClient();
	$PLdata2= $fibonacci_rpc2->APIdataTS2();
	$response3 = $fibonacci_rpc2->call($PLdata2);  


		if ($response3 == 1){

		
			$fibonacci_rpc3 = new FibonacciRpcClient();
			$PLdata3=$fibonacci_rpc3->APIdataTS3();
                        var_dump($PLdata3);
			$response4 = $fibonacci_rpc3->call($PLdata3);

			
			if ($response4 == 1){

				
					$fibonacci_rpc4 = new FibonacciRpcClient();
					$PLdata4= $fibonacci_rpc4->APIdataTS4();
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
