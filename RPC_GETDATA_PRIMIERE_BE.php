<?php

require_once __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$connection = new AMQPStreamConnection('localhost', 5672, 'admin', 'asdf');
$channel = $connection->channel();

$channel->queue_declare('rpc_queue', false, false, false, false);

function APIdataDB($apiData) {
$con =new  mysqli("10.200.44.235","ket","knp33");
        if ($con->connect_error) {
        die("Connection failed:".$con->connect_error);
}
echo '<br><br>Connected Successfully<br><br>';
//echo "-----$apiData-----";
 mysqli_select_db($con,"Soccer");
echo "Connection Soccer  Success";
$fixtures=json_decode($apiData,true);
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
$sql = "INSERT INTO PremiereLeague VALUES('$id','$position','$teamName','$playedGames','$points','$goals','$goalsAgainst','$goalDifference','$wins','$draws','$losses');";

        if(mysqli_query($con,$sql)){

                echo "<br><br>New record created successfully <br><br>";
            //return true;
}

        else {

                echo "Error: ".$sql."<br>".mysqli_error($con);

	//	return false;
} 
}
}

echo " [x] Awaiting RPC requests\n";
$callback = function($req) {
    $n = ($req->body);

//    $data= json_decode($req->body);
//    echo " [.] APIdataBE(",(string) $n, ")\n";

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
$channel->basic_consume('rpc_queue', '', false, false, false, false, $callback);

while(count($channel->callbacks)) {
    $channel->wait();
}

$channel->close();
$connection->close();

?>
