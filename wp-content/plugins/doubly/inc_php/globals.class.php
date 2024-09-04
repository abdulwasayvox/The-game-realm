<?php
/**
 * @package Doubly
 * @author Unlimited Elements
 * @copyright (C) 2022 Unlimited Elements, All Rights Reserved. 
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 **/

if(!defined("DOUBLY_INC")) die("restricted access");

class GlobalsDOUBLY{
		
	const DEBUG_ERRORS = false;
	const SHOW_DEBUG_MENU = false;
	
	public static $inDev = false;
	public static $showTrace = false;
	const PLUGIN_TITLE = "Doubly";
	const PLUGIN_SLUG = "doubly";
	const DIR_CACHE = "doubly_cache";
	const DEFAULT_VIEW = "welcome";
	const VIEW_WELCOME = "welcome";
	const VIEW_SETTINGS = "settings";
	const OPTIONS_GROUP_NAME = "doubly-settings";
	const OPTION_GENERAL_SETTINGS = "doubly_general_settings";
	
	const DEBUG_SHOW_INPUT = false;
	
	const EXPORT_TYPE_MEDIA = "media";
	const EXPORT_TYPE_POSTS = "posts";
	const EXPORT_TYPE_ELEMENTOR_SECTION = "elementor_section";
	const EXPORT_TYPE_SNIPPET = "snippet";		//code snippets integration
	const EXPORT_TYPE_OBJECTS = "objects";		//code snippets integration
	
	const META_FIELD_TYPE_IMAGE = "image";
	
	const COPY_EXPIRATION_TIME = 300;	//5 minutes
	
	const PASTE_OPERATION_TIMEOUT = 120;	//120 seconds
	
	const URL_WEBSITE = "https://doubly.pro";
	
	public static $capability = "manage_options";
	public static $pathPlugin;
	public static $pathBase;
	public static $pathUploads;
	public static $pathCache;
	public static $pathViews;
	
	public static $urlBase;
	public static $urlAjax;
	public static $urlPlugin;
	public static $urlImages;
	public static $urlComponentAdmin;
	public static $urlUploads;
	public static $urlCurrentPage;
	
	public static $isSSL;
	public static $isAdmin;
	public static $isLocal;
	
	public static $isProVersion = false;	
	public static $isProActive = false;	
	
