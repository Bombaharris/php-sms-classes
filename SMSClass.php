<?php

/**
 * Send SMS by gate
 * 
 * @package php-sms-classes
 * @author Rafal Zielonka
 * @copyright Copyright (C) 2012  Rafał Zielonka
 * @license This program is free software: 
 * you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>
 * 
 * @filesource
 */
interface SMSable
{

    /**
     * Method send request to gate with all data needed to send SMS 
     */
    public function send();

    /**
     * Method send request to gate with all data needed to obtain inforamtion 
     * about sended SMS 
     */
    public function info();

    /**
     * Method send request to gate with all data needed to confirm SMS
     */
    public function confirm();

    /**
     * Method send request to gate with all data needed to get SMS 
     */
    public function get();

    /**
     * Method parse gate response
     */
    public function explainResponse();
}

/**
 * PHP class build to send SMS by Gate
 * 
 * @author Rafał Zielonka
 * @version 1.0
 * 
 * @property string $_gateUrl SMS gate url
 * @property string $_gateUser SMS gate user
 * @property string $_gatePassword SMS gate password
 * @property string $_gateServiceId SMS gate service numeric identifier
 * @property string $_text SMS text
 * @property string $_telephoneNumber reciever telephone number
 * @property string $_numberPrefix reciver telephone number prefix
 * @property string $_originator alphanumeric originator address 
 * (up to 11 characters)
 * @property integer $_dispatchId Service/dispatch numeric identifier
 * @property string $_validTo date
 * @property boolean $_delivNotifRequest delivery notify request from SMSC
 * @property boolean $_zeroclass indicates that this message is to be displayed 
 * on the MS immediately and a message delivery 
 * report is to be sent back to the SMSC 
 * @property boolean $_advancedEncoding indicates if the SMS 
 * will be encoded with UTF-8
 * @property boolean $_deleteContent request for SMS 
 * content removal from SMS gate database
 * @property integer $_smsId SMS numeric identifier
 * @property integer $_timeout timeout time in microseconds
 * @property boolean $_manualConfirm set manual confirm while getting sms
 * @property string $_sslCertPath path to cert file
 * @property string $_sslCertPass  pass to cert file
 * @property string $_schedule time when sms gonna be send
 * 
 */
class SMS
{
    /**
     * @desc array key for error code
     */

    const KEY_ERROR_CODE = 'errorCode';
    /**
     * @desc array key for sms identifier
     */
    const KEY_SMS_ID = 'id';
    /**
     * @desc array key for sms type
     */
    const KEY_TYPE = 'type';
    /**
     * @desc array key for sms text
     */
    const KEY_TEXT = 'text';
    /**
     * @desc array key for protocol nuemric identifier
     */
    const KEY_PROTOCOL_ID = 'protocolId';
    /**
     * @desc array key for chareset scheme identifier
     */
    const KEY_CHARSET_SHCHEME_ID = 'charsetSchemeId';
    /**
     * @desc array key for connector identifier
     */
    const KEY_CONNECTOR_ID = 'connectorId';
    /**
     * @desc array key for service identifier
     */
    const KEY_SERVICE_ID = 'serviceId';
    /**
     * @desc array key for incoming sms identifier
     */
    const KEY_SMS_IN_ID = 'smsInId';
    /**
     * @desc array key for SMS priority
     */
    const KEY_PRIORITY = 'priority';
    /**
     * @desc array key for SMS send datetime
     */
    const KEY_SEND_DATE = 'sendDate';
    /**
     * @desc array key for SMS date
     */
    const KEY_VALID_TO_DATE = 'validToDate';
    /**
     * @desc array key for delivery notify request flag
     */
    const KEY_DELIV_NOTIF_REQUEST = 'delivNotifRequest';
    /**
     * @desc array key for SMS originator
     */
    const KEY_ORIG = 'orig';
    /**
     * @desc array key for SMS destination
     */
    const KEY_DEST = 'dest';
    /**
     * @desc array key for SMS status
     */
    const KEY_STATUS = 'status';
    /**
     * @desc array key for SMS status change date
     */
    const KEY_STATUS_CHANGE_DATE = 'statusChangeDate';
    /**
     * @desc array key for operator identifier
     */
    const KEY_OPERATOR_ID = 'operatorId';
    /**
     * @desc array key for multi part SMS type
     */
    const KEY_MPART_TYPE = 'mPartType';
    /**
     * @desc array key for multi part SMS number of parts
     */
    const KEY_MPART_PARTS = 'mPartParts';
    /**
     * @desc array key for multi part SMS identifier
     */
    const KEY_MPART_ID = 'mPartId';
    /**
     * @desc array key for multi part SMS part number
     */
    const KEY_MPART_NO = 'mPartNo';
    /**
     * @desc array key for multi part SMS max part numbers
     */
    const KEY_MPART_MAX = 'mPartMax';

