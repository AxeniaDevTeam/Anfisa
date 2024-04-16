<?php
include '../Telegram.php';
require_once('service.php');
require_once('config.php');
require_once('locale/Lang.php');

$telegram = new Telegram(BOT_TOKEN);
$service = new Service();
$chat_id = $telegram->ChatID();
$message = $telegram->Message();
$messageId = $telegram->MessageID();
$text = isset($message['text']) ? $message['text'] : $message['caption'];

if ($telegram->messageFromGroup()) {
    if (isset($message['new_chat_member'])) {
        $new_member_id = $message['new_chat_member']['id'];
        $me = $telegram->getMe();
        $bot_id = $me['result']['id'];

        if ($bot_id == $new_member_id) {
            $text = print_r($service->initChat($message), true);
            $response['text'] = $text;
        }
    }

    $service->initLang($chat_id);

    if (isset($message['reply_to_message'])) {
        $to = $message['reply_to_message']['from'];
        $from = $message['from'];

        if (!($to['is_bot'] || $from['is_bot'] || $to['id'] == 777000)) {
            if ($to['id'] != $from['id']) {
                if (preg_match('/^(\+|\-|👍|👎) ?([\s\S]+)?/ui', $text, $matches)) {
                    switch ($matches[1]) {
                        case '+':
                        case '👍':
                            $text = $service->handleKarma($chat_id, $to, $from, True);
                            $response['text'] = $text;
                            $response['reply_to_message_id'] = $messageId;
                            break;
                        case '-':
                        case '👎':
                            $text = $service->handleKarma($chat_id, $to, $from, False);
                            $response['text'] = $text;
                            $response['reply_to_message_id'] = $messageId;
                            break;
                    };
                }
            }

            $command = explode(' ', trim($text))[0];
            switch ($command) {
                case '/set':
                    if ($from['id'] == ADMIN_CHAT_ID) {
                        $value = (int)explode(' ', trim($text))[1];
                        $text = $service->setKarma($chat_id, $to, $value);
                        $response['text'] = $text;
                        $response['reply_to_message_id'] = $messageId;
                    }
                    break;
            }
        }
    } else {
        $me = $telegram->getMe();
        $bot_username = $me['result']['username'];
        $command = explode(' ', trim($text))[0];
        switch ($command) {
            case '/start':
            case "/start@$bot_username":
                $text = print_r($service->initChat($message), true);
                $response['text'] = $text;
                break;
            case '/settings':
            case "/settings@$bot_username":
                $text = $service->makeSetting($chat_id);
                $response['text'] = $text;
                $response['reply_to_message_id'] = $messageId;
                $option = [[
                    $telegram->buildInlineKeyBoardButton(Lang::message('settings.button.lang'), $url = '', $callback_data = 'change_language'),
                ]];
                // Get the keyboard
                $keyb = $telegram->buildInlineKeyBoard($option);
                $response['reply_markup'] = $keyb;
                break;
            case '/top':
            case "/top@$bot_username":
                $top_records = array();
                foreach($service->getTop($chat_id) as $record){
                    $member = $telegram->getChatMember(['chat_id' => $chat_id,'user_id' => $record[0]]);
                    array_push($top_records, [$member['result']['user'], $record[1]]);
                }
                $response['text'] = $service->makeTop($top_records);
                break;
        }
    }

    if (isset($response)) {
        $message = [
            'chat_id' => $chat_id,
            'parse_mode' => 'HTML', 
            'disable_web_page_preview' => true
        ];
        $message = array_merge($message, $response);
        $telegram->sendMessage($message);
    }
}

$callback_query = $telegram->Callback_Query();
if (!empty($callback_query)) {
    $callback_data = $telegram->Callback_Data();
    $callback_data = explode(':', $callback_data);

    switch ($callback_data[0]) {
        case ('change_language'):
            if (count($callback_data) == 1) {
                $langs = Lang::availableLangs();
                $option = array();
                foreach ($langs as $key => $value) {
                    array_push($option, $telegram->buildInlineKeyBoardButton($value, $url = '', $callback_data = "change_language:$key"));
                }
                $option = [$option];
                $keyb = $telegram->buildInlineKeyBoard($option);
                $response_markup['reply_markup'] = $keyb;
            } else {
                $lang = $callback_data[1];
                $service->setLang($chat_id, $lang);
                $text = Lang::message('settings.answer.changeLang',[Lang::availableLangs()[$lang]]);
                $response_text['text'] = $text;
            }
            break;
    };

    $message = [
        'chat_id' => $chat_id,
        'message_id' => $callback_query['message']['message_id'],
        'parse_mode' => 'HTML'
    ];
    if (isset($response_markup)) {
        $message = array_merge($message, $response_markup);
        $telegram->editMessageReplyMarkup($message);
    } else if (isset($response_text)) {
        $message = array_merge($message, $response_text);
        $telegram->editMessageText($message);
    }
}
