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

    public function setKarma($chat_id, $to, $value) {
        $toName = Utils::makeName($to);
        $this->db->setKarma($chat_id, $to['id'], $value);
        $text = 'Пользователю <b>%s</b> установлена карма %d';
        return sprintf($text, $toName, $value);
    }

    public function handleKarma($chat_id, $to, $from, $isRise)
    {
        $this->updateUser($to);
        $this->updateUser($from);

        $toName = Utils::makeName($to);
        $fromName = Utils::makeName($from);
        $fromKarma = $this->db->getKarma($chat_id,$from['id']);

        if($fromKarma < 0) {
            $text = "У пользователя <b>%s</b>(%d) недостаточно кармы.";
            return sprintf($text, $fromName, $fromKarma);
        } else {
            
            if($fromKarma < 1) {
                $point = 1;
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

            $toKarma = $this->db->setKarmaIncr($chat_id, $to['id'], $point);
            return sprintf($text, $fromName, $fromKarma, $toName, $toKarma);
        }
    }
}