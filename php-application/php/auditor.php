<!--//请从登录页面传送,补充获取USERID的函数，先以$USERID作为已知变量，后需补充获取函数-->
<!--公告时间获取未做处理，copy student的函数-->
<!--数据库的连接-->
<?php
//数据库的连接
$servername = "localhost";
$username = "group";
$password = "zsygsjk123";
$dbname = "scholarship";
// 创建连接
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("连接失败: " . $conn->connect_error);
}

//通过cookie检测登陆状态
$user_id=$_COOKIE["user_id"];
$USERID=$user_id;//要获取的，暂时假定，获取后需删除
if($user_id!=''){//检测有没有cookie
    $cookie_user_pass_hash=$_COOKIE["user_pass_hash"];
    $query = "SELECT User_password FROM User_info WHERE User_id=$user_id";
    $password_return = mysqli_query($conn, $query);
    $row=mysqli_fetch_array($password_return);
    if ($cookie_user_pass_hash == md5(md5($row['User_password']))) {//登陆成功
        setcookie("user_id",$user_id,time()+3600);//给cookie增加时间
        setcookie("user_pass_hash",$cookie_user_pass_hash,time()+3600);//给cookie增加时间
        $query = "SELECT User_type FROM User_info WHERE User_id=$user_id";
        $jump_type = mysqli_query($conn, $query);
        $row=mysqli_fetch_array($jump_type);
        if ($row['User_type'] == 1) {//判断用户类型跳转到正确的页面
            $jump_url = "/student.php";
            header('Location: ' . $jump_url);
        } else if (($row['User_type'] == 2)||($row['User_type'] == 3)||($row['User_type'] == 4)) {
            ;//不设置跳转
        } else if ($row['User_type'] == 0) {
            $jump_url = "/admin.php";
            header('Location: ' . $jump_url);
        }else{
            echo "<p class=\"mdui-text-color-red\"><strong>账户权限错误！请尝试联系管理员！</strong></p>";
        }
    }
    else{//验证错误设空
        echo "error";
        sleep (3);
        setcookie("user_id", '');
        setcookie("user_pass_hash", '');
        header('Location: /index.php');
    }
}
else {
    header('Location: /index.php');
}

?>

<!--从审核员信息表获取审核员审核范围和审核类型-->
<?php
$sql = "SELECT Belonging_type
        FROM Auditor_info
        WHERE User_id=$USERID";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    // 输出数据
    $row = $result->fetch_assoc();
    $Belonging_type=$row["Belonging_type"];
} else {
    $Belonging_type=0;
}
$sql = "SELECT Belonging_info
        FROM Auditor_info
        WHERE User_id={$USERID}";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    // 输出数据
    $row = $result->fetch_assoc();
    $Belonging_info=$row["Belonging_info"];
} else {
    $Belonging_info=0;
}
//echo "Belonging_type: ".$Belonging_type."Belonging_info". $Belonging_info;//测试
?>

