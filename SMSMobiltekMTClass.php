<?php

require_once 'SMSClass.php';

/**
 * Send SMS MT by Mobiltek gate
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
 * 
 * @property boolean $_continue flag for complete content
 * @property boolean $_forceUpdate flag forcing reset all previous setup for dispatch
 * @property string $_wapInfo wap tab name
 * @property string $_wapUrl wap tab url
 */
class SMSMobiltekMT extends SMSClasses\SMS implements SMSClasses\SMSable
{
    /**
     * @desc path to send action
     */

    const SEND_URL_PATH = 'updatemt.php';
    /**
     * @desc server response separator
     */
    const RESPONSE_SEPARATOR = PHP_EOL;
    /**
     * @desc array key for id of unique dispatch equal id whlie send
     */
    const KEY_BC = 'bc';
    /**
     * @desc array key for end of subscription date
     */
    const KEY_EXPIRE_DATE = 'expireDate';
    /**
     * @desc server response status
     */
    const RESPONSE_OK = 'OK';
    /**
     * @desc url param user name
     * 
     * required: yes <br />
     * type: string
     */
    const PARAM_LOGIN = 'login';
    /**
     * @desc url param password
     * 
     * required: yes <br />
     * type: string
     */
    const PARAM_PASS = 'passwd';
    /**
     * @desc url param service id
     * 
     * required: yes <br />
     * type: string
     */
    const PARAM_SERVICE_ID = 'service';
    /**
     * @desc url param SMS message
     * 
     * required: yes (no if param wap_url is not empty)<br />
     * type: string
     */
    const PARAM_TEXT = 'text';
    /**
     * @desc url param telephone number with prefix
     * 
     * required: no <br />
     * type: string
     */
    const PARAM_DEST = 'dest';
    /**
     * @desc url param wap tab address
     * 
     * required: no (yes if param text is empty) <br />
     * type: string
     */
    const PARAM_WAP_URL = 'wap_url';
    /**
     * @desc url param wap tab name
     * 
     * required: no (yes if param text is empty) <br />
     * type: string
     */
    const PARAM_WAP_INFO = 'wap_info';
    /**
     * @desc url param telephone numbers with prefix,text
     * 
     * required: no <br />
     * type: string
     */
    const PARAM_DESTS = 'dests[]';
    /**
     * @desc param url for resposne SMS numeric identifier
     * 
     * required: no <br />
     * default: null<br />
     * type: integer
     */
    const PARAM_SMS_IN_ID = 'id';
    /**
     * @desc param url for alphanumeric originator address
     * 
     * required: no <br />
     * type: string
     */
    const PARAM_ORIG = 'orig';
    /**
     * @desc information about type of notification
     *  - REGISTRATION
     *  - CHARGED
     *  - DEREGISTARTION
     * 
     * required: no <br />
     * type: striog
     */
    const PARAM_TYPE = 'type';
    /**
     * @desc param url for scheldued delivery time
     * 
     * required: yes <br />
     * type: string
     */
    const PARAM_SCHEDULE = 'schedule';
    /**
     * @desc url param unix timestamp date when sms was recived by operator
     * 
     * required: no <br />
     * type: string
     */
    const PARAM_RECIVED_TIME = 'tm';
    /**
     * @desc digital sign generated from md5 hash of configuration paramters
     * and password, known only for client and gate server
     * 
     * required: no <br />
     * type: string
     */
    const PARAM_SIGN = 'sign';
    /**
     * @desc operator numeric idenfier i.e.: 
     *  - 501 PTK
     *  - 601 Polkomtel
     *  - 602 PTC
     *  - 701 P4
     *  - 26012 Polsat Cyfrowy
     * 
     * required: no <br />
     * type: string
     */
    const PARAM_OPERATOR_ID = 'op';
    /**
     * @desc param url for resposne SMS numeric identifier
     * 
     * required: no <br />
     * default: null<br />
     * type: integer
     */
    const PARAM_SMS_ID = 'id';
    /**
     * @desc param url flag for complete content
     * 
     * required: no <br />
     * default: false <br />
     * type: boolean
     */
    const PARAM_CONTINUE = 'continue';
    /**
     * @desc param url flag forcing reset all previous setup for dispatch
     * 
     * required: no <br />
     * default: false <br />
     * type: boolean
     */
    const PARAM_FORCE_UPDATE = 'force_update';
    /**
     * @desc param url id of unique dispatch
     * 
     * required: no <br />
     * type: integer
     */
    const PARAM_BC = 'bc';
    /**
     * @desc param url for end of subscription
     * 
     * required: no <br />
     * type: string
     */
    const PARAM_EXPIRE_DATE = 'expire_date';

