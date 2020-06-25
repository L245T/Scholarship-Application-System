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
        <a href="#user-add" class="mdui-ripple mdui-ripple-white">增加用户</a>
        <a href="admin_delet_user.php#user-del" class="mdui-ripple mdui-ripple-white">删除用户</a>
        <a href="admin_personal_center.php#user" class="mdui-ripple mdui-ripple-white">个人中心</a>
    </div>
</div>

<!--留白2行-->
<br/><br/>

<div id="user-add">
    <div class="mdui-container">
        <div class="mdui-row">
            <div class="mdui-col-sm-8 mdui-col-md-8">
                <div class="mdui-container">
                    <div class="typo">
                        <h1>用户类型选择</h1>
                    </div>
                    <form method="post">
                        <select name="select_user_type" class="mdui-select" mdui-select >
                            <option value="1">学生</option>
                            <option value="2">班级审核员</option>
                            <option value="3">学院审核员</option>
                            <option value="4">学校审核员</option>
                            <option value="0">管理员</option>
                        </select>

                        <div class="mdui-typo">
                            <h1>用户信息填写</h1>
                        </div>

                        <div class="mdui-row-md-3">
                            <div class="mdui-col">
                                <div class="mdui-textfield mdui-textfield-floating-label">
                                    <label class="mdui-textfield-label">姓名</label>
                                    <input name="user_name" class="mdui-textfield-input" type="text"/>
                                </div>
                            </div>

                            <div class="mdui-col">
                                <div class="mdui-textfield mdui-textfield-floating-label">
                                    <label class="mdui-textfield-label">学号/工号</label>
                                    <input name="user_id" class="mdui-textfield-input" type="text"/>
                                </div>
                            </div>
                            <div class="mdui-col">
                                <div class="mdui-textfield mdui-textfield-floating-label">
                                    <label class="mdui-textfield-label">身份证号码（重置密码用）</label>
                                    <input name="user_personal_identify_id" class="mdui-textfield-input" type="text"/>
                                </div>
                            </div>
                        </div>
                        <br/><br/>
                        <div class="mdui-row-md-3">
                            <div class="mdui-col">
                                <div class="mdui-typo-body-2-opacity">请选择学院</div>
                                <select name="select_college" class="mdui-select" mdui-select>
                                    <option value="0">未选择</option>
                                    <option value="1">校级</option>
                                    <option value="193">计算机学院</option>
                                </select>
                            </div>
                            <div class="mdui-col">
                                <div class="mdui-typo-body-2-opacity">请选择班级</div>
                                <select name="select_class" class="mdui-select" mdui-select>
                                    <option value="0">未选择</option>
                                    <option value="193181">193181</option>
                                    <option value="193182">193182</option>
                                </select>
                            </div>
                            <div class="mdui-col">
                                <div class="mdui-typo-body-2-opacity">请选择性别</div>
                                <select name="select_sex" class="mdui-select" mdui-select>
                                    <option value="0">未选择</option>
                                    <option value="1">男</option>
                                    <option value="2">女</option>
                                </select>
                            </div>
                        </div>
                        <br/><br/>
                        <button name="submit_add_user" class="mdui-btn mdui-btn-raised mdui-ripple mdui-color-pink" type="submit">提交</button>
                    </form>
                    <?php
                    if(isset($_POST['submit_add_user'])){
                        $user_type_submit=$_POST['select_user_type'];
                        $user_name_submit=$_POST['user_name'];
                        $user_id_submit=$_POST['user_id'];
                        $user_personal_identify_id_submit=$_POST['user_personal_identify_id'];
                        $college_submit=$_POST['select_college'];
                        $class_submit=$_POST['select_class'];
                        $sex_submit=$_POST['select_sex'];
                        if($user_id_submit==null&&$user_personal_identify_id_submit==null&&$user_name_submit==null){
                            echo '填写不完整！请重新填写后提交！';
                        } else {
                            $query="SELECT count( * ) FROM User_info WHERE User_id =$user_id_submit";
                            $find=mysqli_query($dbc,$query);
                            if(find!=0){
                                echo '用户ID已存在！';
                            } else {
                                $user_password=123456;
                                if($user_type_submit==0){
                                    $query="INSERT INTO User_info(User_id,User_name,User_password,User_type,User_personal_identify_id) 
                                            VALUES($user_id_submit,\"$user_name_submit\",$user_password,$user_type_submit,$user_personal_identify_id_submit )";
                                    $success=mysqli_query($dbc,$query);
                                    if($success){
                                        echo '添加成功!';
                                    } else {
                                        echo '系统错误！添加失败！';
                                    }
                                } else if($user_type_submit==1){
                                    if($class_submit==0&&$college_submit==0){
                                        echo '请选择班级学院！';
                                    } else {
                                        $score=4;
                                        $Bank_account=NULL;
                                        $query1="INSERT INTO User_info(User_id,User_name,User_password,User_type,User_personal_identify_id) VALUES($user_id_submit,\"$user_name_submit\",$user_password,$user_type_submit,$user_personal_identify_id_submit)";
                                        $success1=mysqli_query($dbc,$query1);
                                        
                                        $query2="INSERT INTO Stu_info(Stu_id,Stu_Name,User_id,Class_id,Academy_id,Stu_score,Sex) VALUES($user_id_submit,\"$user_name_submit\",$user_id_submit,$class_submit,$college_submit,$score,$sex_submit)";

                                        $success2=mysqli_query($dbc,$query2);
                                        if($success1&&$success2){
                                            echo '添加成功!';
                                        } else {
                                            echo '系统错误！添加失败！';
                                        }
                                    }
                                } else if($user_type_submit==2){
                                    if($class_submit==0&&$college_submit==0&&$college_submit!=1){
                                        echo '请选择班级学院！';
                                    } else {
                                        $belonging_type=$user_type_submit-1;
                                        $query1="INSERT INTO User_info(User_id,User_name,User_password,User_type,User_personal_identify_id) VALUES($user_id_submit,\"$user_name_submit\",$user_password,$user_type_submit,$user_personal_identify_id_submit)";
                                        $success1=mysqli_query($dbc,$query1);
                                        $query2="INSERT INTO Auditor_info(Auditor_id,Auditor_name,User_id,Belonging_type,Belonging_info) VALUES($user_id_submit,\"$user_name_submit\",$user_id_submit,$belonging_type,$class_submit)";
                                        $success2=mysqli_query($dbc,$query2);
                                        if($success1&&$success2){
                                            echo '添加成功!';
                                        } else {
                                            echo '系统错误！添加失败！';
                                        }
                                    }
                                } else if($user_type_submit==3){
                                    if($college_submit==0&&$college_submit!=1){
                                        echo '请选择学院！';
                                    } else {
                                        $belonging_type=$user_type_submit-1;
                                        $query1="INSERT INTO User_info(User_id,User_name,User_password,User_type,User_personal_identify_id) VALUES($user_id_submit,\"$user_name_submit\",$user_password,$user_type_submit,$user_personal_identify_id_submit)";
                                        $success1=mysqli_query($dbc,$query1);
                                        $query2="INSERT INTO Auditor_info(Auditor_id,Auditor_name,User_id,Belonging_type,Belonging_info) VALUES($user_id_submit,\"$user_name_submit\",$user_id_submit,$belonging_type,$college_submit)";
                                        $success2=mysqli_query($dbc,$query2);                                        
                                        if($success1&&$success2){
                                            echo '添加成功!';
                                        } else {
                                            echo '系统错误！添加失败！';
                                        }
                                    }
                                } else if($user_type_submit==4){
                                    $belonging_type=$user_type_submit-1;
                                    $belonging_info=null;
                                    $query1="INSERT INTO User_info(User_id,User_name,User_password,User_type,User_personal_identify_id) VALUES($user_id_submit,\"$user_name_submit\",$user_password,$user_type_submit,$user_personal_identify_id_submit)";
                                    $success1=mysqli_query($dbc,$query1);
                                    $query2="INSERT INTO Auditor_info(Auditor_id,Auditor_name,User_id,Belonging_type,Belonging_info) VALUES($user_id_submit,\"$user_name_submit\",$user_id_submit,$belonging_type,$belonging_info)";
                                    $success2=mysqli_query($dbc,$query2);                                        
                                    if($success1&&$success2){
                                        echo '添加成功!';
                                    } else {
                                        echo '系统错误！添加失败！';
                                    }
                                } else {
                                    echo '提交错误！请重新填写！';
                                }
                            }
                            

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