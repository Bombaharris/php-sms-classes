<?php

header("Content-Type: text/plain");
require '../SMSMobiltekMTClass.php';

$Gate = new SMSMobiltekMT();
$Gate->setGateURL('https://ssl.mobiltek.pl/api/')
        ->setGateUser('user')
        ->setGatePassword('password')
        ->setGateServiceId('1')
        ->setOriginator('77000')
        ->setNumberPrefix(48)
        ->setTelephoneNumber(111111111)
        ->setText('Hello1')
        ->setSchedule('2013-08-20 12:00:00')
        ->setContinue(true);

$Gate->send();

echo $Gate.PHP_EOL;

$Gate->setTelephoneNumber(222222222)
        ->setText('Hello2')
        ->setContinue(false);

$Gate->send();
echo $Gate.PHP_EOL;