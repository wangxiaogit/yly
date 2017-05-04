;(function () {
    //全局ajax处理
    $.ajaxSetup({
        complete: function (jqXHR) {},
        data: {},
        error: function (jqXHR, textStatus, errorThrown) {
            //请求失败处理
        }
    });

    if ($.browser.msie) {
        //ie 都不缓存
        $.ajaxSetup({
            cache: false
        });
    }
    
    //全局layer配置
    layer.config({extend :['skin/moon/style.css'],skin: 'layer-ext-moon'});
    
    //不支持placeholder浏览器下对placeholder进行处理
    if (document.createElement('input').placeholder !== '') {
        $('[placeholder]').focus(function () {
            var input = $(this);
            if (input.val() == input.attr('placeholder')) {
                input.val('');
                input.removeClass('placeholder');
            }
        }).blur(function () {
            var input = $(this);
            if (input.val() == '' || input.val() == input.attr('placeholder')) {
                input.addClass('placeholder');
                input.val(input.attr('placeholder'));
            }
        }).blur().parents('form').submit(function () {
            $(this).find('[placeholder]').each(function () {
                var input = $(this);
                if (input.val() == input.attr('placeholder')) {
                    input.val('');
                }
            });
        });
    }
    
    //ajax form提交,由于大多业务逻辑都是一样的，故统一处理
    var ajaxForm_list = $('form.js-ajax-form');
    if (ajaxForm_list.length) {
        Wind.use('ajaxForm', function() {
           if ($.browser.msie) {
                //ie8及以下，表单中只有一个可见的input:text时，会整个页面会跳转提交
                ajaxForm_list.on('submit', function (e) {
                    //表单中只有一个可见的input:text时，enter提交无效
                    e.preventDefault();
                });
            }
            
            $('button.js-ajax-submit').on('click', function (e) {
                e.preventDefault();
                
                var btn = $(this),
                    form = btn.parents('form.js-ajax-form');
                
                if(btn.data("loading")) {
                    return;
            	}
                
                //ie处理placeholder提交问题
                if ($.browser.msie) {
                    form.find('[placeholder]').each(function () {
                        var input = $(this);
                        if (input.val() == input.attr('placeholder')) {
                            input.val('');
                        }
                    });
                }
                
                form.ajaxSubmit({
                    url: btn.data('action') ? btn.data('action') : form.attr('action'), //按钮上是否自定义提交地址(多按钮情况)
                    dataType: 'json',
                    beforeSubmit: function (arr, $form, options) {
                    	
                    	btn.data("loading",true);
                        var text = btn.text();

                        //按钮文案、状态修改
                        btn.text(text + '中...').prop('disabled', true).addClass('disabled');
                    },
                    success: function (data, statusText, xhr, $form) {
                        //按钮文案、状态修改
                        var text = btn.text();
                        btn.removeClass('disabled').text(text.replace('中...', '')).parent().find('span').remove();
                        if (data.state === 'success') {
                            $('<span class="tips_success">' + data.info + '</span>').appendTo(btn.parent()).fadeIn('slow').delay(1000).fadeOut(function () {
                            });
                        } else if (data.state === 'fail') {
                        	var $verify_img=form.find(".verify_img");
                        	if($verify_img.length) {
                                    $verify_img.attr("src",$verify_img.attr("src")+"&refresh="+Math.random()); 
                        	}
                        	
                        	var $verify_input=form.find("[name='verify']");
                        	$verify_input.val("");
                        	
                            $('<span class="tips_error">' + data.info + '</span>').appendTo(btn.parent()).fadeIn('fast');
                            btn.removeProp('disabled').removeClass('disabled');
                        }
                        
                        if (data.referer) {
                            //返回带跳转地址
                            if(parent.layer != null && parent.layer.getFrameIndex(window.name)){
                                //iframe弹出页
                                window.parent.location.href = data.referer;
                            }else{
                                window.location.href = data.referer;
                            }
                        } else {
                            if (data.state === 'success') {
                                if(parent.layer != null && parent.layer.getFrameIndex(window.name)){
                                    reloadPage(window.parent);
                                }else{
                                    //刷新当前页
                                    reloadPage(window);
                                }
                            }
                        }
                        
                    },
                    complete: function(){
                    	btn.data("loading",false);
                    }
                });
            });
        });
    }
    
    //删除操作
    if ($("a.js-ajax-delete").length) {
        $('.js-ajax-delete').on('click', function (e) {
            e.preventDefault();
            var $_this = this, $this = $($_this), href = $this.prop('href');
            layer.confirm("确定要删除吗？", {icon: 3, title:'提示'}, function(index) {
                $.getJSON(href).done(function (data) {
                    if (data.state === 'success') {
                        layer.msg(data.info, {icon: 1, time: 1000}, function(index) {
                            if (data.referer) {
                                location.href = data.referer;
                            } else {
                                reloadPage(window);
                            }
                        });    
                    } else if (data.state === 'fail') {
                        layer.msg(data.info, {icon: 2, time: 1000});
                    }
                });
            });
        });    
    }
    
    //会话窗口
    if ($("a.js-ajax-dialog-btn").length) {
        $('.js-ajax-dialog-btn').on('click', function (e) {
            e.preventDefault();
            var $_this = this, $this = $($_this), 
            title = $this.prop('title') ? $this.prop('title') : $this.attr('title'), 
            url = $this.prop('href') ? $this.prop('href') : $this.attr('href');
            
            layer.open({
                type: 2,
                area: ['800px', ($(window).height() - 50) + 'px'],
                fix: false, //不固定
                maxmin: true,
                shade:0.4,
                title: title ? title : false,
                content: url
            });
        });
    }
    
    //日期选择器
    var dateInput = $("input.js-date")
    if (dateInput.length) {
        Wind.use('datePicker', function () {
            dateInput.datePicker();
        });
    }

    //日期+时间选择器
    var dateTimeInput = $("input.js-datetime");
    if (dateTimeInput.length) {
        Wind.use('datePicker', function () {
            dateTimeInput.datePicker({
                time: true
            });
        });
    }
    
    //tab
    var tabs_nav = $('ul.js-tabs-nav');
    if (tabs_nav.length) {
        Wind.use('tabs', function () {
            tabs_nav.tabs('.js-tabs-content > div');
        });
    }
    
    /*复选框全选(支持多个，纵横双控全选)。
     *实例：版块编辑-权限相关（双控），验证机制-验证策略（单控）
     *说明：
     *	"js-check"的"data-xid"对应其左侧"js-check-all"的"data-checklist"；
     *	"js-check"的"data-yid"对应其上方"js-check-all"的"data-checklist"；
     *	全选框的"data-direction"代表其控制的全选方向(x或y)；
     *	"js-check-wrap"同一块全选操作区域的父标签class，多个调用考虑
     */

    if ($('.js-check-wrap').length) {
        var total_check_all = $('input.js-check-all');

        //遍历所有全选框
        $.each(total_check_all, function () {
            var check_all = $(this),
                check_items;

            //分组各纵横项
            var check_all_direction = check_all.data('direction');
            check_items = $('input.js-check[data-' + check_all_direction + 'id="' + check_all.data('checklist') + '"]');
            
            //点击全选框
            check_all.change(function (e) {
                var check_wrap = check_all.parents('.js-check-wrap'); //当前操作区域所有复选框的父标签（重用考虑）

                if ($(this).attr('checked')) {
                    //全选状态
                    check_items.attr('checked', true);

                    //所有项都被选中
                    if (check_wrap.find('input.js-check').length === check_wrap.find('input.js-check:checked').length) {
                        check_wrap.find(total_check_all).attr('checked', true);
                    }

                } else {
                    //非全选状态
                    check_items.removeAttr('checked');
                    
                    check_wrap.find(total_check_all).removeAttr('checked');

                    //另一方向的全选框取消全选状态
                    var direction_invert = check_all_direction === 'x' ? 'y' : 'x';
                    check_wrap.find($('input.js-check-all[data-direction="' + direction_invert + '"]')).removeAttr('checked');
                }

            });

            //点击非全选时判断是否全部勾选
            check_items.change(function () {
                if ($(this).attr('checked')) {
                    if (check_items.filter(':checked').length === check_items.length) {
                        //已选择和未选择的复选框数相等
                        check_all.attr('checked', true);
                    }
                } else {
                    check_all.removeAttr('checked');
                }
            });
        });
    }
})();

function reloadPage(win) {
    var redirect = win.location;
    redirect.href = redirect.pathname + redirect.search;   
}


