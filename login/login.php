<?php
/**
 * Created by PhpStorm.
 * Op: Lenovo
 * Date: 2018/12/30
 * Time: 22:46
 */
header("Content-type:text/html;charset=utf-8");

require_once '../config/functions.php';
$conn=connectdb();

$username = isset($_GET['logname'])?$_GET['logname']:"username不存在";
$password = isset($_GET['logpass'])?$_GET['logpass']:"password不存在";
$result_ID=mysqli_query($conn,"select * from  `users`  where `Username`='{$username}'  and  `Password`='{$password}'");

if(mysqli_errno($conn)){
    echo mysqli_error($conn);
}else {
    $dataCount = mysqli_num_rows($result_ID);
    if($dataCount>0){
        $row_ID=mysqli_fetch_assoc($result_ID);
        $result=mysqli_query($conn,"select max(`Rolelevel`),Rolename from `roles` where `ID`=(select distinct `Rid` from `userroles` where `Uid`={$row_ID['ID']})");
        $row=mysqli_fetch_assoc($result);
        $time = time() + 60 * 60 * 24 * 7;  //保存一周
        setcookie("islogin",true,$time,"/");
        setcookie("username", $username, $time,"/");
        setcookie("uid", $row_ID["ID"], $time,"/");
        header("Location:../Op/index.php");
    }
    else{
        echo '<script language="JavaScript">alert("用户名或密码错误");location.href="login.html";</script>;';
    }
}


