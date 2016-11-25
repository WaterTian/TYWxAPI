<?php
    $appId     = 'wxbc6fe0817c412923';
    $cookieTime = 30; // s
    $getWxInfo = "http://www.southint.com/TYWxAPI/getWxInfo.php";

    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    $url = rawurlencode("$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");
    if (isset($_COOKIE['wxinfo'])) {
      //cookie
      // print_r($_COOKIE);
    }else{
      //uncookie
      if(!array_key_exists('code',$_GET) || trim($_GET['code']) == ""){
        //uncode uncookie
        $authorize ='https://open.weixin.qq.com/connect/oauth2/authorize?appid='.$appId.'&redirect_uri='.$url.'&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect';
        //cookie uncode url 30s
        setcookie("h5url",$url, time()+30,"/");

        header('Location:'.$authorize);
      }else{
        //code cookie
        $wx_info = json_decode(file_get_contents($getWxInfo."?code=".$_GET['code']),true);
        // print_r($wx_info);
        setcookie("wxinfo[openid]",$wx_info['openid'], time()+$cookieTime,"/");
        setcookie("wxinfo[nickname]",$wx_info['nickname'], time()+$cookieTime,"/");
        setcookie("wxinfo[headimgurl]",$wx_info['headimgurl'], time()+$cookieTime,"/");
        setcookie("wxinfo[sex]",$wx_info['sex'], time()+$cookieTime,"/");
        setcookie("wxinfo[city]",$wx_info['city'], time()+$cookieTime,"/");
        setcookie("wxinfo[country]",$wx_info['country'], time()+$cookieTime,"/");

        header('Location:'.urldecode($_COOKIE['h5url']));
      } 
    }
?>


<!DOCTYPE html>
<meta charset="utf-8" /> 

<div id="openid"></div>
<img id="myImage" src="" />
<div id="uname"></div>
<div id="sex"></div>
<div id="city"></div>
<script>
    var user_info=
    {
        "openid": "<?php echo $_COOKIE['wxinfo']["openid"];?>",
        "nickname": "<?php echo $_COOKIE['wxinfo']["nickname"];?>",
        "headimgurl":"<?php echo $_COOKIE['wxinfo']["headimgurl"];?>",
        "sex":"<?php echo $_COOKIE['wxinfo']["sex"];?>",
        "city":"<?php echo $_COOKIE['wxinfo']["city"];?>"
    }


    document.getElementById('openid').innerHTML = '<span>'+user_info["openid"]+'</span>';
    document.getElementById("myImage").src=user_info["headimgurl"];
    document.getElementById('uname').innerHTML = '<span>'+user_info["nickname"]+'</span>';
    document.getElementById('sex').innerHTML = '<span>'+user_info["sex"]+'</span>';
    document.getElementById('city').innerHTML = '<span>'+user_info["city"]+'</span>';
    
</script>
