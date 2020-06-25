<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'group');
define('DB_PASS', 'zsygsjk123');
define('DB_NAME', 'scholarship');

$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($dbc->error) {
    die("连接失败; ".$dbc->error );
}

//通过cookie检测登陆状态
$user_id=$_COOKIE["user_id"];
if($user_id!=''){//检测有没有cookie
    $cookie_user_pass_hash=$_COOKIE["user_pass_hash"];
    $query = "SELECT User_password FROM User_info WHERE User_id=$user_id";
    $password_return = mysqli_query($dbc, $query);
    $row=mysqli_fetch_array($password_return);
    if ($cookie_user_pass_hash == md5(md5($row['User_password']))) {//登陆成功
        setcookie("user_id",$user_id,time()+3600);//给cookie增加时间
        setcookie("user_pass_hash",$cookie_user_pass_hash,time()+3600);//给cookie增加时间
        $query = "SELECT User_type FROM User_info WHERE User_id=$user_id";
        $jump_type = mysqli_query($dbc, $query);
        $row=mysqli_fetch_array($jump_type);
        if ($row['User_type'] == 1) {//判断用户类型跳转到正确的页面
            ;//不跳转
        } else if (($row['User_type'] == 2)||($row['User_type'] == 3)||($row['User_type'] == 4)) {
            $jump_url = "/auditor.php";
            header('Location: ' . $jump_url);
        } else if ($row['User_type'] == 0) {
            $jump_url = "/admin.php";
            header('Location: ' . $jump_url);
        }else{
            echo "<p class=\"mdui-text-color-red\"><strong>账户权限错误！请尝试联系管理员！</strong></p>";
        }
    }
    else{//验证错误设空
        setcookie("user_id", '');
        setcookie("user_pass_hash", '');
        header('Location: /index.php');
    }
}
else {
    header('Location: /index.php');
}


?>
<!DOCTYPE html>
<html lang="zh-cn">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width">
    <title>申报端 | 奖学金申报系统</title>
    <link rel="stylesheet" href="css/mdui.min.css">
    <link rel="stylesheet" href="css/style.css">
    <script src="js/mdui.min.js"></script>
</head>
<body>
<!--头信息&工具栏-->
<div class="mdui-appbar">
    <div class="mdui-toolbar mdui-color-blue-900">
        <a href="student.php" class="mdui-typo-headline">奖学金申报系统</a>
        <a href="student.php" class="mdui-typo-title">申报端</a>
        <div class="mdui-toolbar-spacer"></div>
        <!--登出按钮-->
        <a href="logout.php" class="mdui-btn mdui-btn-raised mdui-ripple mdui-color-pink">退出登录</a>
    </div>
    <!--工具栏-->
    <div class="mdui-tab mdui-tab-centered mdui-color-blue-900" mdui-tab>
        <a href="#apply" class="mdui-ripple mdui-ripple-white">申报填写</a>
        <a href="#student-query" class="mdui-ripple mdui-ripple-white">申报查询</a>
        <a href="#user" class="mdui-ripple mdui-ripple-white">个人中心</a>
    </div>
</div>

<!--留白2行-->
<br/><br/>

<!--申报页面-->
<div id="apply">
    <div class="mdui-container">
        <div class="mdui-row">
            <!--信息展示模块-->
            <?php
                require_once("announce.php");
            ?>
            
            <!--申报填写模块-->
            <div class="mdui-col-sm-8 mdui-col-md-8">
                <div class="mdui-container">
                <div  method="post">
                    <form method="post">
                        <div class="mdui-typo">
                            <h1>奖学金申报填写</h1>
                        </div>
                        <div class="mdui-typo-caption">
                            <p class="mdui-text-color-grey-600">请选择奖学金种类</p>
                        </div>
                        <select class="mdui-select" mdui-select name="select">
                            <option value="1">国家奖学金</option>
                            <option value="2">湖北省奖学金</option>
                            <option value="3">这是一个名字长一点的奖学金样例</option>
                            <option value="4">校级奖学金</option>
                            <option value="5">国家助学金</option>
                            <option value="6">李四光奖学金</option>
                            <option value="7"></option>
                        </select>
                        <div class="mdui-textfield mdui-textfield-floating-label">
                            <label class="mdui-textfield-label">请填写申报原因</label>
                            <textarea class="mdui-textfield-input" type="text" maxlength="800" name="apply_reason"></textarea>
                        </div>
                        <button type="submit" class="mdui-btn mdui-color-pink mdui-ripple" value="submit" name="submit">提交</button>
                    </form>
                    <?php
                    if(isset($_POST['submit'])){
                        $type = $_POST['select'];
                        $reason = $_POST['apply_reason'];
                        $Stu_id=mysqli_query($dbc,"SELECT Stu_id FROM Stu_info WHERE User_id=$user_id");
                        $row=mysqli_fetch_array($Stu_id);
                        if (!$row) {
                            printf("Error: %s\n", mysqli_error($dbc));
                            exit();
                        }
                        $stu_id=$row['Stu_id'];
                        $query = "INSERT INTO Apply_date (Apply_type,Apply_reason,Stu_id) VALUES ('$type', '$reason','$stu_id')";
                        $return=mysqli_query($dbc, $query);
                        $query1="SELECT Apply_id FROM Apply_date WHERE Stu_id=$stu_id";
                        $return1=mysqli_query($dbc,$query1);
                        $row=mysqli_fetch_array($return1);
                        $apply_id=$row['Apply_id'];
                        $query2="INSERT INTO Audit_date (Apply_id) VALUES ('$apply_id')";
                        $return2=mysqli_query($dbc,$query2);
                        if($return&&$return2){
                            echo "<p>提交成功</p>";
                        }

                    }
                    ?>
                </div>
                </div>
            </div>
        </div>
    </div>
