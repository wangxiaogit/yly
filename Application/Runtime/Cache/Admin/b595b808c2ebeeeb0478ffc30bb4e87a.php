<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="zh_CN" style="overflow: hidden;">
<head>
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<!-- Set render engine for 360 browser -->
<meta name="renderer" content="webkit">
<meta charset="utf-8">
<title><?php echo L('WEBSITE_TITLE');?></title>

<meta name="keywords" content="<?php echo L('WEBSITE_KEYWORDS');?>">
<meta name="description" content="<?php echo L('WEBSITE_DESCRIPTION');?>">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link href="/yly/Public/Admin/img/favicon.ico" type="image/x-icon" rel="shortcut icon">
<link rel="stylesheet" href="/yly/Public/Static/bootstrap/css/bootstrap.min.css">
<link rel="stylesheet" href="/yly/Public/Admin/theme/simpleboot/simpleboot.css">
<link rel="stylesheet" href="/yly/Public/Static/font-awesome/4.4.0/css/font-awesome.min.css">
<link rel="stylesheet" href="/yly/Public/Admin/css/default.css">

<style>
/*-----------------导航hack--------------------*/
.nav-list>li.open{position: relative;}
.nav-list>li.open .back {display: none;}
.nav-list>li.open .normal {display: inline-block !important;}
.nav-list>li.open a {padding-left: 7px;}
.nav-list>li .submenu>li>a {background: #fff;}
.nav-list>li .submenu>li a>[class*="fa-"]:first-child{left:20px;}
.nav-list>li ul.submenu ul.submenu>li a>[class*="fa-"]:first-child{left:30px;}
/*----------------导航hack--------------------*/
</style>
<script>
    var GV = {
        DIMAUB: "",
        JS_ROOT: "/yly/Public/Static/",//js版本号
        TOKEN : ''	//token ajax全局
    };
</script> 

<?php function get_sub_menu($menus) { ?>
    <?php foreach($menus as $menu){ ?>
        <li>
            <?php $menu_name = $menu['name']; ?>
            <?php if (empty($menu['items'])) { ?>
                <a href="javascript:openapp('<?php echo ($menu["url"]); ?>','<?php echo ($menu["id"]); ?>','<?php echo ($menu_name); ?>',true);">
                    <i class="fa <?php echo ((isset($menu["icon"]) && ($menu["icon"] !== ""))?($menu["icon"]):'fa-desktop'); ?>"></i>
                    <span class="menu-text">
                        <?php echo ($menu_name); ?>
                    </span>
                </a>
            <?php } else { ?>
                <a href="#" class="dropdown-toggle">
                    <i class="fa <?php echo ((isset($menu["icon"]) && ($menu["icon"] !== ""))?($menu["icon"]):'fa-desktop'); ?> normal"></i>
                    <span class="menu-text normal">
                            <?php echo ($menu_name); ?>
                    </span>
                    <b class="arrow fa fa-angle-right normal"></b>
                    <i class="fa fa-reply back"></i>
                    <span class="menu-text back">返回</span>
                </a>

                <ul  class="submenu">
                    <?php echo get_sub_menu1($menu['items']);?>
                </ul>    
            <?php } ?>
        </li>
    <?php } ?>
<?php } ?>

<?php function get_sub_menu1($menus) { ?>
    <?php foreach($menus as $menu){ ?>
        <li>
            <?php $menu_name = $menu['name']; ?>
            <?php if(empty($menu['items'])){ ?>
                <a href="javascript:openapp('<?php echo ($menu["url"]); ?>','<?php echo ($menu["id"]); ?>','<?php echo ($menu_name); ?>',true);">
                    <i class="fa fa-caret-right"></i>
                    <span class="menu-text">
                            <?php echo ($menu_name); ?>
                    </span>
                </a>
            <?php }else{ ?>
                <a href="#" class="dropdown-toggle">
                    <i class="fa fa-caret-right"></i>
                    <span class="menu-text">
                            <?php echo ($menu_name); ?>
                    </span>
                    <b class="arrow fa fa-angle-right"></b>
                </a>
                    <ul  class="submenu">
                            <?php echo get_sub_menu2($menu['items']);?>
                    </ul>	
            <?php } ?>
        </li>
    <?php } ?>
<?php } ?>

<?php function get_sub_menu2($menus){ ?>
    <?php foreach($menus as $menu){ ?>
        <li>
            <?php $menu_name = $menu['name']; ?>

            <a href="javascript:openapp('<?php echo ($menu["url"]); ?>','<?php echo ($menu["id"]); ?>','<?php echo ($menu_name); ?>',true);">
                &nbsp;<i class="fa fa-angle-double-right"></i>
                <span class="menu-text">
                        <?php echo ($menu_name); ?>
                </span>
            </a>
        </li>
    <?php } ?>
<?php } ?>

</head>
<body style="min-width:900px;" screen_capture_injected="true">
    <div id="loading"><i class="loadingicon"></i></div>
    <div id="right_tools_wrapper">
        <span id="refresh_wrapper" title="<?php echo L('LOADING');?>" ><i class="fa fa-refresh right_tool_icon"></i></span>
    </div>
    
    <div class="navbar">
        <div class="navbar-inner">
            <div class="container-fluid">
                <a href="<?php echo U('index/index');?>" class="brand"> 
                    <small><img src="/yly/Public/Admin/img/logo-18.png">  <?php echo L('COMPANY_NAME');?></small>
                </a>
                
                <div class="pull-left nav_shortcuts" >		
                    <a class="btn btn-small btn-warning" href="/yly/" title="<?php echo L('HOME');?>" target="_blank">
                        <i class="fa fa-home"></i>
                    </a>
                </div>
                
                <ul class="nav simplewind-nav pull-right">
                    <li class="light-blue">
                        <a data-toggle="dropdown" href="#" class="dropdown-toggle">
                            <?php if($admin['avatar']): ?><img class="nav-user-photo" width="30" height="30" src="<?php echo sp_get_user_avatar_url($admin['avatar']);?>" alt="<?php echo ($admin["user_login"]); ?>">
                            <?php else: ?>
                                    <img class="nav-user-photo" width="30" height="30" src="/yly/Public/Admin/img/logo-18.png" alt="<?php echo ($admin["user_login"]); ?>"><?php endif; ?>
                            <span class="user-info">
                                欢迎, 管理员
                            </span>
                            <i class="fa fa-caret-down"></i>
                        </a>
                        <ul class="user-menu pull-right dropdown-menu dropdown-yellow dropdown-caret dropdown-closer">
   
                            <li><a href="<?php echo U('Login/out');?>"><i class="fa fa-sign-out"></i> <?php echo L('LOGOUT');?></a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>  
    </div>
    <div class="main-container container-fluid" >
        <div class="sidebar" id="sidebar">  
            <div id="nav_wraper">
                <ul class="nav nav-list"><?php echo get_sub_menu($admin_menu);?></ul>
            </div>    
        </div>
        <div class="dislpayArrow"><a class="pngfix" href="javascript:void(0);" onClick="displaysidebar(this)"></a></div>
        <div class="main-content">
            <div class="breadcrumbs" id="breadcrumbs">
                    <a id="task-pre" class="task-changebt" href="javascript:void(0)">←</a>
                    <div id="task-content">
                        <ul class="macro-component-tab" id="task-content-inner">
                            <li class="macro-component-tabitem noclose" app-id="0" app-url="<?php echo U('Main/index');?>" app-name="首页">
                                <span class="macro-tabs-item-text"><?php echo L('HOME');?></span>
                            </li>
                        </ul>
                        <div style="clear:both;"></div>
                    </div>
                    <a id="task-next" class="task-changebt" href="javascript:void(0)">→</a>
            </div>

            <div class="page-content" id="content">
                    <iframe src="<?php echo U('Main/index');?>" style="width:100%;height: 100%;" frameborder="0" id="appiframe-0" class="appiframe"></iframe>
            </div>
        </div>
    </div>
    <script src="/yly/Public/Static/jquery.js"></script>
    <script src="/yly/Public/Static/bootstrap/js/bootstrap.min.js"></script>
    <script>
        $(".nav-list").on( "click",function(event) {
            var closest_a = $(event.target).closest("a");
            if (!closest_a || closest_a.length == 0) {
                    return
            }
            
            if (!closest_a.hasClass("dropdown-toggle")) {
                return;
            }
            
            var closest_a_next = closest_a.next().get(0);
            if (!$(closest_a_next).is(":visible")) {
                var closest_ul = $(closest_a_next.parentNode).closest("ul");
                
                closest_ul.find("> .open > .submenu").each(function() {
                    if (this != closest_a_next && !$(this.parentNode).hasClass("active")) {
                            $(this).slideUp(150).parent().removeClass("open")
                    }
                });
            }
            
            $(closest_a_next).slideToggle(150).parent().toggleClass("open");
        });    
    </script>
    <script src="/yly/Public/Admin/js/index.js"></script>
</body>
</html>