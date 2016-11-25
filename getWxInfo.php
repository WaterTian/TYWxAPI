<?php

  	$appId     = 'wxbc6fe0817c412923';
	$appSecret = 'd4624c36b6795d1d99dcf0547af5443d';

	getUserInfo();

  	function getUserInfo(){
  		global $appId,$appSecret;

  		$code = $_GET['code'];
  		if($code == ''){
  			echo 'code err，empty';exit;
  		}

	    $getTokenUrl = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=$appId&secret=$appSecret&code=$code&grant_type=authorization_code";
	    
	    $r = json_decode(file_get_contents($getTokenUrl));

	    // setcookie("openid",$r->openid, time()+3600*2,"/");
	    // setcookie("access_token",$r->access_token,time()+3600*2,"/");

	    $info = getInfo($r->access_token, $r->openid);
	    response($info);
  	}

  	function getInfo($access_token, $openid){
	    $appInfoUrl = "https://api.weixin.qq.com/sns/userinfo?access_token=$access_token&openid=$openid&lang=zh_CN";
	    return json_decode(file_get_contents($appInfoUrl));
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
