<!--[if lt IE 9]><script language="javascript" type="text/javascript" src="./Lib/jqplot/excanvas.js"></script><![endif]-->
<script language="javascript" type="text/javascript" src="./Lib/jqplot/jquery.jqplot.min.js"></script>
<link rel="stylesheet" type="text/css" href="./Lib/jqplot/jquery.jqplot.css" />
<script type="text/javascript" src="./Lib/jqplot/plugins/jqplot.pieRenderer.min.js"></script>
<script type="text/javascript" src="./Lib/jqplot/plugins/jqplot.donutRenderer.min.js"></script>
<script type="text/javascript" src="./Lib/jqplot/plugins/jqplot.highlighter.min.js"></script>
<script type="text/javascript" src="./Lib/jqplot/plugins/jqplot.cursor.min.js"></script>
<div class= "hidden-xs">
<div  id="chartdiv" ></div>
<script>
$(document).ready(function(){
  var data = [
    <?php echo fs_get_stats();?>
  ];
  var plot1 = jQuery.jqplot ('chartdiv', [data], 
    { 
      seriesDefaults: {
        // Make this a pie chart.
        renderer: jQuery.jqplot.PieRenderer, 
        rendererOptions: {
          // Put data labels on the pie slices.
          // By default, labels show the percentage of the slice.
          showDataLabels: true,
		  sortData: true,
		  showLabel: true,
		  dataLabels: [<?php echo fs_get_stats();?>],
		  dataLabelNudge: 30,	
		  varyBarColor: true,
		  cursor:{ 
			show: true,			
			showTooltip:true,
			 tooltipLocation:'sw'
		  } 		 
        },				
      }, 	  
      legend: { renderer: $.jqplot.EnhancedLegendRenderer,
    show: true,
    rendererOptions: {
		border: "0px",
        numberRows: 10
    } }	  
    }
  );
});
</script>
</div>