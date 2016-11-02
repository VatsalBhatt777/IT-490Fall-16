<?php
//connect to mysql
 $con =new  mysqli("localhost","root","toor");

	if ($con->connect_error) {

	die("Connection failed:".$con->connect_error);

}

echo '<br><br>Connected Successfully<br><br>';

echo 'HELLO';
 
 mysqli_select_db($con,"Soccer");



    $uri = 'http://api.football-data.org/v1/competitions/440/leagueTable';
    $reqPrefs['http']['method'] = 'GET';
    $reqPrefs['http']['header'] = 'X-Auth-Token: d65835349a984abcb4d4e66cb66af5f1';
    $stream_context = stream_context_create($reqPrefs);
    $response = file_get_contents($uri, false, $stream_context);
    $fixtures = json_decode($response,true);
 //   print '<prev>';
//   print_r($fixtures);
  //  print '</prev>';

	//print "<b> Positions     TeamName </b> <br><br>";
	
print "<br><br> Hello <br><br>";
//standing(s) for champ league
	for ($x = 0; $x < count($fixtures['standings']); $x++){
	
	$id=$x;

        $position= $fixtures['standings'][$x]['position'];

	$teamName= $fixtures['standings'][$x]['teamName'];

	$playedGames= $fixtures['standings'][$x]['playedGames'];

	$points =  $fixtures['standings'][$x]['points'];

        $goals =  $fixtures['standings'][$x]['goals'];

	$goalsAgainst =  $fixtures['standings'][$x]['goalsAgainst'];

	$goalDifference =  $fixtures['standings'][$x]['goalDifference'];

	$wins =  $fixtures['standings'][$x]['wins'];

	$draws =  $fixtures['standings'][$x]['draws'];

	$losses =  $fixtures['standings'][$x]['losses'];

	

	echo "--- ". $position. "      ".$teamName."      ".$playedGames."      " .$points."      ".$goals."    ".$goalsAgainst."     ".$goalDifference. "     ".$wins. "    ".$draws."    ".$losses." ---";

//$sql = "INSERT INTO PremiereLeague VALUES('$id','$position','$teamName','$playedGames','$points','$goals','$goalsAgainst','$goalDifference','$wins','$draws','$losses');";

//	if(mysqli_query($con,$sql)){

//		echo "<br><br>New record created successfully <br><br>";

//					}

//	else {

//		echo "Error: ".$sql."<br>".mysqli_error($con);
//}

}

?>
