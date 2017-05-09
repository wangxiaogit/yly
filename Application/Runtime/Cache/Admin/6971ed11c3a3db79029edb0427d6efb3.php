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
    <div class="wrap">
        <ul class="nav nav-tabs">
            <li><a href="<?php echo U('FeeStandard/index');?>">费用标准</a></li>
            <li><a href="<?php echo U('FeeStandard/add');?>">新增</a></li>
            <li><a href="<?php echo U('FeeStandard/edit', array('id'=>$id));?>">编辑</a></li>
        </ul>
        <form method="post" class="form-horizontal js-ajax-form" action="<?php echo U('FeeStandard/do_edit');?>">
            <fieldset>
                <div class="control-group">
                    <label class="control-label">费用名称</label>
                    <div class="controls">
                        <input type="text" name="fee_name" value="<?php echo ($feeInfo['fee_name']); ?>">
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label">费用类型</label>
                    <div class="controls">
                        <select name='fee_type'>
                            <?php if(is_array($feeType)): foreach($feeType as $k=>$vo): ?><option value='<?php echo ($k); ?>'<?php if($k == $feeInfo['fee_type']): ?>selected<?php endif; ?>><?php echo ($vo); ?></option><?php endforeach; endif; ?>
                        </select>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label">费用标准</label>
                    <div class="controls">
                        <input type="text" name="fee" value="<?php echo ($feeInfo['fee']); ?>">
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label">是否允许选择</label>
                    <div class="controls">
                        <label class="radio inline" for="allow_select_true"><input type="radio" name="is_allow_select" value="1" id="allow_select_true" <?php if(1 == $feeInfo['is_allow_select']): ?>checked<?php endif; ?> />允许</label>
                        <label class="radio inline" for="allow_select_false"><input type="radio" name="is_allow_select" value="0" id="allow_select_false" <?php if(0 == $feeInfo['is_allow_select']): ?>checked<?php endif; ?>/>不允许</label>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label">是否自定义费用</label>
                    <div class="controls">
                        <label class="radio inline" for="is_custom_false"><input type="radio" name="is_custom" value="1" id="is_custom_false" <?php if(1 == $feeInfo['is_custom']): ?>checked<?php endif; ?>>是</label>
                        <label class="radio inline" for="is_custom_true"><input type="radio" name="is_custom" value="0"  id="is_custom_true"  <?php if(0 == $feeInfo['is_custom']): ?>checked<?php endif; ?>/>否</label>  
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label">是否有效</label>
                    <div class="controls">
                        <label class="radio inline" for="isvalid_true"><input type="radio" name="isvalid" value="1"  id="isvalid_true"  <?php if(1 == $feeInfo['isvalid']): ?>checked<?php endif; ?>/>有效</label>
                        <label class="radio inline" for="isvalid_false"><input type="radio" name="isvalid" value="0" id="isvalid_false" <?php if(0 == $feeInfo['isvalid']): ?>checked<?php endif; ?>>无效</label>
                    </div>
                </div>
            </fieldset>
            <div class="form-actions">
                <input type="hidden" name="id" value="<?php echo ($id); ?>" />
                <button type="submit" class="btn btn-primary js-ajax-submit">提交</button>
                <a class="btn" href="<?php echo U('FeeStandard/index');?>">后退</a>
            </div>
        </form>
    </div>
<script src="/yly/Public/Static/common.js"></script>
</body>
</html>