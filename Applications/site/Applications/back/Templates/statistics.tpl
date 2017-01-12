<?php
/**
 * @name	/statistics.tpl
 * @version	1.0 2012-09-18 12:09:31
 *
 * @usage:	<?php $this->includeTpl('/statistics.tpl'); ?>
 */
?>
<?php 
$this->includeTpl('includes/header.tpl'); 
$this->includeTpl('includes/topbar.tpl'); 

/**
 * @desc	Hier opent de content (grids, forms etc)
 */
?>
<div id='content-container'>
	<h1><?=$this->Titel?></h1>
	<?=isset($this->Subtitel) && $this->Subtitel ? '<h3>'.$this->Subtitel.'</h3>' : ''?>	

	
<?php $this->includeTpl('includes/content/blocks/includes/header.tpl', array('width' => 'tweederde')); ?>
<div id='chart-website1'></div>
<script type="text/javascript">
$(function () {
	Highcharts.theme = {
		chart: {
			backgroundColor: '#f1f1f1'
		},
		colors: [
			'#009900', // eerste lijn 
			'#006600', // grote pie (unieke visitors)
			'#00cc00' // kleine pie
		]	
	};
	
	
		
var highchartsOptions = Highcharts.setOptions(Highcharts.theme);		
		
    var chart1;
    $(document).ready(function() {
    
		Highcharts.getOptions().colors = $.map(Highcharts.getOptions().colors, function(color) {
		    return {
		        radialGradient: { cx: 0.5, cy: 0.3, r: 0.7 },
		        stops: [
		            [0, color],
		            [1, Highcharts.Color(color).brighten(-0.3).get('rgb')] // darken
		        ]
		    };
		});
		    
        chart1 = new Highcharts.Chart({
            chart: {
                renderTo: 'chart-website1',
                type: 'line',
                spacingBottom: 30
            },
            
            title: {
                text: '<?=$this->VisitsHeader?>',
                x: -20 //center
            },
            subtitle: {
                text: '<?=$this->VisitsSubheader?>',
                x: -20
            },
            xAxis: {
                categories: ['<?=join("', '", array_keys($this->Visits))?>'],
                labels: {
                	step: 5
            	}
            },
            yAxis: {
            	allowDecimals : false,
                title: {
                    text: '<?=$this->VisitsHeaderXAxis?>'
                },
                plotLines: [{
                    value: 0,
                    width: 1,
                    color: '#808080'
                }],
                min: 0
            },
            tooltip: {
                formatter: function() {
                        return '<b>'+ this.series.name +'</b><br/>'+
                        this.x +': '+ this.y;
                }
            },            
            series: [{
            	type : 'spline',
                name: 'Totaal aantal bezoeken',
                data: [<?=join(", ", $this->Visits)?>]
            }]
        });
        
		// Build the chart
        chart2 = new Highcharts.Chart({
            chart: {
                renderTo: 'chart-website2',
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false,
                type : 'pie',
                spacingBottom: 30
            },
            title: {
                text: '<?=$this->UniqueVersusReturningVisitsHeader?>'
            },
			subtitle: {
                text: '<?=$this->UniqueVersusReturningVisitsSubheader?>'
            },            
           	tooltip: {
        	    pointFormat: '<b>{point.percentage}%</b>',
            	percentageDecimals: 2
            },
            plotOptions: {
            	pie: {
                    allowPointSelect: false,
                    cursor: 'pointer'
                }                                
            },
      		series: [{
        	    data: [
            		{
            			name : 'Nieuwe bezoekers',
            			y : <?=$this->UniqueVisitsPercentage?>           			            			
            		},
					{
                        name: 'Terugkerende bezoekers',
                        y: <?=$this->ReturningVisitsPercentage?> ,
                        sliced: true,
                        selected: true                       
                    }
            	],
           	 	size: 160,
           	 	showInLegend: true,
				dataLabels: {
                	enabled: true,
					formatter: function() {
                    	return this.point.percentage+'%';
                    }                       
                }           	 	
            }]
        });        
    });
    
});
</script>
<?php $this->includeTpl('includes/content/blocks/includes/footer.tpl'); ?>

<?php $this->includeTpl('includes/content/blocks/includes/header.tpl', array('width' => 'eenderde')); ?>
<div id='chart-website2'></div>
<?php $this->includeTpl('includes/content/blocks/includes/footer.tpl'); ?>

<?php $this->includeTpl('includes/content/blocks/includes/header.tpl', array('width' => 'eenvierde')); ?>
<h4>Totaal aantal bezoeken</h4>
<p><strong><?=$this->TotalVisits?$this->TotalVisits:'0'?></strong></p>
<?php $this->includeTpl('includes/content/blocks/includes/footer.tpl'); ?>

<?php $this->includeTpl('includes/content/blocks/includes/header.tpl', array('width' => 'eenvierde')); ?>
<h4>Totaal aantal bezoekers</h4>
<p><strong><?=$this->TotalVisits?$this->TotalVisitors:'0'?></strong></p>
<?php $this->includeTpl('includes/content/blocks/includes/footer.tpl'); ?>

<?php $this->includeTpl('includes/content/blocks/includes/header.tpl', array('width' => 'eenvierde')); ?>
<h4>Unieke bezoekers</h4>
<p><strong><?=$this->UniqueVisitors?$this->UniqueVisitors:'0'?></strong> <span class='comment'>(<?=$this->UniqueVisitsPercentage?>% van totaal aantal bezoekers)</span></p>
<?php $this->includeTpl('includes/content/blocks/includes/footer.tpl'); ?>

<?php $this->includeTpl('includes/content/blocks/includes/header.tpl', array('width' => 'eenvierde')); ?>
<h4>Terugkerende bezoekers</h4>
<p><strong><?=$this->ReturningVisitors?$this->ReturningVisitors:'0'?></strong> <span class='comment'>(<?=$this->ReturningVisitsPercentage?>% van totaal aantal bezoekers)</span></p>
<?php $this->includeTpl('includes/content/blocks/includes/footer.tpl'); ?>

<div class='spacer'>&nbsp;</div>
<h2><?=$this->GridTitel?></h2>
<?php if($this->GridSubTitel): ?>
<h3><?=$this->GridSubTitel?></h3>
<?php endif; ?>

<?php if($this->GridSubLink): ?>
<a class='margin' href='<?=$this->GridSubLink;?>'>Terug naar het landenoverzicht</a>
<?php endif; ?>

<?php
if($this->isTrue('SearchForm'))
	$this->includeTpl('includes/content/blocks/search.tpl'); 

?>	 

<?php
if($this->isTrue('DataSet'))
	$this->includeTpl('includes/content/blocks/grid.tpl', array('dataset' => $this->DataSet, 'dataheader' => $this->DataHeader,'buttons' => $this->Buttons)); 
?>

</div>
	
<?php
$this->includeTpl('includes/leftmenu.tpl'); 
$this->includeTpl('includes/credits.tpl'); 
$this->includeTpl('includes/footer.tpl'); 
?>