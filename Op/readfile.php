<html>
<head>
    <title>访问控制</title>
</head>
<body bgcolor="#dcdcdc">
<center>
    <h3>读文件</h3>
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
        echo "<a href='showrole.php'>查看角色</a>&nbsp;&nbsp;&nbsp;";
        echo "<a href='index.php'>查看权限</a><br>";
    }
    echo "<a href='javascript:window.history.back()'>返回</a>&nbsp;&nbsp;&nbsp;";
    echo '<a href="signout.php">退出登录</a><br><br>';
    //读文件
    $filename = "../resource/" . $_GET["filename"];
    if(file_exists($filename)) {
        $myfile = fopen($filename, "r") or die("Unable to open file!");
        $contents=fread($myfile,filesize($filename));
        //$contents=str_replace("\r\n","<br />",$contents);
        echo "<textarea readonly='readonly' style='height: 50%;width: 30%'>".$contents;

        echo "</textarea>";
        fclose($myfile);
    }
    else{
        echo "文件:".$filename."不存在！！<br>";
    }
    ?>
</center>
<?php

?>
</body>
</html>
