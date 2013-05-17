<?php

header("Content-Type: text/plain");
require '../SMSMultiInfoClass.php';

$Gate = new SMSMultiInfo ();
$Gate->setGateURL('https://api1.multiinfo.plus.pl/')
        ->setGateUser('username')
        ->setGatePassword('password')
        ->setGateServiceId(1)
        ->setSslCertPath('/path/to/cert/file')
        ->setSslCertPass('cert_password')
        ->setNumberPrefix(48)
        ->setTelephoneNumber(111111111)
        ->setOriginator('ORIGINATOR')
        ->setText('Hello World!');
$Gate->send();
echo $Gate;