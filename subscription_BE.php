<?php
require_once __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
#include("standby.php");

$connection = new AMQPStreamConnection('localhost', 5672, 'admin', 'knp33');
$channel = $connection->channel();

$channel->queue_declare('user_fav', false, false, false, false);
function subscription($n) {
	
	$info=json_decode($n, true);
	//echo "Hello";
	 echo "info: $info[0]" . PHP_EOL;
     $UserName=$info[0];
	echo "info: $info[1]" . PHP_EOL;
  	$TeamName=$info[1];
	echo "info: $info[2]". PHP_EOL;
	$LeagueName= $info[2];
	$LeagueAcr = ""; 
	if($LeagueName == '1. Bundesliga 2016/17'){
		$LeagueAcr = 'B';
	}
	elseif($LeagueName == 'Serie A 2016/17'){
		$LeagueAcr = 'S';
	}
	elseif($LeagueName == 'Primera Division 2016/17'){
		$LeagueAcr = 'L';
	}
	elseif($LeagueName == 'Premier League 2016/17'){
		$LeagueAcr = 'P';
	}
	elseif($LeagueName == 'Ligue 1 2016/17'){
		$LeagueAcr = 'l';
	}
	$con =new  mysqli("10.200.173.63","ket","knp33");
        if ($con->connect_error) {
        die("Connection failed:".$con->connect_error);
	}
	mysqli_select_db($con,"Soccer");
	$sql= "INSERT INTO `Subscription` (`UserName`, `TeamName`, `LeagueName`, `LeagueAcr`) VALUES ('$UserName','$TeamName', '$LeagueName', '$LeagueAcr')";

     if(mysqli_query($con,$sql)){

             echo "<br><br>New record created successfully <br><br>" .PHP_EOL;
		     return "Subscribed";
                                      }

     else {

            echo "Error: ".$sql."<br>".mysqli_error($con) . PHP_EOL;
		    //error_log("Something went wrong with subscribing". mysqli_error($con),3, "/var/log/php_erros.log");
		  	return "Oops Something's wrong";
}
//$result=mysqli_query($con,$sql) or die("Error: ".mysqli_error($connection) . error_log("Failed to add user fav", 3, "/var/log/php_error.log"));
$sql = "SELECT DISTINCT LeagueAcr, TeamName  FROM Subscription WHERE UserName='$UserName' ";
$result=mysqli_query($con,$sql) or die ("Error: ".mysqli_error($con));

$arrayOfLeague=Array();

while ($row=mysqli_fetch_array($result)){

$arrayOfLeague[]=$row['TeamName'].$row['LeagueAcr'];

}
$current_date=date("Y-m-d", time());
//var_dump($arrayOfLeague);

$numOfLeagues=count($arrayOfLeague)-1;

$temp=Array();
for($i=0; $i<=$numOfLeagues; $i++){

$teamName=substr($arrayOfLeague[$i],0,-1);
$LeagueName=substr($arrayOfLeague[$i],-1);
echo "$LeagueName".PHP_EOL;
$GetGames="SELECT GameID,GameDate,GameStatus,homeTeamName,awayTeamName  FROM $LeagueName WHERE homeTeamName= '$teamName' AND GameStatus = 'SCHEDULED' OR awayTeamName='$teamName' AND GameStatus = 'SCHEDULED' LIMIT 1";

$r=mysqli_query($con,$GetGames) or die ("Error: ".mysqli_error($con));

while ($row=mysqli_fetch_assoc($r)){

$temp[]=$row;
$GameID=$row['GameID'];
$GameDate=$row['GameDate'];
$GameStatus=$row['GameStatus'];
$homeTeamName=$row['homeTeamName'];
$awayTeamName=$row['awayTeamName'];

echo "$userName $GameID $GameStatus $homeTeamName $awayTeamName".PHP_EOL;

$InsertNoti="INSERT INTO NOTIFICATIONS (UserName, GameID, GameDate,GameStatus,homeTeamName,awayTeamName,Acknowledged) VALUES ('$UserName', '$GameID','$GameDate', '$GameStatus','$homeTeamName','$awayTeamName','No')";

$resq=mysqli_query($con,$InsertNoti) or die ("Error88: ".mysqli_error($con));
}

}
}


echo " [x] Awaiting RPC requests\n" . PHP_EOL;
$callback = function($req) {
    $n = ($req->body);
    echo " [.] fib(", $n, ")\n"; 

    $msg = new AMQPMessage(
        (string)subscription($n),
        array('correlation_id' => $req->get('correlation_id'))
        );

    $req->delivery_info['channel']->basic_publish(
        $msg, '', $req->get('reply_to'));
    $req->delivery_info['channel']->basic_ack(
        $req->delivery_info['delivery_tag']);


};

$channel->basic_qos(null, 1, null);
$channel->basic_consume('user_fav', '', false, false, false, false, $callback);

while(count($channel->callbacks)) {
    $channel->wait();
}

$channel->close();
$connection->close();
?>
