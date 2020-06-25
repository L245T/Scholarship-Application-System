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
$cookie_user_id=$_COOKIE["user_id"];
if($cookie_user_id!=''){
    $cookie_user_pass_hash=$_COOKIE["user_pass_hash"];
    $query = "SELECT User_password FROM User_info WHERE User_id=$cookie_user_id";
    $password_return = mysqli_query($dbc, $query);
    $row=mysqli_fetch_array($password_return);
    if ($cookie_user_pass_hash == md5(md5($row['User_password']))) {
        setcookie("user_id",$cookie_user_id,time()+3600);
        setcookie("user_pass_hash",$cookie_user_pass_hash,time()+3600);
        $query = "SELECT User_type FROM User_info WHERE User_id=$cookie_user_id";
        $jump_type = mysqli_query($dbc, $query);
        $row=mysqli_fetch_array($jump_type);
        if ($row['User_type'] == 1) {
            $jump_url = "/student.php";
        } else if (($row['User_type'] == 2)||($row['User_type'] == 3)||($row['User_type'] == 4)) {
            $jump_url = "/auditor.php";
        } else if ($row['User_type'] == 0) {
            $jump_url = "/admin.php";
        }else{
            echo "<p class=\"mdui-text-color-red\"><strong>账户权限错误！请尝试联系管理员！</strong></p>";
        }
        header('Location: ' . $jump_url);
    }
    else{
        //验证错误设空
        setcookie("user_id", '');
        setcookie("user_pass_hash", '');
    }
}
?>

<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width">
    <title>登录 | 奖学金申报系统</title>
    <link rel="stylesheet" href="css/mdui.min.css">
    <link rel="stylesheet" href="css/style.css">
    <script src="js/mdui.min.js"></script>
</head>
<body>
<div class="mdui-container login-mdui-container">
    <div class="mdui-row">
        <div class="login-box">
        <div class="login-title login-dialog-title mdui-color-amber mdui-text-color-blue-50">
            忘记密码？
        </div>
        <!--表单-->
        <form method="post">
            <div class="mdui-container login-dialog-box">
                <div class="mdui-textfield mdui-textfield-floating-label login-textfield login-dialog-textfield">
                    <div class="mdui-textfield mdui-textfield-floating-label">
                        <label class="mdui-textfield-label">请输入用户ID</label>
                        <input name="user_id" class="mdui-textfield-input" type="username"/>
                    </div>
                    <div class="mdui-textfield mdui-textfield-floating-label">
                        <label class="mdui-textfield-label">请输入身份证号码</label>
                        <input name="user_account_id" class="mdui-textfield-input" type="username"/>
                    </div>
                    <div class="mdui-textfield mdui-textfield-floating-label">
                        <label class="mdui-textfield-label">请输入新的密码</label>
                        <input name="user_password_new_1" class="mdui-textfield-input" type="password">
                    </div>
                    <div class="mdui-textfield mdui-textfield-floating-label">
                        <label class="mdui-textfield-label">请再次新的密码</label>
                        <input name="user_password_new_2" class="mdui-textfield-input" type="password">
                    </div>
                </div>
            </div>
            <div class="mdui-dialog-actions">
                <a href="index.php" class="mdui-btn mdui-ripple" mdui-dialog-close>返回登录</a>
                <button name="submit" class="mdui-btn mdui-ripple" mdui-dialog-confirm>提交</button>
            </div>
        </form>
        <div class="mdui-text-center">
        <?php
        if (isset($_POST['submit'])) {
            $user_id = $_POST['user_id'];
            $user_account_id = $_POST['user_account_id'];
            $new_password_submit = $_POST['user_password_new_1'];
            $new_password_submit_repeat = $_POST['user_password_new_2'];
            if ($new_password_submit != $new_password_submit_repeat) {
                echo "<p class=\"mdui-text-color-red\"><strong>两次输入不同！请重新输入！</strong></p>";
            } else {
                $query = "SELECT User_personal_identify_id FROM User_info WHERE User_id=$user_id";
                $user_personal_identify_id_return = mysqli_query($dbc, $query);
                $row=mysqli_fetch_array($user_personal_identify_id_return);
                if ($user_account_id == $row['User_personal_identify_id']) {
                    $query = "UPDATE User_info SET User_password=\"".$new_password_submit."\" WHERE User_id=$user_id";
                    $success = mysqli_query($dbc, $query);
                    if ($success) {
                        echo "<p class=\"mdui-text-color-green\"><strong>修改成功！请使用新密码重新登录！</strong></p>";
                    }
                    else{
                        echo "<p class=\"mdui-text-color-red\"><strong>系统错误！请稍后再试！</strong></p>";
                    }
                }
                else{
                    echo "<p class=\"mdui-text-color-red\"><strong>用户不存在或身份证号码错误！</strong></p>";
                }
            }
        }
        ?>
        </div>
    </div>
</div>


</body>
</html>