<?php
	//global $_W;
	
class model_sms {
	


    public function sendSms($key,$secret,$signName, $templateCode, $phoneNumbers, $templateParam = null) {

		$apiParams["RegionId"] = 'cn-hangzhou';
		$apiParams["AccessKeyId"] = $key;
		$apiParams["Format"] = 'JSON';
		$apiParams["SignatureMethod"] = 'HMAC-SHA1';
		$apiParams["SignatureVersion"] = '1.0';
		$apiParams["SignatureNonce"] = uniqid();
		date_default_timezone_set("GMT");
		$apiParams["Timestamp"] = date('Y-m-d\TH:i:s\Z');
		$apiParams["Action"] = 'SendSms';
		$apiParams["Version"] = '2017-05-25';
		$apiParams["OutId"] = '123';

		$apiParams["PhoneNumbers"] = $phoneNumbers;
		$apiParams["SignName"] = $signName;
		$apiParams["TemplateCode"] = $templateCode;
		$apiParams["TemplateParam"] = $templateParam;

		$apiParams["Signature"] = $this->computeSignature($apiParams, $secret);

		$http = 'http://dysmsapi.aliyuncs.com/';


        //$res =  util::httpGet( $http.$paurl );
        $headers["x-sdk-client"] = "php/2.0.0";
        $res =  $this->curl( $http,'POST',$apiParams, $headers);
        
        return $res;
    }



	private function computeSignature($parameters, $accessKeySecret)
	{

	    ksort($parameters);

	    $canonicalizedQueryString = '';
	    foreach($parameters as $key => $value)
	    {
			$canonicalizedQueryString .= '&' . $this->percentEncode($key). '=' . $this->percentEncode($value);
	    }
	    
	    $stringToSign = 'POST&%2F&' . $this->percentEncode(substr($canonicalizedQueryString, 1));
 	    
	    $signature = $this->signString($stringToSign, $accessKeySecret."&");

	    return $signature;
	}
	
	public function signString($source, $accessSecret)
	{
		
		return	base64_encode(hash_hmac('sha1', $source, $accessSecret, true));
	}

	protected function percentEncode($str)
	{
	    $res = urlencode($str);
	    $res = preg_replace('/\+/', '%20', $res);
	    $res = preg_replace('/\*/', '%2A', $res);
	    $res = preg_replace('/%7E/', '~', $res);
	    return $res;
	}

	public static function curl($url, $httpMethod = "GET", $postFields = null,$headers = null)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $httpMethod); 
				
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_FAILONERROR, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, is_array($postFields) ? self::getPostHttpBody($postFields) : $postFields);
		
		
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		
		
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
		
		//https request
		if(strlen($url) > 5 && strtolower(substr($url,0,5)) == "https" ) {
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		}
		if (is_array($headers) && 0 < count($headers))
		{
			$httpHeaders =self::getHttpHearders($headers);
			curl_setopt($ch,CURLOPT_HTTPHEADER,$httpHeaders);
		}
		$res = curl_exec($ch);

		if (curl_errno($ch))
		{
	
			return curl_errno($ch);
		}
		curl_close($ch);
		return $res;
	}

	static function getPostHttpBody($postFildes){		
		$content = "";
		foreach ($postFildes as $apiParamKey => $apiParamValue)
		{			
			$content .= "$apiParamKey=" . urlencode($apiParamValue) . "&";
		}
		return substr($content, 0, -1);
	}
	static function getHttpHearders($headers)
	{
		$httpHeader = array();
		foreach ($headers as $key => $value)
		{
			array_push($httpHeader, $key.":".$value);	
		}
		return $httpHeader;
	}

}

