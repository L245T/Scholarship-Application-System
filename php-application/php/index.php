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
            <div class="login-title mdui-color-blue-900 mdui-text-color-white">
                登录
            </div>

            <!--登录表单-->
            <form method="post">
                <div class="mdui-container login-form-box">
                    <div class="mdui-textfield mdui-textfield-floating-label login-textfield">
                        <i class="mdui-icon material-icons">account_circle</i>
                        <label class="mdui-textfield-label">请输入用户id</label>
                        <input name="user_id" class="mdui-textfield-input" type="text"/>
                    </div>
                    <div class="mdui-textfield mdui-textfield-floating-label login-textfield">
                        <i class="mdui-icon material-icons">lock</i>
                        <label class="mdui-textfield-label">请输入密码</label>
                        <input name="password" class="mdui-textfield-input" type="password"/>
                    </div>
                </div>
                <!--提交按钮-->
                <div class="mdui-container login-footer">
                    <div class="mdui-row-xs-2">
                        <div class="mdui-col">
                            <button name="submit" type="submit"
                                    class="mdui-btn mdui-btn-block mdui-btn-raised mdui-color-green-200 mdui-ripple">
                                登录
                            </button>
                        </div>
                        <div class="mdui-col">
                            <a href="forget_pass.php" class="mdui-btn mdui-btn-block mdui-btn-raised mdui-color-amber-500 mdui-ripple">
                                忘记密码
                            </a>
                        </div>
                    </div>
                </div>
            </form>
            <div class="mdui-text-center">
                <div class="login-response">
                    <div class="mdui-typo-body-2 ">
                        <?php
                        if (isset($_POST['submit'])) {
                            $user_id = $_POST['user_id'];
                            $password_submit = $_POST['password'];
                            $password_hash = md5($password_submit);
                            $query = "SELECT User_password FROM User_info WHERE User_id=$user_id";
                            $password_return = mysqli_query($dbc, $query);
                            $row=mysqli_fetch_array($password_return);
                            if ($password_hash != md5($row['User_password'])) {
                                echo '<p class=\"mdui-text-color-red\"><strong>账户或密码错误！请重新输入！</strong></p>';
                            } else {
                                echo '<p class="mdui-text-color-green"><strong>登陆成功！正在进入系统！</strong></p>';
                                setcookie("user_id", $user_id, time()+3600);
                                setcookie("user_pass_hash", md5($password_hash), time()+3600);//cookie的密码值为密码的MD5的MD5
                                $query = "SELECT User_type FROM User_info WHERE User_id=$user_id";
                                $jump_type = mysqli_query($dbc, $query);
                                $row=mysqli_fetch_array($jump_type);
                                if ($row['User_type'] == 1) {
                                    $jump_url = "student.php";
                                } else if (($row['User_type'] == 2)||($row['User_type'] == 3)||($row['User_type'] == 4)) {
                                    $jump_url = "auditor.php";
                                    setcookie("user_id", $user_id, time()+18000);
                                    setcookie("user_pass_hash", md5($password_hash), time()+18000);//cookie的密码值为密码的MD5的MD5
                                } else if ($row['User_type'] == 0) {
                                    $jump_url = "admin.php";
                                }else{
                                    echo "<p class=\"mdui-text-color-red\"><strong>账户权限错误！请尝试联系管理员！</strong></p>";
                                }
                                header('Location: ' . $jump_url);
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>