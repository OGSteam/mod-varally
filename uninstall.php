<?php
/**
* Desinstallation du module
* @package varAlly
* @author Aeris
* @link http://ogsteam.fr
 */
if (!defined('IN_SPYOGAME')) die('Hacking attempt'); 
include('./parameters/id.php');
global $db,$table_prefix;

$mod_uninstall_name = "nom du mode";
$mod_uninstall_table = $table_prefix.'varAlly';
uninstall_mod($mod_uninstall_name,$mod_uninstall_table);


$queries = array();
$queries[] = 'DELETE FROM `'.TABLE_MOD_CFG.'` WHERE `mod`=\'varally\'';
			 
											 
foreach ($queries as $query) {
	$db->sql_query($query);
}
?>