	public static $dbPrefix;
	public static $tablePosts, $tablePostMeta, $tableTerms, $tableTermsMeta;
	public static $enableCopy = false;
	public static $enableFrontCopy = false;
	public static $enablePaste = false;
	public static $currentSiteUrl;
	public static $showDebugMenu = false;
	public static $isWordpressCom = false;
	
	
	/**
	 * init the globals
	 */
	public static function initGlobals(){
		
		//set dev mode
		if(defined("UC_DEVMODE") && UC_DEVMODE === true)
			self::$inDev = true;			
				
		//paths
			
		self::$pathPlugin = realpath(dirname(__FILE__)."/../")."/";
		self::$pathPlugin = UniteFunctionsDOUBLY::pathToUnix(self::$pathPlugin);
		
		self::$pathViews = self::$pathPlugin."views/";

		//set current page url
 		
		$isSSL = is_ssl();
		
		$protocol = "http://";
		if($isSSL == true)
			$protocol = "https://";
		
		$host = UniteFunctionsDOUBLY::getVal($_SERVER, "HTTP_HOST");
		
		//add https:// prefix
		if(strpos($host, "https://") === false && strpos($host, "http://") === false)
			$host = $protocol.$host;
		
		self::$urlCurrentPage = $host.UniteFunctionsDOUBLY::getVal($_SERVER, "REQUEST_URI");
				
		self::$pathBase = ABSPATH;
		if(strpos(self::$pathBase, self::$pathPlugin) === false){
			self::$pathBase = realpath(self::$pathPlugin."../../../")."/";			
			self::$pathBase = UniteFunctionsDOUBLY::pathToUnix(self::$pathBase);
		}

		$arrUploadDir = wp_upload_dir();		
		self::$pathUploads = $arrUploadDir["basedir"]."/";
		
		//cache path
		self::$pathCache = self::$pathUploads.self::DIR_CACHE."/";
		if(is_dir(self::$pathCache) == false){
			@mkdir(self::$pathCache);
			
			if(is_dir(self::$pathCache) == false)
				self::$pathCache = self::$pathPlugin."cache/";
		}
		
		//urls
		$pluginUrlAdminBase = self::PLUGIN_SLUG;
		self::$urlComponentAdmin = admin_url()."admin.php?page=$pluginUrlAdminBase";
		
		$pluginDir = basename(self::$pathPlugin);
		self::$urlPlugin = plugins_url($pluginDir)."/";
		self::$urlImages = self::$urlPlugin."assets/images/";
		
		self::$urlUploads = $arrUploadDir["baseurl"]."/";
		self::$urlAjax = admin_url("admin-ajax.php");
		self::$urlBase = site_url()."/";
		
		self::$isAdmin = is_admin();
		self::$isSSL = is_ssl();
		
		//init tables
		global $wpdb;
		self::$dbPrefix = $wpdb->prefix;
		
		self::$tablePosts = self::$dbPrefix."posts";
		self::$tablePostMeta = self::$dbPrefix."postmeta";
		self::$tableTerms = self::$dbPrefix."terms";
		self::$tableTermsMeta = self::$dbPrefix."termmeta";
		
		self::$isLocal = UniteFunctionsDOUBLY::isLocal();

		self::$currentSiteUrl = UniteFunctionsDOUBLY::getVal($_SERVER, "HTTP_HOST");
		
		$siteUrl = site_url();
		$siteUrl = str_replace("http://", "", $siteUrl);
		$siteUrl = str_replace("https://", "", $siteUrl);
		
		self::$currentSiteUrl = $siteUrl;
		
		//set if wordpress.com site
		
		global $atomic_hosting_provider;
		if(isset($atomic_hosting_provider) && $atomic_hosting_provider == "WordPress.com")
			self::$isWordpressCom = true;
		
		
	}
	
	
	/**
	 * set enabled copy paste by setting
	 */
	public static function setEnabledCopyPasteBySetting(){
		
		$options = HelperDOUBLY::getGeneralSettings();
		$enabledFunc = UniteFunctionsDOUBLY::getVal($options, "enabled_func");
					
		switch($enabledFunc){
			default:
			case "all":
				self::$enableCopy = true;
				self::$enablePaste = true;
			break;
			case "copy":
				self::$enableCopy = true;				
			break;
			case "paste":
				self::$enablePaste = true;
			break;
			case "none":		//disable all
				self::$enableCopy = false;
				self::$enablePaste = false;
			break;
		}
						
		HelperDOUBLY::addDebug("copy: ".self::$enableCopy." paste: ".self::$enablePaste);
		
	}
	
	
	/**
	 * continue set some settings
	 */
	public static function onWPInit(){

		if(defined("DISABLE_DOUBLY"))
			return(false);
				
		//debug menu
		if(class_exists("Doubly_Pro"))
			Doubly_Pro::onInit();
		
	    self::$showDebugMenu = (self::SHOW_DEBUG_MENU == true || defined("DOUBLY_ENABLE_DEBUG_MENU") && DOUBLY_ENABLE_DEBUG_MENU == true);
		
		HelperDOUBLY::addDebug("set enable copy paste");
		
		$isAllowedByRole = HelperDOUBLY::isCurrentUserRoleAlowed();
		
		if($isAllowedByRole == false){
			self::$enableCopy = false;
			self::$enablePaste = false;
			
			HelperDOUBLY::addDebug("all false");			
		}else
			self::setEnabledCopyPasteBySetting();
		
		self::setEnableFrontCopy();

		//self::printVars();
		
	}
	
	/**
	 * enable front copy
	 */
	public static function setEnableFrontCopy(){
		
		$options = HelperDOUBLY::getGeneralSettings();
		
		$enableFrontCopy = UniteFunctionsDOUBLY::getVal($options, "enabled_elementor_front_copy");
				
		if($enableFrontCopy === "enable")
			$enableFrontCopy = true;
		else
			$enableFrontCopy = false;
		
		if($enableFrontCopy == false){

			HelperDOUBLY::addDebug(" front copy: false");
			
			return(false);
		}
					
		$enableFrontCopy = HelperDOUBLY::isCurrentUserRoleAlowed(true);
		
		
		HelperDOUBLY::addDebug(" front copy: ".self::$enableFrontCopy);
		
		self::$enableFrontCopy = $enableFrontCopy;
		
	}
	
	
	/**
	 * print all globals variables
	 */
	public static function printVars(){
		
		$methods = get_class_vars( "GlobalsDOUBLY" );
		dmp($methods);
		exit();
	}
	
	
}

GlobalsDOUBLY::initGlobals();

