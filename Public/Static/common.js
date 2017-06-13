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
                                var isdialog = btn.data('isdialog') ? btn.data('isdialog') : btn.attr('isdialog');
                                
                                if (isdialog) {
                                    window.location.href = data.referer;
                                } else {
                                    window.parent.location.href = data.referer;
                                }
                                
                            }else{
                                window.location.href = data.referer;
                            }
                        } else {
                            if (data.state === 'success') {
                                if(parent.layer != null && parent.layer.getFrameIndex(window.name)){
                                    var isdialog = btn.data('isdialog') ? btn.data('isdialog') : btn.attr('isdialog');
                                    if (isdialog) {
                                        reloadPage(window);
                                    } else {
                                        reloadPage(window.parent);
                                    }
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

function myclose() {
	parent.layer.close(parent.layer.getFrameIndex(window.name));
}
function send_form(from_id, post_url, return_url, callback) {
    
    var btn = $(".ajax-submit");
    var text = btn.text();
    $(this).text( text +'中...').prop('disabled', true).addClass('disabled');
    check_form(from_id, function (ret) {
        $('span').remove('.tips_error');
        if (ret.status) {
            if ($("#ajax").val() == 1) {
                var vars = $("#" + from_id).serialize();
                $.ajax({
                    type: "POST",
                    url: post_url,
                    data: vars,
                    dataType: "json",
                    success: function (data) {
                        if (typeof (callback) == 'function') {
                            callback(data);
                        } else {
                            if (data.status == 1) {
                                $('<span class="tips_success">' + data.info + '</span>').appendTo(btn.parent()).fadeIn('slow').delay(1000).fadeOut(function () {
                                if (data.referer) {
                                    //返回带跳转地址
                                    if(parent.layer != null && parent.layer.getFrameIndex(window.name)){
                                        //iframe弹出页
                                        var isdialog = btn.data('isdialog') ? btn.data('isdialog') : btn.attr('isdialog');

                                        if (isdialog) {
                                            window.location.href = data.referer;
                                        } else {
                                            window.parent.location.href = data.referer;
                                        }

                                    }else{
                                        window.location.href = data.referer;
                                    }
                                } else {
                                        if(parent.layer != null && parent.layer.getFrameIndex(window.name)){
                                            var isdialog = btn.data('isdialog') ? btn.data('isdialog') : btn.attr('isdialog');
                                            if (isdialog) {
                                                reloadPage(window);
                                            } else {
                                                reloadPage(window.parent);
                                            }
                                        }else{
                                            //刷新当前页
                                            reloadPage(window.parent);
                                        }
                                    }
                                });
                            } else {
                                $('<span class="tips_error">' + data.info + '</span>').appendTo(btn.parent()).fadeIn('fast');
                                $(this).text(text).prop('disabled', false).removeClass('disabled');
                                return false;
                            }
                        }
                    }
                });
            } else {
                //取消beforeunload事件
                if (return_url) {
                    set_return_url(return_url);
                }
                $(window).unbind('beforeunload', null);
                $("#" + from_id).attr("action", post_url);
                $("#" + from_id).submit();
            }
        } else {
            $('<span class="tips_error">' + ret.info + '</span>').appendTo(btn.parent()).fadeIn('fast');
            ret.dom.focus();
            return false;
        }
    });
}
    function send_ajax(url, vars, callback) {
                    return $.ajax({
                            type : "POST",
                            url : url,
                            data : vars + "&ajax=1",
                            dataType : "json",
                            success : callback
                    });
            }
    function check_form(form_id, callback) {
	var form = document.getElementById(form_id);
	if ( typeof (tinyMCE) != 'undefined') {
		tinyMCE.triggerSave(true);
	}

	var check_flag = true;
	for (var i = 0; i < form.elements.length; i++) {
		var el = form.elements[i];
		if ("INPUT" == el.tagName || "TEXTAREA" == el.tagName) {
			var check = get_attr(el, 'check');
			if (check != null) {
				if (!validate(el.value, check)) {
					var ret = {};
					ret.status = 0;
					ret.info = get_attr(el, 'info');
					ret.dom = el;
					callback(ret);
					return false;
				}
			}
		}
		if ("SELECT" == el.tagName) {
			var check = get_attr(el, 'check');
			if (check != null) {
				if (el.selectedIndex == 0 && el.value == '') {
					var ret = {};
					ret.status = 0;
					ret.info = get_attr(el, 'info');
					ret.dom = el;
					callback(ret);
					return false;
				}
			}
		}
	};

	last_submit = get_attr(form, 'last_submit');
	var now = new Date().getTime();
	if (last_submit == null) {
		set_attr(form, 'last_submit', now);
	} else {
		if (now - last_submit > 5000) {
			set_attr(form, 'last_submit', now);
			last_submit = get_attr(form, 'last_submit');
			console.log(last_submit);
		} else {
			return false;
		}
	};
	var ret = {};
	ret.status = 1;
	ret.info = '通过验证';
	callback(ret);
}

/* 验证数据类型*/
function validate(data, datatype) {
            var typeArr = [];
        if (datatype.indexOf(",")) {
            typeArr = datatype.split(",");
	} else {
            typeArr[0] = datatype;
        }
        for(var i=0;i<typeArr.length;i++) {
            if (typeArr[i].indexOf("|")) {
		tmp = datatype.split("|");
		datatype = tmp[0];
		data2 = tmp[1];
            }
            switch (datatype) {
                    case "required":
                            if (data == "") {
                                return false;
                            }
                            break;
                    case "number":
                            var reg = /^[0-9]+\.{0,1}[0-9]{0,3}$/;
                            if(data != "" && reg.test(data) === false){
                                return false;
                            }
                            break;
                    case "eqt":
                            if(data != "" && data >= data2){
                                return false;
                            }
                            break;
                    case "phone":
                            var reg = /^1\d{10}$/;
                            if(data != "" && reg.test(data) === false){
                                return false;
                            }
                            break;
                    case "cardno":
                            var reg = /(^\d{15}$)|(^\d{18}$)|(^\d{17}(\d|X|x)$)/;
                            if(data != "" && reg.test(data) === false){
                                return false;
                            }
                            break;    
            }
        }
    return true;
}
function set_cookie(key, value, exp, path, domain, secure) {
	key = cookie_prefix + key;
	path = "/";
	var cookie_string = key + "=" + escape(value);
	if (exp) {
		cookie_string += "; expires=" + exp.toGMTString();
	}
	if (path)
		cookie_string += "; path=" + escape(path);
	if (domain)
		cookie_string += "; domain=" + escape(domain);
	if (secure)
		cookie_string += "; secure";
	document.cookie = cookie_string;
}

/*读取 cookie*/
function get_cookie(cookie_name) {
	cookie_name = cookie_prefix + cookie_name;
	var results = document.cookie.match('(^|;) ?' + cookie_name + '=([^;]*)(;|$)');
	if (results)
		return (unescape(results[2]));
	else
		return null;
}

/*设置要返回的URL*/
function set_return_url(url) {
	var return_url = get_cookie('return_url');
	if (return_url == null || url === null) {
		arr_return_url = [];
	} else {
		arr_return_url = return_url.split('$');
	}
	if (url == undefined || url == null) {
		url = document.location.pathname + location.search;
	}
	if (arr_return_url.slice(-1) != url) {
		arr_return_url.push(url);
	}
	console.log(arr_return_url);
	set_cookie("return_url", arr_return_url.join('$'));
}


/*返回到上一页*/
function go_return_url() {
	var return_url = get_cookie('return_url');
	if (return_url == null) {
		return false;
	} else {
		arr_return_url = return_url.split('$');
	}

	go_url = arr_return_url.pop();
	if (go_url == document.location.pathname + location.search) {
		go_url = arr_return_url.pop();
	}
	console.log(go_url);
	set_cookie("return_url", arr_return_url.join('$'));
	if (go_url != undefined) {
		location.href = go_url;
	}
	return false;
}

/* 删除左右两端的空格*/
function trim(str) {
	return str.replace(/(^\s*)|(\s*$)/g, "");
}

function get_attr(dom, attr) {
	if ( typeof (dom) == 'object') {
		var d = dom;
	}
	if ( typeof (dom) == 'string') {
		var d = document.getElementById(dom);
	}
	//获取该节点
	if ((d !== null) && (undefined !== d.attributes[attr])) {
		return d.attributes[attr].value;
	}

	//获取该原生属性的值。
	return null;
}

function set_attr(dom, attr, val) {
	if ( typeof (dom) == 'object') {
		var d = dom;
	}
	if ( typeof (dom) == 'string') {
		var d = document.getElementById(dom);
	}
	var node = document.createAttribute(attr);
	node.nodeValue = val;
	d.attributes.setNamedItem(node);
}

/*切割公司及部门 */
function get_dept_org_id(res)
{
    var result = [];
    var arr = res.split('|');
    for(var i=0;i<arr.length;i++){
        var obj = arr[i].split('_');
        result[obj[0]] = obj[1];
    }
    return result;
}