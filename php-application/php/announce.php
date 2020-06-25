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
        $user_id=$cookie_user_id;
        
        $query = "SELECT User_name FROM User_info WHERE User_id=$user_id";
        $return = mysqli_query($dbc, $query);
        $row= mysqli_fetch_array($return);
        $user_name=$row['User_name'];

        $query = "SELECT Announce_head FROM Announce_info WHERE Announce_id in (SELECT max(Announce_id) FROM Announce_info)";
        $return = mysqli_query($dbc, $query);
        $row = mysqli_fetch_array($return);
        $announce_head = $row['Announce_head'];
        
        $query = "SELECT Announce_data FROM Announce_info WHERE Announce_id in (SELECT max(Announce_id) FROM Announce_info)";
        $return = mysqli_query($dbc, $query);
        $row = mysqli_fetch_array($return);
        $announce_data=$row['Announce_data'];
        
        echo "<div class=\"mdui-col-sm-6 mdui-col-md-4\">
                <div class=\"mdui-card\">
                    <div class=\"mdui-card-header\">
                        <img class=\"mdui-card-header-avatar\" src=\"img/user.jpg\"/>
                        <div class=\"mdui-card-header-title\">".$user_name."</div>
                        <div class=\"mdui-card-header-subtitle\">".$user_id."</div>
                    </div>
                    <div class=\"mdui-card-media\">
                        <img src=\"img/card-background.jpg\"/>
                        <div class=\"mdui-card-media-covered\">
                            <div class=\"mdui-card-primary\">
                                <div class=\"mdui-card-primary-title\">你好！</div>
                                <div class=\"mdui-card-primary-subtitle\">欢迎使用本系统</div>
                            </div>
                        </div>
                    </div>
                    <div class=\"mdui-card-primary\">
                        <div class=\"mdui-card-primary-title\">
                            公告：".$announce_head."&nbsp;
                        </div>
                    </div>
                    <div class=\"mdui-card-content\">
                        <p>
                            ".$announce_data."
                        </p>
                        <br/>
                    </div>
                </div>
            </div>";
    } else {
        //验证错误设空
        setcookie("user_id", '');
        setcookie("user_pass_hash", '');
    }
}