</div>


<!--查询页面-->
<div id="student-query">
    <div class="mdui-container">
        <div class="mdui-row">
            <!--申报填写模块-->
            <div class="mdui-col-sm-8 mdui-col-md-8">
                <div class="mdui-container">
                    <div class="mdui-typo-caption">
                        <p class="mdui-text-color-grey-600">请选择学年</p>
                    </div>
                    <!--<select class="mdui-select" mdui-select>
                        <option value="20201">2020年春学期</option>
                        <option value="20192">2019年秋学期</option>
                        <option value="20191">2019年春学期</option>
                        <option value="20182">2018年秋学期</option>
                    </select>&nbsp;&nbsp;&nbsp;&nbsp;
                    <button class="mdui-btn mdui-color-pink mdui-ripple">查询</button>-->
                    <div class="mdui-typo-caption">
                        <p class="mdui-text-color-grey-600">当前申请的奖学金种类</p>
                    </div>
                    <select class="mdui-select" mdui-select>
                        <option value="1">国家奖学金</option>
                        <option value="2">湖北省奖学金</option>
                        <option value="3">这是一个名字长一点的奖学金样例</option>
                        <option value="4">校级奖学金</option>
                        <option value="5">国家助学金</option>
                        <option value="6">李四光奖学金</option>
                    </select>
                    <form method="post">
                        <div class="mdui-textfield mdui-textfield-floating-label">
                            <label class="mdui-textfield-label">当前填写的申报原因</label>
                            <textarea class="mdui-textfield-input" type="text" maxlength="800" name="reason"></textarea>
                        </div>

                        <div class="mdui-typo">
                            <label class="mdui-textfield-label">当前申报状态</label>
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
                                            $return=NULL;
                                            $query = "SELECT First_result FROM Audit_date WHERE Apply_id in (SELECT Apply_id FROM Apply_date WHERE Stu_id in (SELECT Stu_id FROM Stu_info WHERE User_id=$user_id))";
                                            $return=mysqli_query($dbc, $query);
                                        $row=mysqli_fetch_array($return);
                                            if($row['First_result']==NULL){
                                                echo "未审批";
                                            }
                                            else{
                                                if($row['First_result']==1){
                                                    echo "通过";
                                                }
                                                else {
                                                    echo "不通过";
                                                }
                                            }

                                        ?>
                                    </td>
                                    <td class="mdui-text-color-green">
                                        <?php
                                        $return=NULL;
                                            $query = "SELECT First_reason FROM Audit_date WHERE Apply_id in (SELECT Apply_id FROM Apply_date WHERE Stu_id in (SELECT Stu_id FROM Stu_info WHERE User_id=$user_id))";
                                            $return=mysqli_query($dbc, $query);
                                        $row=mysqli_fetch_array($return);
                                        if(!$row['First_reason']){
                                            echo "无";
                                        }
                                        else{

                                            echo $row['First_reason'];
                                        }
                                        ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>学院</td>
                                    <td class="mdui-text-color-red">
                                        <?php
                                        $return=NULL;
                                        $query = "SELECT Second_result FROM Audit_date WHERE Apply_id in (SELECT Apply_id FROM Apply_date WHERE Stu_id in (SELECT Stu_id FROM Stu_info WHERE User_id=$user_id))";
                                        $return=mysqli_query($dbc, $query);
                                        $row=mysqli_fetch_array($return);
                                        if(!$row['Second_result']){
                                            echo "未审批";
                                        }
                                        else{
                                            if($row['Second_result']==1){
                                                echo "通过";
                                            }
                                            else {
                                                echo "不通过";
                                            }
                                        }
                                        ?>
                                    </td>
                                    <td class="mdui-text-color-red">
                                        <?php
                                        $return=NULL;
                                        $query = "SELECT Second_reason FROM Audit_date WHERE Apply_id in (SELECT Apply_id FROM Apply_date WHERE Stu_id in (SELECT Stu_id FROM Stu_info WHERE User_id=$user_id))";
                                        $return=mysqli_query($dbc, $query);
                                        $row=mysqli_fetch_array($return);
                                        if(!$row['Second_reason']){
                                            echo "无";
                                        }
                                        else{

                                            echo $row['Second_reason'];
                                        }
                                        ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>学校</td>
                                    <td class="mdui-text-color-grey">
                                        <?php
                                        $return=NULL;
                                        $query = "SELECT Third_result FROM Audit_date WHERE Apply_id in (SELECT Apply_id FROM Apply_date WHERE Stu_id in (SELECT Stu_id FROM Stu_info WHERE User_id=$user_id))";
                                        $return=mysqli_query($dbc, $query);
                                        $row=mysqli_fetch_array($return);
                                        if(!$row['Third_result']){
                                            echo "未审批";
                                        }
                                        else{
                                            if($row['Third_result']==1){
                                                echo "通过";
                                            }
                                            else {
                                                echo "不通过";
                                            }
                                        }
                                        ?>
                                    </td>
                                    <td class="mdui-text-color-grey">
                                        <?php
                                        $return=NULL;
                                        $query = "SELECT Third_reason FROM Audit_date WHERE Apply_id in (SELECT Apply_id FROM Apply_date WHERE Stu_id in (SELECT Stu_id FROM Stu_info WHERE User_id=$user_id))";
                                        $return=mysqli_query($dbc, $query);
                                        $row=mysqli_fetch_array($return);
                                        if(!$row['Third_reason']){
                                            echo "无";
                                        }
                                        else{
                                            echo $row['Third_reason'];
                                        }
                                        ?>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <br/>
                        <button class="mdui-btn mdui-color-pink mdui-ripple" name="submit">提交修改</button>
                    </form>
                    <?php
                    if(isset($_POST['submit'])){
                        $reason = $_POST['apply_reason'];
                        $Stu_id=mysqli_query($dbc,"SELECT Stu_id FROM Stu_info WHERE User_id=$user_id");
                        $query = "UPDATE Apply_date SET Apply_reason=$reason WHERE Stu_id=$Stu_id";
                        mysqli_query($dbc, $query);
                        echo "<p>提交成功</p>";
                    }
                    ?>
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
                        <h2>信息补全</h2>
                    </div>
                    <form method="post">
                        <div class="mdui-textfield mdui-textfield-floating-label">
                            <label class="mdui-textfield-label">银行卡号</label>
                            <input class="mdui-textfield-input" name="cardid" type="cardID"/>
                        </div>
                        <button class="mdui-btn mdui-color-pink mdui-ripple" name="submit2">提交</button>
                    </form>
                    <?php
                    if(isset($_POST['submit2'])){
                        $cardID=$_POST['cardid'];
                        $query="UPDATE Stu_info SET Band_account=$cardID WHERE User_id=$user_id";
                        $return=NULL;
                        $return=mysqli_query($dbc,$query);
                        if($return){
                            echo '提交成功';
                        }
                    }
                    ?>

                    <br/><br/><br/>
                    <div id="user">
                        <div class="mdui-container">
                            <div class="mdui-row">
                                <div class="mdui-col-sm-8 mdui-col-md-8">
                                    <div class="mdui-container">
                                        <div class="mdui-typo">
                                            <h2>重置密码</h2>
                                        </div>
                                        <form method="post">
                                            <div class="mdui-textfield mdui-textfield-floating-label">
                                                <label class="mdui-textfield-label">原密码</label>
                                                <input name="origion_password" class="mdui-textfield-input"/>
                                            </div>
                                            <div class="mdui-textfield mdui-textfield-floating-label">
                                                <label class="mdui-textfield-label">新密码</label>
                                                <input name="new_password" class="mdui-textfield-input"
                                                       pattern="^.*(?=.{8,})(?=.*[a-z])(?=.*[A-Z]).*$" required/>
                                                <div class="mdui-textfield-error">密码至少 8 位，且包含大小写字母</div>
                                                <div class="mdui-textfield-helper">请输入至少 8 位，且包含大小写字母的密码</div>
                                            </div>
                                            <div class="mdui-textfield mdui-textfield-floating-label">
                                                <label class="mdui-textfield-label">确认新密码</label>
                                                <input name="new_password_repeat" class="mdui-textfield-input" type="NewPasswd2"/>
                                            </div>
                                            <button name="submit_password" class="mdui-btn mdui-color-pink mdui-ripple">提交</button>
                                        </form>
                                        <?php
                                        if (isset($_POST['submit_password'])) {
                                            $origion_password = $_POST['origion_password'];
                                            $new_password = $_POST['new_password'];
                                            $new_password_repeat = $_POST['new_password_repeat'];
                                            if ($new_password == $new_password_repeat) {
                                                $query = "SELECT User_password FROM User_info WHERE User_id=$user_id";
                                                $password_get = mysqli_query($dbc, $query);
                                                $row=mysqli_fetch_array($password_get);
                                                if ($row['User_password'] == $origion_password) {
                                                    $query = "UPDATE User_info SET User_password = \"".$new_password_repeat."\" WHERE User_id = $user_id";
                                                    $success = mysqli_query($dbc, $query);
                                                    if ($success) {
                                                        echo '修改成功！下次请使用新密码！';
                                                        sleep(1);
                                                        #header("Location: index.php");
                                                    } else {
                                                        echo '系统错误！修改失败！';
                                                    }
                                                } else {
                                                    echo '原密码错误！修改失败！';
                                                }
                                            } else {
                                                echo "两次输入的密码不同！";
                                            }
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
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