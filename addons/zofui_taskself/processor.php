<?php
/**
 * 模块处理程序
 *
 * @author 众惠科技
 * @url http://www.zy40.cn/
 */
global $_W;
defined('IN_IA') or exit('Access Denied');
define('TSTB_ROOT',IA_ROOT.'/addons/zofui_taskself/');
define('TSTB_URL',$_W['siteroot'].'addons/zofui_taskself/');
define('MODULE','zofui_taskself');

class Zofui_taskselfModuleProcessor extends WeModuleProcessor {
	public function respond() {
		global $_W;
		$content = $this->message;
		$_W['dev'] = 'wx';
			
		if( ( $content['msgtype'] == 'event' && $content['event'] == 'CLICK' ) || $content['msgtype'] == 'text' ){
			$key = pdo_get('zofui_tasktb_selfposter',array('uniacid'=>$_W['uniacid'],'key'=>$content['content']),array('key','pid','ccontent'));
			
//file_put_contents(POSETERH_ROOT."params.log", var_export($key, true).PHP_EOL, FILE_APPEND);
			
			if( !empty( $key ) ){
				
	            if( !class_exists( 'Util' ) ){
	                include TSTB_ROOT.'class/Util.class.php';
	            }
	            if( !class_exists( 'model_user' ) ){
	                include TSTB_ROOT.'class/model_user.class.php';
	            }
	            if( !class_exists( 'Message' ) ){
	                include TSTB_ROOT.'class/Message.class.php';
	            }
	            if( !class_exists( 'model_poster' ) ){
	                include TSTB_ROOT.'class/model_poster.class.php';
	            }
                if( !class_exists( 'model_mess' ) ){
                    include TSTB_ROOT.'class/model_mess.class.php';
                } 
                

				if( $_SESSION['___posttime'] > TIMESTAMP ) {
					return $this->respText('请'.($_SESSION['___posttime'] - TIMESTAMP).'秒后再试...');
				}
				$_SESSION['___posttime'] = TIMESTAMP + 20;
				$this->sendText($content['from'], '处理中，请稍候...');
				
				$user = pdo_get('zofui_task_user',array('uniacid'=>$_W['uniacid'],'openid'=>$content['from']));

				if( $user['status'] == 2 ) return $this->respText('您不能生成');

				if( empty( $user ) ) {
					$res = model_user::bindDown( $content['from'],array() );
					if( $res ){
						$user = model_user::getSingleUser( $content['from'] );
					}else{
						return $this->respText('您不能生成');
					}
				}

				$res = model_poster::getPoster($user);
                if( $res['status'] == 200 ){
                    $media = $this->uploadImage($res['res']['dir']);
                    $this->sendImage($content['from'], $media);

                    if( !empty( $key['ccontent'] ) ) {
                        return $this->respText( $key['ccontent'] );
                    }
                }else{
                    return $this->respText( $res['res'] );
                }


				

			}

			

		}

		
		
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
