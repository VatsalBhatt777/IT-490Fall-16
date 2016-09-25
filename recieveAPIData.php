<?php
//connect to mysql
 $con =new  mysqli("localhost","root","toor");

	if ($con->connect_error) {

	die("Connection failed:".$con->connect_error);

}

echo '<br><br>Connected Successfully<br><br>';

echo 'HELLO';
 
 mysqli_select_db($con,"Soccer");



    $uri = 'http://api.football-data.org/v1/competitions/426/leagueTable';
    $reqPrefs['http']['method'] = 'GET';
    $reqPrefs['http']['header'] = 'X-Auth-Token: d65835349a984abcb4d4e66cb66af5f1';
    $stream_context = stream_context_create($reqPrefs);
    $response = file_get_contents($uri, false, $stream_context);
    $fixtures = json_decode($response,TRUE);
 //   print '<prev>';
//   print_r($fixtures);
  //  print '</prev>';

	//print "<b> Positions     TeamName </b> <br><br>";
	
print "<br><br> Hello <br><br>";

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

	

//	echo "    ".$id."      ". $position. "      ".$teamName."      ".$playedGames."      " .$points."      ".$goals."    ".$goalsAgainst."     ".$goalDifference. "     ".$wins. "    ".$draws."    ".$losses."   <br/><br/>";

$sql = "INSERT INTO PremiereLeague VALUES('$id','$position','$teamName','$playedGames','$points','$goals','$goalsAgainst','$goalDifference','$wins','$draws','$losses');";

	if(mysqli_query($con,$sql)){

		echo "<br><br>New record created successfully <br><br>";

					}

	else {

		echo "Error: ".$sql."<br>".mysqli_error($con);
}

}

?>
