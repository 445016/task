<?php
/**
 * 模块微站定义
 *
 * @author 众惠科技
 * @url http://www.zy40.cn/ 
 */
global $_W;
defined('IN_IA') or exit('Access Denied');
define('TSTB_ROOT',IA_ROOT.'/addons/zofui_taskself/');
define('TSTB_URL',$_W['siteroot'].'addons/zofui_taskself/');
define('MODULE','zofui_taskself');
require_once(TSTB_ROOT.'class/autoload.php');

class Zofui_taskselfModuleSite extends WeModuleSite {
	
	public function __construct(){
		global $_W;
		if( defined('IN_SYS') ){
			if( method_exists( get_parent_class(),'__construct' ) ){
				parent::__construct();
			}
			$_W['mtemp'] = empty( $this->module['config']['webtemp'] ) ? 'bdui' : $this->module['config']['webtemp'];
		}
	}

	//支付
	function payResult($params){
		pay::payResult($params);
	}
	
}
