<?php

require_once __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$connection = new AMQPStreamConnection('localhost', 5672, 'admin', 'asdf');
$channel = $connection->channel();

$channel->queue_declare('rpc_queue_tables', false, false, false, false);

function APIdataDB($apiData) {
$con =new  mysqli("localhost","root","toor");
        if ($con->connect_error) {
        die("Connection failed:".$con->connect_error);
}
echo '<br><br>Connected Successfully<br><br>';
//echo "-----$apiData-----";
 mysqli_select_db($con,"Soccer");
echo "Connection Soccer  Success";

$fixtures=json_decode($apiData,true);

$LeagueType=$fixtures["leagueCaption"];

$f=count($fixtures);
echo "-----$f------";
for ($x = 0; $x < count($fixtures['standing']); $x++){

        $id=$x;
        $position= $fixtures['standing'][$x]['position'];
        $teamName= $fixtures['standing'][$x]['teamName'];
        $playedGames= $fixtures['standing'][$x]['playedGames'];
        $points =  $fixtures['standing'][$x]['points'];
        $goals =  $fixtures['standing'][$x]['goals'];
        $goalsAgainst =  $fixtures['standing'][$x]['goalsAgainst'];
        $goalDifference =  $fixtures['standing'][$x]['goalDifference'];
        $wins =  $fixtures['standing'][$x]['wins'];
        $draws =  $fixtures['standing'][$x]['draws'];
        $losses =  $fixtures['standing'][$x]['losses'];
//$sql = "INSERT INTO `$LeagueType` VALUES('$id','$position','$teamName','$playedGames','$points','$goals','$goalsAgainst','$goalDifference','$wins','$draws','$losses');";

$sql = "UPDATE `$LeagueType` SET Position='$position',teamName='$teamName',playedGames='$playedGames',points='$points',goals='$goals',goalsAgainst='$goalsAgainst',goalDifference='$goalDifference',wins='$wins',draws='$draws',losses='$losses' WHERE Id='$id';";


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
$channel->basic_consume('rpc_queue_tables', '', false, false, false, false, $callback);

while(count($channel->callbacks)) {
    $channel->wait();
}

$channel->close();
$connection->close();

?>
