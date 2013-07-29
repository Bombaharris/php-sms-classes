<?php

require '../SMSMobiltekMTClass.php';

/**
 * @requires PHP 5.3
 * @requires extension curl
 */
class SMSMobiltekMTTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var SMSMobiltekMT
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new SMSMobiltekMT;
        $this->object
                ->setGateURL('http://dev.clickquicknow.pl/mobiltek_mt_d/')
                ->setGateUser('user')
                ->setGatePassword('password')
                ->setGateServiceId('1');
    }

    /**
     * @covers SMSMobiltekMT::send
     * @dataProvider provider
     */
    public function testSend($orig, $scheldue, $numberPrefix, $number, $text)
    {
        $this->object
                ->setOriginator($orig)
                ->setSchedule($scheldue)
                ->setNumberPrefix($numberPrefix)
                ->setTelephoneNumber($number)
                ->setText($text)
                ->send();
        $this->assertEquals(SMSMobiltekMT::ACTION_STATUS_SUCCESS, $this->object->getActionStatus());
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
     * @covers SMSMobiltekMT::info
     */
    public function testInfo()
    {
        $this->object
                ->info();
        $this->assertEquals(SMSMobiltekMT::ACTION_STATUS_FAIL, $this->object->getActionStatus());
    }

    /**
     * @covers SMSMobiltekMT::confirm
     */
    public function testConfirm()
    {
        $this->object
                ->confirm();
        $this->assertEquals(SMSMobiltekMT::ACTION_STATUS_FAIL, $this->object->getActionStatus());
    }

    /**
     * @covers SMSMobiltekMT::get
     * @todo emulate curl with gate
     */
    public function testGet()
    {
        $_REQUEST['type'] = 'CHARGE';
        $this->object
                ->get();
        $this->assertEquals(SMSMobiltekMT::ACTION_STATUS_SUCCESS, $this->object->getActionStatus());
    }

    /**
     * @covers SMSMobiltekMT::explainResponse
     * @dataProvider provider
     */
    public function testExplainResponse($orig, $scheldue, $numberPrefix, $number, $text)
    {
        $this->object
                ->setOriginator($orig)
                ->setSchedule($scheldue)
                ->setNumberPrefix($numberPrefix)
                ->setTelephoneNumber($number)
                ->setText($text)
                ->send();
        $this->assertEquals(array(
            'responseStatus' => 'OK',
            'errorCode' => '1000:Enqueued',
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
     * @covers SMSMobiltekMT::__toString
     * @dataProvider provider
     */
    public function testToString($orig, $scheldue, $numberPrefix, $number, $text)
    {
        $this->object
                ->setOriginator($orig)
                ->setSchedule($scheldue)
                ->setNumberPrefix($numberPrefix)
                ->setTelephoneNumber($number)
                ->setText($text)
                ->send();
        $this->assertEquals(SMSMobiltekMT::RESPONSE_OK, $this->object);
    }

}