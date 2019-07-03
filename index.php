<?php
    include('vendor/autoload.php'); 
    use Telegram\Bot\Api; 
    use Predis\Client as PredisClient;

    $redis = new PredisClient(array(
        'host' => parse_url($_ENV['REDISCLOUD_URL'], PHP_URL_HOST),
        'port' => parse_url($_ENV['REDISCLOUD_URL'], PHP_URL_PORT),
        'password' => parse_url($_ENV['REDISCLOUD_URL'], PHP_URL_PASS),
    ));

    $telegram = new Api('857128399:AAHHVAeKS_31miXbzVh4-1ZSzH2POkh0AyI'); 
    $result = $telegram -> getWebhookUpdates();
    
    $text = $result["message"]["text"];
    $chat_id = $result["message"]["chat"]["id"]; 
    $name = $result["message"]["from"]["username"]; 
    $keyboard = [];
    $russian = 'ru-ru';
    $english = 'en-us';	
    
    $redis->set('русский', $russian); // save lang
    $redis->set('english', $english);
 
    if($text){
         if ($text == "/start") {
            $reply = "Добро пожаловать в бота!";
            $reply_markup = $telegram->replyKeyboardMarkup([ 
                'keyboard' => $keyboard, 
                'resize_keyboard' => true, 
                'one_time_keyboard' => false ]);
            $telegram->sendMessage([ 
                'chat_id' => $chat_id, 
                'text' => $reply, 
                'reply_markup' => $reply_markup ]);
        } elseif ($text == "/sayhello") {
            if (!empty($name)) {
                $reply = "Привет, ". $name;
                $telegram->sendMessage([ 
                    'chat_id' => $chat_id, 
                    'text' => $reply ]); 
            } else {
                $reply = "Привет, незнакомец";
                $telegram->sendMessage([ 
                    'chat_id' => $chat_id, 
                    'text' => $reply ]); 
            }
        } else {
            $baseUrl = 'http://api.voicerss.org/?';
            $text = str_replace(' ','',$text); 
            $id_lang = end(explode(" ",$text));
             
            $lang = $redis->get($id_lang); // get lang
            $lang = $lang ? $lang : 'en-us';
             
            $params = [
                'key'=> 'b2da3917c24d458fbb6009689f2dfc9b',
                'hl'=> $lang,
                'src'=> $text, 
                'c'=> 'mp3'
            ];
            $url = $baseUrl . http_build_query($params); 
            
        	$telegram->sendAudio([
                'chat_id' => $chat_id,
                'audio' => $url 
            ]);
         }
    }
    register_shutdown_function(function () {
	   http_response_code(200);
    });