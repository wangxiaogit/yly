<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title><?php echo ($meta_title); ?></title>
        <!-- Set render engine for 360 browser -->
	<meta name="renderer" content="webkit">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        
        <!-- HTML5 shim for IE8 support of HTML5 elements -->
        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <![endif]-->
        
        <link rel="stylesheet" href="/yly/Public/Static/bootstrap/css/bootstrap.min.css">
        <link href="/yly/Public/Admin/css/admin.css" rel="stylesheet">
        <link href="/yly/Public/Static/font-awesome/4.4.0/css/font-awesome.min.css"  rel="stylesheet" type="text/css">
        <link href="/yly/Public/Admin/css/default.css" rel="stylesheet">
        
        <!--[if IE 7]>
                <link rel="stylesheet" href="/yly/Public/Static/css/font-awesome-ie7.min.css">
        <![endif]-->
        
        <script>
            var GV = {
                DIMAUB: "",
                JS_ROOT: "/yly/Public/Static/",//js版本号
                TOKEN : ''	//token ajax全局
            };
        </script>  
        <script src="/yly/Public/Static/jquery.js"></script>	
        <script src="/yly/Public/Static/wind.js"></script>	
        <script src="/yly/Public/Static/bootstrap/js/bootstrap.min.js"></script>	
        <script src="/yly/Public/Static/layer/layer.js"></script>	
    

</head>
<body>
    <div class="wrap js-check-wrap">
        <ul class="nav nav-tabs">
            <li class="active"><a href="<?php echo U('FeeStandard/index');?>"><?php echo ($meta_title); ?></a></li>
            <li><a href="<?php echo U('FeeStandard/add');?>">增加</a></li>
        </ul>
        
        <table class="table table-hover table-bordered">
            <thead>
                <tr>
                    <th width="50">编号</th>
                    <th>费用名称</th>
                    <th>费用类型</th>
                    <th>收费标准</th>
                    <th>是否可以选择</th>
                    <th>是否自定义收费</th>
                    <th>状态</th>
                    <th width="120">操作</th>
                </tr>
            </thead>
            <tbody>
                <?php if(is_array($fees)): foreach($fees as $key=>$vo): ?><tr>
                    <td><?php echo ($vo["id"]); ?></td>
                    <td><?php echo ($vo["fee_name"]); ?></td>
                    <td><?php echo ($feeType[$vo['fee_type']]); ?></td>
                    <td><?php echo ($vo["fee"]); ?></td>
                    <td><?php echo ($vo['is_allow_select']?'是':'否'); ?></td>
                    <td><?php echo ($vo['is_custom']?'是':'否'); ?></td>
                    <td><?php echo ($vo['isvalid']?'有效':'失效'); ?></td>
                    <td>
                            <a href='<?php echo U("FeeStandard/edit",array("id"=>$vo["id"]));?>'>编辑</a>
                        </if>
                    </td>
                </tr><?php endforeach; endif; ?>
            </tbody>
        </table>
        <?php echo ($_Page); ?>
    </div>
<script src="/yly/Public/Static/common.js"></script>
</body>
</html>