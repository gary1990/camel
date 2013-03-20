<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
		<title>PIM测试详情</title>
		<style type="text/css">
			body{
				border:0;
				margin:0;
			}
			.span-20{
				font-size:20px;
				font-weight: bold;
				margin-top:10px;
				margin-bottom:30px;
			}
			.span-15{
				margin-bottom:5px;
			}
			.blue{
				color:blue;
				font-weight:bold;
			}
			hr{
				color:1px solid #CCCCCC;
			}
			table{
				margin-top:15px;
				margin-bottom:20px;
			}
			img{
				width:640px;
				height:480px;
			}
			.image{
				float:left;
			}
		</style>
	</head>
	<body>
		<div class="span-20"><span>质量追溯报告</span></div>
		<div>
			<span style='width:100px'>产品序列号：</span>{$pimDetailArray[0]['model']}
		</div>
		<div>
			<span style='width:100px'>型号：</span>{$pimDetailArray[0]['ser_num']}
		</div>
		<div >
			<span style='width:100px'>订单号：</span>{$pimDetailArray[0]['name']}
		</div>
		<hr/>
		<div  class="span-15 blue">
			<span>生产信息</span>
		</div>
		<table border="1px" cellspacing="0">
			<tr><th>测试项目</th><th>测试时间</th><th>测试设备型号</th><th>测试设备序列号</th><th>测试员</th><th>测试结果</th></tr>
			<tr><td>PIM</td><td>{$pimTesttime}</td><td>&nbsp</td><td>&nbsp</td><td>{$pimDetailArray[0]['work_num']}</td><td>&nbsp</td></tr>
		</table>
		<hr/>
		<div  class="span-15 blue">
			<span>PIM测试数据</span>
		</div>
		<table border="1px" cellspacing="0">
			<tr>
				{foreach from=$pimDetailArray key=key item=value}
					<th>数据{$key+1}</th>
				{/foreach}
			</tr>
			<tr>
				{foreach from=$pimDetailArray key=key item=value}
					<td>{$value['value']}</td>
				{/foreach}
			</tr>
		</table>
		<div>
			{foreach from=$pimDetailArray key=key item=value}
				<div class="image">
					<div>图{$key+1}</div>
					<img alt="服务器上可能无此图片" src="{base_url()}assets/uploadedSource/pim/{$value['upload_date']|regex_replace:"/[-]/":"_"}/{$value['name']}/{$value['ser_num']}_{$value['test_time']|regex_replace:'/[-:\s]/':''}.jpg"/>
				</div>
			{/foreach}
		</div>
	</body>
</html>