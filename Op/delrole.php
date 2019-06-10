<?php
echo '<body bgcolor="#dcdcdc">';
echo "<center>";
echo "<h3>删除角色</h3>";
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
if(isset($_GET["rid"])) {
    $rid=$_GET["rid"];
    //删除用户角色表中的记录
    $sql1="select * from userroles where Rid='{$rid}'";
    $result1=mysqli_query($conn,$sql1);
    $dateCount1=mysqli_num_rows($result1);
    if($dateCount1>0) {//有用户是该角色
        $sql2 = "delete from userroles where Rid='{$rid}'";
        $result2 = mysqli_query($conn, $sql2);
        if (mysqli_affected_rows($conn) > 0) {//用户角色表删除成功
            echo "用户角色表删除成功！<br>";
        } else {
            echo "用户角色表删除失败！<br>" . mysqli_error($conn);
        }
    }
    //删除角色权限表中的记录
    $sql3="select * from rolepermissions where Rid='{$rid}'";
    $result3=mysqli_query($conn,$sql3);
    $dateCount3=mysqli_num_rows($result3);
    if($dateCount3>0) {//该角色有权限
        $sql4 = "delete from rolepermissions where Rid='{$rid}'";
        $result4 = mysqli_query($conn, $sql4);
        if (mysqli_affected_rows($conn) > 0) {//角色权限表删除成功
            echo "角色权限表删除成功！<br>";
        } else {
            echo "角色权限表删除失败！<br>" . mysqli_error($conn);
        }
    }
    //删除角色表中的记录
    $sql5="delete from roles where ID='{$rid}'";
    $result5=mysqli_query($conn,$sql5);
    if(mysqli_affected_rows($conn)>0) {//角色权限表删除成功
        echo "角色表删除成功！<br>";
    }
    else{
        echo "角色表删除失败！<br>". mysqli_error($conn);
    }
}else {
    echo "未获取到要删除的角色信息！";
}
echo '</body>';
mysqli_close($conn);
echo "</center>";
?>