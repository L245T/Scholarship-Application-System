<?php
define('host', 'localhost');
define('db_user', 'group');
define('db_password', 'zsygsjk123');
define('db_name', 'scholarship');

$dbc = mysqli_connect(host, db_user, db_password, db_name);

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
        <a href="#add-notice" class="mdui-ripple mdui-ripple-white">公告发布</a>
        <a href="admin_add_new_user.php#user-add" class="mdui-ripple mdui-ripple-white">增加用户</a>
        <a href="admin_delet_user.php#user-del" class="mdui-ripple mdui-ripple-white">删除用户</a>
        <a href="admin_personal_center.php#user" class="mdui-ripple mdui-ripple-white">个人中心</a>
    </div>
</div>

<!--留白2行-->
<br/><br/>

<div id="add-notice">
    <div class="mdui-container">
        <div class="mdui-row">
            <!--信息展示模块-->
            <?php
                require_once("announce.php");
            ?>
            <div class="mdui-col-sm-8 mdui-col-md-8">
                <div class="mdui-container">
                    <form method="POST">
                        <div class="mdui-typo">
                            <h1>公告标题</h1>
                        </div>
                        <div class="mdui-textfield mdui-textfield-floating-label">
                            <label class="mdui-textfield-label">请填写公告标题</label>
                            <input name="announce_head_submit" class="mdui-textfield-input" type="text" maxlength="10"/>
                        </div>
                        <div class="mdui-typo">
                            <h1>公告内容</h1>
                        </div>
                        <div class="mdui-textfield mdui-textfield-floating-label">
                            <label class="mdui-textfield-label">请填写公告内容</label>
                            <input name="announce_data_submit" class="mdui-textfield-input" type="text"
                                   maxlength="1000"/>
                        </div>
                        <button name="submit_add_announce" class="mdui-btn mdui-btn-raised mdui-ripple mdui-color-pink">发布</button>
                    </form>
                    <?php
                    if (isset($_POST['submit_add_announce'])) {
                        $announce_head_submit = $_POST['announce_head_submit'];
                        $announce_data_submit = $_POST['announce_data_submit'];
                        $query = "SELECT max(Announce_id) FROM Announce_info";
                        // $new_announce_id = mysqli_query($dbc, $query);
                        // $row=mysqli_fetch_array($new_announce_id);
                        // $old_announce_get=$row['Announce_id'];
                        // echo $old_announce_get."???";
                        // $new_announce_id_get = $old_announce_get + 1;
                        // echo $new_announce_id_get;
                        $query1 = "UPDATE Announce_info SET Announce_head = \"".$announce_head_submit."\" WHERE Announce_id = 1";
                        $query2 = "UPDATE Announce_info SET Announce_data = \"".$announce_data_submit."\" WHERE Announce_id = 1";
                        $success1 = mysqli_query($dbc, $query1);
                        $success2 = mysqli_query($dbc, $query2);
                        if ($success1 == 1 && $success2 == 2) {
                            echo '发布成功！';
                        } else {
                            echo '系统错误！发布失败';
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