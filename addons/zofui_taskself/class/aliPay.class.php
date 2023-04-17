<?php 

class aliPay
{
		
	public $prikey = '';

	//转账到支付宝
	public function sendMoneyToUser($appid,$toaccount,$toname,$fee,$prikey) {
		global $_W;
		

		// 需要验证php版本大于5.5
		if(version_compare(PHP_VERSION,'5.6.0', '<')){
			return array('status'=>false,'msg'=>'php版本必须大于等于5.6');
		}
		
		if( empty( $toaccount ) ) return array('status'=>false,'msg'=>'收款账户不正确');
		if( empty( $toname ) ) return array('status'=>false,'msg'=>'收款方姓名不正确');
		if( $fee <= 0 ) return array('status'=>false,'msg'=>'付款金额不正确');
		if( empty( $prikey ) ) return array('status'=>false,'msg'=>'还没有设置应用私钥，在参数设置-提现参数内设置');

		$data['app_id'] = $appid;
		$data['method'] = 'alipay.fund.trans.toaccount.transfer';
		$data['format'] = 'JSON';
		$data['charset'] = 'UTF-8';
		$data['sign_type'] = 'RSA2';
		$data['timestamp'] = date('Y-m-d H:i:s',time());
		$data['version'] = '1.0';

		$out_biz_no = date('YmdHis',time()).rand(111,999);
		$payee_type = 'ALIPAY_LOGONID';
		$payee_account = $toaccount; // 收款方账户
		$payee_real_name = $toname; // 收款方姓名
		$remark = '提现支付';

		$data['biz_content'] = "{" .
		"\"out_biz_no\":\"".$out_biz_no."\"," .
		"\"payee_type\":\"".$payee_type."\"," .
		"\"payee_account\":\"".$payee_account."\"," .
		"\"amount\":\"".$fee."\"," .
		"\"payee_real_name\":\"".$payee_real_name."\"," .
		"\"remark\":\"".$remark."\"" .
		"}";


		$data['sign'] = $this->generateSign( $data,$prikey );
			
		if( !$data['sign'] ){
			return array('status'=>false,'msg'=>'应用私钥不正确');
		}

		$requestUrl ="https://openapi.alipay.com/gateway.do?";
		foreach ($data as $sysParamKey => $sysParamValue) {
			$requestUrl .= "$sysParamKey=" . urlencode($this->characet($sysParamValue, $this->postCharset)) . "&";
		}
		$requestUrl = substr($requestUrl, 0, -1);
		$re = Util::httpGet($requestUrl);
		$res = json_decode( $re,true );
		if( !is_array( $res ) || empty( $res['alipay_fund_trans_toaccount_transfer_response'] ) ){
			return array('status'=>false,'msg'=>'转账失败');
		}
		if( $res['alipay_fund_trans_toaccount_transfer_response']['code'] == "10000" && $res['alipay_fund_trans_toaccount_transfer_response']['msg'] == "Success" ){
			return array('status'=>true,'msg'=>'转账成功','bizno'=>$res['alipay_fund_trans_toaccount_transfer_response']['out_biz_no'],'orderid'=>$res['alipay_fund_trans_toaccount_transfer_response']['order_id']);
		}else{
			return array('status'=>false,'msg'=>'转账失败,原因:'.$res['alipay_fund_trans_toaccount_transfer_response']['sub_msg']);
		}
		
	}


	public function generateSign($params,$prikey) {

		return $this->sign($this->getSignContent($params),$prikey);
	}


	public function sign($data,$prikey) {
			
		$res = "-----BEGIN RSA PRIVATE KEY-----\n" .
			wordwrap($prikey, 64, "\n", true) .
			"\n-----END RSA PRIVATE KEY-----";

		if(!$res){
			return false;
		}
		
		openssl_sign($data, $sign, $res,OPENSSL_ALGO_SHA256);
		//openssl_sign($data, $sign, $res,SHA256);
		$sign = base64_encode($sign);
		return $sign;
	}


	public function getSignContent($params) {
		ksort($params);

		$stringToBeSigned = "";
		$i = 0;
		foreach ($params as $k => $v) {
			if (false === $this->checkEmpty($v) && "@" != substr($v, 0, 1)) {

				if ($i == 0) {
					$stringToBeSigned .= "$k" . "=" . "$v";
				} else {
					$stringToBeSigned .= "&" . $k . "=" . "$v";
				}

				$i++;
			}
		}
		unset ($k, $v);
		
		return $stringToBeSigned;
	}
	
	/**
	 * 校验$value是否非空
	 *  if not set ,return true;
	 *    if is null , return true;
	 **/
	protected function checkEmpty($value) {
		if (!isset($value))
			return true;
		if ($value === null)
			return true;
		if (trim($value) === "")
			return true;

		return false;
	}


	/**
	 * 转换字符集编码
	 * @param $data
	 * @param $targetCharset
	 * @return string
	 */
	function characet($data, $targetCharset) {
		
		if (!empty($data)) {
			$fileType = 'UTF-8';
			if (strcasecmp($fileType, $targetCharset) != 0) {
				$data = mb_convert_encoding($data, $targetCharset, $fileType);
				//$data = iconv($fileType, $targetCharset.'//IGNORE', $data);
			}
		}
		return $data;
	}


	
	
}
