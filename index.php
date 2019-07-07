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
    $chatId = $result['message']['chat']['id']; 
    $name = $result['message']['from']['username']; 
    $keyboard = [['Русский'],['English']];

    const WELCOME = 'Добро пожаловать в бота! Выберите, пожалуйста, язык и ведите текст, который нужно преобразовать в речь';
    const START = '/start';
    const COMMAND_SAY_HELLO = '/sayhello';
    const HELLO = 'Привет, ';
    const HELLO_UNKNOWN = 'Привет, незнакомец';
  /*  const HELLO = 'Привет, ';
    const HELLO = 'Привет, ';
    const HELLO = 'Привет, ';
    const HELLO = 'Привет, '; */


    if ($text){
         if ($text == START) {
             $reply = WELCOME;
             $reply_markup = $telegram->replyKeyboardMarkup([ 
                 'keyboard' => $keyboard, 
                 'resize_keyboard' => true, 
                 'one_time_keyboard' => false ]);
             $telegram->sendMessage([ 
                 'chat_id' => $chatId, 
                 'text' => $reply, 
                 'reply_markup' => $reply_markup ]);
         } elseif ($text == COMMAND_SAY_HELLO) {
             if (!empty($name)) {
                 $reply = HELLO . $name;
                 $telegram->sendMessage([ 
                     'chat_id' => $chatId, 
                     'text' => $reply ]); 
             } else {
                 $reply = HELLO_UNKNOWN;
                 $telegram->sendMessage([ 
                     'chat_id' => $chatId, 
                     'text' => $reply ]); 
             }
         } elseif ($text == 'Русский') {
             $db->set($chatId, 'ru-ru');
         } elseif ($text == 'English') {
             $db->set($chatId, 'en-us');
         } else {
             $baseUrl = 'http://api.voicerss.org/?';
             $text = str_replace(' ','',$text);
             
             $lang = $db->get($chatId);
             $lang = $lang ? $lang : 'en-us';
             
             $params = [
                 'key'=> 'b2da3917c24d458fbb6009689f2dfc9b',
                 'hl'=> $lang,
                 'src'=> $text,
                 'c'=> 'mp3'
             ];
             $url = $baseUrl . http_build_query($params); 
             
             $telegram->sendAudio([
                 'chat_id' => $chatId,
                 'audio' => $url 
             ]);
         }
    }
    register_shutdown_function(function () {
        http_response_code(200);
    });