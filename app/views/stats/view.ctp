<?php $this->Javascript->link(array("jquery/jquery-1.4.4.min.js","jquery/jquery.jqplot", "jquery/plugins/jqplot.donutRenderer.js", "jquery/plugins/jqplot.dateAxisRenderer.js", "jquery/plugins/jqplot.categoryAxisRenderer.min.js", "jquery/plugins/jqplot.canvasAxisTickRenderer.min.js", "jquery/plugins/jqplot.canvasTextRenderer.min.js"), false); ?>
<?php echo $this->Html->css("/js/jquery/jquery.jqplot.css")?>
<div class="users view">
<h2><?php  __('Stat');?></h2>
	<tr><?php $i = 0; $class = ' class="altrow"';?>
		<th<?php if ($i % 2 == 0) echo $class;?>><?php __('Id :'); ?></th>
		<td<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $stat['Stat']['id']; ?>
			&nbsp;
		</td>
		<th<?php if ($i % 2 == 0) echo $class;?>><?php __('Créée par l\'adrIp'); ?></th>
		<td<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $stat['Stat']['adrIp']; ?>
			&nbsp;
		</td>
		<th<?php if ($i % 2 == 0) echo $class;?>><?php __('Identifiant de l\'url :'); ?></th>
		<td<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $stat['Stat']['url_id']; ?>
			&nbsp;
		</td>
		<th<?php if ($i % 2 == 0) echo $class;?>><?php __('Nb d\'appel :'); ?></th>
		<td<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $counter['NbAppel']; ?>
			&nbsp;
		</td>		
	</tr>
</div>
<ul>
	<li><a id="switcher-day" href="#" class="toggler conteneurDay">Jour</a></li>
	<li><a id="switcher-week" href="#" class="toggler conteneurWeek">Semaine</a></li>
	<li><a id="switcher-month" href="#" class="toggler conteneurMonth">Mois</a></li>
</ul>
<div id='conteneurTypeVisitor'></div>
<div id='conteneurDay' class="cache"></div>
<div id='conteneurWeek' class="cache"></div>
<div id='conteneurMonth' class="cache"></div>
<div class="actions">
	<h3><?php __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('List Stats', true), array('action' => 'index')); ?> </li>
	</ul>
</div>
<?php debug($url); ?>
<script type="text/javascript">
	$().ready(function() {
		$('.cache').css('visibility','hidden');
		$('.toggler').click(function() {
			$('.cache:visible').hide();
			var caller = $(this).attr('class').replace('toggler ', '');
			$('#'+caller).css({'visibility':'visible', 'display':'block'});
		});
	});
