<?php

require '../SMSMobiltekClass.php';

/**
 * @requires PHP 5.3
 * @requires extension curl
 */
class SMSMobiltekTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var SMSMobiltek
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new SMSMobiltek;
        $this->object
                ->setGateURL('https://gw2.mobiltek.pl/api/')
                ->setGateUser('user')
                ->setGatePassword('password')
                ->setGateServiceId('1')               
                ->setOriginator('11111');
    }
    /**
     * @covers SMSMobiltek::send
     * @dataProvider provider
     */
    public function testSend($orig, $scheldue, $numberPrefix, $number, $text)
    {
        $this->object
                ->setNumberPrefix($numberPrefix)
                ->setTelephoneNumber($number)
                ->setText($text)
                ->setAdvancedEncoding(true)
                ->setSMSId(mt_rand(1, SMSMobiltek::MAX_SMS_ID))
                ->send();
        $this->assertEquals(SMSMobiltek::ACTION_STATUS_SUCCESS, $this->object->getActionStatus());
    }

    public function provider()
    {
        $data = array();
        $file = new SplFileObject('data.csv');
        while ( $file->valid() ) {
            $data[] = $file->fgetcsv('|');
        }
        return $data;
    }

    /**
     * @covers SMSMobiltek::info
     */
    public function testInfo()
    {
        $this->object
                ->info();
        $this->assertEquals(SMSMobiltek::ACTION_STATUS_FAIL, $this->object->getActionStatus());
    }

    /**
     * @covers SMSMobiltek::confirm
     */
    public function testConfirm()
    {
        $this->object
                ->confirm();
        $this->assertEquals(SMSMobiltek::ACTION_STATUS_FAIL, $this->object->getActionStatus());
    }

    /**
     * @covers SMSMobiltek::get
     * @todo emulate curl with gate
     */
    public function testGet()
    {
        $_REQUEST['id'] = 1;
        $this->object
                ->get();
        $this->assertEquals(SMSMobiltek::ACTION_STATUS_SUCCESS, $this->object->getActionStatus());
    }

    /**
     * @covers SMSMobiltek::explainResponse
     * @dataProvider provider
     */
    public function testExplainResponse($orig, $scheldue, $numberPrefix, $number, $text)
    {
        $this->object
                ->setNumberPrefix($numberPrefix)
                ->setTelephoneNumber($number)
                ->setText($text)
                ->setSMSId(mt_rand(1, SMSMobiltek::MAX_SMS_ID))
                ->send();
        $this->assertEquals(array(
            'responseStatus' => 'OK',
            'id' => $this->object->getSMSId(),
                ), $this->object->explainResponse());
    }

    public function testGetException()
    {
        try {
            $this->object
                    ->get();
        } catch ( RuntimeException $expected ) {
            return;
        }

        $this->fail('An expected exception has not been raised.');
    }

    public function testSendException()
    {
        try {
            $this->object
                    ->send();
        } catch ( UnexpectedValueException $expected ) {
            return;
        }

        $this->fail('An expected exception has not been raised.');
    }

    /**
     * @covers SMSMobiltek::__toString
     */
    public function testToString()
    {
        $this->object
                ->info();
        $this->assertEmpty((string) $this->object);
    }

}