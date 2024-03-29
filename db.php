<?php
require_once('config.php');

class DB
{
    private $conn;
    public function __construct()
    {
        $servername = "db";
        $username = MYSQL_USER;
        $password = MYSQL_PASSWORD;
        $dbname = "anfisa";
        
        $this->conn = new mysqli($servername, $username, $password, $dbname);
        
    }


    public function getKarma($chat_id, $user_id)
    {
        $sql = "SELECT level FROM `Karma` WHERE `chat_id`=%s AND `user_id`=%s";
        $sql = sprintf($sql, $chat_id, $user_id);
        $result = $this->conn->query($sql);
        if ($result->num_rows > 0) {
            $row = $result->fetch_row();
            return $row[0];
        } else {
            $sql = "INSERT INTO `Karma`(`user_id`, `chat_id`, `level`) VALUES ('%s','%s',0)";
            $sql = sprintf($sql, $user_id, $chat_id);
            $this->conn->query($sql);
            // $this->conn->prepare($sql)->execute([$user_id, $chat_id]);
            return 0;
        }
    }

    public function setKarma($chat_id, $user_id, $point)
    {
        $sql = "UPDATE Karma SET level = %.2f WHERE chat_id = %s AND user_id = %s";
        $sql = sprintf($sql, $point, $chat_id, $user_id);
        $this->conn->query($sql);
    }

    public function setKarmaIncr($chat_id, $user_id, $point)
    {
        $point = $this->getKarma($chat_id, $user_id) + $point;
        $this->setKarma($chat_id, $user_id, $point);
        return $point;
    }

    public function updateUser($user_id, $username, $title, $lang)
    {
        $sql = "
            INSERT INTO Users(id, username, title, lang) 
            VALUES($user_id, $username, $title, $lang) 
            ON DUPLICATE KEY UPDATE username=$username, title=$title
        ";
        $this->conn->query($sql);
    }
}