<!--审批过程，修改数据库,逻辑上优先执行-->
<?php
$Apply=$_GET["group1"];//获取输入
if($Apply){//有审批审批
    $Apply_id=$_GET["ID"];
    $reason=$_GET["reason"];
    if($Apply=="yes")//通过审批
        $Apply_result=1;
    else
        $Apply_result=2;
//写入数据库
    if($Belonging_type==1)//班级审核员
    {
        $sql = "UPDATE Audit_date 
        SET First_result={$Apply_result},First_reason='{$reason}'
        WHERE Apply_id=$Apply_id";
    }
    else if($Belonging_type==2)
    {
        $sql = "UPDATE Audit_date 
        SET Second_result={$Apply_result},Second_reason='{$reason}'
        WHERE Apply_id={$Apply_id}";
    }
    else
    {
        $sql = "UPDATE Audit_date 
        SET Third_result={$Apply_result},Third_reason='{$reason}'
        WHERE Apply_id=$Apply_id";
    }
    if ($conn->query($sql) === FLASE) {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>

<!--从申核表中获取该审核员的待审批数量n和已审批m-->
<?php
if($Belonging_type==1)//班级审核员
{
    $sql = "SELECT COUNT(*)
        FROM Audit_date
        WHERE First_result is NULL AND Apply_id IN 
            (   
                SELECT Apply_id
                FROM Apply_date
                WHERE Stu_id IN 
                (
                    SELECT Stu_id
                     FROM Stu_info
                     WHERE Class_id={$Belonging_info}   
                )
            )";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        // 输出数据
        $row = $result->fetch_assoc();
        $n=$row["COUNT(*)"];
    } else {
        $n=0;
    }

    $sql = "SELECT COUNT(*)
        FROM Audit_date
        WHERE First_result=1 AND Apply_id IN 
            (   
                SELECT Apply_id
                FROM Apply_date
                WHERE Stu_id IN 
                (
                    SELECT Stu_id
                     FROM Stu_info
                     WHERE Class_id={$Belonging_info}    
                )
            )";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        // 输出数据
        $row = $result->fetch_assoc();
        $m=$row["COUNT(*)"];
    } else {
        $m=0;
    }
}
else if($Belonging_type==2)//学院审核员
{
    $sql = "SELECT COUNT(*)
        FROM Audit_date
        WHERE First_result is not NULL AND Second_result is NULL AND Apply_id IN 
            (   
                SELECT Apply_id
                FROM Apply_date
                WHERE Stu_id IN 
                (
                    SELECT Stu_id
                     FROM Stu_info
                     WHERE Academy_id={$Belonging_info}    
                )
            )";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        // 输出数据
        $row = $result->fetch_assoc();
        $n=$row["COUNT(*)"];
    } else {
        $n=0;
    }
    $sql = "SELECT COUNT(*)
        FROM Audit_date
        WHERE First_result=1 AND Second_result is not NULL AND Apply_id IN 
            (   
                SELECT Apply_id
                FROM Apply_date
                WHERE Stu_id IN 
                (
                    SELECT Stu_id
                     FROM Stu_info
                     WHERE Academy_id={$Belonging_info}    
                )
            )";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        // 输出数据
        $row = $result->fetch_assoc();
        $m=$row["COUNT(*)"];
    } else {
        $m=0;
    }
}
else //学校管理员
{
    $sql = "SELECT COUNT(*)
        FROM Audit_date
        WHERE Second_result=1 AND Third_result is NULL";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        // 输出数据
        $row = $result->fetch_assoc();
        $n=$row["COUNT(*)"];
    } else {
        $n=0;
    }
    $sql = "SELECT COUNT(*)
        FROM Audit_date
        WHERE Second_result=1 AND Third_result is not NULL";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        // 输出数据
        $row = $result->fetch_assoc();
        $m=$row["COUNT(*)"];
    } else {
        $m=0;
    }
}
?>

<!--读取一条未审核信息-->
<?php
if($n!=0){//有未审核的信息
    if($Belonging_type==1)//班级审核员
    {
        $sql = "SELECT Stu_Name,Stu_id,Sex,Academy_id,Stu_score
        FROM Stu_info
        WHERE Class_id={$Belonging_info} AND Stu_id IN 
            (   
                SELECT Stu_id
                FROM Apply_date
                WHERE Apply_id IN 
                (
                    SELECT Apply_id
                     FROM Audit_date
                     WHERE First_result is NULL    
                )
            )";
        $result= $conn->query($sql);//result为一个结果集合
        $row1 = $result->fetch_assoc();//获取第一条信息
    }
    else if($Belonging_type==2)//学院审核员
    {
        $sql = "SELECT Stu_Name,Stu_id,Sex,Academy_id,Stu_score
        FROM Stu_info
        WHERE Academy_id={$Belonging_info}  AND Stu_id IN
            (   
                SELECT Stu_id
                FROM Apply_date
                WHERE Apply_id IN 
                (
                    SELECT Apply_id
                     FROM Audit_date
                     WHERE First_result=1 AND  Second_result is NULL  
                )
            )";
        $result= $conn->query($sql);//result为一个结果集合
        $row1 = $result->fetch_assoc();//获取第一条信息
    }
    else //学校管理员
    {
        $sql = "SELECT Stu_Name,Stu_id,Sex,Academy_id,Stu_score
        FROM Stu_info
        WHERE  Stu_id IN 
         (
                 SELECT Stu_id
                FROM Apply_date
                WHERE Apply_id IN 
                (
                    SELECT Apply_id
                     FROM Audit_date
                     WHERE Second_result=1 AND  Third_result is NULL  
                )          
        )";
        $result= $conn->query($sql);//result为一个结果集合
        $row1 = $result->fetch_assoc();//获取第一条信息
    }
}
?>
<!--查询学院名称-->
<?php
if($n!=0) {
    $Academy_id=$row1["Academy_id"];//获取学院id
    $sql = "SELECT Academy_Name
            FROM Academy_info
            WHERE Academy_id={$Academy_id}";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        // 输出数据
        $row = $result->fetch_assoc();
        $Academy_Name=$row["Academy_Name"];
    } else {
        $Academy_Name=0;
    }
}
?>

