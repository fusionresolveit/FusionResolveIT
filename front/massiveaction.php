<?php
/*
 * @version $Id$
 ----------------------------------------------------------------------
 GLPI - Gestionnaire Libre de Parc Informatique
 Copyright (C) 2003-2006 by the INDEPNET Development Team.
 
 http://indepnet.net/   http://glpi.indepnet.org
 ----------------------------------------------------------------------

 LICENSE

	This file is part of GLPI.

    GLPI is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    GLPI is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with GLPI; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 ------------------------------------------------------------------------
*/

// ----------------------------------------------------------------------
// Original Author of file:
// Purpose of file:
// ----------------------------------------------------------------------

include ("_relpos.php");
$NEEDED_ITEMS=array("user","tracking","reservation","document","computer","device","printer","networking","peripheral","monitor","software","infocom","phone","state","link","ocsng","consumable","cartridge");
include ($phproot . "/inc/includes.php");

header("Content-Type: text/html; charset=UTF-8");
header_nocache();

checkTypeRight($_POST["device_type"],"w");

commonHeader($lang["title"][42],$_SERVER["PHP_SELF"]);

if (isset($_POST["action"])&&isset($_POST["device_type"])&&isset($_POST["item"])&&count($_POST["item"])){

	switch($_POST["action"]){
		case "delete":
			$ci=new CommonItem();
			$ci->getFromDB($_POST["device_type"],-1);
			foreach ($_POST["item"] as $key => $val){
				if ($val==1) {
					$ci->obj->delete(array("ID"=>$key));
				}
			}
		break;
		case "purge":
			$ci=new CommonItem();
			$ci->getFromDB($_POST["device_type"],-1);
			foreach ($_POST["item"] as $key => $val){
				if ($val==1) {
					$ci->obj->delete(array("ID"=>$key),1);
				}
			}
		break;
		case "restore":
			$ci=new CommonItem();
			$ci->getFromDB($_POST["device_type"],-1);
			foreach ($_POST["item"] as $key => $val){
				if ($val==1) {
					$ci->obj->restore(array("ID"=>$key));
				}
			}
		break;
		case "update":
			// Infocoms case
			if (($_POST["id_field"]>=25&&$_POST["id_field"]<=28)||($_POST["id_field"]>=37&&$_POST["id_field"]<=38)||($_POST["id_field"]>=50&&$_POST["id_field"]<=58)){
				foreach ($_POST["item"] as $key => $val)
				if ($val==1){
					$_POST["FK_device"]=$key;
					updateInfocom($_POST);
				}
			} else {
				$ci=new CommonItem();
				$ci->getFromDB($_POST["device_type"],-1);
				foreach ($_POST["item"] as $key => $val){
					if ($val==1) {
						$ci->obj->update(array("ID"=>$key,$_POST["field"] => $_POST[$_POST["field"]]));
					}
				}
			}
		break;
	}

	echo "<div align='center'><strong>".$lang["common"][23]."<br>";
	echo "<a href='".$_SERVER['HTTP_REFERER']."'>".$lang["buttons"][13]."</a>";
	echo "</strong></div>";



} else echo $lang["common"][24];

commonFooter();

?>
