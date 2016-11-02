<?php

require_once __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$connection = new AMQPStreamConnection('localhost', 5672, 'admin', 'asdf');
$channel = $connection->channel();

$channel->queue_declare('top-scorer', false, false, false, false);

function APIdataDB($apiData) {
$con =new  mysqli("localhost","root","toor");
        if ($con->connect_error) {
        die("Connection failed:".$con->connect_error);
}
//echo '<br><br>Connected Successfully<br><br>';
//echo "-----$apiData-----";
 mysqli_select_db($con,"Soccer");
//echo "Connection Soccer  Success";

$fixtures=json_decode($apiData,true);

$LeagueType=$fixtures["Type"];
echo "NAME:    $LeagueType\n";


$f=count($fixtures);
echo "-----$f------";
for ($x = 0; $x < count($fixtures); $x++){

        $id=$x;
        $team= $fixtures[$x]['team'];
        $PlayaRank= $fixtures[$x]['rank'];
        $goals= $fixtures[$x]['goals'];
        $PlayaName =  $fixtures[$x]['name'];
        
       
//echo "$team $PlayaRank $goals $PlayaName\n\n";
//$sql = "INSERT INTO `$LeagueType` VALUES('$id','$team','$PlayaRank','$goals','$PlayaName');";

$sql = "UPDATE `$LeagueType` SET teamName='$team',Rank='$PlayaRank',TotalGoals='$goals',Name='$PlayaName' WHERE id='$id';";



    if(mysqli_query($con,$sql)){

                echo PHP_EOL."New record created successfully".PHP_EOL;
            //return true;
}

        else {

                echo PHP_EOL."Error: ".$sql."<br>".mysqli_error($con).PHP_EOL;

	//	return false;
} 
}
return true;
}

echo " [x] Awaiting RPC requests\n";
$callback = function($req) {

 // var_dump($type1);


  
  $n = ($req->body);

//    $data= json_decode($req->body);
//    echo " [.] APIdataBE(",(string) $n, ")\n";
//   $type = json_decode($n,true);
	
  // $leagueType=$type["leagueCaption"];	
          
//	print_r($leagueType);
   //  echo "\n\n\n$n\n\n\n";

    $msg = new AMQPMessage(
        (string) APIdataDB($n),
        array('correlation_id' => $req->get('correlation_id'))
      );

    $req->delivery_info['channel']->basic_publish(
        $msg, '', $req->get('reply_to'));
    $req->delivery_info['channel']->basic_ack(
      $req->delivery_info['delivery_tag']);
};

$channel->basic_qos(null, 1, null);
$channel->basic_consume('top-scorer', '', false, false, false, false, $callback);

while(count($channel->callbacks)) {
    $channel->wait();
}

$channel->close();
$connection->close();

?>