<!--查询奖学金名称-->
<?php
if($n!=0) {
    $Stu_id=$row1["Stu_id"];//获取学生id
    $sql = "SELECT Apply_type
            FROM Apply_date
            WHERE Stu_id={$Stu_id}";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        // 输出数据
        $row = $result->fetch_assoc();
        $Apply_type=$row["Apply_type"];
    } else {
        $Apply_type=0;
    }
}
?>
<!--查询申报原因-->
<?php
if($n!=0) {
    $sql = "SELECT Apply_reason
            FROM Apply_date
            WHERE Stu_id= {$Stu_id}";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        // 输出数据
        $row = $result->fetch_assoc();
        $Apply_reason=$row["Apply_reason"];
    } else {
        $Apply_reason=0;
    }
}
?>
<!--获取Apply_id>
<?php
if($n!=0) {
    //获取Apply_id
    $sql = "SELECT Apply_id
        FROM Apply_date
        WHERE Stu_id={$Stu_id}";
    // 输出数据
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        // 输出数据
        $row = $result->fetch_assoc();
        $Apply_id=$row["Apply_id"];
    } else {
        $Apply_id=0;
    }
}
?>


<!--用户信息情况查询-->
<?php
$name=$_GET["name"];//获取输入
if($name)
{
    if($Belonging_type==1)//班级
    {
        $sql = "SELECT Stu_Name,Stu_id,Sex,Academy_id,Stu_score
        FROM Stu_info
        WHERE Class_id={$Belonging_info} AND Stu_Name='{$name}'";
        $result2= $conn->query($sql);//result为一个结果集合
        if ($result2->num_rows > 0)
            $row2= $result2->fetch_assoc();//获取第一条信息
    }
    else if($Belonging_type==2)//学院
    {
        $sql = "SELECT Stu_Name,Stu_id,Sex,Academy_id,Stu_score
        FROM Stu_info
        WHERE Academy_id={$Belonging_info} AND Stu_Name='{$name}' ";
        $result2= $conn->query($sql);//result为一个结果集合
        if ($result2->num_rows > 0)
            $row2 = $result2->fetch_assoc();//获取第一条信息
    }
    else //学校
    {
        $sql = "SELECT Stu_Name,Stu_id,Sex,Academy_id,Stu_score
        FROM Stu_info
        WHERE Stu_Name='{$name}'";
        $result2= $conn->query($sql);//result为一个结果集合
        if ($result2->num_rows > 0)
            $row2 = $result2->fetch_assoc();//获取第一条信息
    }
}
if ($result2->num_rows > 0)
{
    $seek_Stu_id=$row2["Stu_id"];
    //学院查询
    $seek_Academy_id=$row2["Academy_id"];//获取学院id
    $sql = "SELECT Academy_Name
            FROM Academy_info
            WHERE Academy_id={$seek_Academy_id}";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        // 输出数据
        $row = $result->fetch_assoc();
        $seek_Academy_Name=$row["Academy_Name"];
    } else {
        $seek_Academy_Name=0;
    }
    //查询奖学金名称
    $seek_Stu_id=$row2["Stu_id"];//获取学生id
    $sql = "SELECT Apply_type
            FROM Apply_date
            WHERE Stu_id={$seek_Stu_id}";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        // 输出数据
        $row = $result->fetch_assoc();
        $seek_Apply_type=$row["Apply_type"];
    } else {
        $seek_Apply_type=0;
    }
    //查询申报原因
    $sql = "SELECT Apply_reason
            FROM Apply_date
            WHERE Stu_id= {$seek_Stu_id}";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        // 输出数据
        $row = $result->fetch_assoc();
        $seek_Apply_reason=$row["Apply_reason"];
    } else {
        $seek_Apply_reason=0;
    }
    //审批结果查询
    if($Belonging_type==1)//班级审核员
    {
        $sql = "SELECT First_result,First_reason
        FROM Audit_date
        WHERE Apply_id IN 
            (   
                SELECT Apply_id
                FROM Apply_date
                WHERE Stu_id={$seek_Stu_id}
            )";
        $result3= $conn->query($sql);//result为一个结果集合
        if ($result3->num_rows > 0)
            $row3 = $result3->fetch_assoc();//获取第一条信息
    }
    else if($Belonging_type==2)//学院审核员
    {
        $sql = "SELECT First_result,First_reason,Second_result,Second_reason
        FROM Audit_date
        WHERE Apply_id IN 
            (   
                SELECT Apply_id
                FROM Apply_date
                WHERE Stu_id={$seek_Stu_id}
            )";
        $result3= $conn->query($sql);//result为一个结果集合
        if ($result3->num_rows > 0)
            $row3 = $result3->fetch_assoc();//获取第一条信息
    }
    else
    {
        $sql = "SELECT First_result,First_reason,Second_result,Second_reason,Third_result,Third_reason
        FROM Audit_date
        WHERE Apply_id IN 
            (   
                SELECT Apply_id
                FROM Apply_date
                WHERE Stu_id={$seek_Stu_id}
            )";
        $result3= $conn->query($sql);//result为一个结果集合
        if ($result3->num_rows > 0)
            $row3 = $result3->fetch_assoc();//获取第一条信息
    }
}
?>

