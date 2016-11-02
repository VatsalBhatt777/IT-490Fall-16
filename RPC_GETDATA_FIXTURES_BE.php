<?php

require_once __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$connection = new AMQPStreamConnection('localhost', 5672, 'admin', 'asdf');
$channel = $connection->channel();

$channel->queue_declare('rpc_queue_fixtures', false, false, false, false);

function APIdataDB($apiData) {


$con =new  mysqli("10.200.173.90","vatsal","toor");
        if ($con->connect_error) {
        die("Connection failed:".$con->connect_error);
}
echo '<br><br>Connected Successfully<br><br>';
//echo "-----$apiData-----";
 mysqli_select_db($con,"Soccer");
echo "Connection Soccer  Success";



$fixtures=json_decode($apiData,true);

$LeagueType=$fixtures['Cap'][0];


//$cap=$fixtures[0]['LeagueCaption'];
echo "  type= $LeagueType    ";


$f=count($fixtures);
echo "-----$f------";
for ($x = 0; $x < count($fixtures['fixtures']); $x++){

        $id=$x;
        $playGamesDate= $fixtures['fixtures'][$x]['date'];
      $status =  $fixtures['fixtures'][$x]['status'];
        $matchDay =  $fixtures['fixtures'][$x]['matchday'];
        $homeTeamName =  $fixtures['fixtures'][$x]['homeTeamName'];
        $awayTeamName =  $fixtures['fixtures'][$x]['awayTeamName'];
	$HomeTeamGoals=$fixtures['fixtures'][$x]['result']['goalsHomeTeam'];
	$AwayTeamGoals=$fixtures['fixtures'][$x]['result']['goalsAwayTeam'];
       // $wins =  $fixtures['standing'][$x]['wins'];
       // $draws =  $fixtures['standing'][$x]['draws'];
       // $losses =  $fixtures['standing'][$x]['losses'];

//echo "MatchDay=".$matchDay." status=".$status." playDate=".$playGamesDate." homeTeamName=".$HomeTeamGoals." awayTeamGoals=".$AwayTeamGoals;

//$sql = "INSERT INTO `$LeagueType` VALUES('$id','$playGamesDate','$status','$homeTeamName','$awayTeamName',(NULLIF('$HomeTeamGoals','')),(NULLIF('$AwayTeamGoals','')),'$matchDay');";
$sql = "UPDATE `$LeagueType` SET GameDate='$playGamesDate',GameStatus='$status',homeTeamName='$homeTeamName',awayTeamName='$awayTeamName',homeTeamGoals=(NULLIF('$HomeTeamGoals','')),awayTeamGoals=(NULLIF('$AwayTeamGoals','')),MatchDay='$matchDay' WHERE GameID='$id';";

   if(mysqli_query($con,$sql)){

                echo "<br><br>New record created successfully <br><br>";
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

//echo $n; 	
          

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
$channel->basic_consume('rpc_queue_fixtures', '', false, false, false, false, $callback);

while(count($channel->callbacks)) {
    $channel->wait();
}

$channel->close();
$connection->close();

?>
