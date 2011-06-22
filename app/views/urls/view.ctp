<div id="header">
	<h1 class="logo"><a href="/">Organic Web gets shorter</a></h1>
	<h1>Détails de l'url</h1>
</div>
<?php if($group_id == 1) :?>
<ul id="nav">
	<li>Urls
		<ul>
			<li><a href="/urls/add">Ajouter une url</a></li>
			<li><a href="/urls/index">Lister les urls</a></li>
		</ul>
	</li>
	<li>Utilisateurs
		<ul>
			<li><a href="/users/add">Ajouter un utilisateur</a></li>
			<li><a href="/users/index">Lister les utilisateurs</a></li>
			<li><a href="/users/logout">Déconnexion</a></li>
		</ul>
	</li>
	<li>Groupes
		<ul>
			<li><a href="/groups/index">Lister les groupes</a></li>
		</ul>
	</li>
</ul>
<?php endif; ?>
<?php if(@$url) {
	?>
<div id="view_urls">
	<table>
		<tr>
			<th>Url de départ</th>
			<th>Url réduite</th>		
			<th>Crée le :</th>
			<th>Modifié le :</th>
			<th>Nombre de clicks</th>
		</tr>
		<tr>		
			<td><?php echo $url['Url']['longUrl']; ?>&nbsp;</td>
			<td><?php echo $this->Html->link("ow.gs/" .$url['Url']['shortUrl'], 'http://ow.gs/'.$url['Url']['shortUrl']); ?></td>
			<td><?php echo $url['Url']['created']; ?>&nbsp;</td>
			<td><?php echo $url['Url']['modified']; ?>&nbsp;</td>
			<td><?php echo $counter['NbAppel']; ?>&nbsp;</td>		
		</tr>
	</table>
</div>
<div id="link"><?php echo $this->Html->link('Voir mes urls réduites', array('action'=>'index')); ?></div>
<div id="periodes_urls">
	<h3>Périodes</h3>
	<ul>
		<tr>
			
			<li><a id="switcher-day" href="#" class="toggler conteneurDay">Jour</a></li>
			<li><a id="switcher-week" href="#" class="toggler conteneurWeek">Semaine</a></li>
			<li><a id="switcher-month" href="#" class="toggler conteneurMonth">Mois</a></li>
		</tr>
	</ul>
</div>
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
				innerDiameter: 70,
				shadowOffset: false,
				highlightMouseOver: true,
				dataLabels: 'percent',
				showDataLabels: true,
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
	        background: '#9D9386',      	// CSS color spec for background color of grid.
	        borderColor: '#9D9386',     // CSS color spec for border around grid.
	        borderWidth: 2.0,           // pixel width of border around grid.
	        shadow: false,               // draw a shadow for grid.
	        renderer: $.jqplot.CanvasGridRenderer,  // renderer to use to draw the grid.
	        rendererOptions: {}         // options to pass to the renderer.  Note, the default
	                                    // CanvasGridRenderer takes no additional options.
	    },
		seriesColors: [ "#951f16", "#675f55"]
	});
	
	// initialisation des données
	var serie = <?php echo $counter['ClicksOfTheDay']; ?>;

	courbe = $.jqplot('conteneurDay', [serie],
	{ 
		title: 
		{
	        text: 'Nombre de visiteurs depuis un jour',  
	        show: true,
	    },
		legend: 
		{
        	show: false,
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
	        gridLineColor: '#5D5144',   // Color of the grid lines.
	        background: '#9D9386',      	// CSS color spec for background color of grid.
	        borderColor: '#9D9386',     // CSS color spec for border around grid.
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
				color:'#951f16',
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
	        text: 'Nombre de visiteurs depuis une semaine',  
	        show: true,
	    },
		legend: 
		{
        	show: false,
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
	        gridLineColor: '#5D5144',   // Color of the grid lines.
	        background: '#9D9386',      	// CSS color spec for background color of grid.
	        borderColor: '#9D9386',     // CSS color spec for border around grid.
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
				color:'#951f16',
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
        	show: false,
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
	        gridLineColor: '#5D5144',   // Color of the grid lines.
	        background: '#9D9386',      	// CSS color spec for background color of grid.
	        borderColor: '#9D9386',     // CSS color spec for border around grid.
	        borderWidth: 2.0,
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
				color:'#951f16', 
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