    private $_continue = false;
    private $_forceUpdate = false;
    private $_wapInfo = null;
    private $_wapUrl = null;

    /**
     * Check flag for continue MT setup
     * 
     * @return boolean
     */
    public function isContinue()
    {
        return $this->_continue;
    }

    /**
     * Set flag for continue MT setup
     * 
     * @param boolean $continue
     * @return \SMSMobiltekMT
     */
    public function setContinue($continue)
    {
        $this->_continue = $continue;
        return $this;
    }

    /**
     * Check flag forcing reset all previous setup for dispatch
     * 
     * @return boolean
     */
    public function isForceUpdate()
    {
        return $this->_forceUpdate;
    }

    /**
     * Set flag forcing reset all previous setup for dispatch
     * 
     * @param boolean $forceUpdate
     * @return \SMSMobiltekMT
     */
    public function setForceUpdate($forceUpdate)
    {
        $this->_forceUpdate = $forceUpdate;
        return $this;
    }

    /**
     * Return wap tab name
     * 
     * @return string
     */
    public function getWapInfo()
    {
        return $this->_wapInfo;
    }

    /**
     * Set wap tab name
     * 
     * @param string $wapInfo
     * @return \SMSMobiltekMT
     */
    public function setWapInfo($wapInfo)
    {
        $this->_wapInfo = $wapInfo;
        return $this;
    }

    /**
     * Return wap url
     * 
     * @return string
     */
    public function getWapUrl()
    {
        return $this->_wapUrl;
    }

    /**
     * Set wap url
     * 
     * @param string $wapUrl
     * @return \SMSMobiltekMT
     */
    public function setWapUrl($wapUrl)
    {
        $this->_wapUrl = $wapUrl;
        return $this;
    }

    /**
     * Returns send URL
     * 
     * @return string
     * @throws UnexpectedValueException
     */
    protected function _get_send_url()
    {
        $data = array(
            self::PARAM_LOGIN => $this->getGateUser(),
            self::PARAM_PASS => $this->getGatePassword(),
            self::PARAM_SERVICE_ID => $this->getGateServiceId(),
            self::PARAM_ORIG => $this->getOriginator(),
            self::PARAM_SCHEDULE => $this->_format_date($this->getSchedule()),
        );
        if ( $this->getText() !== null ) {
            $data[self::PARAM_TEXT] = $this->_text_encode($this->getText());
        } else if ( $this->getWapInfo() !== null ) {
            $data[self::PARAM_WAP_INFO] = $this->getWapInfo();
            $data[self::PARAM_WAP_URL] = $this->getWapUrl();
        } else if ( $this->getText() !== null AND $this->getWapInfo() !== null ) {
            throw new UnexpectedValueException("U can't send wap/push with text param.", 2);
        }
        ($this->getTelephoneNumber() !== null) AND $data[self::PARAM_DESTS] = $this->getNumberPrefix() . $this->getTelephoneNumber() . ';' . $this->_text_encode($this->getText());
        ($this->isContinue()) AND $data[self::PARAM_CONTINUE] = "true";
        ($this->isForceUpdate()) AND $data[self::PARAM_FORCE_UPDATE] = "true";

        if ( in_array(null, $data) ) {
            throw new UnexpectedValueException("Not enough data to send sms.", 1);
        }
        return $this->getGateURL() . self::SEND_URL_PATH . "?" . http_build_query($data);
    }

    /**
     * Send request to SMS gate server
     * 
     * @param string $uri
     * @return boolean 
     */
    protected function _request($uri)
    {
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $uri);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $this->_parse_respone(curl_exec($curl));

