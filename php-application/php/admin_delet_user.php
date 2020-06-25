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
        <a href="admin.php#add-notice" class="mdui-ripple mdui-ripple-white">公告发布</a>
        <a href="admin_add_new_user.php#user-add" class="mdui-ripple mdui-ripple-white">增加用户</a>
        <a href="#user-del" class="mdui-ripple mdui-ripple-white">删除用户</a>
        <a href="admin_personal_center.php#user" class="mdui-ripple mdui-ripple-white">个人中心</a>
    </div>
</div>

<!--留白2行-->
<br/><br/>

<div id="user-del">
    <div class="mdui-container">
        <div class="mdui-row">
            <div class="mdui-col-sm-8 mdui-col-md-8">
                <div class="mdui-container">
                    <div class="mdui-typo">
                        <h1>请输入下面的信息</h1>
                        <p>为确保安全性，两项都需要正确输入才能进行删除。</p>
                    </div>
                    <form method="post">
                        <div class="mdui-row-md-2">
                            <div class="mdui-col">
                                <div class="mdui-textfield mdui-textfield-floating-label">
                                    <label class="mdui-textfield-label">请输入姓名</label>
                                    <input name="user_name_sub" class="mdui-textfield-input" type="text"/>
                                </div>
                            </div>
                            <div class="mdui-col">
                                <div class="mdui-textfield mdui-textfield-floating-label">
                                    <label class="mdui-textfield-label">请输入用户ID</label>
                                    <input name="user_id_sub" class="mdui-textfield-input" type="text"/>
                                </div>
                            </div>
                        </div>
                        <br/><br/>
                        <button name="submit_delet_user" class="mdui-btn mdui-btn-raised mdui-ripple mdui-color-pink">删除</button>
                    </form>
                    <?php
                    if (isset($_POST['submit_delet_user'])) {
                        $user_name_submit = $_POST['user_name_sub'];
                        $user_id_submit = $_POST['user_id_sub'];
                        $query = "SELECT User_name FROM User_info WHERE User_id=$user_id_submit";
                        $return = mysqli_query($dbc, $query);   
                        $row= mysqli_fetch_array($return);
                        if ($row['User_name'] == $user_name_submit) {
                            $query = "SELECT User_type FROM User_info WHERE User_id=$user_id_submit";
                            $user_type = mysqli_query($dbc, $query);
                            $row=mysqli_fetch_array($user_type);
                            if($row['User_type']==1){
                                $query = "DELETE FROM Stu_info WHERE Stu_id=$user_id_submit";
                                $success2 = mysqli_query($dbc, $query);   
                            }else if($row['User_type']==2||$row['User_type']==3||$row['User_type']==4){
                                $query = "DELETE FROM Auditor_info WHERE Auditor_id=$user_id_submit";
                                $success2 = mysqli_query($dbc, $query); 
                            } else {
                                $success2=true;
                            }
                            $query = "DELETE FROM User_info WHERE User_id=$user_id_submit";
                            $success = mysqli_query($dbc, $query);
                            if ($success&&$success2) {
                                echo '删除成功！';
                            } else {
                                echo '系统错误！删除失败！';
                            }
                        } else {
                            echo '输入错误！删除失败！';
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