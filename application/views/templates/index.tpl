<!--{extends file='defaultPage.tpl'}-->
<!--{block name=title}-->
<title>导航页</title>	
<!--{/block}-->
<!--{block name=style}-->
	<style>
		.subitems{
			margin-top:70px;
		}
		.subitem{
			border:1px solid black;
			height:250px;
			padding:15px;
			margin-left:35px;
			position:relative;
		}
		.itemTitle{
			font-size:16px;
			text-align:center;
			margin-bottom:20px;
		}
		.subitem .lt{
			left:-2px; 
			top:-3px; 
			position:absolute; 
			width:20px; 
			height:20px;
		}
		.subitem .rt{
			left:170px; 
			top:-2px; 
			position:absolute; 
			width:30px; 
			height:20px;
		}
		.subitem .lb{
			left:-1px; 
			top:255px; 
			position:absolute; 
			width:20px; 
			height:30px;
		}
		.subitem .rb{
			left:172px; 
			top:255px; 
			position:absolute; 
			width:30px; 
			height:30px;
		}
		span a{
			text-decoration:none;
		}
	</style>
<!--{/block}-->
<!--{block name=script}-->
<script type="text/javascript">
</script>
<!--{block name=subScript}-->
<!--{/block}-->
<!--{/block}-->
<!--{block name=body}-->
<div class="span-64 last subitems">
	<div style="color:red;text-align: center;">{$nopermissionErr|default:''}</div>
	<div class="span-11 subitem">
		<div class="lt" style="background: url({base_url()}resource/img/top-left.bmp) no-repeat;">
		</div>
        <div class="rt" style="background: url({base_url()}resource/img/top-right.bmp) no-repeat;">
        </div>
        <div class="lb" style="background: url({base_url()}resource/img/bottom-left.bmp) no-repeat;">
        </div>
        <div class="rb" style="background: url({base_url()}resource/img/bottom-right.bmp) no-repeat;">
        </div>
		<div class="itemTitle"><b>质量追溯</b></div>
		<div>
			<span><a href="{site_url()}/vna_pim/vna">1.VNA测试记录</a></span><br/>
			<span><a href="{site_url()}/vna_pim/pim">2.PIM测试记录</a></span><br/>
			<span><a href="{site_url()}/packing">3.包装记录</a></span><br/>
		</div>
	</div>
	<div class="span-11 subitem">
		<div class="lt" style="background: url({base_url()}resource/img/top-left.bmp) no-repeat;">
		</div>
        <div class="rt" style="background: url({base_url()}resource/img/top-right.bmp) no-repeat;">
        </div>
        <div class="lb" style="background: url({base_url()}resource/img/bottom-left.bmp) no-repeat;">
        </div>
        <div class="rb" style="background: url({base_url()}resource/img/bottom-right.bmp) no-repeat;">
        </div>
		<div class="itemTitle"><b>测试管理</b></div>
		<div>
			<span><a href="{site_url()}/producttestcase">1.测试方案</a></span><br/>
			<span><a href="{site_url()}/firstPage/producttype">2.产品型号</a></span><br/>
			<span><a href="{site_url()}/firstPage/testitem">3.测试项</a></span><br/>
			<span><a href="{site_url()}/firstPage/teststation">4.测试站点</a></span><br/>
			<span><a href="{site_url()}/firstPage/equipment">5.测试设备</a></span><br/>
			<span><a href="{site_url()}/firstPage/tester">6.测试员</a></span><br/>
			<span><a href="{site_url()}/testStandard">7.测试项判断标准</a></span><br/>
		</div>
	</div>
	<div class="span-11 subitem">
		<div class="lt" style="background: url({base_url()}resource/img/top-left.bmp) no-repeat;">
		</div>
        <div class="rt" style="background: url({base_url()}resource/img/top-right.bmp) no-repeat;">
        </div>
        <div class="lb" style="background: url({base_url()}resource/img/bottom-left.bmp) no-repeat;">
        </div>
        <div class="rb" style="background: url({base_url()}resource/img/bottom-right.bmp) no-repeat;">
        </div>
		<div class="itemTitle"><b>生产看板</b></div>
		<div>
			<span><a href="{site_url()}/advancedsearch">1.高级查询(Beta)</a></span><br/>
			<span><a href="{site_url()}/reportform">2.报表(Beta)</a></span><br/>
			<span><a href="{site_url()}/qualitypassTongzhou">3.同轴质量放行记录表</a></span><br/>
			<span><a href="{site_url()}/qualitypass">4.跳线质量放行记录表</a></span><br/>
			<span><a href="{site_url()}/qualityStat">5.同轴不合格质量统计表</a></span><br/>
			<span><a href="{site_url()}/productQualityIndex">6.同轴产品指标统计表</a></span><br/>
		</div>
	</div>
	<div class="span-11 subitem">
		<div class="lt" style="background: url({base_url()}resource/img/top-left.bmp) no-repeat;">
		</div>
        <div class="rt" style="background: url({base_url()}resource/img/top-right.bmp) no-repeat;">
        </div>
        <div class="lb" style="background: url({base_url()}resource/img/bottom-left.bmp) no-repeat;">
        </div>
        <div class="rb" style="background: url({base_url()}resource/img/bottom-right.bmp) no-repeat;">
        </div>
		<div class="itemTitle"><b>其他</b></div>
		<div>
			<span><a href="{site_url()}/firstPage/user">1.用户</a></span><br/>
			<span><a href="{site_url()}/firstPage/team">2.用户组</a></span><br/>
			<span><a href="{site_url()}/firstPage/factory">3.工厂</a></span><br/>
			<span><a href="{site_url()}/firstPage/department">4.车间</a></span><br/>
			<span><a href="{site_url()}/qualitylosspercent">5.质量损失费用比例</a></span><br/>
		</div>
	</div>
</div>
<!--{/block}-->
