<?php

require_once __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$connection = new AMQPStreamConnection('localhost', 5672, 'admin', 'asdf');
$channel = $connection->channel();

$channel->queue_declare('ping', false, false, false, false);

function fib() {
echo "FUCL".PHP_EOL;	
	return shell_exec('hostname');
}

echo " [x] Awaiting RPC requests\n";
$callback = function($req) {
	$n = ($req->body);
	echo " [.] fib(", $n, ")\n";
	$res = fib();
	echo "result is $res".PHP_EOL;
	$msg = new AMQPMessage(
		(string) $res,
		array('correlation_id' => $req->get('correlation_id'))
		);

	$req->delivery_info['channel']->basic_publish(
		$msg, '', $req->get('reply_to'));
	$req->delivery_info['channel']->basic_ack(
		$req->delivery_info['delivery_tag']);
};

$channel->basic_qos(null, 1, null);
$channel->basic_consume('ping', '', false, false, false, false, $callback);

while(count($channel->callbacks)) {
    $channel->wait();
}

$channel->close();
$connection->close();

?>
