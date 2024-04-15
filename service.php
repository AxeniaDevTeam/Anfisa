<?php
require_once('utils.php');
require_once('db.php');

class Service
{
    private $db;
    public function __construct()
    {
        $this->db = new DB();
    }

    public function initLang($chat_id)
    {
        $lang = $this->db->getLang($chat_id);
        if ($lang == false) {
            $lang = 'en';
            $this->db->setChat($chat_id, $lang);
        }
        Lang::init($lang);
    }

    public function setKarma($chat_id, $to, $value)
    {
        $toName = Utils::makeName($to);
        $this->db->setKarma($chat_id, $to['id'], $value);
        return Lang::message("karma.set", [$toName, $value]);
    }

    public function handleKarma($chat_id, $to, $from, $isRise)
    {
        $toName = Utils::makeName($to);
        $fromName = Utils::makeName($from);
        $fromKarma = $this->db->getKarma($chat_id, $from['id']);

        if ($fromKarma < 0) {
            return Lang::message('karma.notEnough', [$fromName, $fromKarma]);
        } else {

            if ($fromKarma < 1) {
                $point = 1;
            } else {
                $point = sqrt($fromKarma);
            }


            switch ($isRise) {
                case True:
                    $text_token = "karma.plus";
                    break;
                case False:
                    $text_token = "karma.minus";
                    $point *= -1;
                    break;
            }

            $toKarma = $this->db->setKarmaIncr($chat_id, $to['id'], $point);

            return Lang::message($text_token, [$fromName, $fromKarma, $toName, $toKarma]);
        }
    }

    public function initChat($message)
    {
        $chat_id = $message['chat']['id'];
        $lang = $message['from']['language_code'];
        $query = $this->db->setChat($chat_id, $lang);
        if ($query == 1) return Lang::message("chat.greetings");
    }

    public function setLang($chat_id, $lang)
    {
        $this->db->setChat($chat_id, $lang);
        Lang::init($lang);
    }

    public function makeSetting($chat_id)
    {
        $text = Lang::message("settings.language", [Lang::message("language")]);
        return $text;
    }

    public function getTop($chat_id)
    {

        return $this->db->getTop($chat_id);
    }

    public function makeTop($top_records)
    {
        if (count($top_records) > 0) {
            $text = Lang::message("top.title")."\r\n ⭐️";
            foreach($top_records as $record){
                $name = Utils::makeName($record[0]);
                if(isset($record[0]['username'])){
                    $name=sprintf("<a href='t.me/%s'>%s</a>", $record[0]['username'], $name);
                }
                $text .= Lang::message("top.record", [$name, $record[1]])."\r\n";
            }
            return $text;
        } else {
            return Lang::message("top.empty");
        }
    }
}
