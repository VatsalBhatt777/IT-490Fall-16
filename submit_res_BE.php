<?php

require_once __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$connection = new AMQPStreamConnection('localhost', 5672, 'admin', 'asdfasdf');
$channel = $connection->channel();

$channel->queue_declare('user_req', false, false, false, false);

function tables($n) {
  
$con =new  mysqli("localhost","root","Yeff1234");
        if ($con->connect_error) {
        die("Connection failed:".$con->connect_error);
}
mysqli_select_db($con,"Soccer");
$sql= "SELECT Position, teamName, playedGames, points, goals, goalsAgainst, goalDifference, wins, draws, losses FROM `$n`;";
echo "FUCK1";
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

$con =new  mysqli("localhost","root","Yeff1234");
        if ($con->connect_error) {
        die("Connection failed:".$con->connect_error);
}
mysqli_select_db($con,"Soccer");
$sql= "SELECT GameDate, GameStatus, homeTeamName, homeTeamGoals, awayTeamGoals, awayTeamName, MatchDay FROM `$n`;";
echo "FCUK2";
$result=mysqli_query($con,$sql) or die("Error: ".mysqli_error($connection));
$temp = array();
$temp[0]='Matches';
while ($row = mysqli_fetch_assoc($result)){
$temp[]=$row;
}
$cont=json_encode($temp);

  return  $cont;

}

function TopScorer($n){

$con =new  mysqli("localhost","root","Yeff1234");
        if ($con->connect_error) {
        die("Connection failed:".$con->connect_error);
}
mysqli_select_db($con,"Soccer");
$sql= "SELECT teamName, Rank , TotalGoals, Name FROM `$n`;";
echo "FUCK3";
$result=mysqli_query($con,$sql) or die("Error: ".mysqli_error($connection));
$temp = array();
$temp[0]='TopScorer';
while ($row = mysqli_fetch_assoc($result)){
$temp[]=$row;
}
$cont=json_encode($temp);

  return  $cont;

}
function FavMatches($n){
$con =new  mysqli("localhost","root","toor");
        if ($con->connect_error) {
        die("Connection failed:".$con->connect_error);
}
mysqli_select_db($con,"Soccer");
$sql= "SELECT DISTINCT LeagueAcr FROM `Subscription` WHERE UserName='$n';";
echo "FUCK4";
$result=mysqli_query($con,$sql) or die("Error: ".mysqli_error($connection));
$temp = array();
$temp[0]='FavMatches';
$x=1;
$data=array();
$data[0]='FavMatches';
while ($row = mysqli_fetch_assoc($result)){
$temp[$x]=$row;
$x+=1;
//	echo "$x      $temp[$x]";
}

		
	for ($x=1; $x<count($temp);$x++){
	var_dump($temp[$x]);
		
		foreach ($temp[$x] as $tem){
		echo "$x is $tem\n\n";
		$sql2= "SELECT GameDate, GameStatus, homeTeamName, homeTeamGoals,awayTeamGoals, awayTeamName FROM $tem, Subscription WHERE 
		$tem.homeTeamName=Subscription.TeamName OR
		$tem.awayTeamName=Subscription.TeamName AND Subscription.UserName='$n' LIMIT 35;";
            
		echo "FUCK5". PHP_EOL;
		
		$res=mysqli_query($con,$sql2) or die("Error: ".mysqli_error($con));
			while ($r=mysqli_fetch_assoc($res)){
			$data[]=$r;	
			}
	
}

	}

$cont=json_encode($data);
	var_dump($cont);
	$error = json_last_error();

var_dump($cont, $error === JSON_ERROR_UTF8);
//echo "\n\n".var_dump($cont);
  return $cont;
	
}

echo " [x] Awaiting RPC requests\n";
$callback = function($req) {
    $n = ($req->body);
//    echo " [.] fib(", $n, ")\n"; 

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
     else if ($n =='PTS'){

     $TopScores='LaLiga-TopScorer';  
      $outputTable=TopScorer($TopScores);
      echo "you asked for $outputTable";
    
}


     else  if ($n == 'PremierTable'){
     $LeagueName='Premier League 2016/17';
    $outputTable=tables($LeagueName);
    echo "you asked for $LeagueName";
}
      else if($n == 'PremierMatches') {
     $LeagueFixtureTable ='P';

     $outputTable=matches($LeagueFixtureTable);
     echo "you asked for $outputTable";

}
     else if ($n =='ETS'){

     $TopScores='EPL-TopScorer';
      $outputTable=TopScorer($TopScores);
      echo "you asked for $outputTable";

}

 else  if ($n == 'Ligue1Table'){
     $LeagueName='Ligue 1 2016/17';
    $outputTable=tables($LeagueName);
    echo "you asked for $LeagueName";
}
      else if($n == 'Ligue1Matches') {
     $LeagueFixtureTable ='l';

     $outputTable=matches($LeagueFixtureTable);
     echo "you asked for $outputTable";

}
     else if ($n =='LTS'){

     $TopScores='Ligue1-TopScorer';
      $outputTable=TopScorer($TopScores);
      echo "you asked for $outputTable";

}

else  if ($n == 'BundesligaTable'){
     $LeagueName='1. Bundesliga 2016/17';
    $outputTable=tables($LeagueName);
    echo "you asked for $LeagueName";
}
      else if($n == 'BundesligaMatches') {
     $LeagueFixtureTable ='B';
     $outputTable=matches($LeagueFixtureTable);
     echo "you asked for $outputTable";

}
     else if ($n =='BTS'){

     $TopScores='Bundesliga-TopScorer';
      $outputTable=TopScorer($TopScores);
      echo "you asked for $outputTable";

}

else  if ($n == 'SerieATable'){
     $LeagueName='Serie A 2016/17';
    $outputTable=tables($LeagueName);
    echo "you asked for $LeagueName";
}
      else if($n == 'SerieAMatches') {
     $LeagueFixtureTable ='S';
     $outputTable=matches($LeagueFixtureTable);
     echo "you asked for $outputTable";

}
     else if ($n =='STS'){

     $TopScores='SerieA-TopScorer';
      $outputTable=TopScorer($TopScores);
      echo "you asked for $outputTable";

}
else {
echo "n is : $n";
	$outputTable=FavMatches($n);
//		echo "youasked for ".var_dump($outputTable);
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
