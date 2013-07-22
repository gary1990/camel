<!--{extends file='defaultPage.tpl'}-->
<!--{block name=title}-->
<title>{$title}</title>
<!--{/block}-->
<!--{block name=style}-->
<style type="text/css" media="screen">
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
	.shortinput{
		width:45px;
	}
</style>
<!--{/block}-->
<!--{block name=script}-->
<script>
	$(document).ready(function(){
		$("#lossPercentForm").validationEngine('attach',
		{
			promptPosition : "centerRight",
			autoPositionUpdate : "true"
		});
	});
</script>
<!--{/block}-->
<!--{block name=body}-->
<div class="span-64 last subitems">
	<div class="prepend-1 top_title">
		质量损失费用比例
	</div>
	<div style="color: red;">{$errorMsgPost|default:''}{$errorMsg|default:''}</div>
	<div style="margin-top:50px;">
		{if $errorMsg eq ''}
			<form id="lossPercentForm" action="{site_url()}/qualitylosspercent/qualitylossPost/" method="post">
				<div class="qualitylosspercent_div" style="width: 960px; overflow: auto;margin-bottom: 30px;">
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
								<td>
									<input id="{$qualitylossk}" class="shortinput validate[required,custom[number]]" name="{$qualitylossk}" value="{$qualityloss|default:''}" type="text"/>%
								</td>
							{/foreach}
						</tr>
					</table>
				</div>
				<input type="submit" value="保存"/>
			</form>
			<div style="font-size: 10px;">
				注：<br/>
				该表格为江苏亨鑫科技有限公司订制版。新增测试项，可以展示在该统计表内。但不能分频段或数值范围。
			</div>
		{else}
		{/if}
	</div>
</div>
<!--{/block}-->
