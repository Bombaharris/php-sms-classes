<?php

header("Content-Type: text/plain");

require '../SMSMobiltekClass.php';

$Gate = new SMSMobiltek();
$Gate->setGateURL('https://gw2.mobiltek.pl/api/')
        ->setGateUser('user')
        ->setGatePassword('passowrd')
        ->setGateServiceId('1')
        ->setNumberPrefix(48)
        ->setTelephoneNumber(111111111)
        ->setOriginator('ORIGINATOR')
        ->setAdvancedEncoding(true)
        ->setSMSId(mt_rand(1, 10000000000))
        ->setText('Hello World!');
$Gate->send();
echo $Gate;
