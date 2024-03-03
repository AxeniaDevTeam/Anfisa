<?php
class DB
{
    private $redis;
    public function __construct($host, $port)
    {
        $this->redis = new Redis();
        $this->redis->connect($host, $port);
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

    public function setKarma($chat_id, $user_id, $point)
    {
        return $this->redis->hIncrByFloat($chat_id, $user_id, $point);
    }
}