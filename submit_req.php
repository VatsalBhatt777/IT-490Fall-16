<?php
echo "<html>
<style>
body {

background-color:#8BC53D;

}
th {
font-family: Arial, Helvetica, sans-serif;
background: #3E9E3A;
color: #000;

border: 6px solid #3E9E3A;
}
td {
color:black;
font-family: Arial, Helvetica, sans-serif;
background:#8BC53D;
border: 2px solid #000;
}
</style>";
echo "<body>";


$Type=$_GET["rows"];

echo "Hellooooo Values are PHP.EOL:   $Type     ";

require_once __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class FibonacciRpcClient {
    private $connection;
    private $channel;
    private $callback_queue;
    private $response;
    private $corr_id;

    public function __construct() {
        $this->connection = new AMQPStreamConnection(
            'localhost', 5672, 'admin', 'asdf');
        $this->channel = $this->connection->channel();
        list($this->callback_queue, ,) = $this->channel->queue_declare(
            "", false, false, true, false);
        $this->channel->basic_consume(
            $this->callback_queue, '', false, false, false, false,
            array($this, 'on_response'));
    }
    public function on_response($rep) {
        if($rep->get('correlation_id') == $this->corr_id) {
            $this->response = $rep->body;
        }
    }

    public function call($n) {
        $this->response = null;
        $this->corr_id = uniqid();

        $msg = new AMQPMessage(
            (string) $n,
            array('correlation_id' => $this->corr_id,
                  'reply_to' => $this->callback_queue)
            );
        $this->channel->basic_publish($msg, '', 'user_req');
        while(!$this->response) {
            $this->channel->wait();
        }
        return ($this->response);
    }
};

$fibonacci_rpc = new FibonacciRpcClient();
$response = $fibonacci_rpc->call($Type);
$final=json_decode($response,true);
//echo "$response, \n";
//print "<br> <br>";
//var_dump($final);
//echo "<br><br>";
echo "\n\n\n$final[0]";
if ($final[0] == 'Table'){


//echo count($final);
echo "<center><table border=3 class=table table-striped table-bordered background-color:aqua; border-color:lime; color:green;>";
//echo "<tr> Position</tr>";
 
echo"<TR><TH>Position</TH><TH>TeamName</TH><TH>PlayedGames</TH><TH>Points</TH><TH>Goals</TH><TH>GoalsAgainst</TH><TH>GoalDifference</TH><TH>Wins</TH><TH>Draws</TH><TH>Losses</TH></TR>"; 
for ( $x=1; $x<count($final); $x++){
echo "<tr>";
//echo "<td>$final[1]['Position']</td>";
foreach ($final[$x] as $res){
echo "<td>";
echo " $res";
echo "</td>";
}
}

//echo $final[1][1];
echo "</table></center>";

}

else if ($final[0]=='Matches'){
echo "<center><table border=3 class=table table-striped table-bordered background-color:aqua; border-color:lime; color:green;>";

//var_dump($final[1]);
echo"<TR><TH>GameDate</TH><TH>GameStatus</TH><TH>homeTeamName</TH><TH>homeTeamGoals</TH><TH>awayTeamGoals</TH><TH>awayTeamName</TH><TH>MatchDay</TH></TR>";

for ( $x=1; $x<count($final); $x++){
echo "<tr>";
foreach ($final[$x] as $res){
echo "<td>";
echo " $res";
echo "</td>";

}
//echo "<br>";
}
echo "</table></center>";
}



else if ($final[0] =='TopScorer'){

echo "<center><table border=3 class=table table-striped table-bordered background-color:aqua; border-color:lime; color:green;>";

//var_dump($final[1]);
echo"<TR><TH>Team</TH><TH>Rank</TH><TH>Goals</TH><TH>Player</TH></TR></center>";

for ( $x=1; $x<count($final); $x++){
echo "<tr>";
foreach ($final[$x] as $res){
echo "<td>";
echo " $res";
echo "</td>";

}
//echo "<br>";
}
echo "</table></center>";

}


echo "</body>";
echo "</html>";
?>

