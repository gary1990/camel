<!--{extends file='defaultPage.tpl'}-->
<!--{block name=title}-->
<title>{$title}</title>
<!--{/block}-->
<!--{block name=style}-->
<link rel="stylesheet" type="text/css" href="{base_url()}resource/css/ui.datepicker.css" />
<style>
	.top_title
	{
		width:900px;
		font-weight:bold;
		text-align:center;
	}
	.teststation,.producttype
	{
		width:150px;
	}
	.vna
	{
		border: 1px solid #DDDDDD;
		width: 75px;
		height: 20px;
		margin-top: 10px;
		text-align: center;
		background-color:#0066CC;
		color:#000000;
		font-weight:900;
	}
	.table
	{
		border-collapse: collapse;
	}
	th,td
	{
		border-top:1px solid #DDDDDD;
	}
</style>
<!--{/block}-->
<!--{block name=script}-->
<script type="text/javascript" src="{base_url()}resource/js/calendar/ui.datepicker.js"></script>
<script type="text/javascript" src="{base_url()}resource/js/calendar/ui.datepicker-zh-CN.js"></script>
<script type="text/javascript">
	$(document).ready(function(){
		$(".locPage > a").click(function(e){
			e.preventDefault();
			var url = $("#locForm").attr('action') + $(this).attr('href');
			$("#locForm").attr('action', url);
			$("#locForm").submit();
		});
	});
	
	jQuery(function($)
	{
		$('#date').datepicker({
			yearRange: '1900:2999',
			showOn: 'both',
			buttonImage: '{base_url()}resource/img/calendar.gif',
			buttonImageOnly: true,
			showButtonPanel: true
		});
	});
</script>
<!--{/block}-->
<!--{block name=body}-->
<div class="span-64 last subitems">
	<div class="prepend-1 top_title">
		<h3>待放行产品记录</h3>
	</div>
	<div>
		<div>筛选条件</div>
		<div>
			<form id="locForm" method="post" action="{site_url('qualitypass/index')}">
				<div style="margin-bottom:5px;">
					<div style="margin-top:3px;margin-right: 50px;float:left;">
						日期：<input id="date" name="date" value="{$smarty.post.date|default:''}" type="text"/>
					</div>
					<div style="margin-right: 50px;float:left;">
						测试站：
						{html_options class=teststation name=teststation options=$teststation selected=$smarty.post.teststation|default:''}
					</div>
					<div style="margin-right: 50px;">
						产品型号：
						{html_options class=producttype name=producttype options=$producttype selected=$smarty.post.producttype|default:''}
					</div>
				</div>
				<div style="text-align:right;margin-right:23%;">
					<input type="submit" value="查询"/>
				</div>
			</form>
		</div>
	</div>
	<div>
		<div class="vna">VNA</div>
		<div>
			<form method="post" action="{site_url('qualitypass/savequalitypass')}">
				<table>
					<tr>
						<th>序号</th>
						<th>时间</th>
						<th>测试站</th>
						<th>产品型号</th>
						<th>序列号</th>
						<th>测试结果</th>
						<th>&nbsp;</th>
						<th style="border-left:1px solid #DDDDDD;">转为合格</th>
						<th>责任人</th>
						<th>备注</th>
					</tr>
					{counter start=$totalcount+1 skip=-1 name=count print=false}
					{counter start=0 skip=1 name=recordnum print=false}
					{foreach from=$qualitypassArr item=value}
						<tr style="background:white">
							<td>{counter name=count}</td>
							<td>{$value['testTime']|default:''}</td>
							<td>{$value['teststaion']|default:''}</td>
							<td>{$value['producttype']|default:''}</td>
							<td>{$value['sn']|default:''}</td>
							<td>
								{if $value['result'] eq 1}
									<span style="color:green;">合格</span>
								{else}
									<span style="color:red;">不合格</span>
								{/if}
							</td>
							<td>
								<a href="{site_url('/packing/detail_vna')}/{$value['id']}" target="_blank">详情</a>
							</td>
							<td style="border-left: 1px solid #DDDDDD;">
								<input name="id{counter name=recordnum}" type="checkbox" value="{$value['id']}"/>
							</td>
							<td>
								{$CI->session->userdata('username')}
							</td>
							<td>
								<input name="remark{$value['id']}" type="text"/>
							</td>
						</tr>
					{/foreach}
				</table>
				<input type="hidden" name="totalrecord" value="{count($qualitypassArr)}"/>
				{$CI->pagination->create_links()}
				<div style="text-align:right;">
					<input type="submit" value="保存"/>
				</div>
			</form>
		</div>
	</div>
</div>
<!--{/block}-->