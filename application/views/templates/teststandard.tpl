<!--{extends file='defaultPage.tpl'}-->
<!--{block name=title}-->
<title>{$title}</title>
<!--{/block}-->
<!--{block name=style}-->
<style>
	.seprateline
	{
		height:5px;
		margin:1em 0 1em 0;
	}
	.items{
		margin-bottom:15px;
	}
	.subitem{
		border: 1px solid #DDDDDD;
		width: 75px;
		height: 20px;
		margin-top: 10px;
		padding-top:5px;
		padding-bottom:5px;
		text-align: center;
		cursor:pointer;
		float: left;
	}
	.subitem a
	{
		text-decoration:none;
		color: #DDDDDD;
	}
	.currentitem {
		background-color: #E5ECF9;
    	color: black;
    	font-weight: bold;
	}
	.currentitem a {
		color: black;
	}
</style>
<!--{foreach $css_files as $file}-->
<link type="text/css" rel="stylesheet" href="{$file}" />
<!--{/foreach}-->
<!--{/block}-->
<!--{block name=script}-->
<!--{foreach $js_files as $file}-->
<script src="{$file}"></script>
<!--{/foreach}-->
<script type="text/javascript">
	
</script>
<!--{/block}-->
<!--{block name=body}-->
<div class="span-64 last subitems">
	<div class="items {$currentitem|default:''}">
		<div class="subitem {if $currentitem eq 'shuaijian'}currentitem{else}{/if}">
			<a href="{site_url()}/testStandard">衰减</a>
		</div>
		<div class="subitem {if $currentitem eq 'tdr'}currentitem{else}{/if}">
			<a href="{site_url()}/testStandard/tdrEleLength">TDR电长度</a>
		</div>
		<div class="subitem {if $currentitem eq 'timedomainimpedance'}currentitem{else}{/if}">
			<a href="{site_url()}/testStandard/timedomainImpedance">时域阻抗</a>
		</div>
	</div>
	<div style="clear: both;"></div>
	<hr class="seprateline">
	<div style="margin-bottom: 50px;">
		<!--{$output}-->
	</div>
</div>
<!--{/block}-->