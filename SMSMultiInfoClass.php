<?php

require 'smsClass.php';

/**
 * Send SMS by MultiInfo gate
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
class SMSMultiInfo extends SMS implements SMSable
{
    /**
     * @desc path to send action
     */
    const SEND_URL_PATH = 'sendsms.aspx';
    /**
     * @desc path to send action  max chars 1377
     */
    const SEND_LONG_URL_PATH = 'sendsmslong.aspx';
    /**
     * @desc path to info action
     */
    const INFO_URL_PATH = 'infosms.aspx';
    /**
     * @desc path to confirm action
     */
    const CONFIRM_URL_PATH = 'confirmsms.aspx';
    /**
     * @desc path to get action
     */
    const GET_URL_PATH = 'getsms.aspx';
    /**
     * @desc server response separator
     */
    const RESPONSE_SEPARATOR = '\n';
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
    const PARAM_PASS = 'password';
    /**
     * @desc url param service id
     * 
     * required: yes <br />
     * type: string
     */
    const PARAM_SERVICE_ID = 'serviceId';
    /**
     * @desc url param SMS message
     * 
     * required: yes <br />
     * type: string max 160 chars
     */
    const PARAM_TEXT = 'text';
    /**
     * @desc url param telephone number with prefix
     * 
     * required: yes <br />
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
    const PARAM_DELIV_NOTIF_REQUEST = 'delivNotifRequest';
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
    const PARAM_ADVANCED_ENCODING = 'advancedEncoding';
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
    const PARAM_SMS_IN_ID = 'smsInId';
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
    const PARAM_SMS_ID = 'smsId';
    /**
     * @desc param url for timeout
     * 
     * required: no <br />
     * type: integer
     */
    const PARAM_TIMEOUT = 'timeout';

    /**
     * Returns send URL
     * 
     * @desc depends on string lenght returns short or long url
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
            self::PARAM_DEST => $this->getNumberPrefix() . $this->getTelephoneNumber(),
        );
        (!is_null($this->getOriginator())) AND $data[self::PARAM_ORIG] = $this->getOriginator();
        (!is_null($this->getValidTo())) AND $data[self::PARAM_VALID_TO] = $this->_format_date($this->getValidTo());
        ($this->isDelivNotifRequest()) AND $data[self::PARAM_DELIV_NOTIF_REQUEST] = "true";
        ($this->isZeroClass()) AND $data[self::PARAM_ZEROCLASS] = "true";
        ($this->isAdvancedEncoding()) AND $data[self::PARAM_ADVANCED_ENCODING] = "true";
        ($this->isDeleteContent()) AND $data[self::PARAM_DELETE_CONTENT] = "true";
        if ( in_array(null, $data) ) {
            throw new UnexpectedValueException("Not enough data to send sms.", 1);
        }
        if ( mb_strlen($this->getText()) <= 160 ) {
            return $this->getGateURL() . self::SEND_URL_PATH . "?" . http_build_query($data);
        } else {
            return $this->getGateURL() . self::SEND_LONG_URL_PATH . "?" . http_build_query($data);
        }
    }

    /**
     * Returns info URL
     * 
     * @return string
     * @throws UnexpectedValueException
     */
    protected function _get_info_url()
    {
        $data = array(
            self::PARAM_LOGIN => $this->getGateUser(),
            self::PARAM_PASS => $this->getGatePassword(),
            self::PARAM_SMS_ID => $this->getSMSId(),
        );
        if ( in_array(null, $data) ) {
            throw new UnexpectedValueException("Not enough data to recive status info.", 2);
        }
        return $this->getGateURL() . self::INFO_URL_PATH . "?" . http_build_query($data);
    }

    /**
     * Returns confirm URL
     * 
     * @return string
     * @throws UnexpectedValueException
     */
    protected function _get_confirm_url()
    {
        $data = array(
            self::PARAM_LOGIN => $this->getGateUser(),
            self::PARAM_PASS => $this->getGatePassword(),
            self::PARAM_SMS_ID => $this->getSMSId(),
        );
        ($this->getDeleteContent()) AND $data[self::PARAM_DELETE_CONTENT] = "true";
        if ( in_array(null, $data) ) {
            throw new UnexpectedValueException("Not enough data to confirm sms.", 3);
        }
        return $this->getGateURL() . self::CONFIRM_URL_PATH . "?" . http_build_query($data);
    }

    /**
     * Returns get URL
     * 
     * @return string
     * @throws UnexpectedValueException
     */
    protected function _get_get_url()
    {
        $data = array(
            self::PARAM_LOGIN => $this->getGateUser(),
            self::PARAM_PASS => $this->getGatePassword(),
            self::PARAM_SERVICE_ID => $this->getGateServiceId(),
        );
        ($this->getDeleteContent()) AND $data[self::PARAM_DELETE_CONTENT] = "true";
        ($this->getTimeout()) AND $data[self::PARAM_TIMEOUT] = $this->getTimeout();
        if ( in_array(null, $data) ) {
            throw new UnexpectedValueException("Not enough data to get sms.", 4);
        }
        return $this->getGateURL() . self::GET_URL_PATH . "?" . http_build_query($data);
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
        curl_setopt($curl, CURLOPT_SSLCERT, $this->getSslCertPath());
        curl_setopt($curl, CURLOPT_SSLCERTPASSWD, $this->getSslCertPass());
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
        return $dateTime->format("dmyHms");
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
        $split = str_split($date, 2);
        $year = date("Y");
        try {
            $dateTime = new DateTime("{$year[0]}{$year[1]}{$split[2]}-{$split[1]}-{$split[0]} {$split[3]}:{$split[4]}:{$split[5]}");
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
            return false;
        } else {
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
        $this->_request($this->_get_info_url());

        if ( $this->response['error'] ) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Confirm SMS delivery
     * 
     * @return boolean 
     */
    public function confirm()
    {
        $this->_request($this->_get_confirm_url());

        if ( $this->response['error'] ) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * get SMS from gate
     * 
     * @return boolean 
     */
    public function get()
    {
        $this->_request($this->_get_get_url());

        if ( $this->response['error'] ) {
            return false;
        } else {
            return true;
        }
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
        isset($this->response['data'][1]) ? $data[self::PARAM_SMS_ID] = $this->response['data'][1] : $data['errorCode'] = $this->response['error'];
        isset($this->response['data'][2]) AND $data['type'] = $this->response['data'][2];
        isset($this->response['data'][3]) AND $data[self::PARAM_TEXT] = $this->response['data'][3];
        isset($this->response['data'][4]) AND $data['protocolId'] = $this->response['data'][4];
        isset($this->response['data'][5]) AND $data['charsetSchemeId'] = $this->response['data'][5];
        isset($this->response['data'][6]) AND $data[self::PARAM_SERVICE_ID] = $this->response['data'][6];
        isset($this->response['data'][7]) AND $data['connectorId'] = $this->response['data'][7];
        isset($this->response['data'][8]) AND $data[self::PARAM_SMS_IN_ID] = $this->response['data'][8];
        isset($this->response['data'][9]) AND $data['priority'] = $this->response['data'][9];
        isset($this->response['data'][10]) AND $data['sendDate'] = $this->_unformat_date($this->response['data'][10]);
        isset($this->response['data'][11]) AND $data['validToDate'] = $this->_unformat_date($this->response['data'][11]);
        isset($this->response['data'][12]) AND $data[self::PARAM_DELIV_NOTIF_REQUEST] = $this->response['data'][12];
        isset($this->response['data'][13]) AND $data[self::PARAM_ORIG] = $this->response['data'][13];
        isset($this->response['data'][14]) AND $data[self::PARAM_DEST] = $this->response['data'][14];
        isset($this->response['data'][15]) AND $data['smsStatus'] = $this->response['data'][15];
        isset($this->response['data'][16]) AND $data['statusChangeDate'] = $this->response['data'][16];

        return $data;
    }

}