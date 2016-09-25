<?php
//connect to mysql
 $con =new  mysqli("localhost","root","toor");

	if ($con->connect_error) {

	die("Connection failed:".$con->connect_error);

}

echo '<br><br>Connected Successfully<br><br>';

  mysql_select_db("Soccer", $con);





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

	echo "    ".$id."      ". $position. "      ".$teamName."<br/><br/>";
}

?>
