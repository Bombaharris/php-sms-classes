php-sms-classes
===============

Sets of PHP classes build to cooperate with SMS Gate services.

MultiInfo Example
=============

Sending SMS:

```php
require '../SMSMultiInfoClass.php';

$Gate = new smsMultiInfo ();
$Gate->setGateURL('https://api1.multiinfo.plus.pl/')
        ->setGateUser('username')
        ->setGatePassword('password')
        ->setGateServiceId('1')
        ->setSslCertPath('/path/to/cert/file')
        ->setSslCertPass('cert_password')
        ->setNumberPrefix(48)
        ->setTelephoneNumber(111111111)
        ->setOriginator('ORIGINATOR')
        ->setText('Hello World!');
$Gate->send();
echo $Gate;
```

Mobiltek Example
==============

Sending SMS:

```php
require '../SMSMobiltekClass.php';

$Gate = new SMSMobiltek();
$Gate->setGateURL('https://gw2.mobiltek.pl/api/')
        ->setGateUser('user')
        ->setGatePassword('password')
        ->setGateServiceId('1')
        ->setNumberPrefix(48)
        ->setTelephoneNumber(111111111111)
        ->setOriginator('ORIGINATOR')
        ->setAdvancedEncoding(true)
        ->setSMSId(mt_rand(1, 10000000000))
        ->setText('Hello World!');
$Gate->send();
echo $Gate;
```