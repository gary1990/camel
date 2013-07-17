<!--{extends file='defaultPage.tpl'}-->
<!--{block name=title}-->
<title>{$title}</title>
<!--{/block}-->
<!--{block name=style}-->
<style type="text/css" media="screen">
</style>
<!--{foreach $css_files as $file}-->
<link type="text/css" rel="stylesheet" href="{$file}" />
<!--{/foreach}-->
<!--{/block}-->

<!--{block name=script}-->
<!--{foreach $js_files as $file}-->
<script src="{$file}"></script>
<!--{/foreach}-->
<!--{/block}-->
<!--{block name=body}-->
<!--{$output}-->
<div id="diagram1" class="span-64 last">
</div>
<!--{/block}-->
