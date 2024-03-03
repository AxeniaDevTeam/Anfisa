<?php
require_once('utils.php');
require_once('db.php');
$db = new DB('redis', 6379);

class Service
{
    private $db;
    public function __construct()
    {
        $this->db = new DB('redis', 6379);
    }

    public function handleKarma($chat_id, $to, $from, $isRise)
    {
        $toName = Utils::makeName($to);
        $fromName = Utils::makeName($from);
        $fromKarma = $this->db->getKarma($chat_id,$from['id']);

        if($fromKarma < 0) {
            return 0;
            // $text = "У пользователя <b>%s</b>(%d) недостаточно кармы.";
            // return sprintf($text, $fromName, $fromKarma);
        } else {
            if($fromKarma < 1) {
                $point = $isRise? 1 : -1;
            } else {
                $point = sqrt($fromKarma);
            }

            switch($isRise){
                case True:
                    $text = '<b>%s</b> (%d) добавил кармы <b>%s</b> (%d)';
                    break;
                case False:
                    $text = '<b>%s</b> (%d) забрал карму у <b>%s</b> (%d)';
                    $point *= -1;
                    break;
            }

            $toKarma = $this->db->setKarma($chat_id, $to['id'], $point);
            return sprintf($text, $fromName, $fromKarma, $toName, $toKarma);
        }
        
    }
}