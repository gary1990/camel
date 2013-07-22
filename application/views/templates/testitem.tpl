<!--{extends file='defaultPage.tpl'}-->
<!--{block name=title}-->
<title>{$title}</title>
<!--{/block}-->
<!--{block name=style}-->
<style type="text/css" media="screen">
	#diagram1 {
		height: 400px;
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
{if $diagram|default:false == true}
<script src="{base_url()}resource/js/highCharts/highstock.js"></script>
<script src="{base_url()}resource/js/highCharts/modules/exporting.js"></script>
{/if}
<script>
	$(document).ready(function(){
		//去除特殊测试项后面的“编辑”按钮
		var specialitems = $(".specialitem").parent();
		$.each( specialitems, function( key, value ) {
			$(value).next().next().next().replaceWith("<td></td>");
		});
	});
</script>
<!--{/block}-->
<!--{block name=body}-->
<!--{$output}-->
<div id="diagram1" class="span-64 last">
</div>
<!--{/block}-->
