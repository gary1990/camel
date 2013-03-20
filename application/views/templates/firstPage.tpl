<!--{extends file='userPage.tpl'}-->
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
<!--{block name=subScript}-->
<!--{foreach $js_files as $file}-->
<script src="{$file}"></script>
<!--{/foreach}-->
{if $diagram|default:false == true}
<script src="{base_url()}resource/js/highCharts/highstock.js"></script>
<script src="{base_url()}resource/js/highCharts/modules/exporting.js"></script>
{/if}
<script>
	/*{if $title == '测试员'}*/
	$(document).ready(function()
	{
		$(".performance").parent().click(function(e)
		{
			e.preventDefault();
			var tmpStr = $(this).attr('href');
			var tmpIndex = tmpStr.substring((tmpStr.lastIndexOf('/') + 1));
			$.getJSON("{site_url('csrygl/testerPerformance')}" + "/" + tmpIndex, function(jsonData)
			{
				if (jsonData['length'] == 0)
				{
					return;
				}
				// Create the chart
				window.chart = new Highcharts.StockChart(
				{
					chart :
					{
						renderTo : 'diagram1'
					},
					rangeSelector :
					{
						selected : 1
					},
					title :
					{
						text : jsonData['testerName'] + '的绩效'
					},
					series : [
					{
						name : '测试绩效',
						data : jsonData['data']
					}]
				});
			});
		});
		$(".workload").parent().click(function(e)
		{
			e.preventDefault();
			var tmpStr = $(this).attr('href');
			var tmpIndex = tmpStr.substring((tmpStr.lastIndexOf('/') + 1));
			$.getJSON("{site_url('csrygl/testerWorkLoad')}" + "/" + tmpIndex, function(data)
			{
				// split the data set into ohlc and volume
				var ohlc = [], volume = [], dataLength = data['length'];
				for ( i = 0; i < dataLength; i++)
				{
					ohlc.push([data['data'][i][0], // the date
					data['data'][i][1], // open
					data['data'][i][2], // high
					data['data'][i][3], // low
					data['data'][i][4] // close
					]);
					volume.push([data['data'][i][0], // the date
					data['data'][i][5] // the volume
					])
				}
				// set the allowed units for data grouping
				var groupingUnits = [['week', // unit name
				[1] // allowed multiples
				], ['month', [1, 2, 3, 4, 6]]];
				window.chart = new Highcharts.StockChart(
				{
					chart :
					{
						renderTo : 'diagram1',
						alignTicks : false
					},
					rangeSelector :
					{
						selected : 1
					},
					title :
					{
						text : data['testerName'] + '的工作时间'
					},
					yAxis : [
					{
						title :
						{
							text : 'OHLC'
						},
						height : 200,
						lineWidth : 2
					},
					{
						title :
						{
							text : 'Volume'
						},
						top : 300,
						height : 100,
						offset : 0,
						lineWidth : 2
					}],
					series : [
					{
						type : 'candlestick',
						name : '工作时间',
						data : ohlc,
						dataGrouping :
						{
							units : groupingUnits
						}
					},
					{
						type : 'column',
						name : 'Volume',
						data : volume,
						yAxis : 1,
						dataGrouping :
						{
							units : groupingUnits
						}
					}]
				});
			});
		});
	});
	/*{/if}*/
</script>
<!--{/block}-->
<!--{block name=subBody}-->
<!--{$output}-->
<div id="diagram1" class="span-64 last">
</div>
<!--{/block}-->
