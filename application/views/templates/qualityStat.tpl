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
	.seprateline_short
	{
		height:5px;
		width:89%;
		position: relative;
		top:20px;
		margin-bottom:8px;
	}
	.qualitylosspercent_span{
		cursor: pointer;
		color: blue;
		text-decoration: underline;
	}
	.qualitylosspercent_div{
		display:none;
	}
	.dateinput{
		width:100px;
	}
	.qualitylossinput
	{
		width:50px;
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
		//质量损失费用比例点击
		$('.qualitylosspercent_span').click(function(){
			$(".qualitylosspercent_div").toggle("slow");
		});
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
		同轴不合格质量统计表
	</div>
	<div>
		<form id="locForm" method="post" action="#">
			<span>起始日期：</span>
			<input class="dateinput" id="startdate" name="startdate" value="{$smarty.post.startdate|default:$startTime}" type="text"/>
			<span>截止日期：</span>
			<input class="dateinput" id="enddate" name="enddate" value="{$smarty.post.enddate|default:$endTime}" type="text"/>
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
	{if $errorMsg eq ''}
		<hr align="left" class="seprateline_short"/>
		<div style="text-align:right;">
			<span class="qualitylosspercent_span">质量损失费用比例</span>
		</div>
		<div class="qualitylosspercent_div" style="width: 960px; overflow: auto;">
			<table>
				<tr>
					<th colspan="13"><div class="th7">驻波/回波损耗</div></th>
					{foreach from=$totalTestitemArray item=itemvalue}
						<th rowspan="3"><div class="th7">{$itemvalue['name']|default:''}</div></th>
					{/foreach}
				</tr>
				<tr>
					<th colspan="4"><div class="th6">0.8GHz~1GHz</div></th>
					<th colspan="4"><div class="th7">1.7GHz~2.5GHz</div></th>
					<th colspan="4"><div class="th14">0.8GHz~1GHz且1.7GHz~2.5GHz</div></th>
					<th rowspan="2"><div class="th5">5MHz~3GHz</div></th>
				</tr>
				<tr>
					<th><div class="th4">1.15以下</div></th>
					<th><div class="th4">1.15-1.2</div></th>
					<th><div class="th4">1.2-1.3</div></th>
					<th><div class="th4">1.3以上</div></th>
					<th><div class="th4">1.15以下</div></th>
					<th><div class="th4">1.15-1.2</div></th>
					<th><div class="th4">1.2-1.3</div></th>
					<th><div class="th4">1.3以上</div></th>
					<th><div class="th4">1.15以下</div></th>
					<th><div class="th4">1.15-1.2</div></th>
					<th><div class="th4">1.2-1.3</div></th>
					<th><div class="th4">1.3以上</div></th>
				</tr>
				<tr>
					{foreach from=$qualityLossArry key=qualitylossk item=qualityloss}
						<td>{$qualityloss|default:''}%</td>
					{/foreach}
				</tr>
			</table>
		</div>
		<hr class="seprateline"/>
		<div style="margin-bottom: 50px;">
			<div style="width: 960px; overflow: auto;">
				<table border="1" cellspacing="1" cellpadding="1">
					<tr>
						<th rowspan="3"><div class="th2">车台</div></th>
						<th rowspan="3"><div class="th4">产品型号</div></th>
						<th rowspan="3"><div class="th4">产量(km)</div></th>
						<th rowspan="3"><div class="th4">不合格量</div></th>
						<th rowspan="3"><div class="th3">合格率</div></th>
						<th colspan="13"><div class="th7">驻波/回波损耗</div></th>
						{foreach from=$totalTestitemArray item=itemvalue}
							<th rowspan="3"><div class="th7">{$itemvalue['name']|default:''}</div></th>
						{/foreach}
					</tr>
					<tr>
						<th colspan="4"><div class="th6">0.8GHz~1GHz</div></th>
						<th colspan="4"><div class="th7">1.7GHz~2.5GHz</div></th>
						<th colspan="4"><div class="th14">0.8GHz~1GHz且1.7GHz~2.5GHz</div></th>
						<th rowspan="2"><div class="th5">5MHz~3GHz</div></th>
					</tr>
					<tr>
						<th><div class="th4">1.15以下</div></th>
						<th><div class="th4">1.15-1.2</div></th>
						<th><div class="th4">1.2-1.3</div></th>
						<th><div class="th4">1.3以上</div></th>
						<th><div class="th4">1.15以下</div></th>
						<th><div class="th4">1.15-1.2</div></th>
						<th><div class="th4">1.2-1.3</div></th>
						<th><div class="th4">1.3以上</div></th>
						<th><div class="th4">1.15以下</div></th>
						<th><div class="th4">1.15-1.2</div></th>
						<th><div class="th4">1.2-1.3</div></th>
						<th><div class="th4">1.3以上</div></th>
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
						<td colspan="2"><div class="th2">总计</div></td>
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
	{else}
	{/if}
</div>
<!--{/block}-->