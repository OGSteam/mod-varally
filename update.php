<?php
/** $Id: update.php 6886 2011-07-04 20:00:01Z darknoon $ **/
/**
* Mise à jour du module
* @package varAlly
* @author Aeris
* @version 1.0.0
* @link http://ogsteam.fr
 */
define('IN_SPYOGAME', true);
include('./parameters/id.php');
global $db;

$mod_folder = "varally";
$mod_name = "varally";
update_mod($mod_folder,$mod_name);

$queries = array();

$queries[] = 'INSERT IGNORE INTO '.TABLE_MOD_CFG.' (`mod`,`config`, `value`) VALUES (\'varally\',\'nbrjoueur\',\'3\')';
$queries[] = 'INSERT IGNORE INTO '.TABLE_MOD_CFG.' (`mod`,`config`, `value`) VALUES (\'varally\',\'tagAlly\',\'\')';
$queries[] = 'INSERT IGNORE INTO '.TABLE_MOD_CFG.' (`mod`,`config`, `value`) VALUES (\'varally\',\'tagAllySpy\',\'\')';
$queries[] = 'INSERT IGNORE INTO '.TABLE_MOD_CFG.' (`mod`,`config`, `value`) VALUES (\'varally\',\'bilAlly\',\'\')';
$queries[] = 'INSERT IGNORE INTO '.TABLE_MOD_CFG.' (`mod`,`config`, `value`) VALUES (\'varally\',\'tblAlly\',\'rank_player_points\')';

foreach ($queries as $query) {
	$db->sql_query($query);
}

