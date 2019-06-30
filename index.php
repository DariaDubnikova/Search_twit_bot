<?php
    include('vendor/autoload.php'); //Подключаем библиотеку
    use Telegram\Bot\Api; 

    $telegram = new Api('857128399:AAHHVAeKS_31miXbzVh4-1ZSzH2POkh0AyI'); //Устанавливаем токен, полученный у BotFather
    $result = $telegram -> getWebhookUpdates(); //Передаем в переменную $result полную информацию о сообщении пользователя
    
    $text = $result["message"]["text"]; //Текст сообщения
    $chat_id = $result["message"]["chat"]["id"]; //Уникальный идентификатор пользователя
    $name = $result["message"]["from"]["username"]; //Юзернейм пользователя
    $keyboard = [["Последние статьи"],["Картинка"],["Гифка"]]; //Клавиатура

    if($text){
         if ($text == "/start") {
            $reply = "Добро пожаловать в бота!";
         /*   $reply_markup = $telegram->replyKeyboardMarkup([ 'resize_keyboard' => true, 'one_time_keyboard' => false ]); */
            $telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => $reply ]); 
        }
     /*   if ($text == "/sayhello") {
            $reply = "Привет, ". $name;
            $reply_markup = $telegram->replyKeyboardMarkup([ 'resize_keyboard' => true, 'one_time_keyboard' => false ]);
            $telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => $reply, 'reply_markup' => $reply_markup ]); 
        }  */
    }
?>