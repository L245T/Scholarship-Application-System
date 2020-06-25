<?php
define('host', 'localhost');
define('db_user', 'group');
define('db_password', 'zsygsjk123');
define('db_name', 'scholarship');

$dbc = mysqli_connect(host, db_user, db_password, db_name);
if ($dbc->error) {
    die("连接失败! ".$dbc->error );
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
            $jump_url = "/student.php";
            header('Location: ' . $jump_url);
        } else if (($row['User_type'] == 2)||($row['User_type'] == 3)||($row['User_type'] == 4)) {
            $jump_url = "/auditor.php";
            header('Location: ' . $jump_url);
        } else if ($row['User_type'] == 0) {
            ;//不设置跳转
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
    <title>管理端 | 奖学金申报系统</title>
    <link rel="stylesheet" href="css/mdui.min.css">
    <link rel="stylesheet" href="css/style.css">
    <script src="js/mdui.min.js"></script>
</head>
<body>
<!--头信息&工具栏-->
<div class="mdui-appbar">
    <div class="mdui-toolbar mdui-color-blue-900">
        <a href="admin.php" class="mdui-typo-headline">奖学金申报系统</a>
        <a href="admin.php" class="mdui-typo-title">管理端</a>
        <div class="mdui-toolbar-spacer"></div>
        <!--登出按钮-->
        <a href="logout.php" class="mdui-btn mdui-btn-raised mdui-ripple mdui-color-pink">退出登录</a>
    </div>
    <!--工具栏-->
    <div class="mdui-tab mdui-tab-centered mdui-color-blue-900" mdui-tab>
        <a href="admin.php#add-notice" class="mdui-ripple mdui-ripple-white">公告发布</a>
        <a href="admin_add_new_user.php#user-add" class="mdui-ripple mdui-ripple-white">增加用户</a>
        <a href="admin_delet_user.php#user-del" class="mdui-ripple mdui-ripple-white">删除用户</a>
        <a href="#user" class="mdui-ripple mdui-ripple-white">个人中心</a>
    </div>
</div>

<!--留白2行-->
<br/><br/>

<!--用户中心-->
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
                                    echo '修改成功！正在退出登录，请使用新密码重新登录！';
                                    sleep(1);
                                    header('Location: index.php');
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