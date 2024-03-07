<?php
class DB
{
    private $redis;
    public function __construct()
    {
        $this->redis = new Redis();
        $this->redis->connect('redis', 6379);
    }

    public function getKarma($chat_id, $user_id)
    {
        $karma = $this->redis->hGet($chat_id, $user_id);

        if (!$karma){
            $this->redis->hSet($chat_id, $user_id, 0);
            return 0;
        }

        return $karma;
    }

    public function setKarmaIncr($chat_id, $user_id, $point)
    {
        return $this->redis->hIncrByFloat($chat_id, $user_id, $point);
    }
}