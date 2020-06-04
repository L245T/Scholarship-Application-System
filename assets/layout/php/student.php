<!DOCTYPE html>
<html lang="zh-cn">
<?php

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'database_name');

$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

$user_id=$_GET['user_id'];


?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width">
    <title>申报端 | 奖学金申报系统</title>
    <link rel="stylesheet" href="css/mdui.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<!--头信息&工具栏-->
<div class="mdui-appbar">
    <div class="mdui-toolbar mdui-color-blue-900">
        <a href="student.php" class="mdui-typo-headline">xx大学</a>
        <a href="student.php" class="mdui-typo-title">申报端</a>
        <div class="mdui-toolbar-spacer"></div>
        <!--登出按钮-->
        <a href="logout.html" class="mdui-btn mdui-btn-raised mdui-ripple mdui-color-pink">退出登录</a>
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
            <div class="mdui-col-sm-6 mdui-col-md-4">
                <div class="mdui-card">
                    <!--展示登陆者信息-->
                    <div class="mdui-card-header">
                        <!--展示照片-->
                        <img class="mdui-card-header-avatar" src="img/user.jpg"/>
                        <!--展示姓名-->
                        <div class="mdui-card-header-title">
                            <?php
                                $query = "SELECT User_Name FROM Stu_info WHERE User_id=$user_id";
                                $return=mysqli_query($dbc, $query);
                                echo $return;
                            ?>
                        </div>
                        <!--展示学号-->
                        <div class="mdui-card-header-subtitle">
                            <?php
                                $query = "SELECT User_id FROM Stu_info WHERE User_id=$user_id";
                                $return=mysqli_query($dbc, $query);
                                echo $return;
                            ?>
                        </div>
                    </div>
                    <!--展示登陆者信息模块到此结束-->

                    <!--增强用户体验模块，展示日期，欢迎词-->
                    <div class="mdui-card-media">
                        <!--背景图片早，中晚不同-->
                        <img src="img/card-background.jpg"/>
                        <!--文字展示-->
                        <div class="mdui-card-media-covered">
                            <div class="mdui-card-primary">
                                <!--日期-->
                                <div class="mdui-card-primary-subtitle">
                                    <div id="show_time0" style="">
                                        <script>
                                            setInterval("show_time0.innerHTML=new Date().toLocaleString()+' 星期'+'日一二三四五六'.charAt(new Date().getDay());",1000);
                                        </script>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--增强体验模块到此结束-->

                    <!--公告模块-->
                    <div class="mdui-card-primary">
                        <!--公告标题-->
                        <div class="mdui-card-primary-title">
                            <php?
                                $query = "SELECT Announced_title FROM Announced_info WHERE Announced_id in (SELECT max(Announced_id) FROM Announced_info)";
                                $return=mysqli_query($dbc, $query);
                                echo $return;
                            ?>
                            <a href="oldnotice.html">
                                <div class="mdui-btn mdui-text-color-black">历史公告</div>
                            </a>
                        </div>
                        <!--公告发布日期-->
                        <div class="mdui-card-primary-subtitle">
                            本公告由【管理员】发布于
                            <?php
                                $query = "SELECT Announced_date FROM Announced_info WHERE Announced_id in (SELECT max(Announced_id) FROM Announced_info)";
                                $return=mysqli_query($dbc, $query);
                                echo $return;
                            ?>
                        </div>
                    </div>

                    <!--公告内容信息-->
                    <div class="mdui-card-content">
                        <?php
                            $query = "SELECT Announced_data FROM Announced_info WHERE Announced_id in (SELECT max(Announced_id) FROM Announced_info)";
                            $return=mysqli_query($dbc, $query);
                            echo $return;
                        ?>
                        <!--空白一行，美观-->
                        <br/>
                    </div>

                    <!--公告模块到此结束-->
                </div>
            </div>
            <!--信息展示模块到此结束-->

            <!--申报填写模块-->
            <div class="mdui-col-sm-8 mdui-col-md-8">
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
                        $Stu_id=mysqli_query($dbc,'SELECT Stu_id FROM Stu_info WHERE User_id=$user_id');
                        $query = "INSERT INTO Apply_data (Apply_type,Apply_reason,Stu_id) VALUES ('$type', '$reason','$Stu_id')";
                        mysqli_query($dbc, $query);
                        echo "<p>提交成功</p>";
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>


