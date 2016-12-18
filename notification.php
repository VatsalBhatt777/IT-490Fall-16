<?php
session_start();
//$username = $_SESSION['user_token'];
$username=$_GET['userName'];

echo '<script>   on createRequestObject(){
        var ro;
        //Get name of browser
    var browser = navigator.appName;
        //Create browser-specific HTTP request object
    if(browser == "Microsoft Internet Explorer")
                ro = new ActiveXObject("Microsoft.XMLHTTP");  
    else
                ro = new XMLHttpRequest(); 
    return ro;
}

var http = createRequestObject();
//ajax requests
function sendAjaxReq(){

        teamName = document.getElementById("getTeamNames").value;
	    leagueName = document.getElementById("leagueName").value;
        alert(teamName);
        console.log(teamName);
//      numCols = document.getElementById("Matches").name;
//      console.log(numCols);
        url="fav_add.php?TeamName="+teamName+"&LeagueName="+leagueName+"&junk="+Math.random();
//      url = "hel.php?rows="+numRows+"&cols="+numCols+"&junk="+Math.random();        
        http.open("get", url);
        http.onreadystatechange = handleAjaxResponse;
        http.send(null);
}
	function DeleteAjaxReqNotification(x,userName){
GameID  = document.getElementById(x).name;
console.log(GameID);
console.log(userName);
        url="DeleteNotification.php?GameID="+GameID+"&UserName="+userName+"&junk="+Math.random();
        http.open("get", url);
        http.onreadystatechange = handleAjaxResponse55;
        http.send(null);

}       


function handleAjaxResponse55(){
        if( http.readyState == 4 ){   
        var response=http.responseText;
        console.log(response);
        document.getElementById("buts").innerHTML = response;
    }

}
	
	function getTeams(){

        leagueName = document.getElementById("leagueName").value;
        console.log(leagueName);
//      numCols = document.getElementById("Matches").name;
//      console.log(numCols);
        url= "teams.php?League="+leagueName+"&junk="+Math.random();
//      url = "hel.php?rows="+numRows+"&cols="+numCols+"&junk="+Math.random();        
        http.open("get", url);
        http.onreadystatechange = handleAjaxResponse2;
        http.send(null);
}
//ajax response
	function handleAjaxResponse2(){
        if( http.readyState == 4 ){   
        var response=http.responseText;
		console.log(response);
		//document.getElementById("getTeamNames").value = document.write(response);
        document.getElementById("getTeamNames").innerHTML= response;
    }
}

function handleAjaxResponse(){
        if( http.readyState == 4 ){   
        var response=http.responseText;
        document.getElementById("subresult").innerHTML = response;
    }
}


 </script>';
//echo $username;

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
            '10.200.44.244', 5672, 'admin', 'knp33');
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
        $this->channel->basic_publish($msg, '', 'notifications');
        while(!$this->response) {
            $this->channel->wait();
        }
        return ($this->response);
    }
};
$sendReq = Array();
$sendReq[0]='notification';
$sendReq[1]=$username;
$sent=json_encode($sendReq);
$fibonacci_rpc = new FibonacciRpcClient();
$response = $fibonacci_rpc->call($sent);
//echo " [.] Got ", $response, "\n";
$final=json_decode($response,true);

echo "<center><table border=3 class=table table-striped table-bordered background-color:white; border-color:black; color:green;>";

//var_dump($final[1]);
echo"<TR><TH>GameID</TH><TH>GameDate</TH><TH>GameStatus</TH><TH>homeTeamName</TH><TH>awayTeamName</TH><TH>Action</TH></TR>";
#$_SESSION['user_token']='abc';

$session=$_SESSION['user_token'];
$k=1;
echo "<div class='tbl-content'>";
for ( $x=0; $x<count($final); $x++){
echo "<tr>";
$p=1;
foreach ($final[$x] as $res){
if ($p==5){

echo "<td>";
echo " $res";
echo "</td>";
echo "<td>";
$id=$final[$x]['NotificationID'];
echo "<input type=button id=$id name=$id class=buts value='Dont Show Again' onclick=DeleteAjaxReqNotification('$id','$session'); refreshPage();><br>";
echo "<div id=buts></div>";
echo "</td>";
$p+=1;
}
else{
echo "<td>";
echo " $res";
echo "</td>";
$p+=1;
}
}

echo "</tr>";


}
echo "</div>";






?>