    protected $_gateUrl = null;
    protected $_gateUser = null;
    protected $_gateServiceId = null;
    protected $_gatePassword = null;
    protected $_text = null;
    protected $_telephoneNumber = null;
    protected $_numberPrefix = null;
    protected $_originator = null;
    protected $_dispatchId = 10;
    protected $_validTo = null;
    protected $_delivNotifRequest = false;
    protected $_zeroclass = false;
    protected $_advancedEncoding = false;
    protected $_deleteContent = false;
    protected $_smsId = null;
    protected $_timeout = 30000;
    protected $_manualConfirm = false;
    protected $_sslCertPath = null;
    protected $_sslCertPass = null;
    protected $_schedule = null;
    public $response = array(
        'status' => null,
        'error' => false,
        'data' => array(),
    );

    /**
     * Magic method
     * 
     * @return string response status
     */
    function __toString()
    {
        return (string) $this->response['status'];
    }

    /**
     * Get SMS gate URL
     * 
     * @return string
     */
    public function getGateURL()
    {
        return $this->_gateUrl;
    }

    /**
     * Set SMS gate URL
     * 
     * @param string $_gateUrl
     * @return \SMS
     */
    public function setGateURL($_gateUrl)
    {
        $this->_gateUrl = $_gateUrl;
        return $this;
    }

    /**
     * Get SMS gate user name
     * 
     * @return string
     */
    public function getGateUser()
    {
        return $this->_gateUser;
    }

    /**
     * Set SMS gate user name
     * 
     * @param string $_gateUser
     * @return \SMS
     */
    public function setGateUser($_gateUser)
    {
        $this->_gateUser = $_gateUser;
        return $this;
    }

    /**
     * Get SMS gate password
     * 
     * @return string
     */
    public function getGatePassword()
    {
        return $this->_gatePassword;
    }

    /**
     * Set SMS gate password
     * 
     * @param string $_gatePassword
     * @return \SMS 
     */
    public function setGatePassword($_gatePassword)
    {
        $this->_gatePassword = $_gatePassword;
        return $this;
    }

    /**
     * Get SMS service numeric identifier
     * 
     * @return string  
     */
    public function getGateServiceId()
    {
        return $this->_gateServiceId;
    }

    /**
     * Set SMS service  numeric identifier
     * 
     * @param string $gate_service_id
     * @return \SMS 
     */
    public function setGateServiceId($gateServiceId)
    {
        $this->_gateServiceId = $gateServiceId;
        return $this;
    }

    /**
     * Get SMS message
     * 
     * @return string
     */
    public function getText()
    {
        return $this->_text;
    }

    /**
     * Set SMS message
     * 
     * @param string $_text
     * @return \SMS
     */
    public function setText($_text)
    {
        $this->_text = $_text;
        return $this;
    }

    /**
     * Get SMS reciver telephone number
     * 
     * @return string
     */
    public function getTelephoneNumber()
    {
        return $this->_telephoneNumber;
    }

    /**
     * Set SMS reciver telephone number
     * 
     * @param string $_telephoneNumber
     * @return \SMS
     */
    public function setTelephoneNumber($_telephoneNumber)
    {
        $this->_telephoneNumber = $_telephoneNumber;
        return $this;
    }

    /**
     * Get SMS reciver telephone number prefix
     * 
     * @return string
     */
    public function getNumberPrefix()
    {
        return $this->_numberPrefix;
    }

