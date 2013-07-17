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
			var url = "{site_url()}"+'/qualityStat/index'+ $(this).attr('href');
			$("#locForm").attr('action', url);
			$("#locForm").submit();
		});
		//查询
		$(".searchBtn").click(function(e) {
			e.preventDefault();
			var url = "{site_url()}"+'/qualityStat';
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
		不合格质量统计-同轴
	</div>
	<div>
		<form id="locForm" method="post" action="#">
			<span>起始日期：</span>
			<input id="startdate" name="startdate" value="{$smarty.post.startdate|default:$startTime}" type="text"/>
			<span>截止日期：</span>
			<input id="enddate" name="enddate" value="{$smarty.post.enddate|default:$endTime}" type="text"/>
			<span>工序：</span>
			{html_options class=process name=process options=$processArr selected=$smarty.post.process|default:''}
			<span>车台：</span>
			{html_options class=lathe name=lathe options=$latheArr selected=$smarty.post.lathe|default:''}
			<span>产品型号：</span>
			{html_options class=produdttype name=produdttype options=$producttypeArr selected=$smarty.post.produdttype|default:''}
			<input class="searchBtn" type="submit" value="查询" />
		</form>
	</div>
	<div style="color: red;">{$errorMsg|default:''}</div>
	<div style="margin-bottom: 50px;">
		<div style="width: 960px; overflow: auto;">
			<table border="1" cellspacing="1" cellpadding="1">
				<tr>
					<th rowspan="3">车台</th>
					<th rowspan="3">产品型号</th>
					<th rowspan="3">产量(km)</th>
					<th rowspan="3">不合格量</th>
					<th rowspan="3">合格率</th>
					<th colspan="13">驻波/回波损耗</th>
					{foreach from=$totalTestitemArray item=itemvalue}
						<th rowspan="3">{$itemvalue['name']|default:''}</th>
					{/foreach}
				</tr>
				<tr>
					<th colspan="4">0.8GHz~1GHz</th>
					<th colspan="4">1.7GHz~2.5GHz</th>
					<th colspan="4">0.8GHz~1GHz且1.7GHz~2.5GHz</th>
					<th rowspan="2">5MHz~3GHz</th>
				</tr>
				<tr>
					<th>1.15以下</th>
					<th>1.15-1.2</th>
					<th>1.2-1.3</th>
					<th>1.3以上</th>
					<th>1.15以下</th>
					<th>1.15-1.2</th>
					<th>1.2-1.3</th>
					<th>1.3以上</th>
					<th>1.15以下</th>
					<th>1.15-1.2</th>
					<th>1.2-1.3</th>
					<th>1.3以上</th>
				</tr>
				{foreach from=$resultArr item=perresult}
					<tr>
						{foreach from=$perresult item=val name=resultforeach}
							{if $smarty.foreach.resultforeach.index eq 4}
								<td>{$val|default:''}%</td>
							{else}
								<td>{$val|default:''}</td>
							{/if}
						{/foreach}
					</tr>
				{/foreach}
				<tr>
					<td colspan="2">总计</td>
					{foreach from=$totalStat item=valstat name=statforeach}
						{if $smarty.foreach.statforeach.index eq 2}
							<td>{$valstat|default:''}%</td>
						{else}
							<td>{$valstat|default:''}</td>
						{/if}
					{/foreach}
				</tr>
			</table>
		</div>
		{$CI->pagination->create_links()}
	</div>
</div>
<!--{/block}-->