<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width">
    <title>审批端 | 奖学金申报系统</title>
    <link rel="stylesheet" href="css/mdui.min.css">
    <link rel="stylesheet" href="css/style.css">
    <script src="js/mdui.min.js"></script>
</head>
<body>
<!--头信息&工具栏-->
<div class="mdui-appbar">
    <div class="mdui-toolbar mdui-color-blue-900">
        <a href="auditor.html" class="mdui-typo-headline">奖学金申报系统</a>
        <a href="auditor.html" class="mdui-typo-title">审批端</a>
        <div class="mdui-toolbar-spacer"></div>
        <!--登出按钮-->
        <a href="logout.php" class="mdui-btn mdui-btn-raised mdui-ripple mdui-color-pink">退出登录</a>
    </div>
    <!--工具栏-->
    <div class="mdui-tab mdui-tab-centered mdui-color-blue-900" mdui-tab>
        <a href="#audit" class="mdui-ripple mdui-ripple-white">申报审批</a>
        <a href="#auditor-query" class="mdui-ripple mdui-ripple-white">审批查询</a>
        <a href="#user" class="mdui-ripple mdui-ripple-white">个人中心</a>
    </div>
</div>

<!--留白2行-->
<br/><br/>

<!--审批页面-->
<div id="audit">
    <div class="mdui-container">
        <div class="mdui-row">
            <!--信息展示模块-->
            <?php
                require_once("announce.php");
            ?>
            <div class="mdui-col-sm-8 mdui-col-md-8">
                <div class="mdui-container">
                    <div class="mdui-typo">
                        <h1>审批状态</h1>
                        <div class="mdui-chip mdui-color-red-400">
                            <span class="mdui-chip-title"><?php echo $n;?>条未审批</span>
                        </div>
                        &nbsp;&nbsp;&nbsp;
                        <div class="mdui-chip mdui-color-green-300">
                            <span class="mdui-chip-title"><?php echo $m;?>条已审批</span>
                        </div>
                    </div>

                    <div class="mdui-typo">
                        <h1>未审批</h1>
                    </div>
                    <div class="mdui-table-fluid">
                        <table class="mdui-table">
                            <thead>
                            <tr>
                                <th>姓名</th>
                                <th>性别</th>
                                <th>学号</th>
                                <th>学院</th>
                                <th>平均绩点</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td><?php if($n>0)echo $row1["Stu_Name"];?></td>
                                <td><?php if($n>0)echo $row1["Sex"];?></td>
                                <td><?php if($n>0)echo $row1["Stu_id"];?></td>
                                <td><?php if($n>0)echo $Academy_Name;?></td>
                                <td><?php if($n>0)echo $row1["Stu_score"];?></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <br/>
                    <div class="mdui-table-fluid">
                        <table class="mdui-table">
                            <thead>
                            <tr>
                                <th>所申请奖学金类型</th>
                            </tr>
                            <tr>
                                <td> <?php if($n>0)echo $Apply_type;?></td>
                            </tr>
                            </thead>
                        </table>
                    </div>
                    <div class="mdui-textfield">
                        <label class="mdui-textfield-label">所填写申报原因</label>
                        <textarea class="mdui-textfield-input" type="text" maxlength="800"
                                  disabled><?php if($n>0)echo $Apply_reason;?>
                        </textarea>
                    </div>
                    <div class="mdui-typo">
                        <h3>审批结果</h3>
                    </div>
                    <form action="auditor.php#audit" method="get">
                        <label class="mdui-radio">
                            <input type="hidden" name="ID" value="<?php if($n>0)echo $Apply_id;?>"/>
                            <input type="radio" name="group1" value="yes"/>
                            <i class="mdui-radio-icon"></i>
                            通过
                        </label>
                        <br/>
                        <label class="mdui-radio">
                            <input type="radio" name="group1" value="no"/>
                            <i class="mdui-radio-icon"></i>
                            不通过
                        </label>
                        <div class="mdui-textfield mdui-textfield-floating-label">
                            <label class="mdui-textfield-label">审批意见</label>
                            <input type="text" class="mdui-textfield-input" maxlength="50" name="reason"></input>
                        </div>
                        <br/>
                        <button class="mdui-btn mdui-color-pink mdui-ripple">提交</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>