        if ( curl_error($curl) ) {
            $this->response['status'] = -1;
            $this->response['error'] = curl_error($curl);
            return false;
        }
        if ( $this->response['error'] ) {
            return false;
        }
        curl_close($curl);
        return true;
    }

    /**
     * Parse SMS gate server response
     * 
     * @param string $response 
     */
    protected function _parse_respone($response)
    {
        $response = explode(self::RESPONSE_SEPARATOR, rtrim($response, self::RESPONSE_SEPARATOR));
        if ( is_array($response) ) {
            $this->response['status'] = $response[0];
            if ( $this->response['status'] == self::RESPONSE_OK ) {
                unset($response[0]);
                $this->response['data'] = $response;
            } else {
                $this->response['error'] = $response[1];
            }
        }
    }

    /**
     * Returns date formatted for API
     * 
     * @param string $date DateTime object string i.e. "+1 day" max "+72 hours"
     * @return string 
     */
    protected function _format_date($date)
    {
        $dateTime = new DateTime($date);
        return $dateTime->format("Y-m-d H:i:s");
    }

    /**
     * Return date TIMESTAMP
     * 
     * @param string $date date formatted by API
     * @return string
     * @throws UnexpectedValueException 
     */
    protected function _unformat_date($date)
    {

        $dateTime = DateTime::createFromFormat('YmdHis', $date);
        if ( is_object($dateTime) ) {
            return $dateTime->format("U");
        }
        throw new UnexpectedValueException('Expected date format is YYYYMMddHHmmss', 5);
    }

    /**
     * Encode text to properly escape ';' character
     * 
     * @param string $text
     */
    protected function _text_encode($text)
    {
        return str_replace(';', '\073', $text);
    }

    /**
     * Send SMS by gate
     * 
     * @return boolean
     */
    public function send()
    {
        $this->_request($this->_get_send_url());
        if ( $this->response['error'] ) {
            $this->actionStatus = parent::ACTION_STATUS_ERROR;
            $this->dataExplained['' . parent::KEY_RESPONSE_STATUS . ''] = $this->response['status'];
            $this->dataExplained['' . parent::KEY_ERROR_CODE] = $this->response['error'];
            return false;
        } else {
            $this->actionStatus = ($this->response['status'] == self::RESPONSE_OK) ? parent::ACTION_STATUS_SUCCESS : parent::ACTION_STATUS_FAIL;
            $this->dataExplained['' . parent::KEY_RESPONSE_STATUS . ''] = $this->response['status'];
            $this->dataExplained['' . parent::KEY_ERROR_CODE . ''] = $this->response['data'][1];
            return true;
        }
    }

    /**
     * Obtain information about sended SMS 
     * 
     * @return boolean 
     */
    public function info()
    {
        $this->actionStatus = parent::ACTION_STATUS_FAIL;
        $this->dataExplained['' . parent::KEY_RESPONSE_STATUS . ''] = 1;
        return false;
    }

    /**
     * Confirm SMS delivery
     * 
     * @return boolean 
     */
    public function confirm()
    {
        $this->actionStatus = parent::ACTION_STATUS_FAIL;
        $this->dataExplained['' . parent::KEY_RESPONSE_STATUS . ''] = 1;
        return false;
    }

    /**
     * Listen for request from gate
     * 
     * @return string
     */
    public function get()
    {
        if ( isset($_REQUEST[self::PARAM_TYPE]) ) {
            $this->actionStatus = parent::ACTION_STATUS_SUCCESS;
            $this->dataExplained['' . parent::KEY_RESPONSE_STATUS . ''] = null;
            $this->dataExplained['' . parent::KEY_SMS_ID . ''] = isset($_REQUEST[self::PARAM_SMS_ID]) ? $_REQUEST[self::PARAM_SMS_ID] : null;
            $this->dataExplained['' . parent::KEY_TYPE . ''] = isset($_REQUEST[self::PARAM_TYPE]) ? $_REQUEST[self::PARAM_TYPE] : null;
            $this->dataExplained['' . parent::KEY_TEXT . ''] = isset($_REQUEST[self::PARAM_TEXT]) ? $_REQUEST[self::PARAM_TEXT] : null;
            $this->dataExplained['' . parent::KEY_SERVICE_ID . ''] = isset($_REQUEST[self::PARAM_SERVICE_ID]) ? $_REQUEST[self::PARAM_SERVICE_ID] : null;
            $this->dataExplained['' . parent::KEY_SMSC_RECIVED_DATE . ''] = isset($_REQUEST[self::PARAM_RECIVED_TIME]) ? $this->_unformat_date($_REQUEST[self::PARAM_RECIVED_TIME]) : null;
            $this->dataExplained['' . parent::KEY_ORIG . ''] = isset($_REQUEST[self::PARAM_ORIG]) ? $_REQUEST[self::PARAM_ORIG] : null;
            $this->dataExplained['' . parent::KEY_DEST . ''] = isset($_REQUEST[self::PARAM_DEST]) ? $_REQUEST[self::PARAM_DEST] : null;
            $this->dataExplained['' . parent::KEY_OPERATOR_ID . ''] = isset($_REQUEST[self::PARAM_OPERATOR_ID]) ? $_REQUEST[self::PARAM_OPERATOR_ID] : null;
            $this->dataExplained['' . self::KEY_BC . ''] = isset($_REQUEST[self::PARAM_BC]) ? $_REQUEST[self::PARAM_BC] : null;
            $this->dataExplained['' . self::KEY_EXPIRE_DATE . ''] = isset($_REQUEST[self::PARAM_EXPIRE_DATE]) ? $this->_unformat_date($_REQUEST[self::PARAM_EXPIRE_DATE]) : null;
        } else {
            throw new RuntimeException('The variables in $_REQUEST are NOT provided to the script via the GET or POST method.', 1);
        }
        print self::RESPONSE_OK;
    }

}