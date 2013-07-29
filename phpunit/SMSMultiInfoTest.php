<?php

require '../SMSMultiInfoClass.php';

/**
 * @requires PHP 5.3
 * @requires extension curl
 */
class SMSMultiInfoTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var SMSMultiInfo
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new SMSMultiInfo;
        $this->object
                ->setGateURL('https://api1.multiinfo.plus.pl/')
                ->setGateUser('user')
                ->setGatePassword('password')
                ->setGateServiceId('1')        
                ->setSslCertPath('/path/to/cert/file')
                ->setSslCertPass('cert_password');
    }

    /**
     * @covers SMSMultiInfo::send
     * @dataProvider provider
     */
    public function testSend($orig, $scheldue, $numberPrefix, $number, $text)
    {
        $this->object
                ->setOriginator($orig)
                ->setNumberPrefix($numberPrefix)
                ->setTelephoneNumber($number)
                ->setText($text)
                ->send();
        $this->assertEquals(SMSMultiInfo::ACTION_STATUS_SUCCESS, $this->object->getActionStatus());
    }

    public function provider()
    {
        $data = array();
        $file = new SplFileObject("data.csv");
        while ( $file->valid() ) {
            $data[] = $file->fgetcsv("|");
        }
        return $data;
    }

    /**
     * @covers SMSMultiInfo::info
     * @dataProvider provider
     * @todo use depends
     */
    public function testInfo($orig, $scheldue, $numberPrefix, $number, $text)
    {
        $this->object
                ->setOriginator($orig)
                ->setNumberPrefix($numberPrefix)
                ->setTelephoneNumber($number)
                ->setText($text)
                ->send();
        $sms = $this->object->explainResponse();
        $this->object
                ->setSMSId($sms['id'])
                ->info();
        $this->assertEquals(SMSMultiInfo::ACTION_STATUS_SUCCESS, $this->object->getActionStatus());
    }

    /**
     * @covers SMSMultiInfo::confirm
     * @desc we expect that action status for non existant SMS will be ERROR
     */
    public function testConfirm()
    {
        $this->object
                ->setSMSId(1)
                ->confirm();
        $this->assertEquals(SMSMultiInfo::ACTION_STATUS_ERROR, $this->object->getActionStatus());
    }

    /**
     * @covers SMSMultiInfo::get
     */
    public function testGet()
    {
        $this->object
                ->get();
        $this->assertEquals(SMSMultiInfo::ACTION_STATUS_SUCCESS, $this->object->getActionStatus());
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
     * @covers SMSMultiInfo::__toString
     */
    public function testToString()
    {
        $this->object
                ->get();
        $this->assertEquals(SMSMultiInfo::RESPONSE_OK, $this->object);
    }

}