<?php
/**
 * ReferralCandy PHP API Client by LikeBtn.com
 * https://github.com/LikeBtn/ReferralCandy
 *
 * @author LikeBtn.com <support@likebtn.com>
 */

namespace ReferralCandy;

class ReferralCandy {

    const API_URL = 'https://my.referralcandy.com/api/v1/';

    const MESSAGE_SUCCESS = 'Success';

    public static $methods = array(
        'verify' => array('http_method' => 'get'),
        'referrals' => array('http_method' => 'get'),
        'referrer' => array('http_method' => 'get'),
        'contacts' => array('http_method' => 'get'),
        'rewards' => array('http_method' => 'get'),
        'purchase' => array('http_method' => 'post'),
        'referral' => array('http_method' => 'post'),
        'signup' => array('http_method' => 'post'),
        'invite' => array('http_method' => 'post'),
        'unsubscribed' => array('http_method' => 'put'),
    );

    protected $access_id;
    protected $secret_key;
    protected $logger;

    public function __construct($access_id, $secret_key)
    {
        $this->access_id  = $access_id;
        $this->secret_key = $secret_key;
    }

    /**
     * API Request
     */
    public function doRequest($method, $params = array()) {
        $return = array(
            'success' => false,
            'error_msg' => '',
            'http_code' => 200,
            'response' => array()
        );
        if (!array_key_exists($method, self::$methods)) {
            $return['error_msg'] = 'Method '.$method.' is not supported.';
            return $return;
        }

        $url = self::API_URL.$method.'.json?';

        try {
            $response_string = $this->curl($url, self::$methods[$method]['http_method'], $this->addSignature($params));
        } catch(\Exception $e) {
            $return['error_msg'] = $e->getMessage();
            if ($e->getCode()) {
                $return['http_code'] = $e->getCode();
            }
            return $return;
        }

        try {
            $response = $this->jsonDecode($response_string);
        } catch(\Exception $e) {
            $return['error_msg'] = $e->getMessage();
            return $return;
        }

        if (!is_array($response) || !$response) {
            $return['error_msg'] = 'Empty response from API.';
        } else {
            $return['success'] = true;
            $return['response'] = $response;
        }

        return $return;
    }

    /**
     * Add signature to parameters
     */
    public function addSignature($params) {
        $params['timestamp'] = time();
        $params['accessID'] = $this->access_id;
        ksort($params);
        $params['signature'] = $this->signature($params);

        return $params;
    }

    /**
     * Calculate signature
     */
    public function signature($params) {

        $collected_params = '';
        foreach ($params as $name => $value) {
            $collected_params .= $name."=".$value;
        }

        return md5($this->secret_key.$collected_params);
    }

    /**
     * Retrieve data
     */
    public function curl($url, $http_method = 'get', $params = array()) {

        if (!function_exists('curl_init')) {
            throw new \Exception("curl is not enabled in your PHP", 0);
        }

        $ch = curl_init();
        
        if ($http_method == 'post') {
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        } elseif ($http_method == 'put') {
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        } else {
            curl_setopt($ch, CURLOPT_URL, $url.http_build_query($params));
        }
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $result = curl_exec($ch);

        if ($result === false) {
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error_msg = curl_error($ch);
            curl_close($ch);

            throw new \Exception($error_msg, $http_code);
        }

        return $result;
    }

    /**
     * Decode JSON
     */
    public function jsonDecode($jsong_string) {
        if (!is_string($jsong_string)) {
            return array();
        }
        if (!function_exists('json_decode')) {
            throw new \Exception("json_decode function is not enabled in your PHP", 1);
        }

        return json_decode($jsong_string, true);
    }
}
