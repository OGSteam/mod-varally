<?php
/** $Id: install.php 6886 2011-07-04 20:00:01Z darknoon $ **/
/**
* Install du module
* @package varAlly
* @author Aeris
* @link http://ogsteam.fr
* @version 1.0.0
 */
if (!defined('IN_SPYOGAME')) die('Hacking attempt');
include('./parameters/id.php');
global $db;
$is_ok = false;
$mod_folder = "varally";
$is_ok = install_mod ($mod_folder);
if ($is_ok == true)
	{
		$queries = array();
		$queries[] = 'INSERT IGNORE INTO '.TABLE_MOD_CFG.' (`mod`,`config`, `value`) VALUES (\'varally\',\'nbrjoueur\',\'3\')';
		$queries[] = 'INSERT IGNORE INTO '.TABLE_MOD_CFG.' (`mod`,`config`, `value`) VALUES (\'varally\',\'tagAlly\',\'\')';
		$queries[] = 'INSERT IGNORE INTO '.TABLE_MOD_CFG.' (`mod`,`config`, `value`) VALUES (\'varally\',\'tagAllySpy\',\'\')';
		$queries[] = 'INSERT IGNORE INTO '.TABLE_MOD_CFG.' (`mod`,`config`, `value`) VALUES (\'varally\',\'bilAlly\',\'\')';
		$queries[] = 'INSERT IGNORE INTO '.TABLE_MOD_CFG.' (`mod`,`config`, `value`) VALUES (\'varally\',\'tblAlly\',\'rank_player_points\')';

		foreach ($queries as $query) {
		$db->sql_query($query);
		}
	}
else
	{
		echo  "<script>alert('Désolé, un problème a eu lieu pendant l'installation, corrigez les problèmes survenue et réessayez.');</script>";
	}

