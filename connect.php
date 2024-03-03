<?php

$redis = new Redis();
//Connecting to Redis
$redis->connect('redis', 6379);

if ($redis->ping()) {
 echo "PONG";
}

?>