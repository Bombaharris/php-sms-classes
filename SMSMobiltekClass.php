<?php

require 'SMSClass.php';

/**
 * Send SMS by Mobiltek gate
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
class SMSMobiltek extends SMS implements SMSable
{
    /**
     * @desc path to send action
     */

    const SEND_URL_PATH = 'send.php';
    /**
     * @desc server response separator
     */
    const RESPONSE_SEPARATOR = PHP_EOL;
    /**
     * @desc server response separator
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
     * required: yes <br />
     * type: string
     */
    const PARAM_TEXT = 'text';
    /**
     * @desc url param SMS message encoded in base64
     * 
     * required: yes <br />
     * type: string
     */
    const PARAM_B64_TEXT = 'b64text';
    /**
     * @desc url param telephone number with prefix
     * 
     * required: no <br />
     * type: string
     */
    const PARAM_DEST = 'dest';
    /**
     * @desc url param valid to date
     * 
     * required: yes <br />
     * type: date
     * value: date string formatted ddMMyyHHmmss
     */
    const PARAM_VALID_TO = 'validTo';
    /**
     * @desc  param url for deliver notify request
     * 
     * required: no <br />
     * default: false <br />
     * type: boolean
     */
    const PARAM_DELIV_NOTIF_REQUEST = 'delrq';
    /**
     * @desc  url param for zeroclass message
     * 
     * required: no <br />
     * default: false <br />
     * type: boolean
     */
    const PARAM_ZEROCLASS = 'zeroclass';
    /**
     * @desc param url for advanced encoding
     * 
     * required: no <br />
     * default: false <br />
     * type: boolean
     */
    const PARAM_ADVANCED_ENCODING = 'encoding';
    /**
     * @desc param url for delete content
     * 
     * required: no <br />
     * default: false <br />
     * type: boolean
     */
    const PARAM_DELETE_CONTENT = 'deleteContent';
    /**
     * @desc param url for resposne SMS numeric identifier
     * 
     * required: no <br />
     * default: null<br />
     * type: integer
     */
    const PARAM_SMS_IN_ID = 'id';
    /**
     * @desc param url for alphanumeric originator address (up to 11 characters)
     * 
     * required: no <br />
     * type: string
     */
    const PARAM_ORIG = 'orig';
    /**
     * @desc param url for SMS numeric identifier
     * 
     * required: yes <br />
     * type: integer
     */
    const PARAM_SMS_ID = 'id';
    /**
     * @desc param url for timeout
     * 
     * required: no <br />
     * type: integer
     */
    const PARAM_TIMEOUT = 'timeout';
    /**
     * @desc param url for manual confrim while getting sms
     * 
     * required: no <br />
     * default: false <br />
     * type: boolean
     */
    const PARAM_MANUAL_CONFIRM = 'manualconfirm';
    /**
     * @desc param url for manual confrim while getting sms
     * 
     * required: no <br />
     * type: string
     */
    const PARAM_SCHEDULE = 'schedule';
    /**
     * @desc url param unix timestamp date when sms was recived by operator
     * 
     * required: no <br />
     * type: string
     */
    const PARAM_RECIVED_TIME = 'tmux';
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
     * @desc information about type of method of recivng multipart messages:
     *  - splitted
     *  - joined
     * 
     * required: no <br />
     * type: striog
     */
    const PARAM_MPART_TYPE = 'mpart_type';
    /**
     * @desc information about number of parts of (joined) message
     * 
     * required: no <br />
     * type: integer
     */
    const PARAM_MPART_PARTS = 'mpart_parts';
    /**
     * @desc numeric identifier of (splitted) message the same for all splitted
     * 
     * required: no <br />
     * type: integer
     */
    const PARAM_MPART_ID = 'mpart_id';
    /**
     * @desc define number of part of  (splitted) message
     * 
     * required: no <br />
     * type: integer
     */
    const PARAM_MPART_NO = 'mpart_no';
    /**
     * @desc define max number of parts of  (splitted) message
     * 
     * required: no <br />
     * type: integer
     */
    const PARAM_MPART_MAX = 'mpart_max';

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
            self::PARAM_TEXT => $this->getText(),
            self::PARAM_SMS_ID => $this->getSMSId(),
            self::PARAM_DEST => $this->getNumberPrefix() . $this->getTelephoneNumber(),
        );
        (!is_null($this->getOriginator())) AND $data[self::PARAM_ORIG] = $this->getOriginator();
        (!is_null($this->getValidTo())) AND $data[self::PARAM_VALID_TO] = $this->_format_date($this->getValidTo());
        ($this->isDelivNotifRequest()) AND $data[self::PARAM_DELIV_NOTIF_REQUEST] = "true";
        ($this->isZeroClass()) AND $data[self::PARAM_ZEROCLASS] = "true";
        ($this->isAdvancedEncoding()) AND $data[self::PARAM_ADVANCED_ENCODING] = "UTF-8";
        ($this->isDeleteContent()) AND $data[self::PARAM_DELETE_CONTENT] = "true";
        (!is_null($this->getSchedule())) AND $data[self::PARAM_SCHEDULE] = $this->_format_date($this->getSchedule());

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
            if ( $this->response['status'] >= 0 ) {
                unset($response[0]);
                $this->response['data'] = $response;
            } else {
                $this->response['error'] = $response[1];
            }
        }
    }

    /**
     * Returns date formatted for API HTTPS SMS
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
     * @param string $date date formatted by API HTTPS SMS
     * @return string
     * @throws UnexpectedValueException 
     */
    protected function _unformat_date($date)
    {
        try {
            $dateTime = new DateTime($date);
            return $dateTime->format("U");
        } catch ( Exception $e ) {
            throw new UnexpectedValueException($e->getMessage(), 5);
        }
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
            $this->dataExplained['' . parent::KEY_RESPONSE_STATUS . ''] = $this->response['status'];
            $this->dataExplained['' . parent::KEY_ERROR_CODE] = $this->response['error'];
            return false;
        } else {
            $this->dataExplained['' . parent::KEY_RESPONSE_STATUS . ''] = $this->response['status'];
            $this->dataExplained['' . parent::KEY_SMS_ID . ''] = $this->getSMSId();
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
        if ( isset($_REQUEST[self::PARAM_SMS_ID]) ) {
            $this->dataExplained['' . parent::KEY_RESPONSE_STATUS . ''] = 0;
            $this->dataExplained['' . parent::KEY_SMS_ID . ''] = isset($_REQUEST[self::PARAM_SMS_ID]) ? $_REQUEST[self::PARAM_SMS_ID] : null;
            $this->dataExplained['' . parent::KEY_TEXT . ''] = isset($_REQUEST[self::PARAM_TEXT]) ? $_REQUEST[self::PARAM_TEXT] : null;
            $this->dataExplained['' . parent::KEY_SERVICE_ID . ''] = isset($_REQUEST[self::PARAM_SERVICE_ID]) ? $_REQUEST[self::PARAM_SERVICE_ID] : null;
            $this->dataExplained['' . parent::KEY_SMSC_RECIVED_DATE . ''] = isset($_REQUEST[self::PARAM_RECIVED_TIME]) ? $_REQUEST[self::PARAM_RECIVED_TIME] : null;
            $this->dataExplained['' . parent::KEY_ORIG . ''] = isset($_REQUEST[self::PARAM_ORIG]) ? $_REQUEST[self::PARAM_ORIG] : null;
            $this->dataExplained['' . parent::KEY_DEST . ''] = isset($_REQUEST[self::PARAM_DEST]) ? $_REQUEST[self::PARAM_DEST] : null;
            $this->dataExplained['' . parent::KEY_OPERATOR_ID . ''] = isset($_REQUEST[self::PARAM_OPERATOR_ID]) ? $_REQUEST[self::PARAM_OPERATOR_ID] : null;
            $this->dataExplained['' . parent::KEY_MPART_TYPE . ''] = isset($_REQUEST[self::PARAM_MPART_TYPE]) ? $_REQUEST[self::PARAM_MPART_TYPE] : null;
            $this->dataExplained['' . parent::KEY_MPART_PARTS . ''] = isset($_REQUEST[self::PARAM_MPART_PARTS]) ? $_REQUEST[self::PARAM_MPART_PARTS] : null;
            $this->dataExplained['' . parent::KEY_MPART_ID . ''] = isset($_REQUEST[self::PARAM_MPART_ID]) ? $_REQUEST[self::PARAM_MPART_ID] : null;
            $this->dataExplained['' . parent::KEY_MPART_NO . ''] = isset($_REQUEST[self::PARAM_MPART_NO]) ? $_REQUEST[self::PARAM_MPART_NO] : null;
            $this->dataExplained['' . parent::KEY_MPART_MAX . ''] = isset($_REQUEST[self::PARAM_MPART_MAX]) ? $_REQUEST[self::PARAM_MPART_MAX] : null;
        } else {
            throw new RuntimeException('The variables in $_REQUEST are NOT provided to the script via the GET or POST method.', 1);
        }
        print self::RESPONSE_OK;
    }

}