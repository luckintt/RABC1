<html>
<head>
    <title>注销</title>
</head>
<body bgcolor="#dcdcdc">
<center>
    <?php
        header("Content-type:text/html;charset=utf-8");
        $tag=@$_COOKIE["islogin"];
        if(@$_COOKIE["islogin"]) {
            setcookie("islogin", false,time() - 3600);
            setcookie("username",time() - 3600);
            setcookie("uid", time() - 3600);
        }
        header("refresh:3;url=../login/login.html");
        if($tag)
            print('您已成功退出登录！<br>正在加载，请稍等...<br>三秒后自动跳转~~~');
        else
            print('您还没有登录！<br>正在加载，请稍等...<br>三秒后自动跳转~~~');
    ?>
</center>

</body>
</html>