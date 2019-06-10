<html>
<head>
    <title>权限信息</title>
    <script>
        function getkey(a,value) {
            var perValue=document.getElementById(value.innerHTML).innerHTML;

            var fdStart1 = perValue.indexOf("read");
            var fdStart2 = perValue.indexOf("Read");
            var fdStart3 = perValue.indexOf("write");
            var fdStart4 = perValue.indexOf("Write");

            if(fdStart1 == 0 || fdStart2==0){
                var tmp=perValue.substring(4);
                window.location.href="readfile.php?filename="+tmp;
            }else if(fdStart3 == 0 || fdStart4==0){
                var tmp=perValue.substring(5);
                window.location.href="writefile.php?filename="+tmp;
            } else{
                alert("您有："+perValue+"权限");
            }
        }
    </script>
</head>
<body bgcolor="#dcdcdc">
<center>
    <h3>浏览权限信息</h3>
    <?php
    header("Content-type:text/html;charset=utf-8");
    require_once '../config/functions.php';

    $conn=connectdb();
    $Uid=$_COOKIE["uid"];
    $Username=$_COOKIE['username'];
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
        echo "<a href='showuser.php'>查看组员</a>&nbsp;&nbsp;&nbsp;";
        echo "<a href='showrole.php'>查看角色</a><br>";
    }
    echo "<a href='javascript:window.history.back()'>返回</a>&nbsp;&nbsp;&nbsp;";
    echo '<a href="signout.php">退出登录</a><br>';
    echo 'Hello  '.$Username.'，你的角色是'.$rowr['Rolename'].'<br><br>';
    ?>
    <table border="1"  width="700">
        <tr>
            <th>权限</th>
            <th colspan="2">权限操作</th>
        </tr>
        <?php
        /*查询该角色所有权限*/
        $sql="select distinct * from permissions where ID in(
                select distinct Pid from rolepermissions where Rid in (
                   select distinct ID from roles where Rolelevel < {$row_l['max(`Rolelevel`)']} or ID={$rowr['ID']}));";
        $result=mysqli_query($conn,$sql);
        $dateCount=mysqli_num_rows($result);
        for($i=0;$i<$dateCount;$i++) {
            $row = mysqli_fetch_assoc($result);
            echo "<tr>";
            echo "<td align='center'><button id='{$row['Permission']}' onclick='getkey(this,{$row['Permission']})'>{$row['Permission']}</button></td>";

            if (isset($_COOKIE["username"]) && $rowr["Rolename"]=="Admin") {
                echo "<td align='center'>
                    <a  href='../action/peraction.php?action=delper&pid={$row['ID']}'> 删除</a>";
//              echo '<a href="../login/signup.html">添加用户</a>&nbsp;&nbsp;&nbsp;<a href="../action/useraction.php?action=deluser">删除用户</a><br>';
                if($i==0) {
                    echo "<td align='center' rowspan='{$dateCount}'>
                           <a  href='addper.php'> 添加</a>";
                }
            }
            else {
                echo "<td align='center'>
                       <a  href='#'onclick='return false'><font color='gray'>删除</font></a>";
                if ($i == 0) {
                    echo "<td align='center' rowspan='{$dateCount}'>
                           <a  href='#'onclick='return false'><font color='gray'>添加</font></a>";
                }
            }
            echo "</tr>";
 //           echo "<button id='but' onclick='show(this)' width='30px' height='50px'>{$row['Permission']}</button><br>";
        }
        //释放结果集，关闭数据库
        mysqli_close($conn);
        ?>
</center>
</body>
</html>