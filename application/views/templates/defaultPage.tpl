<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<!--{$commonHead}-->
		<!--{block name=include}-->
		<!--{$jqueryHead}-->
		<!--{$validationEngineHead}-->
		<!--{/block}-->
		<!--{block name=title}-->
		<!--{/block}-->
		<style>
			.span-8,.span-40,.span-4,.span-2{
				margin-top:8px;			
			}
			.defaulttitle{
				margin-bottom:30px;
			}
		</style>
		<!--{block name=style}-->
		
		<!--{/block}-->
		<script>
			$(document).ready(function(){
				$(".items").change(function(){
					var baseurl = $("#base_url").val();
					var item = $(this).val();
					switch(item)
					{
						case "VNA测试记录":
							window.location.href = baseurl+"index.php/vna_pim/vna";
							break;
						case "PIM测试记录":
							window.location.href = baseurl+"index.php/vna_pim/pim";
							break;
						case "包装记录":
							window.location.href = baseurl+"index.php/packing";
							break;
						case "测试方案":
							window.location.href = baseurl+"index.php/producttestcase";
							break;
						case "产品型号":
							window.location.href = baseurl+"index.php/firstPage/producttype";
							break;
						case "测试项":
							window.location.href = baseurl+"index.php/firstPage/testitem";
							break;
						case "测试站点":
							window.location.href = baseurl+"index.php/firstPage/teststation";
							break;
						case "测试设备":
							window.location.href = baseurl+"index.php/firstPage/equipment";
							break;
						case "测试员":
							window.location.href = baseurl+"index.php/firstPage/tester";
							break;
						case "高级查询":
							window.location.href = baseurl+"index.php/advancedsearch";
							break;
						case "报表":
							window.location.href = baseurl+"index.php/reportform";
							break;
						case "同轴质量放行记录表":
							window.location.href = baseurl+"index.php/qualitypassTongzhou";
							break;
						case "跳线质量放行记录表":
							window.location.href = baseurl+"index.php/qualitypass";
							break;
						case "同轴不合格质量统计表":
							window.location.href = baseurl+"index.php/qualityStat";
							break;
						case "同轴产品指标统计表":
							window.location.href = baseurl+"index.php/productQualityIndex";
							break;
						case "用户":
							window.location.href = baseurl+"index.php/firstPage/user";
							break;
						case "用户组":
							window.location.href = baseurl+"index.php/firstPage/team";
							break;
						case "工厂":
							window.location.href = baseurl+"index.php/firstPage/factory";
							break;
						case "车间":
							window.location.href = baseurl+"index.php/firstPage/department";
							break;
						case "质量损失费用比例":
							window.location.href = baseurl+"index.php/qualitylosspercent";
							break;
						case "测试项判断标准":
							window.location.href = baseurl+"index.php/testStandard";
							break;
						default:
							window.location.href = baseurl+"index.php/login/toIndex";
							break;
					}
				});
			});
		</script>
		<!--{block name=script}-->
			
		<!--{/block}-->
	</head>
	<body class="cldn">
		<div class="container">
			<div class="span-64 last defaulttitle">
				<span class="span-52" style="font-size: 26px;">{$producter}</span>
				<span style="display:inline-block;padding-top: 15px;">Camel Production System</span>
				<hr>
				<div class="span-8">
					{$CI->session->userdata('fullname')}，您好
				</div>
				<div class="span-39" style="margin-top: 8px;">
					{$CI->session->userdata('today')}，工作愉快
				</div>
				<div class="span-11">
					{html_options class=items name=items options=$items selected=$item|default:''}
				</div>
				<div class="span-4 last">
					<a href="{site_url('login/toIndex')}">导航页</a>
				</div>
				<div class="span-2 last">
					<a href="{site_url('login/logout')}">退出</a>
				</div>
				<input id="base_url" type="hidden" value="{base_url()}"/>
			</div>
			<div>&nbsp;</div>
			<!--{block name=body}-->
			<!--{/block}-->
		</div>
	</body>
</html>
