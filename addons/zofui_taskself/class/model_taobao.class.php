<?php 

class model_taobao {


	
	static function getGood( $goodurl ){
		global $_W,$_GPC;
		set_time_limit(0);
		ini_set('user_agent','Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.2; SV1; .NET CLR 1.1.4322)');	

		$data = array();
		preg_match('/&id=(\d+)/', $goodurl, $id);
		$id = $id[1];

		if( empty( $id ) ){
			preg_match('/\?id=(\d+)/', $goodurl, $id);
			$id = $id[1];
		}

		if( empty( $id ) ){ // 来自卡券
			preg_match('/itemId=(\d+)/', $goodurl, $id);
			$id = $id[1];
		}

		// 直接复制的口令 或其他链接
		if( empty( $id ) ){
			preg_match('/(http|https|ftp|file){1}(:\/\/)?([\da-z-\.]+)\.([a-z]{2,6})([\/\w \.-?&%-=]*)*\/?/',$goodurl,$urlarr);
			$goodurl = trim( $urlarr[0] );

			$rr = file_get_contents( $goodurl );
			
			preg_match('/&id=(\d+)/', $rr, $id);
			$id = $id[1];
			
			if( empty( $id ) ){
				preg_match('/\?id=(\d+)/', $rr, $id);
				$id = $id[1];						
			}

			if( empty( $id ) ){
				preg_match('/\/i(\d+)/', $rr, $id);
				$id = $id[1];				
			}
			$url = 'https://item.taobao.com/item.htm?id='.$id;
			
		}else{
			$url = $goodurl;
		}

		if(empty($id)) return false;
		
		$taourl = 'http://hws.m.taobao.com/cache/wdetail/5.0/?id='.$id;
		$jsonstr = Util::httpGet($taourl);
		$arr = json_decode((string)$jsonstr,true);
		

		if( empty($arr['data']['itemInfoModel']) ) {

			$contenta = file_get_contents( $url );

			$content = iconv('GB2312', 'UTF-8', $contenta);
			if( empty( $content ) ) {
				$content = mb_convert_encoding($contenta, "UTF-8", "GBK");
			}
			
			
			if( empty( $content ) ) return false;

			if( strpos( $goodurl , 'detail.tmall.com') !== false ){

				preg_match('/<title>(.*)<\/title>/', $content, $matches);	
				$data['title'] = str_replace('-tmall.com天猫','', $matches[1]);

				preg_match('/" src="(.*)data-hasZoom=/', $content, $img); 

				if( empty( $matches[1] ) || empty( $img[1] ) ) return false;

				$data['pic'] = str_replace('" ', '', $img[1]);

			}else{
				preg_match('/auctionImages(.*)]/', $content, $img); 
				preg_match('/<title>(.*)<\/title>/', $content, $matches);			

				if( empty( $matches[1] ) || empty( $img[1] ) ) return false;

				$data['title'] = str_replace('-淘宝网','', $matches[1]);
				$img = str_replace('    : ["', '', $img[1]);

				$list = explode('","', $img);
				$data['pic'] = $list[0];
			}
			
		}else{
			$data['title'] = $arr['data']['itemInfoModel']['title'];
			$data['pic'] = $arr['data']['itemInfoModel']['picsPath'][0];
		}
		
		if( empty( $data['title'] ) ) return false;

		
		$sku = json_decode($arr['data']['apiStack'][0]['value'],true);

		if( strpos( $sku['data']['itemInfoModel']['priceUnits'][0]['price'] , '-' ) !== false ){
			$nowprice = explode('-', $sku['data']['itemInfoModel']['priceUnits'][0]['price']);
			$data['nowprice'] = $nowprice[0];

		}else{
			$data['nowprice']  = $sku['data']['itemInfoModel']['priceUnits'][0]['price'];
		}
		
		
		$data['taourl'] = $url;
		
		/*if( !empty( $key ) ){
			$data['key'] = $key;
		}else{
			$getres = getTaoWord::getLink( $url,$data['title'],$arr['data']['itemInfoModel']['picsPath'][0] );
			
			$keycontent =  empty( $this->module['config']['keycontent'] ) ? '复制这条信息，{code}打开手机淘宝' : $this->module['config']['keycontent'];
			$data['key'] = str_replace('{code}',$getres['model'], $keycontent);

		}*/

		return $data;
	}

    

}