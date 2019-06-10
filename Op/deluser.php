<?php
echo '<body bgcolor="#dcdcdc">';
echo "<center>";
echo "<h3>删除组员</h3>";
require_once '../config/functions.php';
$conn=connectdb();
$Uid=$_COOKIE["uid"];
//查询登录的人的角色等级
$sql_l="select max(`Rolelevel`) from `roles` where `ID` in (                                
                  select distinct `Rid` from `userroles` where `Uid`='{$Uid}')";
$result_l=mysqli_query($conn,$sql_l);
$row_l=mysqli_fetch_assoc($result_l);
if($row_l["max(`Rolelevel`)"] > 1) {
    echo "<a href='showuser.php'>查看组员</a>&nbsp;&nbsp;&nbsp;";
    echo "<a href='showrole.php'>查看角色</a><br>";
}
echo "<a href='javascript:window.history.back()'>返回</a>&nbsp;&nbsp;&nbsp;";
echo '<a href="signout.php">退出登录</a><br><br>';
if(isset($_GET["id"])) {
    $uid=$_GET["id"];
    $sql="select * from userroles where Uid='$uid'";
    $result=mysqli_query($conn,$sql);
    $dateCount=mysqli_num_rows($result);
    $tag=1;
    if($dateCount>0) {
        $sql_ur = "delete from userroles where Uid='$uid'"; //从用户角色表中删除该用户
        mysqli_query($conn, $sql_ur);
        if (mysqli_affected_rows($conn)) {
            echo "<h5>用户角色表删除成功</h5>";
        } else {
            $tag=0;
            echo "<h5>用户角色表删除失败</h5>" . mysqli_error($conn);
        }
    }
    if($tag){//用户角色表删除成功
        $sql_u = "delete from users where ID={$uid}";
        mysqli_query($conn, $sql_u);
        if (mysqli_affected_rows($conn)) {
            echo "<h5>用户删除成功</h5>";
        } else {
            echo "<h5>用户表删除失败</h5>" . mysqli_error($conn);
        }
    }
} else {
    echo "未获取到要删除的用户信息！";
}
echo '</body>';
mysqli_close($conn);
echo "</center>";
?>