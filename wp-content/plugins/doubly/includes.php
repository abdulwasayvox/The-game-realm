<?php
/**
 * @package Doubly
 * @author Unlimited Elements
 * @copyright (C) 2022 Unlimited Elements, All Rights Reserved. 
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 **/

if(!defined("DOUBLY_INC")) die("restricted access");

$currentFolder = dirname($mainFilepath);
$incPHPFolder = $currentFolder."/inc_php/";

require $incPHPFolder."functions.php";
require $incPHPFolder."functions.class.php";
require $incPHPFolder."functions_wp.class.php";
require $incPHPFolder."helper.class.php";
require $incPHPFolder."actions.class.php";
require $incPHPFolder."common.class.php";
require $incPHPFolder."admin.class.php";
require $incPHPFolder."general_settings.class.php";
require $incPHPFolder."front.class.php";
require $incPHPFolder."exporter_base.class.php";
require $incPHPFolder."exporter.class.php";
require $incPHPFolder."importer.class.php";
require $incPHPFolder."provider_db.class.php";
require $incPHPFolder."db.class.php";
require $incPHPFolder."zip.class.php";
require $incPHPFolder."operations.class.php";
require $incPHPFolder."html_output_base.class.php";
require $incPHPFolder."settings.class.php";
require $incPHPFolder."settings_output.class.php";
require $incPHPFolder."settings_output_wide.class.php";
require $incPHPFolder."admin_notices.class.php";
require $incPHPFolder."integrations.class.php";
require $incPHPFolder."object.class.php";
require $incPHPFolder."globals.class.php";


$pathPro = $currentFolder."/pro/doubly_pro.class.php";

if(file_exists($pathPro))
	require $pathPro;


