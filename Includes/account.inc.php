<?php
	/**
	 * @file
	 * @author  squarerootfury <fury224@googlemail.com>	 
	 *
	 * @section LICENSE
	 *
	 * This program is free software; you can redistribute it and/or
	 * modify it under the terms of the GNU General Public License as
	 * published by the Free Software Foundation; either version 3 of
	 * the License, or (at your option) any later version.
	 *
	 * This program is distributed in the hope that it will be useful, but
	 * WITHOUT ANY WARRANTY; without even the implied warranty of
	 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
	 * General Public License for more details at
	 * http://www.gnu.org/copyleft/gpl.html
	 *
	 * @section DESCRIPTION
	 *
	 * This file represents the user account dialog.
	 */	
	require_once ("checkuri.inc.php");
?>
<h1><?php echo $GLOBALS["Program_Language"]["User_Details"];?></h1>
<?php
	//start a session if needed
	if (isset($_SESSION) == false)
			session_start();
	//Include DataBase file
	include $GLOBALS["Program_Dir"]."Includes/DataBase.inc.php";	
	//get the current user id an search for users with this id
	$id = mysqli_real_escape_string($connect,$_SESSION["user_id"]);
	$result = mysqli_query($connect,"Select Email,User,API_Key from Users  where ID = '$id' limit 1") or die("DataBase Error: 001 ".mysqli_error($connect));
	//Display the user informations (Email, Username, API_Key)
	while ($row = mysqli_fetch_object($result)) {
		echo "<b>".$GLOBALS["Program_Language"]["Email"].": </b> ".$row->Email;	
		echo "<br><b>".$GLOBALS["Program_Language"]["Username"].": </b> ".$row->User;
		if ($_SESSION["role"] != 3 && $GLOBALS["config"]["Api_Enable"])
			echo "<br><b>API Token </b><input type ='text' cols='70' rows='2' value ='".$row->API_Key."'></input></p>";
	}	
	echo "<h2>".$GLOBALS["Program_Language"]["Password_Management"]."</h2>";	
	echo "<h3>".$GLOBALS["Program_Language"]["Pass_Changes"]."</h2>";
	$result = mysqli_query($connect,"Select IP ,Changed from Pass_History  where Who = '$id' limit 10") or die("DataBase Error: 001 ".mysqli_error($connect));
	while ($row = mysqli_fetch_object($result)) {		
		echo $row->Changed." - " .$row->IP."<br>";
	}	
	//Display the passwort recovery link if allowed
	echo "<div class=\"btn-group\">";
	if ($GLOBALS["config"]["User_Enable_Recover"] == 1 && ($_SESSION["role"] != 3 || is_guest()))		
		echo "<a type=\"a\" href = 'index.php?module=setpass'class=\"btn btn-default\"><span class=\"elusive icon-edit glyphIcon\"></span>".$GLOBALS["Program_Language"]["Set"]."</a>";
	//Close the connection if finished	
	echo "</div>";
	mysqli_close($connect);	
?>
<h2><?php echo $GLOBALS["Program_Language"]["Files"];?></h2>
<div class="progress">
	<div class="progress-bar" role="progressbar" aria-valuenow="<?php echo fs_get_Percentage_2();?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo fs_get_Percentage_2();?>%;">
	</div>
</div>
<?php	
	echo "&nbsp;".fs_get_Percentage()."&nbsp;(";
?>
<?php
	echo fs_get_Storage_Percentage().")";
?>
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

<?php
	if ($GLOBALS["config"]["Program_Enable_User_Settings"] == 1)
		include $GLOBALS["Program_Dir"]."Includes/settings.inc.php";	
?>
<?php
	if ($_SESSION["role"] == 3)
		exit;
?>
<p>
<div class="btn-group">
<a class = 'btn btn-default' href = "index.php?module=zip&dir=/"><?php echo $GLOBALS["Program_Language"]["Download_All_Files"];?></a>
</div>
<?php
	if ($GLOBALS["config"]["User_Allow_Delete"] == 1 && $_SESSION["role"] != 3)
		echo "<br><br><h3>".$GLOBALS["Program_Language"]["Delete_Account"]."</h3><br><a class = 'btn btn-default' href = 'index.php?module=goodbye'>".$GLOBALS["Program_Language"]["Delete_Account"]."</a><br>";
?>