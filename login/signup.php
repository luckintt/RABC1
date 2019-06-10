<?php
/**
 * Created by PhpStorm.
 * User: Lenovo
 * Date: 2019/1/4
 * Time: 17:39
 */
header("Content-type:text/html;charset=utf-8");
echo '<body bgcolor="#dcdcdc">';
echo "<center>";
echo "<h3>注册组员</h3>";
require_once '../config/functions.php';

$conn=connectdb();
$Uid=$_COOKIE["uid"];
//查询登录的人的角色等级
$sql_l="select max(`Rolelevel`) from `roles` where `ID` in (                                
                  select distinct `Rid` from `userroles` where `Uid`='{$Uid}')";
$result_l=mysqli_query($conn,$sql_l);
$row_l=mysqli_fetch_assoc($result_l);

echo "<a href='../Op/showuser.php'>查看组员</a>&nbsp;&nbsp;&nbsp;";
echo "<a href='../Op/showrole.php'>查看角色</a><br>";
echo "<a href='javascript:window.history.back()'>返回</a>&nbsp;&nbsp;&nbsp;";
echo '<a href="../Op/signout.php">退出登录</a><br><br>';

$username = isset($_GET['signname'])?$_GET['signname']:"username不存在";
$password = isset($_GET['signpass'])?$_GET['signpass']:"password不存在";
$role = isset($_GET['signrole'])?$_GET['signrole']:"role不存在";
$result_u=mysqli_query($conn,"select * from  `users`  where `Username`='{$username}'");
$result_r=mysqli_query($conn,"select * from  `roles`  where `Rolename`='{$role}'");
$row_r = mysqli_fetch_assoc($result_r);
if($row_r['Rolelevel']>=$row_l['max(`Rolelevel`)']){
    echo $row_r['Rolelevel']."注册用户的角色等级只能低于登录用户的等级！".$row_l['max(`Rolelevel`)'];
}
else {
    if (mysqli_errno($conn)) {
        echo mysqli_error($conn);
    } else {
        $dataCount_u = mysqli_num_rows($result_u);
        $dataCount_r = mysqli_num_rows($result_r);
        if ($dataCount_u > 0) {
            echo "该用户名已经存在！<br>";
        }
        if ($dataCount_r <= 0) {
            echo "该角色不存在！<br>";
        }
        if ($dataCount_u <= 0 && $dataCount_r > 0) {
            $sql_u = "insert into `users`(`Username`,`Password`) values ('{$username}','{$password}')";
            mysqli_query($conn, $sql_u);

            if (mysqli_affected_rows($conn) > 0) {
                echo "用户注册成功！<br>";
                $sql_s = "select * from  `users`  where `Username`='{$username}'  and  `Password`='{$password}'";
                $result = mysqli_query($conn, $sql_s);
                //更新用户角色表
                $row_u = mysqli_fetch_assoc($result);
                $sql_ur = "insert into `userroles`(`Uid`,`Rid`) values ('{$row_u['ID']}','{$row_r['ID']}')";
                mysqli_query($conn, $sql_ur);
                if (mysqli_affected_rows($conn) > 0)
                    echo "用户角色表更新成功！<br>";
                else
                    echo "用户角色表更新失败！<br>";
            } else {
                echo "用户注册失败！" . mysqli_error($conn);
            }
        }
    }
}
echo "</center>";
echo "</body>";