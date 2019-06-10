<html>
<head>
    <title>访问控制</title>
</head>
<body bgcolor="#dcdcdc">
<center>
    <h3>添加权限</h3>
    <?php
    require_once '../config/functions.php';
    $conn=connectdb();
    $Uid=$_COOKIE["uid"];
    //查询登录的人的角色等级
    $sql_l="select max(`Rolelevel`) from roles where ID in (
              select distinct Rid from userroles where Uid='{$Uid}')";
    $result_l=mysqli_query($conn,$sql_l);
    $row_l=mysqli_fetch_assoc($result_l);
    if($row_l["max(`Rolelevel`)"] > 1) {
    echo "<a href='showuser.php'>查看组员</a>&nbsp;&nbsp;&nbsp;";
    echo "<a href='showrole.php'>查看角色</a><br>";
    }
    echo "<a href='javascript:window.history.back()'>返回</a>&nbsp;&nbsp;&nbsp;";
    echo '<a href="signout.php">退出登录</a><br><br>';
    ?>
    <form action="../action/peraction.php?action=addper" enctype="multipart/form-data" method="post" >
        <table border="0"  width="400">
            <tr>
                <td align="right">权限名</td>
                <td><input type="text"  name="pername" /></td>
            </tr>
            <tr>
                <td colspan="2" align="center">
                    <input type="submit"  value="添加" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <input type="reset"  value="重置" />
                </td>
            </tr>
        </table>
    </form>
</center>
</body>
</html>