    /**
     * Set SMS reciver telephone number prefix
     * 
     * @param string $_numberPrefix
     * @return \SMS
     */
    public function setNumberPrefix($_numberPrefix)
    {
        $this->_numberPrefix = $_numberPrefix;
        return $this;
    }

    /**
     * Get originator
     * 
     * @return string
     */
    public function getOriginator()
    {
        return $this->_originator;
    }

    /**
     * Set originator
     * 
     * @param string $_originator alphanumeric originator address 
     * (up to 11 characters)
     * @return \SMS
     */
    public function setOriginator($_originator)
    {
        $this->_originator = $_originator;
        return $this;
    }

    /**
     * Get dispatch numeric identifier
     * 
     * @return integer
     */
    public function getDispatchId()
    {
        return (int) $this->_dispatchId;
    }

    /**
     * Set dispathc numeric identifier
     * 
     * @param integer $_dispatch_id
     * @return \SMS
     */
    public function setDispatchId($_dispatchId)
    {
        $this->_dispatchId = (int) $_dispatchId;
        return $this;
    }

    /**
     * Get valid to date
     * 
     * @return string
     */
    public function getValidTo()
    {
        return $this->_validTo;
    }

    /**
     * Set valid to date
     * 
     * @param string $_valid_to 
     * @return \SMS
     */
    public function setValid_to($_validTo)
    {
        $this->_validTo = $_validTo;
        return $this;
    }

    /**
     * Set flag for delivery notify request from SMSC
     * 
     * @return boolean
     */
    public function isDelivNotifRequest()
    {
        return (bool) $this->_delivNotifRequest;
    }

    /**
     * Get flag for delivery notify request from SMSC
     * 
     * @param boolean $_delivNotifRequest
     * @return \SMS
     */
    public function setDelivNotifRequest($_delivNotifRequest)
    {
        $this->_delivNotifRequest = $_delivNotifRequest;
        return $this;
    }

    /**
     * Get flag for zeroclass message
     * 
     * @return boolean
     */
    public function isZeroClass()
    {
        return (bool) $this->_zeroclass;
    }

    /**
     * Set flag for zeroclass message
     * 
     * @return \SMS 
     */
    public function setZeroClass($zeroclass)
    {
        $this->_zeroclass = $zeroclass;
        return $this;
    }

    /**
     * Get flag for advanced encoding
     * 
     * @return boolean  
     */
    public function isAdvancedEncoding()
    {
        return (bool) $this->_advancedEncoding;
    }

    /**
     * Set flag for advanced encoding in utf-8
     * @desc decrase SMS chars limit to 70 chars
     * 
     * @param boolean $_advancedEncoding
     * @return \SMS 
     */
    public function setAdvancedEncoding($_advancedEncoding)
    {
        $this->_advancedEncoding = $_advancedEncoding;
        return $this;
    }

    /**
     * Get flag for delete content
     * 
     * @return boolean
     */
    public function isDeleteContent()
    {
        return (bool) $this->_deleteContent;
    }

    /**
     * Set flag for delete content
     * 
     * @param boolean $_deleteContent 
     * @return \SMS 
     */
    public function setDeleteContent($_deleteContent)
    {
        $this->_deleteContent = $_deleteContent;
        return $this;
    }

    /**
     * Get SMS numeric indentifier
     * 
     * @return integer
     */
    public function getSMSId()
    {
        return (int) $this->_smsId;
    }

    /**
     * Set SMS numeric indentifier
     * 
     * @param integer $_smsId
     * @return \SMS 
     */
    public function setSMSId($_smsId)
    {
        $this->_smsId = $_smsId;
        return $this;
    }

    /**
     * Get timeout
     * 
     * @return integer
     */
    public function getTimeout()
    {
        return (int) $this->_timeout;
    }

    /**
     * Set timeout
     * 
     * @param integer $_timeout
     * @return \SMS 
     */
    public function setTimeout($_timeout)
    {
        $this->_timeout = $_timeout;
        return $this;
    }

    /**
     * Get manual confirm
     *  
     * @return boolean
     */
    public function isManualConfirm()
    {
        return $this->_manualConfirm;
    }

