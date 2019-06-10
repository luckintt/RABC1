<?php
echo '<body bgcolor="#dcdcdc">';
echo "<center>";
echo "<h3>删除权限</h3>";
require_once '../config/functions.php';
$conn=connectdb();
$Uid=$_COOKIE["uid"];
//查询登录的人的角色等级
$sql_l="select max(`Rolelevel`) from `roles` where `ID` in (                                
                  select distinct `Rid` from `userroles` where `Uid`='{$Uid}')";
$result_l=mysqli_query($conn,$sql_l);
$row_l=mysqli_fetch_assoc($result_l);
if($row_l["max(`Rolelevel`)"] > 1) {
    echo "<a href='showrole.php'>查看角色</a>&nbsp;&nbsp;&nbsp;";
    echo "<a href='index.php'>查看权限</a><br>";
}
echo "<a href='javascript:window.history.back()'>返回</a>&nbsp;&nbsp;&nbsp;";
echo '<a href="signout.php">退出登录</a><br><br>';
if(isset($_GET["pid"])) {
    $pid=$_GET["pid"];
    $sql_rp="delete from rolepermissions where Pid='$pid'"; //从角色权限表中删除该权限
//    echo $sql;
    mysqli_query($conn,$sql_rp);
    if(mysqli_affected_rows($conn)){
        $sql_p="delete from permissions where ID='$pid'";
        mysqli_query($conn,$sql_p);
        if(mysqli_affected_rows($conn)) {
            echo "<h5>权限删除成功</h5>";
        }
        else{
            echo "<h5>权限表删除失败</h5>".mysqli_error($conn);
        }
    }else{
        echo "<h5>角色权限表删除失败</h5>".mysqli_error($conn);
    }
} else {
    echo "未获取到要删除的权限信息！";
}
echo '</body>';
/*
echo "<a href='javascript:window.history.back()'>返回</a>";
echo "&nbsp;&nbsp;&nbsp";
echo "<a href='showuser.php'>浏览用户信息</a>";
*/
mysqli_close($conn);
echo "</center>";
?>