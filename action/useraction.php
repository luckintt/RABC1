<?php
require_once '../config/functions.php';
echo "<body bgcolor='#dcdcdc'>";
echo "<center>";
echo "<h3>权限操作</h3>";
$conn=connectdb();
$Uid=$_COOKIE["uid"];
//查询登录的人的角色等级
$sql_l="select max(`Rolelevel`) from roles where ID in (                                
                  select distinct Rid from userroles where Uid='{$Uid}')";
$result_l=mysqli_query($conn,$sql_l);
$row_l=mysqli_fetch_assoc($result_l);
if($row_l["max(`Rolelevel`)"] > 1) {
    echo "<a href='../Op/showuser.php'>查看组员</a>&nbsp;&nbsp;&nbsp;";
    echo "<a href='../Op/showrole.php'>查看角色</a><br>";
}
echo "<a href='javascript:window.history.back()'>返回</a>&nbsp;&nbsp;&nbsp;";
echo '<a href="../Op/signout.php">退出登录</a><br><br>';
echo "</center>";

switch ($_GET["action"]){
    case 'deluser':
        echo "<center>";
        $username=$_GET["username"];
        $sql="select * from users where Username='{$username}'";
        //提交用户信息
        $result=mysqli_query($conn,$sql);
        $result_arr=mysqli_fetch_assoc($result);
        $id=$result_arr['ID'];
        echo "<script LANGUAGE='javascript'>
                        if(confirm('确定要删除吗？')){
                            window.location='../Op/deluser.php?id={$id}';
                        }else{
                            window.location='javascript:window.history.back()';
                        }
                    </script>";
        echo "</center>";
        break;
    default:
        break;
}

echo "</body>";