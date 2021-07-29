<?php

require_once('vendor/autoload.php');
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

require_once('src/Generator.php');
require_once('classes/Amqp.php');

use LeadGenerator\Generator;
use LeadGenerator\Lead;

$generator = new Generator();
$amqp = new Amqp();

$generator->generateLeads(10000, function (Lead $lead) use ($amqp) {
    $amqp->separation($lead);
});


/*foreach($amqp->getSData() as $key => $value) {
    var_dump($value);
    die();
    $amqp->send($amqp->getSData()[$key]);
}*/
/*for($i = 0; $i <= count($amqp->getSData()); $i++) {
    var_dump($amqp->getSData()[$i]);
    $amqp->send($amqp->getSData()[$i]);
}*/

?>