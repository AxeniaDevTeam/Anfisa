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

    public static function escape_mimic($inp)
    {

        if (is_array($inp))
            return array_map(__METHOD__, $inp);

        if (!empty($inp) && is_string($inp)) {
            return str_replace(array('\\', "\0", "\n", "\r", "'", '"', "\x1a"), array('\\\\', '\\0', '\\n', '\\r', "\\'", '\\"', '\\Z'), $inp);
        }

        return $inp;
    }
}