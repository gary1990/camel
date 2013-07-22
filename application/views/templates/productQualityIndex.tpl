<!--{extends file='defaultPage.tpl'}-->
<!--{block name=title}-->
<title>{$title}</title>
<!--{/block}-->
<!--{block name=style}-->
<link rel="stylesheet" type="text/css" href="{base_url()}resource/css/ui.datepicker.css" />
<link rel="stylesheet" type="text/css" href="{base_url()}resource/css/chosen.css" />
<style>
	.seprateline
	{
		height:5px;
		margin:1em 0 1em 0;
	}
	.top_title
	{
		width:900px;
		font-size:20px;
		font-weight:bold;
		text-align:center;
	}
	table
	{
		border-collapse: collapse;
	}
	th,td
	{
		border:1px solid #DDDDDD;
	}
	th,td{
		text-align:center;
	}
	/*
    table td
    {
        word-break: break-all;
        word-wrap: break-word;
    }
    */
</style>
<!--{/block}-->
<!--{block name=script}-->
<script type="text/javascript" src="{base_url()}resource/js/calendar/ui.datepicker.js"></script>
<script type="text/javascript" src="{base_url()}resource/js/calendar/ui.datepicker-zh-CN.js"></script>
<script type="text/javascript" src="{base_url()}resource/js/chosen.jquery.js"></script>
<script type="text/javascript">
	jQuery(function($)
	{	
		//分页 
		$(".locPage > a").click(function(e) {
			e.preventDefault();
			var url = "{site_url()}"+'/productQualityIndex/index'+ $(this).attr('href');
			$("#locForm").attr('action', url);
			$("#locForm").submit();
		});
		//查询
		$(".searchBtn").click(function(e) {
			e.preventDefault();
			var url = "{site_url()}"+'/productQualityIndex';
			$("#locForm").attr('action', url);
			$("#locForm").submit();
		});
		
		$('#startdate').datepicker({
			yearRange: '1900:2999',
			showOn: 'both',
			buttonImage: '{base_url()}resource/img/calendar.gif',
			buttonImageOnly: true,
			showButtonPanel: true
		});
		$('#enddate').datepicker({
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
		同轴产品指标统计表
	</div>
	<div>
		<form id="locForm" method="post" action="{site_url()}/productQualityIndex">
			<span>起始日期：</span>
			<input id="startdate" name="startdate" value="{$smarty.post.startdate|default:$startTime}" type="text"/>
			<span>截止日期：</span>
			<input id="enddate" name="enddate" value="{$smarty.post.enddate|default:$endTime}" type="text"/>
			<span>工序：</span>
			{html_options class=process name=process options=$processArr selected=$smarty.post.process|default:''}
			<span>产品型号：</span>
			{html_options class=produdttype name=produdttype options=$producttypeArr selected=$smarty.post.produdttype|default:''}
			<span>盘号：</span>
			{html_options class=platenum name=platenum options=$platenumArr selected=$smarty.post.platenum|default:''}
			<input class="searchBtn" type="submit" value="查询" />
		</form>
	</div>
	<hr class="seprateline">
	<div style="margin-bottom: 50px;">
		<div style="width: 960px; overflow: auto;">
			<table border="1" cellspacing="1" cellpadding="1">
				<tr>
					<th rowspan="2"><div class="th3">序列号</div></th>
					<th rowspan="2" colspan="3"><div class="th3">驻波1</div></th>
					<th rowspan="2" colspan="3"><div class="th3">驻波2</div></th>
					<th colspan="15"><div class="th2">衰减</div></th>
					<th rowspan="2"><div class="th2">阻抗</div></th>
				</tr>
				<tr>
					<th><div class="th2">100M</div></th>
					<th><div class="th2">150M</div></th>
					<th><div class="th2">200M</div></th>
					<th><div class="th2">280M</div></th>
					<th><div class="th2">450M</div></th>
					<th><div class="th2">800M</div></th>
					<th><div class="th2">900M</div></th>
					<th><div class="th1">1G</div></th>
					<th><div class="th2">1.5G</div></th>
					<th><div class="th2">1.8G</div></th>
					<th><div class="th1">2G</div></th>
					<th><div class="th2">2.2G</div></th>
					<th><div class="th2">2.4G</div></th>
					<th><div class="th2">2.5G</div></th>
					<th><div class="th1">3G</div></th>
				</tr>
				{foreach from=$resultArr item=value}
					<tr>
						<td>{$value['sn']|default:''}</td>
						<td>
							{if isset($value['zhubo11']['value'])}
								{$value['zhubo11']['value']|default:''}/{$value['zhubo11']['mark']|default:''}
							{else}
							{/if}
						</td>
						<td>
							{if isset($value['zhubo12']['value'])}
								{$value['zhubo12']['value']|default:''}/{$value['zhubo12']['mark']|default:''}
							{else}
							{/if}
						</td>
						<td>
							{if isset($value['zhubo13']['value'])}
								{$value['zhubo13']['value']|default:''}/{$value['zhubo13']['mark']|default:''}
							{else}
							{/if}
						</td>
						<td>
							{if isset($value['zhubo21']['value'])}
								{$value['zhubo21']['value']|default:''}/{$value['zhubo21']['mark']|default:''}
							{else}
							{/if}
						</td>
						<td>
							{if isset($value['zhubo22']['value'])}
								{$value['zhubo22']['value']|default:''}/{$value['zhubo22']['mark']|default:''}
							{else}
							{/if}
						</td>
						<td>
							{if isset($value['zhubo23']['value'])}
								{$value['zhubo23']['value']|default:''}/{$value['zhubo23']['mark']|default:''}
							{else}
							{/if}
						</td>
						<td>{$value['shuaijian100']|default:''}</td>
						<td>{$value['shuaijian150']|default:''}</td>
						<td>{$value['shuaijian200']|default:''}</td>
						<td>{$value['shuaijian280']|default:''}</td>
						<td>{$value['shuaijian450']|default:''}</td>
						<td>{$value['shuaijian800']|default:''}</td>
						<td>{$value['shuaijian900']|default:''}</td>
						<td>{$value['shuaijian1000']|default:''}</td>
						<td>{$value['shuaijian1500']|default:''}</td>
						<td>{$value['shuaijian1800']|default:''}</td>
						<td>{$value['shuaijian2000']|default:''}</td>
						<td>{$value['shuaijian2200']|default:''}</td>
						<td>{$value['shuaijian2400']|default:''}</td>
						<td>{$value['shuaijian2500']|default:''}</td>
						<td>{$value['shuaijian3000']|default:''}</td>
						<td>{$value['zukang']|default:''}</td>
					</tr>
				{/foreach}
				<tr>
					<td>最大值</td>
					<td>{$zhubo11Max|default:''}</td>
					<td>{$zhubo12Max|default:''}</td>
					<td>{$zhubo13Max|default:''}</td>
					<td>{$zhubo21Max|default:''}</td>
					<td>{$zhubo22Max|default:''}</td>
					<td>{$zhubo23Max|default:''}</td>
					{foreach from=$shuajianMax item=shuaijianmax}
						<td>{$shuaijianmax|default:''}</td>
					{/foreach}
					<td>{$zukangMax|default:''}</td>
				</tr>
				<tr>
					<td>最小值</td>
					<td>{$zhubo11Min|default:''}</td>
					<td>{$zhubo12Min|default:''}</td>
					<td>{$zhubo13Min|default:''}</td>
					<td>{$zhubo21Min|default:''}</td>
					<td>{$zhubo22Min|default:''}</td>
					<td>{$zhubo23Min|default:''}</td>
					{foreach from=$shuajianMin item=shuaijianmin}
						<td>{$shuaijianmin|default:''}</td>
					{/foreach}
					<td>{$zukangMin|default:''}</td>
				</tr>
				<tr>
					<td>平均值</td>
					<td>{$zhubo11Avg|default:''}</td>
					<td>{$zhubo12Avg|default:''}</td>
					<td>{$zhubo13Avg|default:''}</td>
					<td>{$zhubo21Avg|default:''}</td>
					<td>{$zhubo22Avg|default:''}</td>
					<td>{$zhubo23Avg|default:''}</td>
					{foreach from=$shuajianAvg item=shuaijianavg}
						<td>{$shuaijianavg|default:''}</td>
					{/foreach}
					<td>{$zukangAvg|default:''}</td>
				</tr>
			</table>
		</div>
		{$CI->pagination->create_links()}
		<div style="font-size: 10px;">
			注：本页面为江苏亨鑫科技有限公司订制版报表。只列出驻波1、驻波2、衰减、时域阻抗四项指标信息。
		</div>
	</div>
</div>
<!--{/block}-->