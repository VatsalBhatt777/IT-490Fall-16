<?php

require_once __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$connection = new AMQPStreamConnection('localhost', 5672, 'admin', 'asdf');
$channel = $connection->channel();

$channel->queue_declare('user_req', false, false, false, false);

function tables($n) {
  
$con =new  mysqli("localhost","root","toor");
        if ($con->connect_error) {
        die("Connection failed:".$con->connect_error);
}
mysqli_select_db($con,"Soccer");
$sql= "SELECT Position, teamName, playedGames, points, goals, goalsAgainst, goalDifference, wins, draws, losses FROM `$n`;";
$result=mysqli_query($con,$sql) or die("Error: ".mysqli_error($connection));
$temp = array();
$temp[0]='Table';
while ($row = mysqli_fetch_assoc($result)){
$temp[]=$row;
}
$cont=json_encode($temp);

//print_r( $temp[0]["Id"]);
//echo "FUNCTION GETS $n".PHP_EOL;
  return  $cont;
}
function matches($n){

$con =new  mysqli("localhost","root","toor");
        if ($con->connect_error) {
        die("Connection failed:".$con->connect_error);
}
mysqli_select_db($con,"Soccer");
$sql= "SELECT GameDate, GameStatus, homeTeamName, homeTeamGoals, awayTeamGoals, awayTeamName, MatchDay FROM `$n`;";
$result=mysqli_query($con,$sql) or die("Error: ".mysqli_error($connection));
$temp = array();
$temp[0]='Matches';
while ($row = mysqli_fetch_assoc($result)){
$temp[]=$row;
}
$cont=json_encode($temp);

  return  $cont;
}

echo " [x] Awaiting RPC requests\n";
$callback = function($req) {
    $n = ($req->body);
    echo " [.] fib(", $n, ")\n"; 

    $outputTable;

     if ($n == 'PrimeraTable'){
     $LeagueName='Primera Division 2016/17';
    $outputTable=tables($LeagueName);
    echo "you asked for $LeagueName"; 
}
      else if($n == 'PrimeraMatches') {
     $LeagueFixtureTable ='L';

     $outputTable=matches($LeagueFixtureTable);
     echo "you asked for $outputTable";
      
}
    $msg = new AMQPMessage(
        (string)$outputTable,
        array('correlation_id' => $req->get('correlation_id'))
        );

    $req->delivery_info['channel']->basic_publish(
        $msg, '', $req->get('reply_to'));
    $req->delivery_info['channel']->basic_ack(
        $req->delivery_info['delivery_tag']);


};

$channel->basic_qos(null, 1, null);
$channel->basic_consume('user_req', '', false, false, false, false, $callback);

while(count($channel->callbacks)) {
    $channel->wait();
}

$channel->close();
$connection->close();

?>
