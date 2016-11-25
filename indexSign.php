<?php
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    $url = rawurlencode("$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");
    $signPackage = json_decode(file_get_contents("http://www.southint.com/TYWxAPI/getSignPackage.php?path=$url"),true);
?>


<!DOCTYPE html>
<meta charset="utf-8" /> 
<script src="https://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>

<script>
    wx.config({
      debug: true,
      appId: '<?php echo $signPackage["appId"];?>',
      timestamp: <?php echo $signPackage["timestamp"];?>,
      nonceStr: '<?php echo $signPackage["nonceStr"];?>',
      signature: '<?php echo $signPackage["signature"];?>',
      jsApiList: [
          'checkJsApi',
          'onMenuShareTimeline',
          'onMenuShareAppMessage',
          'onMenuShareQQ',
          'onMenuShareWeibo'
      ]
    });
    var sharePath="http://www.southint.com/";
    var shareImg="";

    wx.ready(function(){
        // 分享到朋友圈
        wx.onMenuShareTimeline({
            title: 'title', // 分享标题
            link: sharePath, // 分享链接
            imgUrl: shareImg, // 分享图标
            success: function () {
                // 用户确认分享后执行的回调函数
            },
            cancel: function () {
                // 用户取消分享后执行的回调函数
            }
        });
        //分享给朋友
        wx.onMenuShareAppMessage({
            title: '标题', // 分享标题
            desc: 'desc', // 分享描述
            link: sharePath, // 分享链接
            imgUrl: shareImg, // 分享图标
            type: 'link', // 分享类型,music、video或link，不填默认为link
            dataUrl: '', // 如果type是music或video，则要提供数据链接，默认为空
            success: function () {
                // 用户确认分享后执行的回调函数
            },
            cancel: function () {
                // 用户取消分享后执行的回调函数
            }
        });
    });
    
</script>
