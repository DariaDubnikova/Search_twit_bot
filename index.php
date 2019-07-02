<?php
    include('vendor/autoload.php'); 
    use Telegram\Bot\Api; 

    $telegram = new Api('857128399:AAHHVAeKS_31miXbzVh4-1ZSzH2POkh0AyI'); 
    $result = $telegram -> getWebhookUpdates();
    
    $text = $result["message"]["text"];
    $chat_id = $result["message"]["chat"]["id"]; 
    $name = $result["message"]["from"]["username"]; 

    if($text){
         if ($text == "/start") {
            $reply = "Добро пожаловать в бота!";
            $telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => $reply ]);
        } elseif ($text == "/sayhello") {
            if (!empty($name)) {
                $reply = "Привет, ". $name;
                $telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => $reply, ]); 
            } else {
                $reply = "Привет, незнакомец";
                $telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => $reply, ]); 
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
            
        	$reply = $url;
        	$telegram->sendMessage([ 'chat_id' => $chat_id, 'parse_mode'=> 'HTML', 'text' => $reply ]);
         }
    }
?>