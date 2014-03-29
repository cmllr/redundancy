<div class = "table table-responsive">
	<table>
		<tr>
		<th></th>
		<th>Value</th>
		<th>Details</th>
		</tr>
		<tr>
			<td>
				<?php
				if (class_exists("ZipArchive") == true)
				{
					echo "<span class=\"successValue elusive icon-ok glyphIcon\">";
				}	
				else
				{
					echo "<span class=\"errorValue elusive icon-remove glyphIcon\">";
				}
				?>
			</td>
			<td>Zip Support</td>
			<td></td>
		</tr>
		<tr>
			<td>
				<?php
					if (function_exists("imagecreate") == true)
					{
						echo "<span class=\"successValue elusive icon-ok glyphIcon\">";
					}	
					else
					{
						echo "<span class=\"errorValue elusive icon-remove glyphIcon\">";
					}
				?>
			</td>
			<td>Image Support</td>
			<td></td>
		</tr>
		<tr>
			<td>
				<?php
				if (function_exists("move_uploaded_file") == true)
				{
					echo "<span class=\"successValue elusive icon-ok glyphIcon\">";
				}	
				else
				{
					echo "<span class=\"errorValue elusive icon-remove glyphIcon\">";
				}
				?>
			</td>
			<td>FileSystem Support</td>
			<td></td>
		</tr>
		<tr>
			<td>
				<?php
				if (function_exists("mysqli_query") == true)
				{
					echo "<span class=\"successValue elusive icon-ok glyphIcon\">";
				}	
				else
				{
					echo "<span class=\"errorValue elusive icon-remove glyphIcon\">";
				}
				?>
			</td>
			<td>MySQLi Support</td>
			<td></td>
		</tr>
		<tr>
			<td>
				<?php
				if (is_writable(getStoragePath()) == true)
					{
						echo "<span class=\"successValue elusive icon-ok glyphIcon\">";
					}	
					else
					{
						echo "<span class=\"errorValue elusive icon-remove glyphIcon\">";
					}
				?>
			</td>
			<td>Storage Access</td>
			<td></td>
		</tr>
		<tr>
			<td>
				<?php
				if (is_writable(getTempPath()) == true)
				{
					echo "<span class=\"successValue elusive icon-ok glyphIcon\">";
				}	
				else
				{
					echo "<span class=\"errorValue elusive icon-remove glyphIcon\">";
				}
				?>
			</td>
			<td>Temp Access</td>
			<td></td>
		</tr>
		<tr>
			<td>
				<?php
				if (is_writable(getSnapshotsPath()) == true)
				{
					echo "<span class=\"successValue elusive icon-ok glyphIcon\">";
				}	
				else
				{
					echo "<span class=\"errorValue elusive icon-remove glyphIcon\">";
				}
				?>
			</td>
			<td>Snapshots Access</td>
			<td></td>
		</tr>
		<tr>
			<td>
				<?php
				include $GLOBALS["config"]["Program_Path"]."Includes/DataBase.inc.php";	
				$query = mysqli_query($connect,"Select * from Files");
				$count = 0;
				$countMissing = 0;	
				while ($row = mysqli_fetch_object($query)) {		
					if ($row->Filename != $row->Displayname){
					$count++;			
						if (file_exists(getStoragePath().$row->Filename) == false)
						{
							$countMissing++;
							echo "<span class=\"errorValue elusive icon-remove glyphIcon\"></span>- File \"".$row->Displayname."\" (".$row->Filename.") in database, but not on filesystem!<br>";
							
						}
					}		
				}	
				if ($countMissing == 0)
					echo "<span class=\"successValue elusive icon-ok glyphIcon\"></span>";
				else
					echo "<span class=\"errorValue elusive icon-remove glyphIcon\"></span>";
				$percent = 0;
				if ($countMissing != 0)
				{
					$percent = round(100/($count/$countMissing),2);
				}
				echo "<td>Database check</td><td>Found ".$count." files in database, ".$countMissing." (".$percent."%)";
				$count = 0;
				$countMissing = 0;
				?>
			</td>
		</tr>
		<tr>
			<td>
				<?php
				if ($handle = opendir($GLOBALS["config"]["Program_Path"].$GLOBALS["config"]["Program_Storage_Dir"]."/")) {
					while (false !== ($file = readdir($handle))) {			
						if ($file != "." && $file != ".." && $file != ".htaccess" && $file != "index.php" )
						{
							$count++;
							$result = mysqli_query($connect,"Select * from Files where Filename = '$file'");
							if (mysqli_affected_rows($connect) == 0)
							{
								$countMissing++;
								echo "<span class=\"errorValue elusive icon-remove glyphIcon\"></span>- File \"$file\" on filesystem, but not in database!<br>";
							}
						}
					}
					closedir($handle);
				}
				if ($countMissing == 0)
					echo "<span class=\"successValue elusive icon-ok glyphIcon\"></span>";
				
				if ($countMissing != 0)
				{
					$percent = round(100/($count/$countMissing),2);
				}
				echo "<td>Filesystem check</td><td>Found ".$count." files on filesystem, ".$countMissing." too much (".$percent."%)";
				?>
			</td>
		</tr>
		<tr>
			<td>
				<?php
					$count = 0;
					$query = mysqli_query($connect,"Select * from Banned where Reason = 'XSS'");
					while ($row = mysqli_fetch_object($query)) {		
							$count++;
					}	
					if ($count == 0)
						echo "<span class=\"successValue elusive icon-ok glyphIcon\"></span>";
					else
						echo "<span class=\"errorValue elusive icon-remove glyphIcon\"></span>";
					echo "<td>Security check</td><td>$count XSS attacks blocked";
				?>
			</td>
		</tr>
		<tr>
			<td>
				<?php
				$count = 0;
				$query = mysqli_query($connect,"Select * from Users where Enabled = 0");
				while ($row = mysqli_fetch_object($query)) {		
						$count++;
				}	
				if ($count == 0)
					echo "<span class=\"successValue elusive icon-ok glyphIcon\"></span>";
				else
					echo "<span class=\"errorValue elusive icon-remove glyphIcon\"></span>";
				echo "</td><td>User check</td><td>$count disabled user(s) found<br>";	
				?>
			</td>
		</tr>
		<tr>
			<td>
				<?php
					if ($GLOBALS["config"]["Enable_register"]== "1")
						echo "<span class=\"successValue elusive icon-ok glyphIcon\"></span>";
					else
						echo "<span class=\"errorValue elusive icon-remove glyphIcon\"></span>";
				?>
			</td>
			<td>Register</td>
			<td></td>
		</tr>
		<tr>
			<td>
				<?php
					if ($GLOBALS["config"]["Program_HTTPS_Redirect"]=="1")
						echo "<span class=\"successValue elusive icon-ok glyphIcon\"></span>";
					else
						echo "<span class=\"errorValue elusive icon-remove glyphIcon\"></span>";
				?>
			</td>
			<td>HTTPS</td>
			<td></td>
		</tr>
		<tr>
			<td>
				<?php
				if ($GLOBALS["config"]["Api_Enable"] == 1 )
					echo "<span class=\"successValue elusive icon-ok glyphIcon\"></span>";
				else
					echo "<span class=\"errorValue elusive icon-remove glyphIcon\"></span>";
				?>
			</td>
			<td>API</td>
			<td></td>
		</tr>
		<tr>
			<td>
				<?php
					if ($GLOBALS["config"]["User_Enable_Recover"]==1)
						echo "<span class=\"successValue elusive icon-ok glyphIcon\"></span>";
					else
						echo "<span class=\"errorValue elusive icon-remove glyphIcon\"></span>";
				?>
			</td>
			<td>Password recovery</td>
			<td></td>
		</tr>
		<tr>
			<td>
				<?php
					if ($GLOBALS["config"]["Program_Enable_Plugins"]==1)
						echo "<span class=\"successValue elusive icon-ok glyphIcon\"></span>";
					else
						echo "<span class=\"errorValue elusive icon-remove glyphIcon\"></span>";
				?>
			</td>
			<td>Plugins</td>
			<td></td>
		</tr>
		<tr>
			<td>
				<?php
				if ($GLOBALS["config"]["Program_Debug"]==1)
					echo "<span class=\"successValue elusive icon-ok glyphIcon\"></span>";
				else
					echo "<span class=\"errorValue elusive icon-remove glyphIcon\"></span>";
				mysqli_close($connect);
				?>
			</td>
			<td>Debug mode</td>
			<td></td>
		</tr>
	</table>
</div>
<?php
	$snapshotcount = 0;
	$last = "-";
	if ($handle = opendir($GLOBALS["config"]["Program_Path"].$GLOBALS["config"]["Program_Snapshots_Dir"]."/")) {
		while (false !== ($file = readdir($handle))) {			
			if ($file != "." && $file != ".." && endsWith($file,".zip") )
			{		
				$snapshotcount++;
				$last = date ("d.m.Y H:i:s", filemtime($GLOBALS["config"]["Program_Path"].$GLOBALS["config"]["Program_Snapshots_Dir"]."/".$file));
			}
		}		
	}
	closedir($handle);	
	if ($snapshotcount == 0)
		echo "<p><span class=\"successValue elusive icon-remove glyphIcon\"></span>0 Snapshots</p>";
	else
		echo "<p><span class=\"successValue elusive icon-ok glyphIcon\"></span>".$snapshotcount.($snapshotcount == 1 ? " Snapshot" : " Snapshots"). " (last from ".$last.")</p>";	
	echo "<a type=\"a\" href = 'index.php?module=snapshot' class=\"btn btn-default\"><span class=\"elusive icon-camera glyphIcon\"></span>Snapshot</a>";
?>