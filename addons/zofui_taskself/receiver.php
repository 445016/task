<?php
/**
 * 订阅器
 *
 * @author 众惠
 * @url http://bbs.we7.cc/
 */
global $_W;
defined('IN_IA') or exit('Access Denied');
define('TSTB_ROOT',IA_ROOT.'/addons/zofui_taskself/');
define('TSTB_URL',$_W['siteroot'].'addons/zofui_taskself/');
define('MODULE','zofui_taskself');

class Zofui_taskselfModuleReceiver extends WeModuleReceiver {

	public function receive() {
		global $_W;
        $_W['dev'] = 'wx';
        
//file_put_contents(TSTB_ROOT."/params.log", var_export(POSETERH_ROOT, true).PHP_EOL, FILE_APPEND);				
		$content = $this->message;
 		

		if( ( $content['event'] == 'subscribe' || $content['event'] == 'SCAN' ) && !empty( $content['scene'] )  ){

            if( $_W['account']['level'] != 4 ) return false;
            
            if( !class_exists( 'Util' ) ){
                include TSTB_ROOT.'class/Util.class.php';
            }
            if( !class_exists( 'model_user' ) ){
                include TSTB_ROOT.'class/model_user.class.php';
            }
            if( !class_exists( 'Message' ) ){
                include TSTB_ROOT.'class/Message.class.php';
            }
            if( !class_exists( 'model_mess' ) ){
                include TSTB_ROOT.'class/model_mess.class.php';
            }         


            $flag = Util::getCache('doing',$content['from']);
            $now = self::toMicTime();
            if( $flag >= $now ){
                $this->sendText($content['from'], '请不要频繁扫二维码...');
                return false;
            }
            Util::setCache('doing',$content['from'], $now+2000 );


			$qr = pdo_get('zofui_tasktb_selfqrcode',array('uniacid'=>$_W['uniacid'],'sence'=>$content['scene']));

            if( $qr['openid'] == $content['from'] ){
                $this->sendText($content['from'], '自己扫码无效...');
                Util::deleteCache('doing',$content['from']);
                return false;
            }

            $parentinfo = model_user::getSingleUser( $qr['userid'] );
	

			if( empty( $qr ) || empty( $parentinfo ) ){
				Util::deleteCache('doing',$content['from']);
				return false;
			}
           
            $poster = pdo_get('zofui_tasktb_selfposter',array('uniacid'=>$_W['uniacid']),array('content'));

            // 再验证一次 防止多次扫码触发
            $flag = Util::getCache('doing',$content['from']);
            if(  $flag != ($now+2000) ){
                return false;
            }

            $res = model_user::bindDown( $content['from'],$parentinfo );

	           
            if( $res && !empty( $poster['content'] ) ){
                $this->sendText( $content['from'],$poster['content'] );
            }
            
            
            Util::deleteCache('doing',$content['from']);
		}
        
        
        return true;
	}


    public function sendText($openid, $text)
    {
        $post = '{"touser":"' . $openid . '","msgtype":"text","text":{"content":"' . $text . '"}}';
        $ret = $this->sendRes($this->getAccessToken(), $post);
        return $ret;
    }
    public function sendNews($openid, $response)
    {
        $data = array("touser" => $openid, "msgtype" => "news", "news" => array("articles" => $response));
        $ret = $this->sendRes($this->getAccessToken(), $this->json_encode2($data));
        return $ret;
    }
    private function json_encode2($arr)
    {
        array_walk_recursive($arr, function (&$item, $key) {
            if (is_string($item)) {
                $item = mb_encode_numericentity($item, array(0x80, 0xffff, 0, 0xffff), 'UTF-8');
            }
        });
        return mb_decode_numericentity(json_encode($arr), array(0x80, 0xffff, 0, 0xffff), 'UTF-8');
    }
    public function sendImage($openid, $media_id)
    {
        $data = array("touser" => $openid, "msgtype" => "image", "image" => array("media_id" => $media_id));
        $ret = $this->sendRes($this->getAccessToken(), json_encode($data));
        return $ret;
    }
    private function sendRes($access_token, $data)
    {
        $url = "https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token={$access_token}";
        load()->func('communication');
        $ret = ihttp_request($url, $data);

        $content = @json_decode($ret['content'], true);
        return $content['errmsg'];
    }
    private function uploadImage($img)
    {
        $url = "http://file.api.weixin.qq.com/cgi-bin/media/upload?access_token=" . $this->getAccessToken() . "&type=image";
        $post = array('media' => '@'.$img);
        load()->func('communication');
        $ret = ihttp_request($url, $post);       
        $content = @json_decode($ret['content'], true);
        return $content['media_id'];
    }
    private function getAccessToken()
    {
        global $_W;
        load()->model('account');
        $acid = $_W['acid'];
        if (empty($acid)) {
            $acid = $_W['uniacid'];
        }
        $account = WeAccount::create($acid);
        $token = $account->fetch_available_token();
        return $token;
    }

    static function toMicTime(){
        $time = microtime();
        $list = explode(' ', $time);
        return $list[1].intval( $list[0]*1000 );
    }

}