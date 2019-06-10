<html>
<head>
    <title>访问控制</title>
</head>
<body bgcolor="#dcdcdc">
<center>
    <h3>添加角色</h3>
    <?php
    require_once '../config/functions.php';
    $conn=connectdb();
    $Uid=$_COOKIE["uid"];
    //查询登录的人的角色等级
    $sql_l="select max(`Rolelevel`) from roles where ID in (
              select distinct Rid from userroles where Uid='{$Uid}')";
    $result_l=mysqli_query($conn,$sql_l);
    $row_l=mysqli_fetch_assoc($result_l);
    /*查询出该用户的角色*/
    $sqlr="select Rolename,r.ID from roles r,userroles ur where Rolelevel={$row_l['max(`Rolelevel`)']}
                            and Uid='{$Uid}' and r.ID=ur.Rid";
    $resultr=mysqli_query($conn,$sqlr);
    $rowr=mysqli_fetch_assoc($resultr);

    if($row_l["max(`Rolelevel`)"] > 1) {
        echo "<a href='showrole.php'>查看角色</a>&nbsp;&nbsp;&nbsp;";
        echo "<a href='index.php'>查看权限</a><br>";
    }
    echo "<a href='javascript:window.history.back()'>返回</a>&nbsp;&nbsp;&nbsp;";
    echo '<a href="signout.php">退出登录</a><br><br>';
    ?>
    <form action="../action/roleaction.php?action=addrole" enctype="multipart/form-data" method="post" >
        &nbsp;&nbsp;&nbsp;&nbsp;用户名：<input type="text" name="username" placeholder="用户名不能为空"> <br>
        &nbsp;&nbsp;&nbsp;&nbsp;角色名：<input type="text" name="rolename" placeholder="角色名不能为空"> <br>
        角色等级：
        <select name="rolelevel">
        <?php
        for($i=1;$i<$row_l['max(`Rolelevel`)'];$i++){
            echo "<option value='{$i}'>{$i}</option>";
        }
        ?>
        </select><br>
        角色权限：
            <?php
            /*查询该角色所有权限*/
            $sql="select distinct * from permissions where ID in(
                    select distinct Pid from rolepermissions where Rid in (
                      select distinct ID from roles where Rolelevel < {$row_l['max(`Rolelevel`)']} or ID={$rowr['ID']}));";
            $result=mysqli_query($conn,$sql);
            $dateCount=mysqli_num_rows($result);
            for($i=0;$i<$dateCount;$i++){
                $row=mysqli_fetch_assoc($result);
                echo "<input align='left' type='checkbox' name='per[]' value={$row['ID']}>{$row['Permission']}";
            }
            ?>
        <br>
        <input  type="submit"  value="提交">
        <input  type="reset"  value="重填">
    </form>
</center>
</body>
</html>