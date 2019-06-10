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
    echo "<a href='../Op/showrole.php'>查看角色</a>&nbsp;&nbsp;&nbsp;";
    echo "<a href='../Op/index.php'>查看权限</a><br>";
}
echo "<a href='javascript:window.history.back()'>返回</a>&nbsp;&nbsp;&nbsp;";
echo '<a href="../Op/signout.php">退出登录</a><br><br>';
echo "</center>";

switch ($_GET["action"]){
    case 'addrole':
        echo "<center>";
        //1.获取添加信息
        $username=$_POST['username'];
        $rolename=$_POST['rolename'];
        $rolelevel=$_POST['rolelevel'];
        $per_arr = $_POST['per'];

        //2.验证
        $result=mysqli_query($conn,"select  *  from  roles  where  Rolename='{$rolename}'");
        $dateCount=mysqli_num_rows($result);
        if($dateCount<=0) {   //该角色不存在
            $result1=mysqli_query($conn,"select  *  from  users  where  Username='{$username}'");
            $dateCount1=mysqli_num_rows($result1);
            if($dateCount1>0) {   //该用户存在
                //查询置为该角色的用户ID
                $row1=mysqli_fetch_assoc($result1);
                //查询等级比该角色低的角色拥有的权限
                $sql2 = "select  Pid  from  roles,rolepermissions where  Rolelevel<{$rolelevel} and roles.ID=rolepermissions.Rid";
                $result2 = mysqli_query($conn, $sql2);
                while($row2=mysqli_fetch_assoc($result2)){//去掉低级角色拥有的权限
                    foreach($per_arr as $k=>$v){
                        if($v == $row2["Pid"]){
                            unset($per_arr[$k]);
                        }
                    }
                }
//            echo $sql1 . "<br>" . $dateCount1 . "<br>" . $sql2 . "<br>" . $dateCount2 . "<br>";
                $result3=mysqli_query($conn,"insert into roles(Rolename,Rolelevel) values ('{$rolename}',{$rolelevel})");
                if(mysqli_affected_rows($conn)>0){//角色表插入成功
                    $result4=mysqli_query($conn,"select  *  from  roles  where  Rolename='{$rolename}'");
                    $row4=mysqli_fetch_assoc($result4);
                    $tag=1;
                    foreach($per_arr as $k=>$v){
                        //将下级角色没有的权限插入到角色权限表中
                        $result5=mysqli_query($conn,"insert into rolepermissions(Rid,Pid) values ('{$row4['ID']}','{$v}')");
                        if (mysqli_affected_rows($conn) <= 0) {
                            $tag=0;
                            echo "角色权限表添加失败！<br>". mysqli_error($conn);
                            break;
                        }
                    }
                    if($tag){
                        echo "角色权限表添加成功！<br>";
                        $result6=mysqli_query($conn,"insert into userroles(Uid,Rid) values ('{$row1['ID']}','{$row4['ID']}');");
                        if(mysqli_affected_rows($conn)>0) {//用户角色表插入成功
                            echo "用户角色表添加成功！<br>";
                        }
                        else{
                            echo "用户角色表添加失败！<br>". mysqli_error($conn);
                        }
                    }
                }
                else{
                    echo "角色表添加失败！<br>". mysqli_error($conn);
                }
            }
            else{
                echo "该用户不存在！<br>". mysqli_error($conn);
            }
        }else{
            echo "该角色已经存在！<br>";
        }
        echo "</center>";
        break;
    case 'delrole':
        echo "<center>";
        $rid=$_GET["rid"];
        //查找具有该角色的组员
        $sql1="select * from userroles where Rid='{$rid}'";
        $result1=mysqli_query($conn,$sql1);
        $dateCount1=mysqli_num_rows($result1);
        //查找该角色具有的权限
        $sql2="select * from rolepermissions where Rid='{$rid}'";
        $result2=mysqli_query($conn,$sql2);
        $dateCount2=mysqli_num_rows($result2);
        $msg="";
        if($dateCount1>0 && $dateCount2>0)
            $msg="有用户具有该角色，且该角色具有部分权限，确认删除该角色吗？";
        else if($dateCount1>0 && $dateCount2<=0)
            $msg="有用户具有该角色，确认删除该角色吗？";
        else if($dateCount1<=0 && $dateCount2>0)
            $msg="该角色具有部分权限，确认删除该角色吗？";
        else
            $msg="确认删除该角色吗？";
        echo "<script LANGUAGE='javascript'>
                        if(confirm('{$msg}')){
                            window.location='../Op/delrole.php?rid={$rid}';
                        }else{
                            window.location='javascript:window.history.back()';
                        }
                    </script>";
        echo "</center>";
        break;
    default:
        break;
}
