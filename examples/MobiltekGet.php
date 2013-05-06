<?php

header("Content-Type: text/plain");
require 'libs/SMSMobiltekClass.php';

$Gate = new SMSMobiltek();
$Gate->get();
$Gate->explainResponse();