<!--查询页面-->
<div id="auditor-query">
    <div class="mdui-container">
        <div class="mdui-row">
          
            <div class="mdui-col-sm-8 mdui-col-md-8">
                <div class="mdui-container">
                    <div class="mdui-typo">
                        <h1>本班/院/校整体审批情况</h1>
                    </div>
                    <div class="mdui-table-fluid">
                        <table class="mdui-table">
                            <thead>
                            <tr>
                                <th>总申请数</th>
                                <th>班级审批通过</th>
                                <th>学院审批通过</th>
                                <th>学校审批通过</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>
                                    <?php
                                    $sql="SELECT COUNT(*) FROM Audit_date";
                                    $result=$conn->query($sql);
                                    $row=$result->fetch_assoc();
                                    echo $row['COUNT(*)'];
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    $sql="SELECT COUNT(First_result) FROM Audit_date WHERE First_result=1";
                                    $result = $conn->query($sql);
                                    $row = $result->fetch_assoc();
                                    echo $row['COUNT(First_result)'];
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    $sql="SELECT COUNT(Second_result) FROM Audit_date WHERE Second_result=1";
                                    $result = $conn->query($sql);
                                    $row = $result->fetch_assoc();
                                    echo $row['COUNT(Second_result)'];
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    $sql="SELECT COUNT(Third_result) FROM Audit_date WHERE Third_result=1";
                                    $result = $conn->query($sql);
                                    $row = $result->fetch_assoc();
                                    echo $row['COUNT(Third_result)'];
                                    ?>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <br/>
                    <a href="all-status.html">
                        <div class="mdui-btn mdui-color-grey-200 mdui-text-color-black">详细状态</div>
                    </a>
                    <div class="mdui-typo">
                        <h1>单用户查询</h1>
                    </div>
                    <div class="mdui-typo-caption">
                        <p class="mdui-text-color-grey-600">请选择学年</p>
                    </div>
                    <select class="mdui-select" mdui-select>
                        <option value="20201">2020年春学期</option>
                        <option value="20192">2019年秋学期</option>
                        <option value="20191">2019年春学期</option>
                        <option value="20182">2018年秋学期</option>
                    </select>
                    <form action="auditor.php#auditor-query" method="get">
                        <div class="mdui-textfield  mdui-textfield-floating-label">
                            <label class="mdui-textfield-label">姓名</label>
                            <input class="mdui-textfield-input" type="text" name="name"/>
                        </div>
                        <button class="mdui-btn mdui-color-pink mdui-ripple">查询</button>
                    </form>
                    <?php if ($result2->num_rows<=0) echo "无此人或无权限";?>
                    <div class="mdui-table-fluid">
                        <table class="mdui-table">
                            <thead>
                            <tr>
                                <th>姓名</th>
                                <th>性别</th>
                                <th>学号</th>
                                <th>学院</th>
                                <th>平均绩点</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td><?php if ($result2->num_rows > 0) echo $row2["Stu_Name"];?></td>
                                <td><?php if ($result2->num_rows > 0)echo $row2["Sex"];?></td>
                                <td><?php if ($result2->num_rows > 0)echo $row2["Stu_id"];?></td>
                                <td><?php if ($result2->num_rows > 0)echo $seek_Academy_Name;?></td>
                                <td><?php if ($result2->num_rows > 0)echo $row2["Stu_score"];?></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <br/>
                    <div class="mdui-table-fluid">
                        <table class="mdui-table">
                            <thead>
                            <tr>
                                <th>所申请奖学金类型</th>
                            </tr>
                            <tr>
                                <td><?php if ($result2->num_rows > 0)echo $seek_Apply_type;?></td>
                            </tr>
                            </thead>
                        </table>
                    </div>

                    <div class="mdui-textfield">
                        <label class="mdui-textfield-label">所填写申报原因</label>
                        <textarea class="mdui-textfield-input" type="text" maxlength="800"
                                  disabled><?php if ($result2->num_rows > 0)echo $seek_Apply_reason;?>
                        </textarea>
                    </div>
                    <div class="mdui-table-fluid">
                        <table class="mdui-table">
                            <thead>
                            <tr>
                                <th>审批级别</th>
                                <th>审批结果</th>
                                <th>审批意见</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>班级</td>
                                <td class="mdui-text-color-green">
                                    <?php
                                    if($result2->num_rows > 0)
                                        if($row3["First_result"]==1) echo "通过";
                                        else if($row3["First_result"]==2)echo "不通过且不打回";
                                        else if($row3["First_result"]==3)echo "不通过且打回";
                                        else echo "未审核";
                                    ?>
                                </td>
                                <td class="mdui-text-color-green">
                                    <?php
                                    if($result2->num_rows > 0)
                                        if($row3["First_result"]==4) echo "/";
                                        else echo $row3["First_reason"];
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <td>学院</td>
                                <td class="mdui-text-color-red">
                                    <?php
                                    if($result2->num_rows > 0)
                                        if($Belonging_type<2)
                                            echo "无权限";
                                        else
                                        {
                                            if($row3["Second_result"]==1) echo "通过";
                                            else if($row3["Second_result"]==2)echo "不通过且不打回";
                                            else if($row3["Second_result"]==3)echo "不通过且打回";
                                            else echo "未审核";
                                        }
                                    ?>
                                </td>
                                <td class="mdui-text-color-red">
                                    <?php
                                    if($result2->num_rows > 0)
                                        if($Belonging_type<2)
                                            echo "/";
                                        else
                                        {
                                            if($row3["Second_result"]==4) echo "/";
                                            else echo $row3["Second_reason"];
                                        }
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <td>学校</td>
                                <td class="mdui-text-color-grey">
                                    <?php
                                    if($result2->num_rows > 0)
                                        if($Belonging_type<3)
                                            echo "无权限";
                                        else
                                        {
                                            if($row3["Third_result"]==1) echo "通过";
                                            else if($row3["Third_result"]==2)echo "不通过且不打回";
                                            else if($row3["Third_result"]==3)echo "不通过且打回";
                                            else echo "未审核";
                                        }
                                    ?>
                                </td>
                                <td class="mdui-text-color-grey">
                                    <?php
                                    if($result2->num_rows > 0)
                                        if($Belonging_type<3)
                                            echo "/";
                                        else
                                        {
                                            if($row3["Third_result"]==4) echo "/";
                                            else echo $row3["Third_reason"];
                                        }
                                    ?>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <br/>
                </div>
            </div>
        </div>
    </div>
