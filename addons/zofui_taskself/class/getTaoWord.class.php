<?php 

class getTaoWord {

	static function getLink( $url,$title='',$pic='' ){
		global $_W;
		$c = new TopClient;

		$set = Util::getModuleConfig();

		if( empty( $set['taoappkey'] ) || empty( $set['taosecret'] ) ) return false;

		$c->appkey = $set['taoappkey'];
		$c->secretKey = $set['taosecret'];

		$logo= empty( $pic ) ? "http://m.taobao.com/xxx.jpg" : $pic;
		$url = $url;
		$text = empty( $title ) ? "点击立即查看" : $title;
		//$tpwd_param->user_id="24234234234";
		
		
		$req = new TbkTpwdCreateRequest;
		$req->setUserId("");
		$req->setText($text);
		$req->setUrl($url);
		$req->setLogo($logo);
		$req->setExt("{}");
		
		
		$resp = $c->execute($req);
		
		$res = json_decode(json_encode($resp),true);	
		
		return $res;

	}

}



class GenPwdIsvParamDto
{
	
	/** 
	 * 扩展字段JSON格式
	 **/
	public $ext;
	
	/** 
	 * 口令弹框logoURL
	 **/
	public $logo;
	
	/** 
	 * 口令弹框内容
	 **/
	public $text;
	
	/** 
	 * 口令跳转url
	 **/
	public $url;
	
	/** 
	 * 生成口令的淘宝用户ID
	 **/
	public $user_id;	
}


class TbkTpwdCreateRequest
{
	/** 
	 * 扩展字段JSON格式
	 **/
	private $ext;
	
	/** 
	 * 口令弹框logoURL
	 **/
	private $logo;
	
	/** 
	 * 口令弹框内容
	 **/
	private $text;
	
	/** 
	 * 口令跳转目标页
	 **/
	private $url;
	
	/** 
	 * 生成口令的淘宝用户ID
	 **/
	private $userId;
	
	private $apiParas = array();
	
	public function setExt($ext)
	{
		$this->ext = $ext;
		$this->apiParas["ext"] = $ext;
	}

	public function getExt()
	{
		return $this->ext;
	}

	public function setLogo($logo)
	{
		$this->logo = $logo;
		$this->apiParas["logo"] = $logo;
	}

	public function getLogo()
	{
		return $this->logo;
	}

	public function setText($text)
	{
		$this->text = $text;
		$this->apiParas["text"] = $text;
	}

	public function getText()
	{
		return $this->text;
	}

	public function setUrl($url)
	{
		$this->url = $url;
		$this->apiParas["url"] = $url;
	}

	public function getUrl()
	{
		return $this->url;
	}

	public function setUserId($userId)
	{
		$this->userId = $userId;
		$this->apiParas["user_id"] = $userId;
	}

	public function getUserId()
	{
		return $this->userId;
	}

	public function getApiMethodName()
	{
		return "taobao.tbk.tpwd.create";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
	
	public function check()
	{
		
		RequestCheckUtil::checkNotNull($this->text,"text");
		RequestCheckUtil::checkNotNull($this->url,"url");
	}
	
	public function putOtherTextParam($key, $value) {
		$this->apiParas[$key] = $value;
		$this->$key = $value;
	}
}