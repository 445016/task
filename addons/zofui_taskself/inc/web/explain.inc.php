<?php 
	global $_W,$_GPC;
	model_user::wInit();
	
	
	include $this->template('web/'.$_W['mtemp'].'/explain');