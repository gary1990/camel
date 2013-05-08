<!--{extends file='defaultPage.tpl'}-->
<!--{block name=title}-->
<title>{$title}</title>
<!--{/block}-->
<!--{block name=style}-->
	<style>
		.separate_line{
			margin-top:20px;
			height:3px;
		}
		.short_input{
			width:30px;
		}
		.long_input{
			width:100px;
		}
		.addbtn{
			cursor:pointer;
		}
		.delbtn{
			cursor:pointer;
		}
		.per_record_hidden{
			display:none;
		}
	</style>
<!--{/block}-->
<!--{block name=script}-->
<script type="text/javascript">
	//在当前行下面添加一行
	function add_record(thisid){
		var num = $(".addcount").val();
		var add_td = $(".per_record").html();
		var add_tr = '<tr class="per_record" id="'+num+'">'+add_td+'</tr>';
		add_tr = add_tr.replace(/per_record_hidden_/g,"");
		add_tr = add_tr.replace(/producttype_/g,'producttype'+num).replace(/testitem_/g,'testitem'+num);
		add_tr = add_tr.replace(/statusfile_/g,'statusfile'+num).replace(/ports_/g,'ports'+num);
		add_tr = add_tr.replace(/channel_/g,'channel'+num).replace(/trace_/g,'trace'+num);
		add_tr = add_tr.replace(/start_/g,'start'+num).replace(/stop_/g,'stop'+num);
		add_tr = add_tr.replace(/mark_/g,'mark'+num).replace(/min_/g,'min'+num);
		add_tr = add_tr.replace(/max_/g,'max'+num).replace(/selfid/g,num);
		$("#"+thisid).after(add_tr);
		$(".addcount").attr("value",parseInt(num)+1);
	}
	//删除当前行
	function del_record(thisid){
		$("#"+thisid).remove();
	}
	
	$(document).ready(function(){
		//产品型号下拉列表的判空
		$("body").delegate(".producttype", "change", function(){
			var producttype = $(this).val();
      		if(producttype == "")
      		{
      			alert("产品型号必填");
      		}
    	});
    	//端口数输入框的为整数判断
		$("body").delegate(".ports", "blur", function(){
			var ports = $(this).val();
			//取整后和原来数比较
      		if(parseInt(ports) != ports)
      		{
      			alert("端口数为整数");
      		}
    	});
    	//start,stop,min,max输入框是否为数字的判断
    	$("body").delegate(".start,.stop,.min,.max", "blur", function(){
			var start = $(this).val();
			//取整后和原来数比较
      		if(isNaN(start))
      		{
      			alert("start,stop,min,max为数字");
      		}
    	});
		//分页事件
		$(".locPage > a").click(function(e) {
			e.preventDefault();
			var url = $("#locForm").attr('action') + $(this).attr('href');
			$("#locForm").attr('action', url);
			$("#locForm").submit();
		});
		//查看按钮点击时，判断页面上是否做了修改
		$(".searchbtn").click(function(e){
			//取得当前记录条数
			var tatolcount = $(".addcount").val();
			//页面内容改变确认框的结果
			var conf;
			for(var i=tatolcount;i >= 1;i--)
			{
				var producttype = $('[name="producttype'+i+'"]').val();
				if(producttype == undefined)
				{
					continue;
				}
				else
				{
					var case1 = $('[name="producttype'+i+'"]').val() == $('[name="producttype'+i+'"]').next().val();
					var case2 = $('[name="testitem'+i+'"]').val() == $('[name="testitem'+i+'"]').next().val();
					var case3 = $('[name="statusfile'+i+'"]').val() == $('[name="statusfile'+i+'"]').next().val();
					var case4 = $('[name="ports'+i+'"]').val() == $('[name="ports'+i+'"]').next().val();
					var case5 = $('[name="channel'+i+'"]').val() == $('[name="channel'+i+'"]').next().val();
					var case6 = $('[name="trace'+i+'"]').val() == $('[name="trace'+i+'"]').next().val();
					var case7 = $('[name="start'+i+'"]').val() == $('[name="start'+i+'"]').next().val();
					var case8 = $('[name="stop'+i+'"]').val() == $('[name="stop'+i+'"]').next().val();
					var case9 = $('[name="mark'+i+'"]').val() == $('[name="mark'+i+'"]').next().val();
					var case10 = $('[name="min'+i+'"]').val() == $('[name="min'+i+'"]').next().val();
					var case11 = $('[name="max'+i+'"]').val() == $('[name="max'+i+'"]').next().val();
					var changed = case1 && case2 && case3 && case4 && case5 && case6 && case7 && case8 && case9 && case10 && case11;
					if(changed)
					{
						//do noting
					}
					else
					{
						var msg = "有修改尚未保存，确定不保存当前修改？";
						conf = confirm(msg);
						break;
					}
				}
			}
			if(conf == undefined)
			{
				//do noting,start search
			}
			else
			{
				if(conf == true)
				{
					//do noting,start search 
				}
				else
				{
					e.preventDefault();
				}
			}
		});
		//保存按钮点击时判断产品型号，测试项，端口数
		$(".savebtn").click(function(e){
			//取得当前记录条数
			var tatolcount = $(".addcount").val();
			//产品型号，测试项，端口数判空
			for(var i=tatolcount;i >= 1;i--)
			{
				var producttype = $('[name="producttype'+i+'"]').val();
				if(producttype == undefined)
				{
					continue;
				}
				else
				{
					var case1 = $('[name="producttype'+i+'"]').val() != "";
					var case2 = $('[name="testitem'+i+'"]').val() != "";
					var case3 = $('[name="ports'+i+'"]').val() != "";
					var empty = case1 && case2 && case3;
					if(empty)
					{
						//save
					}
					else
					{
						alert("产品型号，测试项，端口数不为空！");
						e.preventDefault();
						break;
					}
				}
			}
		});
	});
