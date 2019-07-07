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
    const RUSSIAN = 'Русский';
    const ENGLISH = 'English';
    const RU_SPEECH = 'ru-ru';
    const ENG_SPEECH = 'en-us';
    const API = 'http://api.voicerss.org/?';
    const API_KEY = 'b2da3917c24d458fbb6009689f2dfc9b';
    const FORMAT_AUDIO = 'mp3';
    
    function sendTelegramMessage($reply, $keyboard, $chatId, $telegram) {
        $reply_markup = $telegram->replyKeyboardMarkup([ 
            'keyboard' => $keyboard, 
            'resize_keyboard' => true, 
             'one_time_keyboard' => false ]);
        $telegram->sendMessage([ 
            'chat_id' => $chatId, 
            'text' => $reply, 
            'reply_markup' => $reply_markup ]);
    }

    function sendSpeech($text, $db, $chatId, $telegram) {
        $baseUrl = API;
        $text = str_replace(' ','',$text);
             
        $lang = $db->get($chatId);
        $lang = $lang ? $lang : ENG_SPEECH;
             
        $params = [
            'key'=> API_KEY,
            'hl'=> $lang,
            'src'=> $text,
            'c'=> FORMAT_AUDIO
        ];
        $url = $baseUrl . http_build_query($params); 
             
        $telegram->sendAudio([
            'chat_id' => $chatId,
            'audio' => $url 
        ]);
    }

    if ($text){
         if ($text == START) {
             sendTelegramMessage(WELCOME, $keyboard, $chatId, $telegram);
         } elseif ($text == COMMAND_SAY_HELLO) {
             if (!empty($name)) {
                 sendTelegramMessage(HELLO . $name, $keyboard, $chatId, $telegram);
             } else {
                 sendTelegramMessage(HELLO_UNKNOWN, $keyboard, $chatId, $telegram); 
             }
         } elseif ($text == RUSSIAN) {
             $db->set($chatId, RU_SPEECH);
         } elseif ($text == ENGLISH) {
             $db->set($chatId, ENG_SPEECH);
         } else {
             sendSpeech($text, $db, $chatId, $telegram);
         }
    }
    register_shutdown_function(function () {
        http_response_code(200);
    });