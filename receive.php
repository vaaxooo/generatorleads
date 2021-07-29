<?php

require_once('vendor/autoload.php');
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

require_once('classes/Amqp.php');

$amqp = new Amqp();

$amqp->receive();

?>