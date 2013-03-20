<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
		<title>VNA测试详情</title>
		<style type="text/css">
			body{
				margin:0;
				border:"0";
			}
			.span-20{
				font-size:20px;
				margin-top:10px;
				margin-bottom:30px;
				font-weight:bold;
			}
			.span-15{
				font-size:15px;
				width:100px;
			}
			img{
				width:640px;
				height:480px;
			}
			table{
				width:500px;
			}
			.info{
				margin-bottom:5px;
				margin-top:5px;
			}
			.item{
				font-weight:bold;
				color:blue;
				margin-top:10px;
				margin-bottom:10px;
			}
			.testitem{
				text-align:left;
				height:30px;
				width:200px;
			}
		</style>
	</head>
	<body>
		<div class="span-20"><span>质量追溯报告</span></div>
		<div class="info">
			<span style="width:100px;">产品序列号：</span>{$sn}
		</div>
		<div class="info">
			<span style="width:100px;">型号：</span>{$producttype}
		</div>
		<div class="info">
			<span class="span-15">订单号：</span>
		</div>
		<hr/>
		<div class="info"><span style='color:blue;font-weight:bold;'>生产信息</span></div>
		<div style="margin-bottom:10px">
		<table border="1px" cellspacing="0" style="width:600px;">
			<tr><th>测试项目</th><th>测试时间</th><th>测试设备型号</th><th>测试设备序列号</th><th>测试员</th><th>测试结果</th></tr>
			<tr><td>VNA</td><td>{$testtime}</td><td>{$teststationName}</td><td>{$equipmentSn}</td><td>{$tester}</td><td>{$testresult}</td></tr>
		</table>
		</div>
		<hr/>
		<div class="item">
			<span>VNA测试数据</span>
		</div>
		{foreach from=$result key=key item=value}
			<div class="testitem">
				<span>测试项：</span>{$value[0]}
			</div>
			<div style="float:left;margin-top:10px;margin-bottom:10px;">
				<img src="{base_url()}assets\uploadedSource\{$value[1]}"/>
			</div>
			<div style="margin-top:10px;margin-left:30px">
			<div style="height:485px;">
			<table border="1px" cellspacing="0">
				<tr><th>Freq</th><th>Value</th><th>Result</th></tr>
				{foreach from=$value[2] item=val}
					<tr><td>{$val[1]}</td><td>{$val[0]}</td><td>{$val[4]}</td></tr>
				{/foreach}
			</table>
			</div>
			
			</div>
		{/foreach}
	</body>
</html>