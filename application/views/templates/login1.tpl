<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
		<link rel="stylesheet" href="{base_url()}resource/css/screen.css" type="text/css" media="screen, projection"/>
		<link rel="stylesheet" href="{base_url()}resource/css/print.css" type="text/css" media="print"/>
		<!--[if lt IE 8]><link rel="stylesheet" href="{base_url()}/resource/css/ie.css" type="text/css" media="screen, projection"/><![endif]-->
		<link rel="stylesheet" href="{base_url()}resource/css/user.css" type="text/css" media="screen, projection"/>
		<script src="{base_url()}resource/js/jquery.js" type="text/javascript"></script>
		
		<!-- validationEngine -->
		<link rel="stylesheet" href="{base_url()}resource/css/template.css" type="text/css" media="screen, projection"/>
		<link rel="stylesheet" href="{base_url()}resource/css/validationEngine.jquery.css" type="text/css" media="screen, projection"/>
		<script src="{base_url()}resource/js/jquery.validationEngine.js" type="text/javascript"></script>
		<script src="{base_url()}resource/js/jquery.validationEngine-zh_CN.js" type="text/javascript"></script>

		<title>登录</title>
		<style>
			.logo_appName{
				margin: 0 auto;
				padding-top:130px;
				width: 390px;
			}
			.appName {
			    color: #767676;
			    display: inline-block;
			    font-size: 23px;
			    font-weight: bold;
			    line-height: 83px;
			    vertical-align: top;
			    font-family: Arial;
			}
			img.logo{
				width:100px;
				height:80px;
			}
			.locBlue{
				margin: 0 auto;
				width: 390px;
				padding-top:10px;
				padding-left:8px;
			}
			.formItem {
			    color: #767676;
			    font-size: 13px;
			    padding-top: 15px;
			}
			.formItem label {
			    color: #767676;
			    display: inline;
			    float: left;
			    font-size: 13px;
			    line-height: 28px;
			    top: 10px;
			    width: 85px;
			}
			.label1{
				font-family:Arial;
				font-size:16px;
			}
			.input1{
				height:28px;
				width:226px;
			}
			.button1{
				cursor:pointer;
				color: white;
				background-color:#001429;
				border: medium none;
				border-radius: 0 5px 0 5px;
			    font-size: 15px;
			    margin-left: 70px;
			    outline: medium none;
			    padding: 7px 100px;
			    width:226px;
			}
			.inline{
				margin-right:20px;
			}
			.span-21{
				margin-top:100px;
				text-align:center;
			}
			.error1{
				font-size:13px;
			}
			.body-div{
				position: relative;
				width:100%;
				height:245px;
				background: #E8EAE9;
			}
			.foot-div{
				text-align:right;
				margin-top:125px;
			}
		</style>
		<script>
			$(document).ready(function()
			{
				$(".locDefaultStr").click(function()
				{
					$(this).prev(".locDefaultStrContainer").focus();
				});
				$(".locDefaultStrContainer").focus(function()
				{
					$(this).next(".locDefaultStr").hide();
				});
				$(".locDefaultStrContainer").blur(function()
				{
					if ($(this).val() == "")
					{
						$(this).next(".locDefaultStr").show();
					}
				});
				$(".locDefaultStrContainer").blur();
				$("#locLoginForm").validationEngine('attach',
				{
					promptPosition : "centerRight",
					autoPositionUpdate : "true"
				});
			});
			function checkUserName(field, rules, i, options)
			{
				var err = new Array();
				var reg1 = /^[_\.].*/;
				var reg2 = /.*[_\.]$/;
				var str = field.val();
				if (reg1.test(str) || reg2.test(str))
				{
					err.push('* 不能以下划线或点开始或结束！');
				}
				if ((countOccurrences(str, '.') + countOccurrences(str, '_')) > 1)
				{
					err.push('* 一个用户名仅允许包含一个下划线或一个点！');
				}
				if (err.length > 0)
				{
					return err.join("<br>");
				}
			}
		
			function countOccurrences(str, character)
			{
				var i = 0;
				var count = 0;
				for ( i = 0; i < str.length; i++)
				{
					if (str.charAt(i) == character)
					{
						count++;
					}
				}
				return count;
			}
		</script>
	</head>
	<body>
		<div class="logo_appName head-div">
			<img class="logo" src="{base_url()}resource/img/gemcycle.png"/>
			<div class="appName">Camel Production System</div>
		</div>
		<div class="body-div">
			<form id="locLoginForm" action="{site_url('login/validateLogin')}" method="post">
				<div class="locBlue">
					<div class="formItem clear span-30 inline">
						<label for="username">用户名</label>
						<div class="relative">
							<input id="userName" name="userName" class="locInputYellow locDefaultStrContainer input1 validate[required, custom[onlyLetterNumber], minSize[6]]" value="{$smarty.post.userName|default:''}" type="text" />
							<div class="locDefaultStr defaultStr1 locUserNameDefaultStr">
								请输入用户名
							</div>
						</div>
					</div>
					<div class="formItem clear span-30 inline append-bottom20">
						<label for="username">密码</label>
						<div class="relative">
							<input id="password" name="password" class="locInputYellow locDefaultStrContainer input1 validate[required, custom[onlyLetterNumber], minSize[6], maxSize[20]]" type="password" />
							<div class="locDefaultStr defaultStr1 locUserNameDefaultStr">
								请输入密码
							</div>
						</div>
					</div>
					<div class="clear prepend-1">
						<div class="inline span-5">
							<input id="loginButton" class="button1" type="submit" value="登录"/>
						</div>
						<div class="span-10 locGeneralErrorInfo">
							<span class="error1">{$loginErrorInfo|default:''}</span>
						</div>
					</div>
				</div>
			</form>
		</div>
		<div class="foot-div">
			<div class="clear">
				<span style="color: #767676;">Camel Production System 5.0,Powered by Gemcycle</span>
			</div>
		</div>
	</body>
</html>
