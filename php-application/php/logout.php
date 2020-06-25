<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="UTF-8">
    <title>正在跳转 | 登出</title>
    <meta http-equiv="refresh" content="3;url=index.php">
    <meta name="viewport" content="width=device-width">
    <link rel="stylesheet" href="css/mdui.min.css">
    <link rel="stylesheet" href="css/style.css">
    <script src="js/mdui.min.js"></script>
</head>
<body>
<!--头信息&工具栏-->
<div class="mdui-appbar">
    <div class="mdui-toolbar mdui-color-blue-900">
        <a href="index.php" class="mdui-typo-headline">奖学金申报系统</a>
        <a href="index.php" class="mdui-typo-title">登出</a>
        <div class="mdui-toolbar-spacer"></div>
    </div>
</div>

<!--登出信息展示-->
<div class="mdui-container">
    <br/><br/><br/><br/><br/>
    <div class="mdui-text-center">
        <?php
            setcookie("user_id", '');
            setcookie("user_pass_hash", '');
        ?>
        <div class="mdui-typo-display-3 mdui-text-color-black">
            您已经成功登出<br/><br/>
        </div>
        <div class="mdui-typo-display-1 mdui-text-color-black">
            将在3秒后跳转...
            <br/><br/>
            <a href="index.php">
                如果您的浏览器没有跳转，请点击这里
            </a>
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
