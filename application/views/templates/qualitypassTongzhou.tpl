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
		同轴质量放行记录表
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
			<input style="width:65px;height:25px;" class="searchBtn" type="submit" value="查询" />
		</form>
	</div>
	<hr class="seprateline">
	<div style="margin-bottom: 50px;">
		<form method="post" action="{site_url()}/qualitypassTongzhou/saveQualityPass">
			<div style="width: 960px; overflow: auto; margin-bottom: 10px;">
				<table border="1" cellspacing="1" cellpadding="1">
					<tr>
						<th><div class="th2">序号</div></th>
						<th><div class="th2">车台</div></th>
						<th><div class="th4">产品型号</div></th>
						<th><div class="th2">盘号</div></th>
						<th><div class="th5">长度(千米)</div></th>
						<th><div class="th3">序列号</div></th>
						<th><div class="th3">内外端</div></th>
						<th colspan="4"><div class="th7">驻波/回波损耗</div></th>
						<th><div class="th2">衰减</div></th>
						<th><div class="th4">时域阻抗</div></th>
						<th><div class="th5">TDR电长度</div></th>
						<th><div class="th5">外观及其他</div></th>
						<th><div class="th2">客户</div></th>
						<th><div class="th10">质量工程师/技术部意见</div></th>
						<th><div class="th4">责任部门</div></th>
						<th><div class="th6">质量经理审核</div></th>
						<th><div class="th4">总工审核</div></th>
						<th><div class="th2">放行</div></th>
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
							<a href="{site_url('/packing/detail_vna')}/{$value['id']}" target="_blank">{$value['sn']|default:''}</a>
						</td>
						<td>内端</td>
						<td>
							{if isset($value['驻波1'][0]['mark'])}
								{if $value['驻波1'][0]['result'] eq 0}
									<span style="color: red;">{$value['驻波1'][0]['mark']|default:''}/{$value['驻波1'][0]['value']|default:''}</span>
								{else}
									{$value['驻波1'][0]['mark']|default:''}/{$value['驻波1'][0]['value']|default:''}
								{/if}
							{else}
							{/if}
							{if isset($value['回波损耗1'][0]['mark'])}
								{if $value['回波损耗1'][0]['result'] eq 0}
									<span style="color: red;">{$value['回波损耗1'][0]['mark']|default:''}/{$value['回波损耗1'][0]['value']|default:''}</span>	
								{else}
									{$value['回波损耗1'][0]['mark']|default:''}/{$value['回波损耗1'][0]['value']|default:''}								
								{/if}
							{else}
							{/if}
						</td>
						<td>
							{if isset($value['驻波1'][1]['mark'])}
								{if $value['驻波1'][1]['result'] eq 0}
									<span style="color: red;">{$value['驻波1'][1]['mark']|default:''}/{$value['驻波1'][1]['value']|default:''}</span>
								{else}
									{$value['驻波1'][1]['mark']|default:''}/{$value['驻波1'][1]['value']|default:''}
								{/if}
							{else}	
							{/if}
							{if isset($value['回波损耗1'][1]['mark'])}
								{if $value['回波损耗1'][1]['result'] eq 0}
									<span style="color: red;">{$value['回波损耗1'][1]['mark']|default:''}/{$value['回波损耗1'][1]['value']|default:''}</span>
								{else}
									{$value['回波损耗1'][1]['mark']|default:''}/{$value['回波损耗1'][1]['value']|default:''}
								{/if}	
							{else}	
							{/if}
						</td>
						<td>
							{if isset($value['驻波1'][2]['mark'])}
								{if $value['驻波1'][2]['result'] eq 0}
									<span style="color: red;">{$value['驻波1'][2]['mark']|default:''}/{$value['驻波1'][2]['value']|default:''}</span>
								{else}
									{$value['驻波1'][2]['mark']|default:''}/{$value['驻波1'][2]['value']|default:''}
								{/if}
							{else}	
							{/if}
							{if isset($value['回波损耗1'][2]['mark'])}
								{if $value['回波损耗1'][2]['result'] eq 0}
									<span style="color: red;">{$value['回波损耗1'][2]['mark']|default:''}/{$value['回波损耗1'][2]['value']|default:''}</span>
								{else}
									{$value['回波损耗1'][2]['mark']|default:''}/{$value['回波损耗1'][2]['value']|default:''}
								{/if}
							{else}
							{/if}
						</td>
						<td>
							{if isset($value['驻波1'][3]['mark'])}
								{if $value['驻波1'][3]['result'] eq 0}
									<span style="color: red;">{$value['驻波1'][3]['mark']|default:''}/{$value['驻波1'][3]['value']|default:''}</span>
								{else}
									{$value['驻波1'][3]['mark']|default:''}/{$value['驻波1'][3]['value']|default:''}
								{/if}
							{else}	
							{/if}
							{if isset($value['回波损耗1'][3]['mark'])}
								{if $value['回波损耗1'][3]['result'] eq 0}
									<span style="color: red;">{$value['回波损耗1'][3]['mark']|default:''}/{$value['回波损耗1'][3]['value']|default:''}</span>
								{else}
									{$value['回波损耗1'][3]['mark']|default:''}/{$value['回波损耗1'][3]['value']|default:''}
								{/if}
							{else}
							{/if}
						</td>
						<td rowspan="2">
							{if count($value['衰减']) eq 0}
							{elseif $value['衰减'][0]['testResult'] eq 0}
								<span style="color: red;">不合格</span>
							{else}
								合格
							{/if}
						</td>
						<td rowspan="2">
							{if count($value['时域阻抗']) eq 0}
							{else}
								{foreach from=$value['时域阻抗'] item=val name=shuyuzukangforeach}
									{if $smarty.foreach.shuyuzukangforeach.index > 0}
										{if $val['result'] eq 0}
											<span style="color: red;">/{$val['value']}</span>
										{else}
											/{$val['value']}
										{/if}
									{else}
										{if $val['result'] eq 0}
											<span style="color: red;">{$val['value']}</span>
										{else}
											{$val['value']}
										{/if}
									{/if}
								{/foreach}
							{/if}
						</td>
						<td rowspan="2">
							{if count($value['TDR电长度']) eq 0}
							{else}
								{foreach from=$value['TDR电长度'] item=val name=tdrforeach}
									{if $smarty.foreach.tdrforeach.index > 0}
										{if $val['result'] eq 0}
											<span style="color: red;">/{$val['value']}</span>
										{else}
											/{$val['value']}
										{/if}
									{else}
										{if $val['result'] eq 0}
											<span style="color: red;">{$val['value']}</span>
										{else}
											{$val['value']}
										{/if}
									{/if}
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
							{if isset($value['驻波2'][0]['mark'])}
								{if $value['驻波2'][0]['result'] eq 0}
									<span style="color: red;">{$value['驻波2'][0]['mark']|default:''}/{$value['驻波2'][0]['value']|default:''}</span>
								{else}
									{$value['驻波2'][0]['mark']|default:''}/{$value['驻波2'][0]['value']|default:''}
								{/if}
							{else}
							{/if}
							{if isset($value['回波损耗2'][0]['mark'])}
								{if $value['回波损耗2'][0]['result'] eq 0}
									<span style="color: red;">{$value['回波损耗2'][0]['mark']|default:''}/{$value['回波损耗2'][0]['value']|default:''}</span>
								{else}
									{$value['回波损耗2'][0]['mark']|default:''}/{$value['回波损耗2'][0]['value']|default:''}
								{/if}
							{else}
							{/if}
						</td>
						<td>
							{if isset($value['驻波2'][1]['mark'])}
								{if $value['驻波2'][1]['result'] eq 0}
									<span style="color: red;">{$value['驻波2'][1]['mark']|default:''}/{$value['驻波2'][1]['value']|default:''}</span>
								{else}
									{$value['驻波2'][1]['mark']|default:''}/{$value['驻波2'][1]['value']|default:''}
								{/if}
							{else}
							{/if}
							{if isset($value['回波损耗2'][1]['mark'])}
								{if $value['回波损耗2'][1]['result'] eq 0}
									<span style="color: red;">{$value['回波损耗2'][1]['mark']|default:''}/{$value['回波损耗2'][1]['value']|default:''}</span>
								{else}
									{$value['回波损耗2'][1]['mark']|default:''}/{$value['回波损耗2'][1]['value']|default:''}
								{/if}	
							{else}
							{/if}
						</td>
						<td>
							{if isset($value['驻波2'][2]['mark'])}
								{if $value['驻波2'][2]['result'] eq 0}
									<span style="color: red;">{$value['驻波2'][2]['mark']|default:''}/{$value['驻波2'][2]['value']|default:''}</span>
								{else}
									{$value['驻波2'][2]['mark']|default:''}/{$value['驻波2'][2]['value']|default:''}
								{/if}
							{else}
							{/if}
							{if isset($value['回波损耗2'][2]['mark'])}
								{if $value['回波损耗2'][2]['result'] eq 0}
									<span style="color: red;">{$value['回波损耗2'][2]['mark']|default:''}/{$value['回波损耗2'][2]['value']|default:''}</span>
								{else}
									{$value['回波损耗2'][2]['mark']|default:''}/{$value['回波损耗2'][2]['value']|default:''}
								{/if}					
							{else}
							{/if}
						</td>
						<td>
							{if isset($value['驻波2'][3]['mark'])}
								{if $value['驻波2'][3]['result'] eq 0}
									<span style="color: red;">{$value['驻波2'][3]['mark']|default:''}/{$value['驻波2'][3]['value']|default:''}</span>
								{else}
									{$value['驻波2'][3]['mark']|default:''}/{$value['驻波2'][3]['value']|default:''}
								{/if}
							{else}
							{/if}
							{if isset($value['回波损耗2'][3]['mark'])}
								{if $value['回波损耗2'][3]['result'] eq 0}
									<span style="color: red;">{$value['回波损耗2'][3]['mark']|default:''}/{$value['回波损耗2'][3]['value']|default:''}</span>
								{else}
									{$value['回波损耗2'][3]['mark']|default:''}/{$value['回波损耗2'][3]['value']|default:''}
								{/if}					
							{else}
							{/if}
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
				<div style="text-align: right;margin-top:10px;margin-bottom: 10px;">
					<input style="width:75px;height:30px;" type="submit" value="保存"/>
				</div>
				<div style="text-align: right;margin-top:10px;margin-bottom: 10px;">
					<input style="width:75px;height:30px;" class="exportResult" type="button" value="Export"/>
				</div>
			{/if}
		</form>
		<div style="font-size:10px;">
			注：<br/>
			1.本页面为江苏亨鑫科技有限公司订制版同轴质量放行记录表。按要求只列出驻波/回波损耗、衰减、时域阻抗、TDR电长度指标信息。<br/>
			2.驻波1/回波损耗1为内端。驻波2/回波损耗2为外端。
		</div>
	</div>
</div>
<!--{/block}-->