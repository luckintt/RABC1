<?php
/**
 * Created by PhpStorm.
 * Op: Lenovo
 * Date: 2018/12/30
 * Time: 22:18
 */
require_once 'dbconfig.php';

/*连接数据库*/
function connectdb(){
    $conn=mysqli_connect(HOST,USER,PW);
    if(!$conn){
        die('Can  not  connect  db');
    }
    mysqli_select_db($conn,DBNAME);
    return  $conn;
}

echo "<center>";
function checkLogin(){
    if(!@$_COOKIE["islogin"]){
        echo "<h3>登录之后才能进行此操作！确定跳转到登录界面？</h3>";
        echo "<a href='javascript:window.history.back()'>返回</a>";
        echo "&nbsp;&nbsp;&nbsp";
        echo "<a href='../login/login.html'>登录</a>";
        return false;
    }
    else 
        return true;
}
echo "</center>";