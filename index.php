<?php
    include('vendor/autoload.php'); 
    use Telegram\Bot\Api; 
    use Predis\Client as PredisClient;

    $db = new PredisClient(array(
        'host' => parse_url($_ENV['REDISCLOUD_URL'], PHP_URL_HOST),
        'port' => parse_url($_ENV['REDISCLOUD_URL'], PHP_URL_PORT),
        'password' => parse_url($_ENV['REDISCLOUD_URL'], PHP_URL_PASS),
    ));

    $telegram = new Api('885313182:AAGshWA1PYDU_977cPuHh9BtEgwllFQpUSo'); 
    $result = $telegram -> getWebhookUpdates();
    
    $text = $result['message']['text'];
    $chat_id = $result['message']['chat']['id']; 
    $name = $result['message']['from']['username']; 
    $keyboard = [['Русский'],['English']];

    const WELCOME = 'Добро пожаловать в бота! Выберите,пожалуйста, язык и ведите текст, который нужно преобразовать в речь';
    const START = '/start';
 
    if ($text){
         if ($text == START) {
             $reply = WELCOME;
             $reply_markup = $telegram->replyKeyboardMarkup([ 
                 'keyboard' => $keyboard, 
                 'resize_keyboard' => true, 
                 'one_time_keyboard' => false ]);
             $telegram->sendMessage([ 
                 'chat_id' => $chat_id, 
                 'text' => $reply, 
                 'reply_markup' => $reply_markup ]);
         } elseif ($text == '/sayhello') {
             if (!empty($name)) {
                 $reply = 'Привет, '. $name;
                 $telegram->sendMessage([ 
                     'chat_id' => $chat_id, 
                     'text' => $reply ]); 
             } else {
                 $reply = 'Привет, незнакомец';
                 $telegram->sendMessage([ 
                     'chat_id' => $chat_id, 
                     'text' => $reply ]); 
             }
         } elseif ($text == 'Русский') {
             $db->set($chat_id, 'ru-ru');
         } elseif ($text == 'English') {
             $db->set($chat_id, 'en-us');
         } else {
             $baseUrl = 'http://api.voicerss.org/?';
             $text = str_replace(' ','',$text);
             
             $lang = $db->get($chat_id);
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