<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<!--{$commonHead}-->
		<!--{block name=include}-->
		<!--{$jqueryHead}-->
		<!--{$validationEngineHead}-->
		<!--{/block}-->
		<!--{block name=title}-->
		<title></title>
		<!--{/block}-->
		<!--{block name=style}-->
		<style>
		</style>
		<!--{/block}-->
		<!--{block name=script}-->
		<script>
		</script>
		<!--{/block}-->
	</head>
	<body class="cldn">
		<div class="container">
			<div class="span-64 last">
				<img class="logo span-6" src="{base_url()}resource/img/gc.png"/>
				<span class="cldnH2 prepend-1 span-41">CAMEL精益生产管理系统-测试管理</span>
				<div class="span-8">
					欢迎您:{$CI->session->userdata('username')}
				</div>
				<div class="span-2 last">
					<a href="{site_url('login/logout')}">退出</a>
				</div>
			</div>
			<!--{block name=body}-->
			<!--{/block}-->
		</div>
	</body>
</html>