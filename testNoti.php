<?php
$con =new  mysqli("192.168.84.102","vatsal","toor");
        if ($con->connect_error) {
        die("Connection failed:".$con->connect_error);
}
$userName='abc';
mysqli_select_db($con,"Soccer");

$users="SELECT DISTINCT UserName From Subscription";
$usersRes=mysqli_query($con,$users) or die("Error: ".mysqli_error($con));
$userNames=Array();

while($k=mysqli_fetch_array($usersRes)){
$userNames[]=$k['UserName'];

}
#var_dump($userNames);

foreach ($userNames as $userName){
echo "$userName".PHP_EOL;
$sql = "SELECT DISTINCT LeagueAcr, TeamName  FROM Subscription WHERE UserName='$userName' ";
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
$GetGames="SELECT GameID,GameDate,GameStatus,homeTeamName,awayTeamName  FROM $LeagueName WHERE homeTeamName= '$teamName' AND GameStatus = 'SCHEDULED' OR awayTeamName='$teamName' AND GameStatus = 'SCHEDULED' LIMIT 5";

$r=mysqli_query($con,$GetGames) or die ("Error: ".mysqli_error($con));

while ($row=mysqli_fetch_assoc($r)){

$temp[]=$row;
$GameID=$row['GameID'];
$GameDate=$row['GameDate'];
$GameStatus=$row['GameStatus'];
$homeTeamName=$row['homeTeamName'];
$awayTeamName=$row['awayTeamName'];

echo "$userName $GameID $GameStatus $homeTeamName $awayTeamName".PHP_EOL;

$InsertNoti="INSERT INTO NOTIFICATIONS (UserName, GameID, GameDate,GameStatus,homeTeamName,awayTeamName,Acknowledged) VALUES ('$userName', '$GameID','$GameDate', '$GameStatus','$homeTeamName','$awayTeamName','No')";

$resq=mysqli_query($con,$InsertNoti) or die ("Error88: ".mysqli_error($con));
}

}
}
$cont=json_encode($temp);
return $cont;

?>
