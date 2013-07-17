<!--{extends file='defaultPage.tpl'}-->
<!--{block name=title}-->
<title>{$title}</title>
<!--{/block}-->
<!--{block name=style}-->
<link rel="stylesheet" type="text/css" href="{base_url()}resource/css/ui.datepicker.css" />
<link rel="stylesheet" type="text/css" href="{base_url()}resource/css/chosen.css" />
<style>
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
	th{
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
		产品指标统计-同轴
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
	<div style="margin-bottom: 50px;">
		<div style="width: 960px; overflow: auto;">
			<table border="1" cellspacing="1" cellpadding="1">
				<tr>
					<th rowspan="2">序列号</th>
					<th rowspan="2" colspan="3">驻波1</th>
					<th rowspan="2" colspan="3">驻波2</th>
					<th colspan="15">衰减</th>
					<th rowspan="2">阻抗</th>
				</tr>
				<tr>
					<th>100M</th>
					<th>150M</th>
					<th>200M</th>
					<th>280M</th>
					<th>450M</th>
					<th>800M</th>
					<th>900M</th>
					<th>1G</th>
					<th>1.5G</th>
					<th>1.8G</th>
					<th>2G</th>
					<th>2.2G</th>
					<th>2.4G</th>
					<th>2.5G</th>
					<th>3G</th>
				</tr>
				{foreach from=$resultArr item=value}
					<tr>
						<td>{$value['sn']|default:''}</td>
						<td>{$value['zhubo11']|default:''}</td>
						<td>{$value['zhubo12']|default:''}</td>
						<td>{$value['zhubo13']|default:''}</td>
						<td>{$value['zhubo21']|default:''}</td>
						<td>{$value['zhubo22']|default:''}</td>
						<td>{$value['zhubo23']|default:''}</td>
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
	</div>
</div>
<!--{/block}-->