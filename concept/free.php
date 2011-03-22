<?php
define('INTERNAL', 1);
define('MENUITEM', 'myportfolio/concept');

define('SECTION_PLUGINTYPE', 'core');
define('SECTION_PLUGINNAME', 'concept');

require(dirname(dirname(__FILE__)) . '/init.php');
require_once('concept.php');

$free = Concepts::get_free_fragments();

$smarty = smarty(); 
$smarty->assign('free', $free);
$smarty->display('concept/free.tpl');

?>