    /**
     * Set manual confirm while getting SMS
     * 
     * @param boolean $_manualConfirm
     * @return \SMS 
     */
    public function setManualConfirm($_manualConfirm)
    {
        $this->_manualConfirm = $_manualConfirm;
        return $this;
    }

    /**
     * Get cert path
     * 
     * @return string
     */
    public function getSslCertPath()
    {
        return $this->_sslCertPath;
    }

    /**
     * Set cert path
     * 
     * @param string $_sslCertPath
     * @return \SMS 
     */
    public function setSslCertPath($_sslCertPath)
    {
        $this->_sslCertPath = $_sslCertPath;
        return $this;
    }

    /**
     * Get cert password
     * 
     * @return string
     */
    public function getSslCertPass()
    {
        return $this->_sslCertPass;
    }

    /**
     * Set cert password
     * 
     * @param string $_sslCertPass
     * @return \SMS 
     */
    public function setSslCertPass($_sslCertPass)
    {
        $this->_sslCertPass = $_sslCertPass;
        return $this;
    }

    /**
     * Get send time
     * 
     * @return  string
     */
    public function getSchedule()
    {
        return $this->_schedule;
    }

    /**
     * Set send time
     * 
     * @param string $_schedule DateTime construct string i.e.(+ 2 minutes)
     * @return \SMS 
     */
    public function setSchedule($_schedule)
    {
        $this->_schedule = $_schedule;
        return $this;
    }

    /**
     * Parse server response and returns unified array.
     * 
     * @return array
     */
    public function explainResponse()
    {
        $data = array();

        $data['status'] = $this->response['status'];
        isset($this->response['data'][1]) ? $data[self::KEY_SMS_ID] = $this->response['data'][1] : $data[self::KEY_ERROR_CODE] = $this->response['error'];
        isset($this->response['data'][2]) AND $data[self::KEY_TEXT] = $this->response['data'][2];
        isset($this->response['data'][3]) AND $data[self::KEY_TEXT] = $this->response['data'][3];
        isset($this->response['data'][4]) AND $data[self::KEY_PROTOCOL_ID] = $this->response['data'][4];
        isset($this->response['data'][5]) AND $data[self::KEY_CHARSET_SHCHEME_ID] = $this->response['data'][5];
        isset($this->response['data'][6]) AND $data[self::KEY_SERVICE_ID] = $this->response['data'][6];
        isset($this->response['data'][7]) AND $data[self::KEY_CONNECTOR_ID] = $this->response['data'][7];
        isset($this->response['data'][8]) AND $data[self::KEY_SMS_IN_ID] = $this->response['data'][8];
        isset($this->response['data'][9]) AND $data[self::KEY_PRIORITY] = $this->response['data'][9];
        isset($this->response['data'][10]) AND $data[self::KEY_SEND_DATE] = $this->response['data'][10];
        isset($this->response['data'][11]) AND $data[self::KEY_VALID_TO_DATE] = $this->response['data'][11];
        isset($this->response['data'][12]) AND $data[self::KEY_DELIV_NOTIF_REQUEST] = $this->response['data'][12];
        isset($this->response['data'][13]) AND $data[self::KEY_ORIG] = $this->response['data'][13];
        isset($this->response['data'][14]) AND $data[self::KEY_DEST] = $this->response['data'][14];
        isset($this->response['data'][15]) AND $data[self::KEY_STATUS] = $this->response['data'][15];
        isset($this->response['data'][16]) AND $data[self::KEY_STATUS_CHANGE_DATE] = $this->response['data'][16];
        isset($this->response['data'][17]) AND $data[self::KEY_OPERATOR_ID] = $this->response['data'][17];
        isset($this->response['data'][18]) AND $data[self::KEY_MPART_TYPE] = $this->response['data'][18];
        isset($this->response['data'][19]) AND $data[self::KEY_MPART_PARTS] = $this->response['data'][19];
        isset($this->response['data'][20]) AND $data[self::KEY_MPART_ID] = $this->response['data'][20];
        isset($this->response['data'][21]) AND $data[self::KEY_MPART_NO] = $this->response['data'][21];
        isset($this->response['data'][22]) AND $data[self::KEY_MPART_MAX] = $this->response['data'][22];

        return $data;
    }

}