<?php

header("Content-Type: text/plain");
require '../SMSMobiltekClass.php';

$Gate = new SMSMobiltek();
$Gate->get();
$Gate->explainResponse();
