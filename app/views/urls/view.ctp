<?php 
if(@$url) 
{
	?>
<div class="urls view">
<h2><?php  __('Url');?></h2>
	<dl><?php $i = 0; $class = ' class="altrow"';?>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('LongUrl'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $url['Url']['longUrl']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('ShortUrl'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo "http://ow.gs/" .$url['Url']['shortUrl']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Created'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $url['Url']['created']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Modified'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $url['Url']['modified']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Nb de clicks :'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $counter['NbAppel']; ?>
			&nbsp;
		</dd>		
	</dl>
</div>
<div class="actions">
	<h3><?php __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('Edit Url', true), array('action' => 'edit', $url['Url']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('Delete Url', true), array('action' => 'delete', $url['Url']['id']), null, sprintf(__('Are you sure you want to delete # %s?', true), $url['Url']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('List Urls', true), array('action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Url', true), array('action' => 'add')); ?> </li>
	</ul>
</div>
<h3><?php __('Périodes'); ?></h3>
<ul>
	<li><a id="switcher-day" href="#" class="toggler conteneurDay">Jour</a></li>
	<li><a id="switcher-week" href="#" class="toggler conteneurWeek">Semaine</a></li>
	<li><a id="switcher-month" href="#" class="toggler conteneurMonth">Mois</a></li>
</ul>
<div id='conteneurTypeVisitor'></div>
<div id='conteneurDay' class="cache"></div>
<div id='conteneurWeek' class="cache"></div>
<div id='conteneurMonth' class="cache"></div>

<?php $this->Javascript->link(array("jquery/jquery-1.4.4.min.js","jquery/jquery.jqplot", "jquery/plugins/jqplot.donutRenderer.js", "jquery/plugins/jqplot.dateAxisRenderer.js", "jquery/plugins/jqplot.categoryAxisRenderer.min.js", "jquery/plugins/jqplot.canvasAxisTickRenderer.min.js", "jquery/plugins/jqplot.canvasTextRenderer.min.js"), false); ?>
<?php echo $this->Html->css("/js/jquery/jquery.jqplot.css")?>
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
<?php 
}
else
{
	echo "URL introuvable";
}
?>