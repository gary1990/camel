<!--{extends file='defaultPage.tpl'}-->
<!--{block name=title}-->
<title>{$title}</title>
<!--{/block}-->
<!--{block name=style}-->
<link rel="stylesheet" type="text/css" href="{base_url()}resource/css/chosen.css" />
<style>
	.separate_line{
		height:3px;
	}
	.short_input{
		width:30px;
	}
	.long_input{
		width:55px;
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
	.chzn-container-single{
		vertical-align: middle;
	}
	.producttype{
		width:125px;
	}
	.testitem{
		width:120px;
	}
	.producttypeCondition{
		width:130px;
	}
	.beginstim,.endstim{
		margin:0px;
		padding:0px;
	}
	.beginstimunit,.endstimunit{
		width:38px;
		margin:0px;
		padding:0px;
	}
</style>
<!--{/block}-->
<!--{block name=script}-->
<script type="text/javascript" src="{base_url()}resource/js/chosen.jquery.js"></script>
<script type="text/javascript" src="{base_url()}resource/js/jquery.form.js"></script>
<script type="text/javascript">
	//在当前行下面添加一行 
	function add_record(thisid){
		var num = $(".addcount").val();
		var add_td = $(".per_record").html();
		var add_tr = '<tr class="per_record" id="'+num+'">'+add_td+'</tr>';
		add_tr = add_tr.replace(/per_record_hidden_/g,"").replace(/addproducttype/g,'producttype').replace(/addtestitem/g,'testitem');
		add_tr = add_tr.replace(/producttype_/g,'producttype'+num).replace(/testitem_/g,'testitem'+num);
		add_tr = add_tr.replace(/statusfile_/g,'statusfile'+num).replace(/ports_/g,'ports'+num);
		add_tr = add_tr.replace(/channel_/g,'channel'+num).replace(/trace_/g,'trace'+num);
		add_tr = add_tr.replace(/type_/g,'type'+num).replace(/beginstim_/g,'beginstim'+num);
		add_tr = add_tr.replace(/beginstimunit_/g,'beginstimunit'+num).replace(/endstim_/g,'endstim'+num);
		add_tr = add_tr.replace(/endstimunit_/g,'endstimunit'+num).replace(/beginresp_/g,'beginresp'+num).replace(/endresp_/g,'endresp'+num).replace(/selfid/g,num);
		$("#"+thisid).after(add_tr);
		$("#"+num+" select").eq(0).chosen();
		$("#"+num+" select").eq(1).chosen();
		$(".addcount").attr("value",parseInt(num)+1);
	}
	//删除当前行
	function del_record(thisid){
		$("#"+thisid).remove();
	}
	
	$(document).ready(function(){
		//可选择、输入、搜索的下拉列表
		$(".producttypeCondition").chosen();
		$(".producttype").chosen();
		$(".testitem").chosen();
		//产品型号下拉列表的判空
		$("body").delegate(".producttype", "change", function(){
			var producttype = $(this).val();
      		if(producttype == "")
      		{
      			alert("产品型号必填");
      		}
      		else
      		{
				//do nothing
      		}
    	});
    	//测试项下拉列表change事件
    	$("body").delegate(".testitem", "change", function(){
			var testitem = $(this).val();
      		if(testitem == "")
      		{
      			alert("测试项必填");
      		}
      		else
      		{
				//do nothing
      		}
    	});
    	//端口数输入框的为整数判断
		$("body").delegate(".ports", "blur", function(){
			var ports = $(this).val();
			//取整后和原来数比较
      		if(parseInt(ports) != ports)
      		{
      			alert("端口数为整数");
      			$(this).attr("value","");
      		}
    	});
    	//beginstim,endstim,beginresp,endresp输入框是否为数字的判断
    	$("body").delegate(".beginstim,.endstim,.beginresp,.endresp", "blur", function(){
			var start = $(this).val();
			//取整后和原来数比较
      		if(isNaN(start))
      		{
      			alert("BeginStim,EndStim,BeginResp,EndResp为数字");
      			$(this).attr("value","");
      			$(this).focus();
      		}
    	});
		//分页事件
		$(".locPage > a").click(function(e) {
			e.preventDefault();
			var url = $("#searchForm").attr('action') + $(this).attr('href');
			$("#searchForm").attr('action', url);
			$("#searchForm").submit();
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
					var case1 = $('[name="producttype'+i+'"]').val() == $('[name="producttype'+i+'"]').next().next().val();
					var case2 = $('[name="testitem'+i+'"]').val() == $('[name="testitem'+i+'"]').next().next().val();
					var case3 = $('[name="statusfile'+i+'"]').val() == $('[name="statusfile'+i+'"]').next().val();
					var case4 = $('[name="ports'+i+'"]').val() == $('[name="ports'+i+'"]').next().val();
					var case5 = $('[name="channel'+i+'"]').val() == $('[name="channel'+i+'"]').next().val();
					var case6 = $('[name="trace'+i+'"]').val() == $('[name="trace'+i+'"]').next().val();
					var case7 = $('[name="type'+i+'"]').val() == $('[name="type'+i+'"]').next().val();
					var case8 = $('[name="beginstim'+i+'"]').val() == $('[name="beginstim'+i+'"]').next().val();
					var case9 = $('[name="beginstimunit'+i+'"]').val() == $('[name="beginstimunit'+i+'"]').next().val();
					var case10 = $('[name="endstim'+i+'"]').val() == $('[name="endstim'+i+'"]').next().val();
					var case11 = $('[name="endstimunit'+i+'"]').val() == $('[name="endstimunit'+i+'"]').next().val();
					var case12 = $('[name="beginresp'+i+'"]').val() == $('[name="beginresp'+i+'"]').next().val();
					var case13 = $('[name="endresp'+i+'"]').val() == $('[name="endresp'+i+'"]').next().val();
					var changed = case1 && case2 && case3 && case4 && case5 && case6 && case7 && case8 && case9 && case10 && case12 && case13;
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
		//导出按钮点击事件
		$(".exportbtn").click(function(){
			var oldurl = $("#searchForm").attr('action');
			var url = oldurl+"/0/30/export";
			$("#searchForm").attr('action', url);
			$("#searchForm").submit();
			$("#searchForm").attr('action', oldurl);
		});
		
		
		//保存按钮点击时判断产品型号，测试项，端口数
		$(".savebtn").click(function(e){
			e.preventDefault();
			//取得当前记录条数
			var tatolcount = $(".addcount").val();
			//产品型号，测试项，端口数判空判空结果。默认为true，防止页面记录全部删除后无法比较结果
			var nullResult = true;
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
					var case4 = $('[name="statusfile'+i+'"]').val() != "";
					var empty = case1 && case2 && case3 && case4;
					if(empty)
					{
						nullResult = empty;
					}
					else
					{
						alert("产品型号，测试项，端口数,状态文件不为空！");
						nullResult = empty;
						break;
					}
				}
			}
			if(nullResult)
			{
				for(var i=tatolcount;i >= 1;i--)
				{
					var producttype = $('[name="producttype'+i+'"]').val();
					if(producttype == undefined)
					{
						continue;
					}
					else
					{
						var producttype = $('[name="producttype'+i+'"]').val();
						$('[name="producttype'+i+'"]').next().next().attr("value",producttype);
						var testitem = $('[name="testitem'+i+'"]').val();
						$('[name="testitem'+i+'"]').next().next().attr("value",testitem);
						var statusfile = $('[name="statusfile'+i+'"]').val();
						$('[name="statusfile'+i+'"]').next().attr("value",statusfile);
						var ports = $('[name="ports'+i+'"]').val();
						$('[name="ports'+i+'"]').next().attr("value",ports);
						var channel = $('[name="channel'+i+'"]').val();
						$('[name="channel'+i+'"]').next().attr("value",channel);
						var trace = $('[name="trace'+i+'"]').val();
						$('[name="trace'+i+'"]').next().attr("value",trace);
						var type = $('[name="type'+i+'"]').val();
						$('[name="type'+i+'"]').next().attr("value",type);
						var beginstim = $('[name="beginstim'+i+'"]').val();
						$('[name="beginstim'+i+'"]').next().attr("value",beginstim);
						var beginstimunit = $('[name="beginstimunit'+i+'"]').val();
						$('[name="beginstimunit'+i+'"]').next().attr("value",beginstimunit);
						var endstim = $('[name="endstim'+i+'"]').val();
						$('[name="endstim'+i+'"]').next().attr("value",endstim);
						var endstimunit = $('[name="endstimunit'+i+'"]').val();
						$('[name="endstimunit'+i+'"]').next().attr("value",endstimunit);
						var beginresp = $('[name="beginresp'+i+'"]').val();
						$('[name="beginresp'+i+'"]').next().attr("value",beginresp);
						var endresp = $('[name="endresp'+i+'"]').val();
						$('[name="endresp'+i+'"]').next().attr("value",endresp);
					}
				}
				var options = { 
			        success:function (res){
			        		//改变要删除的记录的ID
			        		$(".ids").attr("value",res);
			        		alert("保存成功！"); 
			        	}
			    };
				$('#locForm').ajaxSubmit(options);
			}
		});
		
		//导入按钮点击事件，触发“浏览”文件输入框点击事件
		$(".importbtn").click(function(e){
			
			e.preventDefault();
			var options = { 
			        success:function (res){ 
			        		alert(res); 
			        	}
			    }; 
			$("#importForm").ajaxSubmit(options);
			
			//$("#importForm").submit();
		});
	});
</script>
<!--{block name=subScript}-->
<!--{/block}-->
<!--{/block}-->
<!--{block name=body}-->
<div class="span-64 last subitems">
	<div class="prepend-1 span-63">
		<form method="post" id="searchForm" action="{site_url()}/producttestcase/index">
			产品型号：
			{html_options name=producttypesearch class="producttypeCondition" options=$producttypeSearch selected=$smarty.post.producttypesearch|default:''}
			&nbsp;&nbsp;&nbsp;
			<input class="searchbtn" type="submit" value="查看" />
		</form>
		<div style="text-align: right;">
			<form id="importForm" action="{site_url()}/producttestcase/importCsvFile" method="post" enctype="multipart/form-data">
				<input type="file" name="file" id="file"/>
				<input class="importbtn" type="submit" value="导入"/>
				<input class="exportbtn" type="button" value="导出" />
			</form>
		</div>
		<hr class="separate_line">
		<div>
			<div>
				<form name="locForm" id="locForm" method="post" action="{site_url('producttestcase/del_ins/')}">
					<table>
						<tr>
							<th>产品型号</th>
							<th>测试项</th><th>状态文件</th>
							<th width="45px">端口数</th><th style="border-left:1px solid #DDDDDD;">Channel</th>
							<th>Trace</th><th>Type</th>
							<th width="90px;">BeginStim (Hz/S)</th>
							<th width="120px;">EndStim (Hz/S)</th>
							<th>Begin Resp</th><th>End Resp</th>
							<th>&nbsp;</th><th style="border-right:1px solid #DDDDDD">&nbsp;</th>
						</tr>
						<tr class="per_record per_record_hidden">
							<td>{html_options class="addproducttype" name=producttype_ options=$producttype}<input type="hidden" class="short_input" value=""/></td>
							<td>{html_options class="addtestitem" name=testitem_ options=$testitem}<input type="hidden" class="short_input" value=""/></td>
							<td><input class="long_input statusfile" name="statusfile_" type="text"/><input type="hidden" class="short_input" value=""/></td>
							<td><input class="short_input ports" name="ports_" maxlength="4" type="text" /><input type="hidden" class="short_input" value=""/></td>
							<td style="border-left:1px solid #DDDDDD;">{html_options name=channel_ class=channel options=$one_tenArr}<input type="hidden" class="short_input" value="1"/></td>
							<td>{html_options name=trace_ class=trace options=$one_tenArr}<input type="hidden" class="short_input" value="1"/></td>
							<td>
								{html_options name=type_ class=type options=$type}
								<input type="hidden" class="short_input" value=""/>
							</td>
							<td>
								<input class="short_input beginstim" name="beginstim_" type="text" value="" />
								<input type="hidden" class="short_input" value=""/>
								{html_options name=beginstimunit_ class=beginstimunit options=$unit}
								<input type="hidden" class="short_input" value=""/>
							</td>
							<td>
								<input class="short_input endstim" name="endstim_" type="text" value="" />
								<input type="hidden" class="short_input" value=""/>
								{html_options name=endstimunit_ class=endstimunit options=$unit}
								<input type="hidden" class="short_input" value=""/>
							</td>
							<td>
								<input class="short_input beginresp" name="beginresp_" type="text" value="" />
								<input type="hidden" class="short_input" value=""/>
							</td>
							<td>
								<input class="short_input endresp" name="endresp_" type="text" value="" />
								<input type="hidden" class="short_input" value=""/>
							</td>
							<td><span class="addbtn" onclick="add_record(selfid)">+</span></td>
							<td><span class="delbtn" onclick="del_record(selfid)">-</span></td>
						</tr>
						{if count($testcaseArr) eq 0}
							<tr class="per_record" id="1">
								<td>{html_options name=producttype1 class=producttype options=$producttype}<input type="hidden" class="short_input" value=""/></td>
								<td>{html_options name=testitem1 class=testitem options=$testitem}<input type="hidden" class="short_input" value=""/></td>
								<td><input class="long_input statusfile" name="statusfile1" type="text" /><input type="hidden" class="short_input" value=""/></td>
								<td><input class="short_input ports" name="ports1" maxlength="4" type="text" /><input type="hidden" class="short_input" value=""/></td>
								<td style="border-left:1px solid #DDDDDD;">{html_options name=channel1 class=channel options=$one_tenArr}<input type="hidden" class="short_input" value="1"/></td>
								<td>{html_options name=trace1 class=trace options=$one_tenArr}<input type="hidden" class="short_input" value="1"/></td>
								<td>
									{html_options name="type1" class=type options=$type}
									<input type="hidden" class="short_input" value=""/>
								</td>
								<td>
									<input class="short_input beginstim" name="beginstim1" type="text" value="" />
									<input type="hidden" class="short_input" value=""/>
									{html_options name="beginstimunit1" class=beginstimunit options=$unit}
									<input type="hidden" class="short_input" value=""/>
								</td>
								<td>
									<input class="short_input endstim" name="endstim1" type="text" value="" />
									<input type="hidden" class="short_input" value=""/>
									{html_options name="endstimunit1" class=endstimunit options=$unit}
									<input type="hidden" class="short_input" value=""/>
								</td>
								<td>
									<input class="short_input beginresp" name="beginresp1" type="text" value="" />
									<input type="hidden" class="short_input" value=""/>
								</td>
								<td>
									<input class="short_input endresp" name="endresp1" type="text" value="" />
									<input type="hidden" class="short_input" value=""/>
								</td>
								<td><span class="addbtn" onclick="add_record(1)">+</span></td>
								<td><span class="delbtn">-</span></td>
							</tr>
						{else}
							{foreach from=$testcaseArr key=k item=value}
								<tr class="per_record" id="{$k+1}">
									<td>{html_options name="producttype{$k+1}" class=producttype options=$producttype selected=$value["producttype"]|default:""}<input type="hidden" class="short_input" value="{$value["producttype"]|default:""}"/></td>
									<td>{html_options name="testitem{$k+1}" class=testitem options=$testitem selected=$value["testitem"]|default:""}<input type="hidden" class="short_input" value="{$value["testitem"]|default:""}"/></td>
									<td><input class="long_input statusfile" name="statusfile{$k+1}" type="text" value="{$value["statefile"]|default:""}" /><input type="hidden" class="short_input" value="{$value["statefile"]|default:""}"/></td>
									<td><input class="short_input ports" name="ports{$k+1}" maxlength="4" type="text" value="{$value["ports"]|default:""}" /><input type="hidden" class="short_input" value="{$value["ports"]|default:""}"/></td>
									<td style="border-left:1px solid #DDDDDD;">{html_options name="channel{$k+1}" class=channel options=$one_tenArr selected=$value["channel"]|default:""}<input type="hidden" class="short_input" value="{$value["channel"]|default:""}"/></td>
									<td>{html_options name="trace{$k+1}" class=trace options=$one_tenArr selected=$value["trace"]|default:""}<input type="hidden" class="short_input" value="{$value["trace"]|default:""}"/></td>
									<td>
										{html_options name="type{$k+1}" class=type options=$type selected=$value["type"]|default:""}
										<input type="hidden" class="short_input" value="{$value["type"]|default:""}"/>
									</td>
									<td>
										{if $value["beginstim"]|substr:-1 eq '#'}
											<input class="short_input beginstim" name="beginstim{$k+1}" type="text" value="{$value["beginstim"]|substr:0:-1|default:""}" />
											<input type="hidden" class="short_input" value="{$value["beginstim"]|substr:0:-1|default:""}"/>
											{html_options name="beginstimunit{$k+1}" class=beginstimunit options=$unit selected=""}
											<input type="hidden" class="short_input" value=""/>
										{else}
											<input class="short_input beginstim" name="beginstim{$k+1}" type="text" value="{$value["beginstim"]|substr:0:-2|default:""}" />
											<input type="hidden" class="short_input" value="{$value["beginstim"]|substr:0:-2|default:""}"/>
											{html_options name="beginstimunit{$k+1}" class=beginstimunit options=$unit selected=$value["beginstim"]|substr:-1|default:""}
											<input type="hidden" class="short_input" value="{$value["beginstim"]|substr:-1|default:""}"/>
										{/if}
									</td>
									<td>
										{if $value["endstim"]|substr:-1 eq '#'}
											<input class="short_input endstim" name="endstim{$k+1}" type="text" value="{$value["endstim"]|substr:0:-1|default:""}" />
											<input type="hidden" class="short_input" value="{$value["endstim"]|substr:0:-1|default:""}"/>
											{html_options name="endstimunit{$k+1}" class=endstimunit options=$unit selected=""}
											<input type="hidden" class="short_input" value=""/>
										{else}
											<input class="short_input endstim" name="endstim{$k+1}" type="text" value="{$value["endstim"]|substr:0:-2|default:""}" />
											<input type="hidden" class="short_input" value="{$value["endstim"]|substr:0:-2|default:""}"/>
											{html_options name="endstimunit{$k+1}" class=endstimunit options=$unit selected=$value["endstim"]|substr:-1|default:""}
											<input type="hidden" class="short_input" value="{$value["endstim"]|substr:-1|default:""}"/>
										{/if}
									</td>
									<td>
										<input class="short_input beginresp" name="beginresp{$k+1}" type="text" value="{$value["beginresp"]|default:""}" />
										<input type="hidden" class="short_input" value="{$value["beginresp"]|default:""}"/>
									</td>
									<td>
										<input class="short_input endresp" name="endresp{$k+1}" type="text" value="{$value["endresp"]|default:""}" />
										<input type="hidden" class="short_input" value="{$value["endresp"]|default:""}"/>
									</td>
									<td><span class="addbtn" onclick="add_record({$k+1})">+</span></td>
									<td><span class="delbtn" onclick="del_record({$k+1})">-</span></td>
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
