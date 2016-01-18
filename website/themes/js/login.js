$(document).ready(function() {
	$('#dologin').click(function() {
		var username = $('#login_username').val();
		var password = $('#login_pwd').val();
		var baseUrl = $('#baseUrl').val();
		if (!$.trim(username)) {
			alert('请输入登录名');
			return;
		}
		if (!$.trim(password)) {
			alert('请输入密码');
			return;
		}

		$.post(baseUrl + "/admin.php/site/login", {
			"username" : username,
			"password" : password
		}, function(result) {
			var obj = jQuery.parseJSON(result);
			if (obj.status == 1) {
				// if ($("#remember_me").is(':checked')) {
				// $.cookie("rememberme", "true", { expires: 30 });
				// $.cookie("email", email, { expires: 30 });
				// $.cookie("password", password, { expires: 30 });
				// }else {
				// $.cookie("rememberme", "false", { expire: -1 });
				// $.cookie("email", "", { expires: -1 });
				// $.cookie("password", "", { expires: -1 });
				// }
				// window.location.href='<?php echo
				// Yii::app()->createUrl('/site/index');?>';
				location.reload();
			} else {
				var message = obj.message;
				if (message) {
					alert(message);
				} else {
					alert('登陆失败');
				}
			}
		});
	});

	// enter事件
	$('#login_username').bind('keydown', function(e) {
		var key = e.which;
		if (key == 13) {
			e.preventDefault();
			$('#dologin').trigger('click');
		}
	});

	$('#login_pwd').bind('keydown', function(e) {
		var key = e.which;
		if (key == 13) {
			e.preventDefault();
			$('#dologin').trigger('click');
		}
	});
});