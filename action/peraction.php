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
/*查询出该用户的角色*/
$sqlr="select Rolename,r.ID from roles r,userroles ur where Rolelevel={$row_l['max(`Rolelevel`)']}
                            and Uid='{$Uid}' and r.ID=ur.Rid";
$resultr=mysqli_query($conn,$sqlr);
$rowr=mysqli_fetch_assoc($resultr);
if($row_l["max(`Rolelevel`)"] > 1) {
    echo "<a href='../Op/showrole.php'>查看角色</a>&nbsp;&nbsp;&nbsp;";
    echo "<a href='../Op/index.php'>查看权限</a><br>";
}
echo "<a href='javascript:window.history.back()'>返回</a>&nbsp;&nbsp;&nbsp;";
echo '<a href="../Op/signout.php">退出登录</a><br><br>';
echo "</center>";

switch ($_GET["action"]){
    case 'addper':
        echo "<center>";
        $pername=$_POST["pername"];
        $sql="select * from permissions where Permission='{$pername}'";
        $result=mysqli_query($conn,$sql);
        $dateCount=mysqli_num_rows($result);
//        echo $sql."<br>".$dateCount;
        if($dateCount>0) {//该权限已经存在
            echo "该权限已经存在!<br>";
        }else{
            $sql1="insert into permissions(Permission) values ('{$pername}')";
            mysqli_query($conn,$sql1);
            if(mysqli_affected_rows($conn)>0){
                //给管理员分配该权限
                if($rowr["Rolename"]=="Admin"){
                    /*
                    $sql2="select distinct Permission from permissions where Permission='{$pername}' and ID in(
                                select distinct Pid from rolepermissions where Rid in (
                                    select ID from roles where Rolename='Admin')));";
                    */
                    //管理员没有该权限，则为管理员分配该权限
                    $sql2="select * from permissions where Permission='{$pername}'";
                    $result2=mysqli_query($conn,$sql2);
                    $row2=mysqli_fetch_assoc($result2);
                    $sql3="select * from roles where Rolename='Admin'";
                    $result3=mysqli_query($conn,$sql3);
                    $row3=mysqli_fetch_assoc($result3);
                    $sql4="insert into rolepermissions(Rid,Pid) values ('{$row3['ID']}','{$row2['ID']}')";
                    mysqli_query($conn,$sql4);
                    if(mysqli_affected_rows($conn)>0){
                        echo "为管理员分配该权限成功！<br>";
                    }
                    else{
                        echo "为管理员分配该权限失败！<br>".mysqli_error($conn);
                    }
                }
                echo "创建权限信息成功！<br>";
            }else{
                echo "创建权限信息失败！<br>".mysqli_error($conn);
            }
        }
        echo "</center>";
        break;
    case 'delper':
        echo "<center>";
        $pid=$_GET["pid"];
        $sql="select * from rolepermissions where Pid='{$pid}'";
        $result=mysqli_query($conn,$sql);
        $dateCount=mysqli_num_rows($result);
        $msg="";
        if($dateCount>0)
            $msg="有角色具有该权限，删除后角色的此权限将一并删除，确定要删除该权限吗？";
        else
            $msg="确定要删除该权限吗？";
        echo "<script LANGUAGE='javascript'>
                        if(confirm('{$msg}')){
                            window.location='../Op/delper.php?pid={$pid}';
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