</script>
<script type="text/javascript" language="javascript">
$(document).ready(
function()
{	
	/*$('#switcher-day').bind('click',
	function()
	{		
		$('#conteneurWeek').addClass('hidden');
		$('#conteneurWeek').removeClass('selected');
		$('#conteneurTypeVisitor').addClass('hidden');
		$('#conteneurTypeVisitor').removeClass('selected');
		$('#conteneurMonth').addClass('hidden');
		$('#conteneurMonth').removeClass('selected');
		$('#conteneurDay').addClass('selected');
	});
	
	$('#switcher-week').bind('click',
	function()
	{		
		$('#conteneurDay').addClass('hidden');
		$('#conteneurDay').removeClass('selected');
		$('#conteneurTypeVisitor').addClass('hidden');
		$('#conteneurTypeVisitor').removeClass('selected');
		$('#conteneurMonth').addClass('hidden');
		$('#conteneurMonth').removeClass('selected');
		$('#conteneurWeek').addClass('selected');
	});
	
	$('#switcher-month').bind('click',
	function()
	{
		$('#conteneurWeek').addClass('hidden');
		$('#conteneurWeek').removeClass('selected');
		$('#conteneurTypeVisitor').addClass('hidden');
		$('#conteneurTypeVisitor').removeClass('selected');
		$('#conteneurDay').addClass('hidden');
		$('#conteneurDay').removeClass('selected');
		$('#conteneurMonth').addClass('selected');
	});
	
	$('#switcher-typeVisitor').bind('click',
	function()
	{		
		$('#conteneurWeek').addClass('hidden');
		$('#conteneurWeek').removeClass('selected');
		$('#conteneurMonth').addClass('hidden');
		$('#conteneurMonth').removeClass('selected');
		$('#conteneurDay').addClass('hidden');
		$('#conteneurDay').removeClass('selected');
		$('#conteneurTypeVisitor').addClass('selected');
	});*/
	 
	var s1 = [['Visiteurs réguliers',<?php echo $counter['regular']; ?>], ['Nouveaux visiteurs',<?php echo $counter['new']; ?>]];
	plot2 = $.jqplot('conteneurTypeVisitor', [s1], 
	{
	    seriesDefaults: 
		{
	        renderer:$.jqplot.DonutRenderer,
	        rendererOptions:
			{
	            startAngle: -90,
				padding: 10,
				sliceMargin: 3,
				innerDiameter: 0,
				shadowOffset: false,
				highlightMouseOver: true,
				dataLabels: 'percent',
				showDataLabels: true
	         }
	     },
		title: 
		{
	        text: 'type de visiteurs',  
	        show: true,
	    },
		legend:
		{
			show: true,
			location:'w',
		},
		grid: 
		{
	        drawGridLines: true,        // wether to draw lines across the grid or not.
	        background: 'grey',      	// CSS color spec for background color of grid.
	        borderColor: '#04FF00',     // CSS color spec for border around grid.
	        borderWidth: 2.0,           // pixel width of border around grid.
	        shadow: false,               // draw a shadow for grid.
	        renderer: $.jqplot.CanvasGridRenderer,  // renderer to use to draw the grid.
	        rendererOptions: {}         // options to pass to the renderer.  Note, the default
	                                    // CanvasGridRenderer takes no additional options.
	    },
	});
	
	// initialisation des données
	var serie = <?php echo $counter['ClicksOfTheDay']; ?>;

	courbe = $.jqplot('conteneurDay', [serie],
	{ 
		title: 
		{
	        text: 'Nombre de visiteurs sur un jour',  
	        show: true,
	    },
		legend: 
		{
        	show: true,
        	location: 'se'
    	},
		axes:
		{
			xaxis:
			{
				renderer:$.jqplot.DateAxisRenderer,
				tickOptions:
				{
					formatString: '%H'
				},
				ticks: <?php echo $date['DatesOfTheDay']; ?>
			},
			yaxis:
			{
				min:0,
				autoscale:true,
				tickInterval:5,
			},
		},
		
	    grid: 
		{
	        drawGridLines: true,        // wether to draw lines across the grid or not.
	        gridLineColor: '#dddddd',   // Color of the grid lines.
	        background: 'grey',      	// CSS color spec for background color of grid.
	        borderColor: '#04FF00',     // CSS color spec for border around grid.
	        borderWidth: 2.0,           // pixel width of border around grid.
	        renderer: $.jqplot.CanvasGridRenderer,  // renderer to use to draw the grid.
	        rendererOptions: 
			{
				dataLabels: 'value',
				showDataLabels: true		
			}
	    },
		series:
		[
			{
				color:'#0085cc',
				markerOptions:
				{
					style:'x'
				}
			}
		],	
	});
	
	// initialisation des données
	var serie = <?php echo $counter['ClicksOfTheWeek']; ?>

	courbe = $.jqplot('conteneurWeek', [serie],
	{ 
		title: 
		{
	        text: 'Nombre de visiteurs sur une semaine',  
	        show: true,
	    },
		legend: 
		{
        	show: true,
        	location: 'se'
    	},	
		axes:
		{
			xaxis:
			{
				renderer:$.jqplot.DateAxisRenderer,
				tickOptions:
				{
					formatString: '%d'
				},
				ticks: <?php echo $date['DatesOfTheWeek']; ?>
			},
			yaxis:
			{
				min:0,
				autoscale:true,
				tickInterval:5,
			},
		},
		
	    grid: 
		{
	        drawGridLines: true,        // wether to draw lines across the grid or not.
	        gridLineColor: '#dddddd',   // Color of the grid lines.
	        background: 'grey',      	// CSS color spec for background color of grid.
	        borderColor: '#04FF00',     // CSS color spec for border around grid.
	        borderWidth: 2.0,           // pixel width of border around grid.
	        renderer: $.jqplot.CanvasGridRenderer,  // renderer to use to draw the grid.
	        rendererOptions: 
			{
				dataLabels: 'value',
				showDataLabels: true		
			}
	    },
		series:
		[
			{
				color:'#0085cc',
				markerOptions:
				{
					style:'x'
				}
			}
		],	
	});
	
	// initialisation des données
	var serie = <?php echo $counter['ClicksOfTheMonth']; ?>

	courbe = $.jqplot('conteneurMonth', [serie],
	{ 
		title: 
		{
	        text: 'Nombre de visiteurs sur un mois',  
	        show: true,
	    },
		legend: 
		{
        	show: true,
        	location: 'se'
    	},
		axes:
		{
			xaxis:
			{
				renderer:$.jqplot.DateAxisRenderer,
				tickOptions:
				{
					formatString: '%d'
				},
				ticks: <?php echo $date['DatesOfTheMonth']; ?>,
			},
			yaxis:
			{
				min:0,
				autoscale:true,
				tickInterval:5,
			},
		},
		
	    grid: 
		{
	        drawGridLines: true,        // wether to draw lines across the grid or not.
	        gridLineColor: '#dddddd',   // Color of the grid lines.
	        background: 'grey',      	// CSS color spec for background color of grid.
	        borderColor: '#04FF00',     // CSS color spec for border around grid.
	        borderWidth: 2.0,           // pixel width of border around grid.
	        renderer: $.jqplot.CanvasGridRenderer,  // renderer to use to draw the grid.
	        rendererOptions: 
			{
				dataLabels: 'value',
				showDataLabels: true		
			}
	    },
		series:
		[
			{
				color:'#0085cc', 
				markerOptions:
				{
					style:'x'
				}
			}
		],	
	});	
		
});
</script>