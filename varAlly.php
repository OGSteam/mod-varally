<?php
/** $Id: varAlly.php 6885 2011-07-04 19:49:14Z darknoon $ **/
/**
* Ce mod permet de suivre l'évolution du classement des membres d'une alliance
* @package varAlly
* @author Aeris
* @link http://ogsteam.fr
* @version 1.0.0
 */
if (!defined('IN_SPYOGAME')) die('Hacking attempt');

require_once('./views/page_header.php');
require_once('./mod/varally/include.php');

$query = 'SELECT `active` FROM `'.TABLE_MOD.'` WHERE `action`=\'varAlly\' AND `active`=\'1\' LIMIT 1';
if (!$db->sql_numrows($db->sql_query($query))) die('Hacking attempt');

if (!isset($pub_subaction)) $pub_subaction='ally';
?>
<table width="100%">
	<tr>
		<td>
<?php
button_bar();
?>
		</td>
	</tr>
	<tr>
		<td>
<?php
switch ($pub_subaction)
{
	case 'admin': require_once('./mod/varally/admin.php'); break;
	case 'ally': require_once('./mod/varally/ally.php'); break;
	case 'display': require_once('./mod/varally/display.php'); break;
	default: require_once('./mod/varally/ally.php'); break;
}
?>
		</td>
	</tr>
</table>
<?php
page_footer();
require_once('./views/page_tail.php');
?>