<!--查询页面-->
<div id="student-query">
    <div class="mdui-container">
        <div class="mdui-row">
            <!--信息展示模块-->
            <div class="mdui-col-sm-6 mdui-col-md-4">
                <div class="mdui-card">
                    <!--展示登陆者信息-->
                    <div class="mdui-card-header">
                        <!--展示照片-->
                        <img class="mdui-card-header-avatar" src="img/user.jpg"/>
                        <!--展示姓名-->
                        <div class="mdui-card-header-title">
                            <?php
                            $query = "SELECT User_Name FROM Stu_info WHERE User_id=$user_id";
                            $return=mysqli_query($dbc, $query);
                            echo $return;
                            ?>
                        </div>
                        <!--展示学号-->
                        <div class="mdui-card-header-subtitle">
                            <?php
                            $query = "SELECT User_id FROM Stu_info WHERE User_id=$user_id";
                            $return=mysqli_query($dbc, $query);
                            echo $return;
                            ?>
                        </div>
                    </div>
                    <!--展示登陆者信息模块到此结束-->

                    <!--增强用户体验模块，展示日期，欢迎词-->
                    <div class="mdui-card-media">
                        <!--背景图片早，中晚不同-->
                        <img src="img/card-background.jpg"/>
                        <!--文字展示-->
                        <div class="mdui-card-media-covered">
                            <div class="mdui-card-primary">
                                <!--日期-->
                                <div class="mdui-card-primary-subtitle">
                                    <div id="show_time0" style="">
                                        <script>
                                            setInterval("show_time0.innerHTML=new Date().toLocaleString()+' 星期'+'日一二三四五六'.charAt(new Date().getDay());",1000);
                                        </script>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--增强体验模块到此结束-->

                    <!--公告模块-->
                    <div class="mdui-card-primary">
                        <!--公告标题-->
                        <div class="mdui-card-primary-title">
                            <php?
                            $query = "SELECT Announced_title FROM Announced_info WHERE Announced_id in (SELECT max(Announced_id) FROM Announced_info)";
                            $return=mysqli_query($dbc, $query);
                            echo $return;
                            ?>
                            <a href="oldnotice.html">
                                <div class="mdui-btn mdui-text-color-black">历史公告</div>
                            </a>
                        </div>
                        <!--公告发布日期-->
                        <div class="mdui-card-primary-subtitle">
                            本公告由【管理员】发布于
                            <?php
                            $query = "SELECT Announced_date FROM Announced_info WHERE Announced_id in (SELECT max(Announced_id) FROM Announced_info)";
                            $return=mysqli_query($dbc, $query);
                            echo $return;
                            ?>
                        </div>
                    </div>

                    <!--公告内容信息-->
                    <div class="mdui-card-content">
                        <?php
                            $query = "SELECT Announced_data FROM Announced_info WHERE Announced_id in (SELECT max(Announced_id) FROM Announced_info)";
                            $return=mysqli_query($dbc, $query);
                            echo $return;
                        ?>
                        <!--空白一行，美观-->
                        <br/>
                    </div>

                    <!--公告模块到此结束-->

                </div>
            </div>
            <!--信息展示模块到此结束-->

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
                                            $query = "SELECT First_result FROM Audit_data WHERE Apply_id in (SELECT Apply_id FROM Apply_data WHERE Stu_id in (SELECT Stu_id FROM Stu_info WHERE User_id=$user_id))";
                                            $return=mysqli_query($dbc, $query);
                                            echo $return;
                                        ?>
                                    </td>
                                    <td class="mdui-text-color-green">
                                        <?php
                                            $query = "SELECT First_reason FROM Audit_data WHERE Apply_id in (SELECT Apply_id FROM Apply_data WHERE Stu_id in (SELECT Stu_id FROM Stu_info WHERE User_id=$user_id))";
                                            $return=mysqli_query($dbc, $query);
                                            echo $return;
                                        ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>学院</td>
                                    <td class="mdui-text-color-red">
                                        <?php
                                        $query = "SELECT Second_result FROM Audit_data WHERE Apply_id in (SELECT Apply_id FROM Apply_data WHERE Stu_id in (SELECT Stu_id FROM Stu_info WHERE User_id=$user_id))";
                                        $return=mysqli_query($dbc, $query);
                                        echo $return;
                                        ?>
                                    </td>
                                    <td class="mdui-text-color-red">
                                        <?php
                                        $query = "SELECT Second_reason FROM Audit_data WHERE Apply_id in (SELECT Apply_id FROM Apply_data WHERE Stu_id in (SELECT Stu_id FROM Stu_info WHERE User_id=$user_id))";
                                        $return=mysqli_query($dbc, $query);
                                        echo $return;
                                        ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>学校</td>
                                    <td class="mdui-text-color-grey">
                                        <?php
                                        $query = "SELECT Third_result FROM Audit_data WHERE Apply_id in (SELECT Apply_id FROM Apply_data WHERE Stu_id in (SELECT Stu_id FROM Stu_info WHERE User_id=$user_id))";
                                        $return=mysqli_query($dbc, $query);
                                        echo $return;
                                        ?>
                                    </td>
                                    <td class="mdui-text-color-grey">
                                        <?php
                                        $query = "SELECT Third_reason FROM Audit_data WHERE Apply_id in (SELECT Apply_id FROM Apply_data WHERE Stu_id in (SELECT Stu_id FROM Stu_info WHERE User_id=$user_id))";
                                        $return=mysqli_query($dbc, $query);
                                        echo $return;
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
                        $Stu_id=mysqli_query($dbc,'SELECT Stu_id FROM Stu_info WHERE User_id=$user_id');
                        $query = "UPDATE Apply_data SET Apply_reason=$reason WHERE Stu_id=$Stu_id";
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
            <!--信息展示模块-->
            <div class="mdui-col-sm-6 mdui-col-md-4">
                <div class="mdui-card">
                    <!--展示登陆者信息-->
                    <div class="mdui-card-header">
                        <!--展示照片-->
                        <img class="mdui-card-header-avatar" src="img/user.jpg"/>
                        <!--展示姓名-->
                        <div class="mdui-card-header-title">
                            <?php
                            $query = "SELECT User_Name FROM Stu_info WHERE User_id=$user_id";
                            $return=mysqli_query($dbc, $query);
                            echo $return;
                            ?>
                        </div>
                        <!--展示学号-->
                        <div class="mdui-card-header-subtitle">
                            <?php
                            $query = "SELECT User_id FROM Stu_info WHERE User_id=$user_id";
                            $return=mysqli_query($dbc, $query);
                            echo $return;
                            ?>
                        </div>
                    </div>
                    <!--展示登陆者信息模块到此结束-->

                    <!--增强用户体验模块，展示日期，欢迎词-->
                    <div class="mdui-card-media">
                        <!--背景图片早，中晚不同-->
                        <img src="img/card-background.jpg"/>
                        <!--文字展示-->
                        <div class="mdui-card-media-covered">
                            <div class="mdui-card-primary">
                                <!--日期-->
                                <div class="mdui-card-primary-subtitle">
                                    <div id="show_time0" style="">
                                        <script>
                                            setInterval("show_time0.innerHTML=new Date().toLocaleString()+' 星期'+'日一二三四五六'.charAt(new Date().getDay());",1000);
                                        </script>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--增强体验模块到此结束-->

                    <!--公告模块-->
                    <div class="mdui-card-primary">
                        <!--公告标题-->
                        <div class="mdui-card-primary-title">
                            <php?
                            $query = "SELECT Announced_title FROM Announced_info WHERE Announced_id in (SELECT max(Announced_id) FROM Announced_info)";
                            $return=mysqli_query($dbc, $query);
                            echo $return;
                            ?>
                            <a href="oldnotice.html">
                                <div class="mdui-btn mdui-text-color-black">历史公告</div>
                            </a>
                        </div>
                        <!--公告发布日期-->
                        <div class="mdui-card-primary-subtitle">
                            本公告由【管理员】发布于
                            <?php
                            $query = "SELECT Announced_date FROM Announced_info WHERE Announced_id in (SELECT max(Announced_id) FROM Announced_info)";
                            $return=mysqli_query($dbc, $query);
                            echo $return;
                            ?>
                        </div>
                    </div>

                    <!--公告内容信息-->
                    <div class="mdui-card-content">
                        <?php
                        $query = "SELECT Announced_data FROM Announced_info WHERE Announced_id in (SELECT max(Announced_id) FROM Announced_info)";
                        $return=mysqli_query($dbc, $query);
                        echo $return;
                        ?>
                        <!--空白一行，美观-->
                        <br/>
                    </div>

                    <!--公告模块到此结束-->

                </div>
            </div>
            <!--信息展示模块到此结束-->
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
                        <button class="mdui-btn mdui-color-pink mdui-ripple" name="submit">提交</button>
                    </form>
                    <?php
                    if(isset($_POST['submit'])){
                        $cardID=$_POST['cardid'];
                        $query='UPDATE Stu_info SET Band_account=$cardID WHERE User_id=$user_id';
                        $return=mysqli_query($dbc,$query);
                        if($return){
                            echo '提交成功';
                        }
                    }
                    ?>

                    <br/><br/><br/>
                    <form method="post">
                        <div class="mdui-typo">
                            <h2>重置密码</h2>
                        </div>
                        <div class="mdui-textfield mdui-textfield-floating-label">
                            <label class="mdui-textfield-label">原密码</label>
                            <input class="mdui-textfield-input" name="oldpwd" type="OldPasswd"/>
                        </div>
                        <div class="mdui-textfield mdui-textfield-floating-label">
                            <label class="mdui-textfield-label">新密码</label>
                            <input class="mdui-textfield-input" type="NewPasswd" name='newpwd1' pattern="^.*(?=.{8,})(?=.*[a-z])(?=.*[A-Z]).*$" required/>
                            <div class="mdui-textfield-error">密码至少 8 位，且包含大小写字母</div>
                            <div class="mdui-textfield-helper">请输入至少 8 位，且包含大小写字母的密码</div>
                        </div>
                        <div class="mdui-textfield mdui-textfield-floating-label">
                            <label class="mdui-textfield-label">确认新密码</label>
                            <input class="mdui-textfield-input" name="newpwd2" type="NewPasswd2"/>
                        </div>
                        <button name="submit" class="mdui-btn mdui-color-pink mdui-ripple">提交</button>
                    </form>
                    <?php
                    if(isset($_POST['submit'])){
                        $oldpwd=$_POST['pldpwd'];
                        $newpwd1=$_POST['newpwd1'];
                        $newpwd2=$_POST['newpwd2'];
                        $query='SELECT Password FROM User_info WHERE User_id=$user_id';
                        $userpwd=mysqli_query($dbc,$query);
                        if($userpwd!=$oldpwd){
                            echo '原密码输入错误';
                        }
                        else{
                            if($newpwd1!=$newpwd2){
                                echo '两次密码不同，请重新输入';
                            }
                            else{
                                $query='UPDATE User_info SET Password=$newpwd2 WHERE User_id=$user_id';
                                $return=mysqli_query($dbc,$query);
                                if($return){
                                    echo '修改成功';
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
                xx大学奖学金申报系统
                <br/>地址：X省Y市Z区A路
                <br/>联系电话：0123456789
            </div>
        </div>
    </div>
</div>
<script src="js/mdui.min.js"></script>
</body>

</html>