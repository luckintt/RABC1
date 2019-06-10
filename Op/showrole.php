<html>
<head>
    <title>角色信息</title>
</head>
<body bgcolor="#dcdcdc">
<center>
    <h3>浏览角色信息</h3>
    <?php
    header("Content-type:text/html;charset=utf-8");
    require_once '../config/functions.php';
    $conn=connectdb();
    $Uid=$_COOKIE["uid"];
    //查询登录的人的角色等级
    $sql_l="select max(`Rolelevel`) from `roles` where `ID` in (                                
                  select distinct `Rid` from `userroles` where `Uid`='{$Uid}')";
    $result_l=mysqli_query($conn,$sql_l);
    $row_l=mysqli_fetch_assoc($result_l);
    /*查询出该用户的角色*/
    $sqlr="select Rolename,r.ID from roles r,userroles ur where Rolelevel={$row_l['max(`Rolelevel`)']}
                            and Uid='{$Uid}' and r.ID=ur.Rid";
    $resultr=mysqli_query($conn,$sqlr);
    $rowr=mysqli_fetch_assoc($resultr);
    
    if($row_l["max(`Rolelevel`)"] > 1) {
        echo "<a href='showuser.php'>查看组员</a>&nbsp;&nbsp;&nbsp;";
        echo "<a href='index.php'>查看权限</a><br>";
    }
    echo "<a href='javascript:window.history.back()'>返回</a>&nbsp;&nbsp;&nbsp;";
    echo '<a href="signout.php">退出登录</a><br><br>';
    ?>
    <table border="1">
        <tr>
            <th>角色</th>
            <th>组员</th>
            <th>权限</th>
            <th colspan="2">角色操作</th>
        </tr>
        <?php
        //找出等级比登录的人低的组员名与ID
        $sql="select Username,Rolename,Rolelevel,r1.ID from users u1,roles r1 ,userroles ur
	            where u1.ID=ur.Uid  and r1.ID=ur.Rid  and u1.ID not in ( 
		          select  u2.ID from users u2,roles r2 ,userroles ur2  /*找出所有等级比自己高的用户ID*/
			        where u2.ID=ur2.Uid and r2.ID=ur2.Rid and r2.Rolelevel>={$row_l['max(`Rolelevel`)']})
			    order by Rolename";
        $result=mysqli_query($conn,$sql);
        $dateCount=mysqli_num_rows($result);
        $j=-1;
        $tmp="";
        $tmpuser="";//记录组员
        $id="";     //记录组员的角色ID
        for($i=0;$i<$dateCount;$i++) {
            $result_arr = mysqli_fetch_assoc($result);
            if($i==0) {
                $tmp = $result_arr['Rolename'];
                $tmpuser=$result_arr['Username'];
                $level=$result_arr['Rolelevel'];
                $id=$result_arr['ID'];
                if($i==($dateCount-1)){//只有一条记录，也可以查找出没有权限的角色
                    $sql2 = "select distinct Permission from permissions where ID in(
	                            select distinct Pid from rolepermissions where Rid in(
	                              select distinct ID from roles where Rolelevel < {$level} or ID={$id}))";
                    $result2 = mysqli_query($conn, $sql2);
                    $tmpper = "";
                    while ($row2 = mysqli_fetch_assoc($result2)) {
                        $tmpper = $tmpper . "   ". $row2['Permission'] ;
                    }
                    $j++;
                    $Username[$j] = $tmpuser;
                    $Rolename[$j] = $tmp;
                    $Permission[$j] = $tmpper;
                    $Id[$j]=$id;
                }
            }
            else{
                if ($tmp == $result_arr['Rolename'] && $i!=($dateCount-1)) {//角色相等但不是最后一条记录
                    $tmpuser = $tmpuser."   ".$result_arr['Username'];
                }
                else {
                    $p=1;//角色相等且是最后一条记录   或   角色不相等但不是最后一条记录
                    if($tmp != $result_arr['Rolename'] && $i==($dateCount-1)){//角色不相等但是最后一条记录,此时要将最后一条记录插入
                        $p=2;
                    }
                    else if($tmp == $result_arr['Rolename'] && $i==($dateCount-1)){//角色相等且是最后一条记录
                        $tmpuser = $tmpuser."   ".$result_arr['Username'];
                    }
                    /*查询该角色具有的权限*/
                    while($p>0) {
                        $sql3 = "select distinct Permission from permissions where ID in(
	                                select distinct Pid from rolepermissions where Rid in(
	                                    select distinct ID from roles where Rolelevel < {$level} or ID={$id}))";
                        $result3 = mysqli_query($conn, $sql3);
                        $tmpper = "";
                        while ($row3 = mysqli_fetch_assoc($result3)) {
                            $tmpper = $tmpper . "   " . $row3['Permission'];
                        }
                        $j++;
                        $Username[$j] = $tmpuser;
                        $Rolename[$j] = $tmp;
                        $Permission[$j] = $tmpper;
                        $Id[$j]=$id;
                        $tmp = $result_arr['Rolename'];
                        $tmpuser = $result_arr['Username'];
                        $level=$result_arr['Rolelevel'];
                        $id = $result_arr['ID'];
                        $p--;
                    }
                }
            }
        }
        //查找没有用户且等级比当前用户低的角色
        $sql4="select * from roles where Rolelevel<{$row_l['max(`Rolelevel`)']} and ID not in(select Rid from userroles)";
        $result4=mysqli_query($conn,$sql4);
        $dateCount4=mysqli_num_rows($result4);

        //解析结果集
        for ($k=0;$k<=$j+$dateCount4;$k++){
            echo "<tr>";
            if($k<=$j) {
                echo "<td align='center'>{$Rolename[$k]}</td>";
                echo "<td align='center'>{$Username[$k]}</td>";
                echo "<td align='center'>{$Permission[$k]}</td>";
            }
            else{
                $row4=mysqli_fetch_assoc($result4);
                $Rolename[$k]=$row4['Rolename'];
                $Id[$k]=$row4['ID'];
                $sql5 = "select distinct Permission from permissions where ID in(
	                            select distinct Pid from rolepermissions where Rid in(
	                              select distinct ID from roles where Rolelevel < {$row4['Rolelevel']} or ID={$Id[$k]}))";
                $result5 = mysqli_query($conn, $sql5);
                $tmpper = "";
                while ($row5 = mysqli_fetch_assoc($result5)) {
                    $tmpper = $tmpper . "   ". $row5['Permission'] ;
                }
                echo "<td align='center'>$Rolename[$k]</td>";
                echo "<td align='center'>&nbsp;</td>";
                echo "<td align='center'>$tmpper</td>";
            }
            if (isset($_COOKIE["username"]) && $rowr["Rolename"]=="Admin") {
//              echo '<a href="../login/signup.html">添加用户</a>&nbsp;&nbsp;&nbsp;<a href="../action/useraction.php?action=deluser">删除用户</a><br>';
                echo "<td align='center'>
                       <a  href='../action/roleaction.php?action=delrole&rid={$Id[$k]}'> 删除</a>";
                if($k==0) {
                    $col = $j +$dateCount4+ 1;
                    echo "<td align='center' rowspan='{$col}'>
                           <a  href='addrole.php'> 添加</a>";
                }
            }
            else {
                echo "<td align='center'
                       <a  href='#'onclick='return false'><font color='gray'>删除</font></a>";
                if($k==0) {
                    $col = $j +$dateCount4+ 1;
                    echo "<td align='center' rowspan='{$col}'>
                           <a  href='#'onclick='return false'><font color='gray'>添加</font></a>";
                }
            }
            echo "</tr>";
        }
        //释放结果集，关闭数据库
        mysqli_close($conn);
        ?>
    </table>
</center>
</body>
</html>