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
 * @property string $_sslCertPath path to cert file
 * @property string $_sslCertPass  pass to cert file
 * 
 */
class SMS
{

    protected $_gateUrl = null;
    protected $_gateUser = null;
    protected $_gateServiceId = null;
    protected $_gatePassword = null;
    protected $_text = null;
    protected $_telephoneNumber = null;
    protected $_numberPrefix = null;
    protected $_originator = false;
    protected $_dispatchId = 10;
    protected $_validTo = false;
    protected $_delivNotifRequest = false;
    protected $_zeroclass = false;
    protected $_advancedEncoding = false;
    protected $_deleteContent = false;
    protected $_smsId = null;
    protected $_timeout = 30000;
    protected $_sslCertPath = null;
    protected $_sslCertPass = null;
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
     * @return \Sms
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
     * @return \Sms
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
     * @return \Sms 
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
     * @return \Sms 
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
     * @return \Sms
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
     * @return \Sms
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
     * @return \Sms
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
     * @return \Sms
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
     * @return \Sms
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
     * @return \Sms
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
     * @return \Sms
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
     * @return \Sms 
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
     * @return \Sms 
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
     * @return \Sms 
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
     * @return \Sms 
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
     * @return \Sms 
     */
    public function setTimeout($_timeout)
    {
        $this->_timeout = $_timeout;
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
     * @return \Sms 
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
     * @return \Sms 
     */
    public function setSslCertPass($_sslCertPass)
    {
        $this->_sslCertPass = $_sslCertPass;
        return $this;
    }

}