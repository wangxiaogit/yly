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
            <li><a href="<?php echo U('RequiredDocument/index');?>">收件资料列表</a></li>
            <li><a href="<?php echo U('RequiredDocument/add');?>">新增</a></li>
            <li><a href="<?php echo U('RequiredDocument/edit', array('id'=>$id));?>">编辑</a></li>
        </ul>
        <form method="post" class="form-horizontal js-ajax-form" action="<?php echo U('RequiredDocument/do_edit');?>">
            <fieldset>
                <div class="control-group">
                    <label class="control-label">资料名称</label>
                    <div class="controls">
                        <input type="text" name="document_name" value="<?php echo ($documentInfo['document_name']); ?>">
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label">资料类型</label>
                    <div class="controls">
                        <select name='document_type'>
                            <?php if(is_array($documentType)): foreach($documentType as $k=>$vo): ?><option value='<?php echo ($k); ?>'<?php if($k == $documentInfo['document_type']): ?>selected<?php endif; ?>><?php echo ($vo); ?></option><?php endforeach; endif; ?>
                        </select>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label">是否有效</label>
                    <div class="controls">
                        <label class="radio inline" for="isvalid_true"><input type="radio" name="isvalid" value="1"  id="isvalid_true"  <?php if(1 == $documentInfo['isvalid']): ?>checked<?php endif; ?>/>有效</label>
                        <label class="radio inline" for="isvalid_false"><input type="radio" name="isvalid" value="0" id="isvalid_false" <?php if(0 == $documentInfo['isvalid']): ?>checked<?php endif; ?>>无效</label>
                    </div>
                </div>
            </fieldset>
            <div class="form-actions">
                <input type="hidden" name="id" value="<?php echo ($id); ?>" />
                <button type="submit" class="btn btn-primary js-ajax-submit">提交</button>
                <a class="btn" href="<?php echo U('RequiredDocument/index');?>">后退</a>
            </div>
        </form>
    </div>
<script src="/yly/Public/Static/common.js"></script>
</body>
</html>