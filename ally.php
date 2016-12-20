<?php
/** $Id: ally.php 6885 2011-07-04 19:49:14Z darknoon $ **/
/**
* page Principale
* @package varAlly
* @author Aeris
* @link http://ogsteam.fr
* @version 1.0.0
 */
if (!defined('IN_SPYOGAME')) die('Hacking attempt');

$sql = 'SELECT `value` FROM `'.TABLE_MOD_CFG.'` WHERE `config`=\'tagAlly\'';
$result = $db->sql_query($sql);
list($tag)=$db->sql_fetch_row($result);

check_tag($tag);
$listTag = explode(';',$tag);

$dateMin = $dateMax = '';
/* If Truc débile pour initialiser les champs*/
if(!isset($pub_dateMin)){
	$query = 'SELECT DISTINCT `datadate` FROM `'.TABLE_VARALLY.'` ORDER BY `datadate` ASC';
	$result = $db->sql_query($query);
	list($pub_dateMin)=$db->sql_fetch_row($result);
	$pub_dateMin= date('d/m/Y H:i:s',$pub_dateMin);
	
}

$query = 'SELECT DISTINCT `datadate` FROM `'.TABLE_VARALLY.'` ORDER BY `datadate` DESC';
$result = $db->sql_query($query);
if(!isset($pub_dateMax)){ 
	list($pub_dateMax)=$db->sql_fetch_row($result);
	$pub_dateMax= date('d/m/Y H:i:s',$pub_dateMax);
}


while ($var = $db->sql_fetch_assoc($result))
{
	$dateMin .= '<option'.((date('d/m/Y H:i:s',$var['datadate'])==$pub_dateMin) ? ' selected' : '').'>'.date('d/m/Y H:i:s',$var['datadate']).'</option>';
	$dateMax .= '<option'.((date('d/m/Y H:i:s',$var['datadate'])==$pub_dateMax) ? ' selected' : '').'>'.date('d/m/Y H:i:s',$var['datadate']).'</option>';
}
?>
<form method='post' action='?action=varAlly&subaction=ally'>
Date min: <select name='dateMin'><?php echo $dateMin; ?></select>&nbsp;-&nbsp;
Date max: <select name='dateMax'><?php echo $dateMax; ?></select>&nbsp;
<input type='submit' value='Afficher'>
</form>
<?php

