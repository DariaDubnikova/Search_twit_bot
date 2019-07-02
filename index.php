<?php
    include('vendor/autoload.php'); 
    use Telegram\Bot\Api; 

    $telegram = new Api('857128399:AAHHVAeKS_31miXbzVh4-1ZSzH2POkh0AyI'); 
    $result = $telegram -> getWebhookUpdates();
    
    $text = $result["message"]["text"];
    $chat_id = $result["message"]["chat"]["id"]; 
    $name = $result["message"]["from"]["username"]; 
    $keyboard = [];
    
    if($text){
         if ($text == "/start") {
            $reply = "Добро пожаловать в бота!";
            $telegram->sendMessage([ 
                'chat_id' => $chat_id, 
                'text' => $reply 
            ]);
        } elseif ($text == "/sayhello") {
            if (!empty($name)) {
                $reply = "Привет, ". $name;
                $telegram->sendMessage([ 
                    'chat_id' => $chat_id, 
                    'text' => $reply, 
                ]); 
            } else {
                $reply = "Привет, незнакомец";
                $telegram->sendMessage([ 
                    'chat_id' => $chat_id, 
                    'text' => $reply, ]); 
            }
        } else{
            $baseUrl = 'http://api.voicerss.org/?';
            $text = str_replace(' ','',$text); 
             
            $params = [
                'key'=> 'b2da3917c24d458fbb6009689f2dfc9b',
                'hl'=> 'en-us',
                'src'=> $text, 
                'c'=> 'mp3'
            ];
            $url = $baseUrl . http_build_query($params); 
             
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HEADER, true);
            $output = curl_exec($ch);
            curl_close($ch);
            
        	$telegram->sendAudio([
                'chat_id' => $chat_id,
                'audio' => $output 
            ]);
         }
    }
    register_shutdown_function(function () {
	http_response_code(200);
?>