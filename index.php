<?php

require 'vendor/autoload.php';

use LINE\LINEBot\SignatureValidator as SignatureValidator;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder as TextMessageBuilder;
foreach (glob("handler/*.php") as $handler){
	if ($handler != 'handler/post.php'){
		include $handler;
	}
}

$dotenv = new Dotenv\Dotenv('env');
$dotenv->load();

$configs =  [
	'settings' => ['displayErrorDetails' => true],
];
$app = new Slim\App($configs);

$app->get('/', function ($request, $response) {
	return "Rahadina Budiman Sundara - https://www.keyzex.com/";
});

$app->post('/', function ($request, $response)
{
	$body 	   = file_get_contents('php://input');
	$signature = $_SERVER['HTTP_X_LINE_SIGNATURE'];
	file_put_contents('php://stderr', 'Body: '.$body);
	
	if (empty($signature)){
		return $response->withStatus(400, 'Signature not set');
	}
	
	if($_ENV['PASS_SIGNATURE'] == false && ! SignatureValidator::validateSignature($body, $_ENV['CHANNEL_SECRET'], $signature)){
		return $response->withStatus(400, 'Invalid signature');
	}
	
	$httpClient = new \LINE\LINEBot\HTTPClient\CurlHTTPClient($_ENV['CHANNEL_ACCESS_TOKEN']);
	$bot = new \LINE\LINEBot($httpClient, ['channelSecret' => $_ENV['CHANNEL_SECRET']]);

	$data = json_decode($body, true);
	foreach ($data['events'] as $event){
		if ($event['type'] == 'message'){
			if($event['message']['type'] == 'text'){

				$inputMessage = $event['message']['text'];
				$userId = $event['source']['userId'];

				if ($inputMessage[0] == '/'){
					
					$inputMessage = ltrim($inputMessage, '/');
					$inputSplit = explode(' ', $inputMessage, 2);
					
					if ( function_exists( $inputSplit[0] ) ){
						$outputMessage = $inputSplit[0]( $inputSplit[1], $userId );
					} else {
						$outputMessage = new TextMessageBuilder('Perintah tidak diketahui. type /help for using this bot');
					}

					$result = $bot->replyMessage($event['replyToken'], $outputMessage);
					return $result->getHTTPStatus() . ' ' . $result->getRawBody();
					
				} //else{
						//	$outputMessage = new TextMessageBuilder('Perintah tidak diketahui.');
						//	$result = $bot->replyMessage($event['replyToken'], $outputMessage);
						//	return $result->getHTTPStatus() . ' ' . $result->getRawBody();
				//}
				
			}
		}
	}

});

$app->run();