php-sms-classes
===============

Sets of PHP classes build to send SMS thru gates.

MultiInfo
==============

Example
============

require '../smsMultiInfo.php';

```php
$Gate = new smsMultiInfo ();
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
```