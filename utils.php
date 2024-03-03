<?php

class Utils
{
    public static function makeName($user)
    {
        if (!isset($user['username'])){
            return $user['first_name'].' '.$user['last_name'];
        } else{
            return $user['username'];
        }
    }
}