</div>

<!--用户中心-->
<div id="user">
    <div class="mdui-container">
        <div class="mdui-row">

            <div class="mdui-col-sm-8 mdui-col-md-8">
                <div class="mdui-container">
                    <div class="mdui-typo">
                        <h2>重置密码</h2>
                    </div>
                    <div class="mdui-textfield mdui-textfield-floating-label">
                        <label class="mdui-textfield-label">原密码</label>
                        <input class="mdui-textfield-input" type="OldPasswd"/>
                    </div>
                    <div class="mdui-textfield mdui-textfield-floating-label">
                        <label class="mdui-textfield-label">新密码</label>
                        <input class="mdui-textfield-input" type="NewPasswd"
                               pattern="^.*(?=.{8,})(?=.*[a-z])(?=.*[A-Z]).*$" required/>
                        <div class="mdui-textfield-error">密码至少 8 位，且包含大小写字母</div>
                        <div class="mdui-textfield-helper">请输入至少 8 位，且包含大小写字母的密码</div>
                    </div>
                    <div class="mdui-textfield mdui-textfield-floating-label">
                        <label class="mdui-textfield-label">确认新密码</label>
                        <input class="mdui-textfield-input" type="NewPasswd2"/>
                    </div>
                    <button class="mdui-btn mdui-color-pink mdui-ripple">提交</button>
                </div>
            </div>


        </div>
    </div>
</div>

<!--底部信息栏-->

<div class="mdui-bottom-nav-fixed">
    <div class="mdui-bottom-nav mdui-bottom-nav-text-auto mdui-bottom-nav-scroll-hide mdui-color-indigo">
        <div class="mdui-container">
            <div class="bottom-text-center mdui-text-center">
                刘松林小组奖学金申报系统
            </div>
        </div>
    </div>
</div>

</body>
</html>
