<?php
/*
Gibbon, Flexible & Open School System
Copyright (C) 2010, Ross Parker

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program. If not, see <http://www.gnu.org/licenses/>.
*/

include "../../functions.php" ;
include "../../config.php" ;

//New PDO DB connection
try {
  	$connection2=new PDO("mysql:host=$databaseServer;dbname=$databaseName;charset=utf8", $databaseUsername, $databasePassword);
	$connection2->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$connection2->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
}
catch(PDOException $e) {
  echo $e->getMessage();
}

@session_start() ;

//Set timezone from session variable
date_default_timezone_set($_SESSION[$guid]["timezone"]);

$gibbonThemeID=$_GET["gibbonThemeID"] ;
$URL=$_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . getModuleName($_POST["address"]) . "/theme_manage_uninstall.php&gibbonThemeID=" . $gibbonThemeID ;
$URLDelete=$_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . getModuleName($_POST["address"]) . "/theme_manage.php" ;

if (isActionAccessible($guid, $connection2, "/modules/System Admin/theme_manage_uninstall.php")==FALSE) {
	//Fail 0
	$URL=$URL . "&deleteReturn=fail0" ;
	header("Location: {$URL}");
}
else {
	//Proceed!
	//Check if role specified
	if ($gibbonThemeID=="") {
		//Fail1
		$URL=$URL . "&deleteReturn=fail1" ;
		header("Location: {$URL}");
	}
	else {
		try {
			$data=array("gibbonThemeID"=>$gibbonThemeID); 
			$sql="SELECT * FROM gibbonTheme WHERE gibbonThemeID=:gibbonThemeID AND active='N'" ;
			$result=$connection2->prepare($sql);
			$result->execute($data);
		}
		catch(PDOException $e) { 
			//Fail2
			$URL=$URL . "&deleteReturn=fail2" ;
			header("Location: {$URL}");
			break ;
		}
		if ($result->rowCount()!=1) {
			//Fail 3
			$URL=$URL . "&deleteReturn=fail3" ;
			header("Location: {$URL}");
		}
		else {
			//Remove theme
			try {
				$dataDelete=array("gibbonThemeID"=>$gibbonThemeID); 
				$sqlDelete="DELETE FROM gibbonTheme WHERE gibbonThemeID=:gibbonThemeID" ;
				$resultDelete=$connection2->prepare($sqlDelete);
				$resultDelete->execute($dataDelete);
			}
			catch(PDOException $e) { 
				//Fail 2
				$URL=$URL . "&deleteReturn=fail2" ;
				header("Location: {$URL}");
				break ;
			}

			//Success 0
			$URLDelete=$URLDelete . "&deleteReturn=success0" ;
			header("Location: {$URLDelete}");
		}
	}
}
?>