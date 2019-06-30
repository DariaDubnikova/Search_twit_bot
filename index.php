<?php
    include('vendor/autoload.php'); //Подключаем библиотеку
    use Telegram\Bot\Api; 

    $telegram = new Api('857128399:AAHHVAeKS_31miXbzVh4-1ZSzH2POkh0AyI'); //Устанавливаем токен, полученный у BotFather
    $result = $telegram -> getWebhookUpdates(); //Передаем в переменную $result полную информацию о сообщении пользователя
    
    $text = $result["message"]["text"]; //Текст сообщения
    $chat_id = $result["message"]["chat"]["id"]; //Уникальный идентификатор пользователя
    $name = $result["message"]["from"]["username"]; //Юзернейм пользователя
    
    

    if($text){
         if ($text == "/start") {
            $reply = "Добро пожаловать в бота!";
            $telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => $reply ]); 
        }
     /*   if ($text == "/sayhello") {
            $reply = "Привет, ". $name;
            $telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => $reply, 'reply_markup' => $reply_markup ]); 
        }  */
    }
?>