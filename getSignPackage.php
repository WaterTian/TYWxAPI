<?php

  	$appId     = 'wxbc6fe0817c412923';
	$appSecret = 'd4624c36b6795d1d99dcf0547af5443d';
	
	getSignPackage();

	function getSignPackage() {
		global $appId,$appSecret;


		$url = $_GET['path'];
		if(empty($url)){
			$result = array();
			$result['errorCode'] = 2000;
			$result['msg'] = 'path empty';
			response($result);
		}
	    $jsapiTicket = getJsApiTicket();

	    // 注意 URL 一定要动态获取，不能 hardcode.
	    // $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
	    // $url = "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

	    $timestamp = time();
	    $nonceStr = createNonceStr();

	    // 这里参数的顺序要按照 key 值 ASCII 码升序排序
	    $string = "jsapi_ticket=$jsapiTicket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";

	    $signature = sha1($string);
	    $signPackage = array(
		    "appId"     => $appId,
		    "nonceStr"  => $nonceStr,
		    "timestamp" => $timestamp,
		    "url"       => $url,
		    "signature" => $signature,
		    "rawString" => $string,
	    );
	    response($signPackage);
  	}
  	function createNonceStr($length = 16) {
    	$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    	$str = "";
    	for ($i = 0; $i < $length; $i++) {
      		$str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
    	}
    	return $str;
  	}
  	function getJsApiTicket() {
  		global $appId,$appSecret;
    
	    $ticket = '';
	    // jsapi_ticket 应该全局存储与更新，以下代码以写入到文件中做示例
        $tokenFile = "./access_token.txt";//缓存文件名
        $data = json_decode(file_get_contents($tokenFile));
        if ($data->expire_time < time() or !$data->expire_time)
        {
	      	$accessToken = getAccessToken();
	      	// 如果是企业号用以下 URL 获取 ticke
	      	$url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=$accessToken";
	      	$res = json_decode(file_get_contents($url));
	      	$ticket = $res->ticket;
	      	if ($ticket) {
	        	$data = array();
	        	$data['app_id'] = $appId;
	        	$data['access_token'] = $accessToken;
	        	$data['jsapi_ticket'] = $ticket;
	        	$data['expire_time'] = time() + 7000;

	        	$fp = fopen($tokenFile, "w");
		        fwrite($fp, json_encode($data));
		        fclose($fp);
	      	}
        }else {
	      	$ticket = $data->jsapi_ticket;;
	    }
	    return $ticket;
	}

  	function getAccessToken() {
  		global $appId,$appSecret;

    	$url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$appId&secret=$appSecret";
    	$res = json_decode(file_get_contents($url));
    	$access_token = $res->access_token;
    	return $access_token;
  	}


  	function httpGet($url) {
	    $curl = curl_init();
	    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt($curl, CURLOPT_TIMEOUT, 500);
	    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
	    curl_setopt($curl, CURLOPT_URL, $url);

	    $res = curl_exec($curl);
	    curl_close($curl);

	    return $res;
  	}
  	function response($data){
		if(array_key_exists('callback',$_GET)){
			$callback = $_GET['callback'];
			echo $callback.'('.json_encode($data).')';exit();
		}
		echo json_encode($data);exit();
	}
