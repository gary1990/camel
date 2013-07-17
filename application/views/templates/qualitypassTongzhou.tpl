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
	/*
    table td
    {
        word-break: break-all;
        word-wrap: break-word;
    }
    */
   .text-area{
   		width:100px;
   		height:30px;
   		resize: none;
   }

</style>
<!--{/block}-->
<!--{block name=script}-->
<script type="text/javascript" src="{base_url()}resource/js/calendar/ui.datepicker.js"></script>
<script type="text/javascript" src="{base_url()}resource/js/calendar/ui.datepicker-zh-CN.js"></script>
<script type="text/javascript" src="{base_url()}resource/js/chosen.jquery.js"></script>
<script type="text/javascript"> 
	jQuery(function($)
	{
		$(".locPage > a").click(function(e) {
			e.preventDefault();
			var url = "{site_url()}"+'/qualitypassTongzhou/index'+ $(this).attr('href');
			$("#locForm").attr('action', url);
			$("#locForm").submit();
		});
		
		$(".searchBtn").click(function(e) {
			e.preventDefault();
			var url = "{site_url()}"+'/qualitypassTongzhou';
			$("#locForm").attr('action', url);
			$("#locForm").submit();
		});
		
		$(".exportResult").click(function(){
			var url = "{site_url()}"+'/qualitypassTongzhou/exportResult';
			$("#locForm").attr('action', url);
			$("#locForm").submit();
		});
		
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
		质量放行-同轴
	</div>
	<div>
		<form id="locForm" method="post" action="{site_url()}/qualitypassTongzhou">
			<span>日期：</span>
			<input id="date" name="date" value="{$smarty.post.date|default:$searchDate}" type="text"/>
			<span>工序：</span>
			{html_options class=process name=process options=$processArr selected=$smarty.post.process|default:''}
			<span>产品型号：</span>
			{html_options class=produdttype name=produdttype options=$producttypeArr selected=$smarty.post.produdttype|default:''}
			<span>放行状态：</span>
			{html_options class=passstatus name=passstatus options=$passStatusArr selected=$smarty.post.passstatus|default:''}
			<input class="searchBtn" type="submit" value="查询" />
		</form>
	</div>
	<div style="margin-bottom: 50px;">
		<form method="post" action="{site_url()}/qualitypassTongzhou/saveQualityPass">
			<div style="width: 960px; overflow: auto;">
				<table border="1" cellspacing="1" cellpadding="1">
					<tr>
						<th>序号</th>
						<th>车台</th>
						<th>产品型号</th>
						<th>盘号</th>
						<th>长度(km)</th>
						<th>序列号</th>
						<th>内外端</th>
						<th colspan="3">驻波/回波损耗</th>
						<th>衰减</th>
						<th>时域阻抗</th>
						<th>TDR电长度</th>
						<th>外观及其他</th>
						<th>客户</th>
						<th>质量工程师/技术部意见</th>
						<th>责任部门</th>
						<th>质量经理审核</th>
						<th>总工审核</th>
						<th>放行</th>
					</tr>
					{foreach from=$infoArr key=k item=value}
					<tr>
						<td rowspan="2">
							{if $value['testTime']|substr:-8:-6 le 7 && $value['testTime']|substr:-8:-6 ge 0}
								{$k+1}C
							{elseif $value['testTime']|substr:-8:-6 le 23 && $value['testTime']|substr:-8:-6 ge 15}
								{$k+1}B
							{else}
								{$k+1}A
							{/if}
						</td>
						<td rowspan="2">
							{$value['lathe']|default:''}
						</td>
						<td rowspan="2">
							{$value['producttypename']|default:''}
						</td>
						<td rowspan="2">
							{$value['platenum']|default:''}
						</td>
						<td rowspan="2">
							{abs($value['innermeter']-$value['outmeter'])|default:''}
						</td>
						<td rowspan="2">
							{$value['sn']|default:''}
						</td>
						<td>内端</td>
						<td>
							{$value['驻波1'][0]['mark']|default:''}/{$value['驻波1'][0]['value']|default:''}
							{$value['回波损耗1'][0]['mark']|default:''}/{$value['回波损耗1'][0]['value']|default:''}
						</td>
						<td>
							{$value['驻波1'][1]['mark']|default:''}/{$value['驻波1'][1]['value']|default:''}
							{$value['回波损耗1'][1]['mark']|default:''}/{$value['回波损耗1'][1]['value']|default:''}
						</td>
						<td>
							{$value['驻波1'][2]['mark']|default:''}/{$value['驻波1'][2]['value']|default:''}
							{$value['回波损耗1'][2]['mark']|default:''}/{$value['回波损耗1'][2]['value']|default:''}
						</td>
						<td rowspan="2">
							{if count($value['衰减']) eq 0}
							{elseif $value['衰减'][0]['testResult'] eq 0}
							不合格
							{else}
							合格
							{/if}
						</td>
						<td rowspan="2">
							{if count($value['时域阻抗']) eq 0}
							{else}
								{foreach from=$value['时域阻抗'] item=val}
									{$val['mark']}/{$val['value']}
								{/foreach}
							{/if}
						</td>
						<td rowspan="2">
							{if count($value['TDR电长度']) eq 0}
							{else}
								{foreach from=$value['TDR电长度'] item=val}
									{$val['mark']}/{$val['value']}
								{/foreach}
							{/if}
						</td>
						<td rowspan="2">
							<textarea name="facadeorother{$k+1}" class="text-area">{$value['facadeorother']|default:''}</textarea>
						</td>
						<td rowspan="2">
							<textarea name="client{$k+1}" class="text-area">{$value['client']|default:''}</textarea>
						</td>
						<td rowspan="2">
							<textarea name="qualityengineersuggestion{$k+1}" class="text-area">{$value['qualityengineersuggestion']|default:''}</textarea>
						</td>
						<td rowspan="2">
							<textarea name="responsibledepartment{$k+1}" class="text-area">{$value['responsibledepartment']|default:''}</textarea>
						</td>
						<td rowspan="2">
							<textarea name="qualitymanagerreview{$k+1}" class="text-area">{$value['qualitymanagerreview']|default:''}</textarea>
						</td>
						<td rowspan="2">
							<textarea name="headengineerreview{$k+1}" class="text-area">{$value['headengineerreview']|default:''}</textarea>
						</td>
						<td rowspan="2">
							{if $value['tag1'] eq 3}
								<input name="qualitypass{$k+1}" checked value = "3" type="checkbox" />
							{else}
								<input name="qualitypass{$k+1}" value="3" type="checkbox" />
							{/if}
						</td>
					</tr>
					<tr>
						<td>外端</td>
						<td>
							{$value['驻波2'][0]['mark']|default:''}/{$value['驻波2'][0]['value']|default:''}
							{$value['回波损耗2'][0]['mark']|default:''}/{$value['回波损耗2'][0]['value']|default:''}
						</td>
						<td>
							{$value['驻波2'][1]['mark']|default:''}/{$value['驻波2'][1]['value']|default:''}
							{$value['回波损耗2'][1]['mark']|default:''}/{$value['回波损耗2'][1]['value']|default:''}
						</td>
						<td>
							{$value['驻波2'][2]['mark']|default:''}/{$value['驻波2'][2]['value']|default:''}
							{$value['回波损耗2'][2]['mark']|default:''}/{$value['回波损耗2'][2]['value']|default:''}
						</td>
					</tr>
					<input name="id{$k+1}" type="hidden" value="{$value['id']}"/>
					{/foreach}
					<input type="hidden" name="totoalnum" value="{count($infoArr)}"/>
				</table>
			</div>
			{$CI->pagination->create_links()}
			{if $CI->session->userdata('team') eq '技术员' || $CI->session->userdata('team') eq '测试员及其他人员'}	
			{else}
				<input type="submit" value="保存"/>
				<input class="exportResult" type="button" value="Export"/>
			{/if}
		</form>
	</div>
</div>
<!--{/block}-->