</script>
<!--{block name=subScript}-->
<!--{/block}-->
<!--{/block}-->
<!--{block name=body}-->
<div class="span-64 last subitems">
	<div class="prepend-1 span-63">
		<form method="post" action="{site_url()}/producttestcase">
			产品型号：
			{html_options name=producttypesearch options=$producttype selected=$smarty.post.producttypesearch|default:''}
			&nbsp;&nbsp;&nbsp;
			<input class="searchbtn" type="submit" value="查看" />
		</form>
		<hr class="separate_line">
		<div>
			<div>
				<div style="width:50%;float:left;">
					&nbsp;
				</div>
				<div style="padding-left:50%;text-align:center;">极限值定义(oiption)</div>
			</div>
			<div>
				<form name="locForm" id="locForm" method="post" action="{site_url('producttestcase/del_ins/')}">
					<table>
						<tr>
							<th>序号</th><th>产品型号</th>
							<th>测试项</th><th>状态文件</th>
							<th>端口数</th><th style="border-left:1px solid #DDDDDD;">Channel</th>
							<th>Trace</th><th>Start MHz</th>
							<th>Stop MHz</th><th>Mark</th>
							<th>Min</th><th>Max</th>
							<th>&nbsp;</th><th style="border-right:1px solid #DDDDDD">&nbsp;</th>
						</tr>
						<tr class="per_record per_record_hidden">
							<td>&nbsp;</td><td>{html_options name=producttype_ class=producttype options=$producttype}<input type="hidden" class="short_input" value=""/></td>
							<td>{html_options name=testitem_ class=testitem options=$testitem}<input type="hidden" class="short_input" value=""/></td>
							<td><input class="long_input statusfile" name="statusfile_" type="text" /><input type="hidden" class="short_input" value=""/></td>
							<td><input class="short_input ports" name="ports_" maxlength="4" type="text" /><input type="hidden" class="short_input" value=""/></td>
							<td style="border-left:1px solid #DDDDDD;">{html_options name=channel_ class=channel options=$one_tenArr}<input type="hidden" class="short_input" value="1"/></td>
							<td>{html_options name=trace_ class=trace options=$one_tenArr}<input type="hidden" class="short_input" value="1"/></td>
							<td><input class="short_input start" name="start_" type="text" /><input type="hidden" class="short_input" value=""/></td>
							<td><input class="short_input stop" name="stop_" type="text" /><input type="hidden" class="short_input" value=""/></td>
							<td>{html_options name=mark_ class=mark options=$one_tenArr}<input type="hidden" class="short_input" value="1"/></td>
							<td><input class="short_input min" name="min_" type="text" /><input type="hidden" class="short_input" value=""/></td>
							<td><input class="short_input max" name="max_" type="text" /><input type="hidden" class="short_input" value=""/></td>
							<td><span class="addbtn" onclick="add_record(selfid)">+</span></td>
							<td style="border-right:1px solid #DDDDDD"><span class="delbtn" onclick="del_record(selfid)">-</span></td>
						</tr>
						{if count($testcaseArr) eq 0}
							<tr class="per_record" id="1">
								<td>1</td><td>{html_options name=producttype1 class=producttype options=$producttype}<input type="hidden" class="short_input" value=""/></td>
								<td>{html_options name=testitem1 class=testitem options=$testitem}<input type="hidden" class="short_input" value=""/></td>
								<td><input class="long_input statusfile" name="statusfile1" type="text" /><input type="hidden" class="short_input" value=""/></td>
								<td><input class="short_input ports" name="ports1" maxlength="4" type="text" /><input type="hidden" class="short_input" value=""/></td>
								<td style="border-left:1px solid #DDDDDD;">{html_options name=channel1 class=channel options=$one_tenArr}<input type="hidden" class="short_input" value="1"/></td>
								<td>{html_options name=trace1 class=trace options=$one_tenArr}<input type="hidden" class="short_input" value="1"/></td>
								<td><input class="short_input start" name="start1" type="text" /><input type="hidden" class="short_input" value=""/></td>
								<td><input class="short_input stop" name="stop1" type="text" /><input type="hidden" class="short_input" value=""/></td>
								<td>{html_options name=mark1 class=mark options=$one_tenArr}<input type="hidden" class="short_input" value="1"/></td>
								<td><input class="short_input min" name="min1" type="text" /><input type="hidden" class="short_input" value=""/></td>
								<td><input class="short_input max" name="max1" type="text" /><input type="hidden" class="short_input" value=""/></td>
								<td><span class="addbtn" onclick="add_record(1)">+</span></td>
								<td style="border-right:1px solid #DDDDDD"><span class="delbtn">-</span></td>
							</tr>
						{else}
							{foreach from=$testcaseArr key=k item=value}
								<tr class="per_record" id="{$k+1}">
									<td>{$k+1}</td><td>{html_options name="producttype{$k+1}" class=producttype options=$producttype selected=$value["producttype"]|default:""}<input type="hidden" class="short_input" value="{$value["producttype"]|default:""}"/></td>
									<td>{html_options name="testitem{$k+1}" class=testitem options=$testitem selected=$value["testitem"]|default:""}<input type="hidden" class="short_input" value="{$value["testitem"]|default:""}"/></td>
									<td><input class="long_input statusfile" name="statusfile{$k+1}" type="text" value="{$value["statefile"]|default:""}"/><input type="hidden" class="short_input" value="{$value["statefile"]|default:""}"/></td>
									<td><input class="short_input ports" name="ports{$k+1}" maxlength="4" type="text" value="{$value["ports"]|default:""}" /><input type="hidden" class="short_input" value="{$value["ports"]|default:""}"/></td>
									<td style="border-left:1px solid #DDDDDD;">{html_options name="channel{$k+1}" class=channel options=$one_tenArr selected=$value["channel"]|default:""}<input type="hidden" class="short_input" value="{$value["channel"]|default:""}"/></td>
									<td>{html_options name="trace{$k+1}" class=trace options=$one_tenArr selected=$value["trace"]|default:""}<input type="hidden" class="short_input" value="{$value["trace"]|default:""}"/></td>
									<td><input class="short_input start" name="start{$k+1}" type="text" value="{$value["startf"]|default:""}" /><input type="hidden" class="short_input" value="{$value["startf"]|default:""}"/></td>
									<td><input class="short_input stop" name="stop{$k+1}" type="text" value="{$value["stopf"]|default:""}" /><input type="hidden" class="short_input" value="{$value["stopf"]|default:""}"/></td>
									<td>{html_options name="mark{$k+1}" class=mark options=$one_tenArr selected=$value["mark"]|default:""}<input type="hidden" class="short_input" value="{$value["mark"]|default:""}"/></td>
									<td><input class="short_input min" name="min{$k+1}" type="text" value="{$value["min"]|default:""}" /><input type="hidden" class="short_input" value="{$value["min"]|default:""}"/></td>
									<td><input class="short_input max" name="max{$k+1}" type="text" value="{$value["max"]|default:""}" /><input type="hidden" class="short_input" value="{$value["max"]|default:""}"/></td>
									<td><span class="addbtn" onclick="add_record({$k+1})">+</span></td>
									<td style="border-right:1px solid #DDDDDD"><span class="delbtn" onclick="del_record({$k+1})">-</span></td>
								</tr>
							{/foreach}
						{/if}
					</table>
					{$CI->pagination->create_links()}
					<div style="text-align: right;">
						<input class="savebtn" type="submit" value="保存"/>
					</div>
					<input name="tatolcount" class="tatolcount" type="hidden" value="{$count}" />
					<input name="addcount" class="addcount" type="hidden" value="{$count+2}" />
					<input name="ids" class="ids" type="hidden" value="{$idStr}" />
				</form>
			</div>
		</div>
	</div>
</div>
<!--{/block}-->