if ($tag == '')
{
	echo '<table width=\'100%\'><tr><td class=\'c\'>Pas d\'alliance sélectionnée</th></tr></table></br>';
} else {
	$whereDate = '';
	if (isset($pub_dateMin) && isset($pub_dateMax))
	{
		$dateMinStamped = parseDate($pub_dateMin);
		$dateMaxStamped = parseDate($pub_dateMax);
		if ($dateMinStamped < $dateMaxStamped)
		{
			$whereDate = ' AND `datadate`>=\''.$dateMinStamped.'\' AND `datadate`<=\''.$dateMaxStamped.'\'';
		} else {
			die ('Dates incorrectes: Merci de sélectionner deux dates cohérentes');
		}
	}
	foreach ($listTag as $tag)
	{
		$tblecart = array();
		
		$sql = 'SELECT DISTINCT `datadate` FROM `'.TABLE_VARALLY.'` WHERE `ally`=\''.$db->sql_escape_string($tag).'\''.$whereDate.' ORDER BY `datadate` DESC LIMIT 2';
		$result = $db->sql_query($sql);
		$nb = $db->sql_numrows($result);
		
		switch ($nb)
		{
			case '0': $evolution = 'Classement inconnu'; $where = ''; break;
			case '1': list($new) = $db->sql_fetch_row($result); $evolution =
			'Classement du '.date('d/m/Y H:i:s',$new); $where = ' AND `datadate` = \''.$new.'\' '; break;
			case '2': list($new) = $db->sql_fetch_row($result); list($ex) = $db->sql_fetch_row($result); 
				$evolution = 'Evolution entre le '.(isset($pub_dateMin) ? $pub_dateMin : date('d/m/Y H:i:s',$ex)).' et le '.(isset($pub_dateMax) ? $pub_dateMax : date('d/m/Y H:i:s',$new)); $where = ' AND (`datadate` = \''.$new.'\' OR `datadate` = \''.$ex.'\') '; break;
		}
		 
		$query = 'SELECT DISTINCT `player` FROM `'.TABLE_VARALLY.'` WHERE `ally`=\''.$db->sql_escape_string($tag).'\''.$where.' ORDER BY `points` DESC';
		$result = $db->sql_query($query);

?>
		<h2>Alliance [<?php echo $tag; ?>]</h2>
		<table width='100%'>
		<tr><td class='c' colspan='7'><?php echo $evolution; ?></td></tr>
		<tr><td class='c'>Joueur</td><td class='c'>Ecart général</td><td class='c'>%</td></tr>		
<?php
		while ($val = $db->sql_fetch_assoc($result))
		{
			$player = $val['player'];
			echo '<tr><th>'.$player.'</th>';
			affPoints($player, $whereDate);
			echo '</tr>';
		}
?>
		</table><br><br />
<?php
$sql = 'SELECT `value` FROM `'.TABLE_MOD_CFG.'` WHERE `config`=\'bilAlly\'';
$result = $db->sql_query($sql);
list($bilSpy)=$db->sql_fetch_row($result);

if($bilSpy =="oui") {
	
	/* Et aller, un bon coup de trash code à cause de la mauvaise gestion des variables...*/
	//$pub = 'pub_';
	
?>
<h2>Bilan entre les deux dates choisies :</h2>

<?php
// Récupération du nombre de joueur par défaut
$sql = "SELECT value FROM ".TABLE_MOD_CFG." WHERE config='nbrjoueur'";
$result = $db->sql_query($sql);
list($nbrjou)=$db->sql_fetch_row($result);
?>

<form method='post' action='?action=varAlly&subaction=ally#bilanJ<?php echo $tag ?>' name="bilanJ<?php echo $tag ?>" id="bilanJ<?php echo $tag ?>">
<fieldset>
Nombre de joueurs à afficher pour&nbsp;:<br />
 Les plus fortes évolutions <input type="text" name="evol<?php echo $tag ?>" value="<?php if(isset(${'pub_evol'.$tag})) echo ${'pub_evol'.$tag}; else echo $nbrjou;?>"> <em>(mettre 0 pour ne pas afficher)</em><br />
 Les plus gros gains de points <input type="text" name="pointsplus<?php echo $tag ?>" value="<?php if(isset(${'pub_pointsplus'.$tag})) echo ${'pub_pointsplus'.$tag}; else echo $nbrjou;?>"> <em>(mettre 0 pour ne pas afficher)</em><br />
 Les chutes <input type="text" name="chute<?php echo $tag ?>" value="<?php if(isset(${'pub_chute'.$tag})) echo ${'pub_chute'.$tag}; else echo '-1';?>"> <em>(pour afficher toutes les chutes mettre -1, 0 pour ne pas afficher)</em><br />
 Les plus faible gains de points <input type="text" name="pointsmoins<?php echo $tag ?>" value="<?php if(isset(${'pub_pointsmoins'.$tag})) echo ${'pub_pointsmoins'.$tag}; else echo $nbrjou;?>"> <em>(mettre 0 pour ne pas afficher)</em><br /><br />
 Taille du champs du bilan <input type="text" name="tarea<?php echo $tag ?>" value="<?php if(isset(${'pub_tarea'.$tag})) echo ${'pub_tarea'.$tag}; else echo '20';?>"><br />
 <input type="hidden" name='dateMin' value="<?php echo isset($pub_dateMin) ? $pub_dateMin : $ex; ?>">
 <input type="hidden" name='dateMax' value="<?php echo isset($pub_dateMin) ? $pub_dateMax : $new; ?>">
 <input type='submit' value='Afficher'>
</fieldset>
</form>
<?php

// Réinitialisation des variables dans le cas où l'on l'a déjà fait pour une autre alliance avant (ca créé un bug si elle avait moins de membre)
$jou = Array(); $pts = Array(); $prc = Array();
foreach ($tblecart as $key => $row) {
    $jou[$key] = $row['joueur'];
    $pts[$key] = $row['pts'];
    $prc[$key] = $row['prc'];
}

if(!isset(${'pub_tarea'.$tag})) ${'pub_tarea'.$tag} = 20;
// Évolution %
?>
<textarea rows="<?php echo ${'pub_tarea'.$tag}; ?>">
Evolution entre le [b]<?php echo isset($pub_dateMin) ? $pub_dateMin : date('d/m/Y H:i:s',$ex) ?>[/b] et le [b]<?php echo isset($pub_dateMax) ? $pub_dateMax : date('d/m/Y H:i:s',$new) ?> [<?php echo $tag; ?>][/b]

<?php
array_multisort($prc, SORT_DESC, $pts, SORT_ASC, $tblecart);
if(!isset(${'pub_evol'.$tag})) ${'pub_evol'.$tag} = $nbrjou;

if(${'pub_evol'.$tag} > 0) {
	echo '[u]Les '.${'pub_evol'.$tag}.' plus fortes évolutions :[/u][list=1]
';
	for($a=0; $a<${'pub_evol'.$tag}; $a++) {
		echo "[*] [b]".$tblecart[$a]['joueur']."[/b] avec [color=#008000]+".number_format($tblecart[$a]['pts'],0,'','.')." points[/color] soit [color=#008000]+".$tblecart[$a]['prc']."%[/color]
";
	}
	echo '[/list]
';
}
?>


<?php
// Évolution pts
@array_multisort($pts, SORT_DESC, $prc, SORT_ASC, $tblecart);
if(!isset(${'pub_pointsplus'.$tag})) ${'pub_pointsplus'.$tag} = $nbrjou;

if(${'pub_pointsplus'.$tag} > 0) {
	echo '[u]Les '.${'pub_pointsplus'.$tag}.' plus gros gains de points : [/u][list=1]
';
	for($b=0; $b<${'pub_pointsplus'.$tag}; $b++) {
		echo "[*] [b]".$tblecart[$b]['joueur']."[/b] avec [color=#008000]+".$tblecart[$b]['prc']."%[/color] soit [color=#008000]+".number_format($tblecart[$b]['pts'],0,'','.')." points[/color]
";
	}
	echo '[/list]
';
}
?>

<?php
// Chutes
@array_multisort($prc, SORT_ASC, $pts, SORT_ASC, $tblecart);
if(!isset(${'pub_chute'.$tag}) || ${'pub_chute'.$tag} == -1 ) ${'pub_chute'.$tag} = count($tblecart);

if(${'pub_chute'.$tag} != 0) {
	echo '[u]Les chutes :[/u][list=1]
';
	$nbr = 0;
	for($c=0; $c<${'pub_chute'.$tag}; $c++) {
		$infc = (integer) $tblecart[$c]['pts'];
		if(strstr($infc,'-')) {
			echo "[*] [b]".$tblecart[$c]['joueur']."[/b] avec [color=#FF0000]".number_format($tblecart[$c]['pts'],0,'','.')." points[/color] soit [color=#FF0000]".$tblecart[$c]['prc']."%[/color]
";
		$nbr++;
		}
	}
	if($nbr<1) {
		echo "[b]Personne n'a chuté[/b]";    
	}
	echo '[/list]
';
}
?>

<?php
// Réveil
@array_multisort($pts, SORT_ASC, $prc, SORT_ASC, $tblecart);
if(!isset(${'pub_pointsmoins'.$tag})) ${'pub_pointsmoins'.$tag} = $nbrjou;

if(${'pub_pointsmoins'.$tag} > 0) {
	echo '[u]Les '.${'pub_pointsmoins'.$tag}.' plus faible gains de points (faut se reveiller la !!!) : [/u][list=1]
';
	for($d=0; $d<${'pub_pointsmoins'.$tag}; $d++) {
		$infd = (integer) $tblecart[$d]['pts'];
		if(!strstr($infd,'-')) {
			echo "[*] [b]".$tblecart[$d]['joueur']."[/b] avec [color=#808000]+".$tblecart[$d]['prc']."%[/color] soit [color=#808000]+".number_format($tblecart[$d]['pts'],0,'','.')." points[/color]
";
		} else {
			${'pub_pointsmoins'.$tag} = ${'pub_pointsmoins'.$tag}+1;
		}
	}
	echo '[/list]';
}
?>
</textarea>

<?php
//unset($tblecart);
$tblecart = array_fill(0, count($tblecart),'');
}
    }
}
?>
