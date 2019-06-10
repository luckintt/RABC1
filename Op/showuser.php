<html>
<head>
    <title>组员信息</title>
</head>
<body bgcolor="#dcdcdc">
<center>
    <h3>浏览组员信息</h3>
    <?php
    header("Content-type:text/html;charset=utf-8");
    require_once '../config/functions.php';
    $conn=connectdb();
    $Uid=$_COOKIE["uid"];
    //查询登录的人的角色等级
    $sql_l="select max(`Rolelevel`) from roles  where `ID` in (                                
                  select distinct `Rid` from `userroles` where `Uid`='{$Uid}')";
    $result_l=mysqli_query($conn,$sql_l);
    $row_l=mysqli_fetch_assoc($result_l);
    /*查询出该用户的角色*/
    $sqlr="select Rolename,r.ID from roles r,userroles ur where Rolelevel='{$row_l['max(`Rolelevel`)']}'
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
    <table border="1">
        <tr>
            <th>用户</th>
            <th>角色</th>
            <th>权限</th>
            <th colspan="2">组员操作</th>
        </tr>
        <?php
        //找出等级比登录的人低的组员名
        $sql="select Username,Rolename from users u1,roles r1 ,userroles ur
	            where u1.ID=ur.Uid  and r1.ID=ur.Rid  and u1.ID not in ( 
		          select  u2.ID from users u2,roles r2 ,userroles ur2  /*找出所有等级比自己高的用户ID*/
			        where u2.ID=ur2.Uid and r2.ID=ur2.Rid and r2.Rolelevel>={$row_l['max(`Rolelevel`)']})
			    Order By Username";
        //echo $sql;
        $result=mysqli_query($conn,$sql);
        $dateCount=mysqli_num_rows($result);
        $j=-1;
        $tmp="";
        $tmprole="";//记录角色
        for($i=0;$i<$dateCount;$i++) {
            $result_arr = mysqli_fetch_assoc($result);
            //print_r(array_values($result_arr));
            if($i==0) {
                $tmp=$result_arr['Username'];
                $tmprole = $result_arr['Rolename'];
                if($i==($dateCount-1)){//只有一条记录
                    /*查询满足条件的用户具有的权限*/
                    /*选出该用户角色等级最高的角色*/
                    $sql2="select max(`Rolelevel`) from roles where ID in (                                
                              select distinct Rid from userroles where Uid in (
                                select ID from users where Username='{$tmp}'))";
                    $result2=mysqli_query($conn,$sql2);
                    $row2=mysqli_fetch_assoc($result2);
                    /*查询出该角色的ID*/
                    $sql3="select Rolename,r.ID from roles r,users u,userroles ur where Rolelevel={$row2['max(`Rolelevel`)']}
                            and Username='{$tmp}' and r.ID=ur.Rid and u.ID=ur.Uid";
                    $result3=mysqli_query($conn,$sql3);
                    $row3=mysqli_fetch_assoc($result3);
                    /*查询该角色所有权限*/
                    $sql4="select distinct Permission from permissions where ID in(
	                          select distinct Pid from rolepermissions where Rid in (
		                        select distinct ID from roles where Rolelevel < {$row2['max(`Rolelevel`)']} or ID='{$row3['ID']}'))";
                    $result4=mysqli_query($conn,$sql4);
                    $tmpper="";
                    while($row4=mysqli_fetch_assoc($result4)){
                        $tmpper=$tmpper.'   '.$row4['Permission'];
                    }
                    $j++;
                    $Username[$j]=$tmp;
                    $Rolename[$j]=$tmprole;
                    $Permission[$j]=$tmpper;
                }
            }
            else{
                if($tmp==$result_arr['Username'] && $i!=($dateCount-1)){//用户名相等但不是最后一条记录
                    $tmprole = $tmprole."   ".$result_arr['Rolename'];
                }
                else{
                    $p=1;//用户名相等且是最后一条记录   或   用户名不相等但不是最后一条记录
                    if($tmp != $result_arr['Username'] && $i==($dateCount-1)){//用户名不相等但是最后一条记录,此时要将最后一条记录插入
                        $p=2;
                    }
                    else if($tmp == $result_arr['Username'] && $i==($dateCount-1)){//用户名相等且是最后一条记录
                        $tmprole = $tmprole."   ".$result_arr['Rolename'];
                    }
                    while($p>0) {
                        /*查询满足条件的用户具有的权限*/
                        /*选出该用户角色等级最高的角色*/
                        $sql4 = "select max(`Rolelevel`) from roles where ID in (                                
                                    select distinct Rid from userroles where Uid in (
                                      select ID from users where Username='{$tmp}'))";
                        $result4 = mysqli_query($conn, $sql4);
                        $row4 = mysqli_fetch_assoc($result4);
                        /*查询出该角色的ID*/
                        $sql5="select Rolename,r.ID from roles r,users u,userroles ur where Rolelevel={$row4['max(`Rolelevel`)']}
                            and Username='{$tmp}' and r.ID=ur.Rid and u.ID=ur.Uid";
                        $result5=mysqli_query($conn,$sql5);
                        $row5=mysqli_fetch_assoc($result5);
                        /*查询该角色所有权限*/
                        $sql6="select distinct Permission from permissions where ID in(
	                          select distinct Pid from rolepermissions where Rid in (
		                        select distinct ID from roles where Rolelevel < {$row4['max(`Rolelevel`)']} or ID='{$row5['ID']}'))";
                        $result6=mysqli_query($conn,$sql6);

                        $tmpper = "";
                        while ($row6 = mysqli_fetch_assoc($result6)) {
                            $tmpper = $tmpper."   ".$row6['Permission'];
                        }
                        $j++;
                        $Username[$j]=$tmp;
                        $Rolename[$j]=$tmprole;
                        $Permission[$j]=$tmpper;
                        $tmp = $result_arr['Username'];
                        $tmprole = $result_arr['Rolename'];
                        $p--;
                    }
                }
            }
        }
        //解析结果集
        //查找没有角色的组员（没有角色故等级比当前用户低）
        $sql7="select Username from users  where ID not in (select Uid from userroles)";
        $result7=mysqli_query($conn,$sql7);
        $dateCount7=mysqli_num_rows($result7);
        for ($k=0;$k<=$j+$dateCount7;$k++){
            echo "<tr>";
            if($k<=$j) {
                echo "<td align='center'>{$Username[$k]}</td>";
                echo "<td align='center'>{$Rolename[$k]}</td>";
                echo "<td align='center'>{$Permission[$k]}</td>";
            }
            else{
                $row7=mysqli_fetch_assoc($result7);
                $Username[$k]=$row7['Username'];
                echo "<td align='center'>{$Username[$k]}</td>";
                echo "<td align='center'>&nbsp;</td>";
                echo "<td align='center'>&nbsp;</td>";
            }
            if (isset($_COOKIE["username"]) && $rowr["Rolename"]=="Admin") {
                echo "<td align='center'>
                       <a  href='../action/useraction.php?action=deluser&username={$Username[$k]}'> 删除</a>";
//              echo '<a href="../login/signup.html">添加用户</a>&nbsp;&nbsp;&nbsp;<a href="../action/useraction.php?action=deluser">删除用户</a><br>';
                if($k==0) {
                    $col = $j+$dateCount7 + 1;
                    echo "<td align='center' rowspan='{$col}'>
                        <a  href='../login/signup.html'> 添加</a>";
                }
            }
            else {
                echo "<td align='center'>
                       <a  href='#'onclick='return false'><font color='gray'>删除</font></a>";
                if($k==0) {
                    $col = $j +$dateCount7 +  1;
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