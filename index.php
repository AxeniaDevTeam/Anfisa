<?php
include '../Telegram.php';
require_once('service.php');
require_once('config.php');

$telegram = new Telegram(BOT_TOKEN);
$service = new Service();
$text = $telegram->Text();
$chat_id = $telegram->ChatID();
$message = $telegram->Message();
$messageId = $telegram->MessageID();


if ($telegram->messageFromGroup()) {
    if (isset($message['reply_to_message'])) {
        $to = $message['reply_to_message']['from'];
        $from = $message['from'];
        if (!($to['is_bot'] || $from['is_bot'])) {
            if ($to['id'] != $from['id']) {
                switch (substr($text, 0, 1)) {
                    case '+':
                        $response = $service -> handleKarma($chat_id, $to, $from, True);
                        break;
                    case '-':
                        $response = $service -> handleKarma($chat_id, $to, $from, False);
                        break;
                };

                if (isset($response)) {
                    $telegram->sendMessage([
                        'chat_id' => ADMIN_CHAT_ID,
                        'text' => $message['id']
                    ]);
                }

                if (isset($response)) {
                    $telegram->sendMessage([
                        'chat_id' => $chat_id,
                        'text' => $response,
                        'parse_mode' => 'HTML',
                        'reply_to_message_id' => $messageId
                    ]);
                }
            }
        }
    }
}
