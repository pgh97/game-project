<?php
header("Content-Type: text/html; charset=UTF-8");
$mcd = new Memcached();
$mcd->addServer("localhost", "11211");     //memcached 서버접속 세팅

$mcd->set("test", "dddd");                  //set
$t = $mcd->get("test");                           //get
print ($t);
