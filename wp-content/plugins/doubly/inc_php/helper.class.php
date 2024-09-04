<?php
/**
 * @package Doubly
 * @author Unlimited Elements
 * @copyright (C) 2022 Unlimited Elements, All Rights Reserved. 
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 **/

if(!defined("DOUBLY_INC")) die("restricted access");

class HelperDOUBLY{

	private static $cacheGeneralSettings;
	private static $db;
	private static $arrElementorGlobalColors;
	private static $arrElementorGlobalTypography;
	private static $arrDebug = array();
	
	
	/**
	 * get database
	 */
	public static function getDB(){
		
		if(empty(self::$db))
			self::$db = new Doubly_PluginDB();
		
		return(self::$db);
	}
	
	
	/**
	 * get nonce
	 */
	public static function getNonce(){
		
		$nonce = wp_create_nonce(GlobalsDOUBLY::PLUGIN_SLUG."_actions");
		
		return($nonce);
	}
	
	/**
	 * veryfy nonce
	 */
	public static function verifyNonce($nonce){
		
		$verified = wp_verify_nonce($nonce, GlobalsDOUBLY::PLUGIN_SLUG."_actions");
		if($verified == false)
			UniteFunctionsDOUBLY::throwError("Action security failed, please refresh the page and repeat action");
	
	}
	
	/**
	 * get ajax url for export
	 */
	public static function getUrlRemoteAjax($urlAjax, $action, $params = ""){
		
		$urlAjax = UniteFunctionsDOUBLY::addUrlParams($urlAjax, "action=".GlobalsDOUBLY::PLUGIN_SLUG."_ajax_actions&client_action={$action}");
		
		if(!empty($params))
			$urlAjax .= "&".$params;
		
		return($urlAjax);
	}
	
	/**
	 * get local ajax url
	 */
	public static function getUrlAjax($action, $params = ""){
		
		$urlAjax = GlobalsDOUBLY::$urlAjax;
		
		$nonce = self::getNonce();
		
		$urlAjax = UniteFunctionsDOUBLY::addUrlParams($urlAjax, "action=".GlobalsDOUBLY::PLUGIN_SLUG."_ajax_actions&nonce={$nonce}&client_action={$action}");
		
		if(!empty($params))
			$urlAjax .= "&".$params;
		
		return($urlAjax);
	}
	
	
	/**
	 * add script absolute url
	 */
	public static function addScriptAbsoluteUrl($url, $handle, $inFooter = false, $deps = array()){
		
		if(empty($url))
			UniteFunctionsDOUBLY::throwError("empty script url, handle: $handle");
		
		$version = DOUBLY_VERSION;
		if(GlobalsDOUBLY::$inDev == true)	//add script
			$version = time();
		
		
		wp_register_script($handle , $url, $deps, $version, $inFooter);
		wp_enqueue_script($handle);
	}

	/**
	 * add style absolute url
	 */
	public static function addStyleAbsoluteUrl($url, $handle){
		
		if(empty($url))
			UniteFunctionsDOUBLY::throwError("empty style url, handle: $handle");
		
		$version = DOUBLY_VERSION;
		if(GlobalsDOUBLY::$inDev == true)	//add script
			$version = time();
		
		$deps = array();
		
		wp_register_style($handle , $url, $deps, $version);
		wp_enqueue_style($handle);
	}
	
	
	/**
	 *
	 * register script helper function
	 */
	public static function addScript($scriptName, $handle=null, $folder="assets/js", $inFooter = false){
		
		if($handle == null)
			$handle = GlobalsDOUBLY::PLUGIN_SLUG."-".$scriptName;
		
		$url = GlobalsDOUBLY::$urlPlugin .$folder."/".$scriptName.".js";
		
		self::addScriptAbsoluteUrl($url, $handle, $inFooter);
	}
	
	/**
	 *
	 * register style helper function
	 */
	public static function addStyle($styleName, $handle=null, $folder="assets/css"){
		
		if($handle == null)
			$handle = GlobalsDOUBLY::PLUGIN_SLUG."-".$styleName;
		
		$url = GlobalsDOUBLY::$urlPlugin .$folder."/".$styleName.".css";
		
		self::addStyleAbsoluteUrl($url, $handle);
	}
	
	
	
	/**
	 * get file path
	 * @param  $filename
	 */
	public static function getPathFile($filename, $path, $defaultPath, $validateName, $ext="php"){
		
		if(empty($path))
			$path = $defaultPath;
		
		$filepath = $path.$filename.".".$ext;
		UniteFunctionsDOUBLY::validateFilepath($filepath, $validateName);
		
		return($filepath);
	}
	
	
	/**
	 *
	 * get url to some view.
	 */
	public static function getViewUrl($viewName, $urlParams=""){
		
		$url = admin_url("admin.php?page=doubly_$viewName");

		if(!empty($params))
			$url = UniteFunctionsDOUBLY::addUrlParams($url, $params);
		
		return($url);
	}
	
	
	/**
	 * require some template from "templates" folder
	 */
	public static function getPathView($viewName, $path = null){
		
		return self::getPathFile($viewName,$path,GlobalsDOUBLY::$pathViews,"View");
	}
	
	
	/**
	 * get general settings object
	 */
	public static function getGeneralSettingsObject($withValues = true){
		
		$settings = Doubly_PluginGeneralSettings::getSettingsObject();
		
		if($withValues == false)
			return($setting);
		
		$settingsSavedValue = get_option(GlobalsDOUBLY::OPTION_GENERAL_SETTINGS);
		
		if(empty($settingsSavedValue))
			$settingsSavedValue = array();
					
		$settings->setStoredValues($settingsSavedValue);
		
		return($settings);
	}
	
	
	/**
	 * get general settings
	 */
	public static function getGeneralSettings(){
		
		if(!empty(self::$cacheGeneralSettings))
			return(self::$cacheGeneralSettings);
		
		$objSettings = self::getGeneralSettingsObject();
		
		$arrValues = $objSettings->getArrValues();
		
		self::$cacheGeneralSettings = $arrValues;
		
		return($arrValues);
	}
	
	
	/**
	 * get general setting
	 */
	public static function getGeneralSetting($name){
		
		$arrSettings = self::getGeneralSettings();
				
		if(array_key_exists($name, $arrSettings) == false)
			UniteFunctionsDOUBLY::throwError("General Setting: $name not found");
		
			
		$value = UniteFunctionsDOUBLY::getVal($arrSettings, $name);
		
		return($value);
	}

	
	/**
	 * get error message html
	 */
	public static function getErrorMessageHtml($message, $trace = ""){
		
		$html = '<div class="unite-error-message">';
		$html .= '<div style="unite-error-message-inner">';
		$html .= $message;
	
		if(!empty($trace)){
			$html .= '<div class="unite-error-message-trace">';
			$html .= "<pre>{$trace}</pre>";
			$html .= "</div>";
		}
	
		$html .= '</div></div>';
	
		return($html);
	}
	
	/**
	 * return if the unlimited elements plugin installed, and right version
	 */
	public static function isUEInstalled(){
		
		if(class_exists("GlobalsUC") == false)
			return(false);
		
		if(property_exists("GlobalsUC", "isDOUBLYSupported") == false)
			return(false);
		
		return(GlobalsUC::$isDOUBLYSupported);
	}
	
	

	/**
	 * is activated by freemis
	 */
	public static function isActivatedByFreemius(){
        
		global $doubly_freemius;
		
        if(isset($doubly_freemius) == false)
        	return(false);
       	
        $isActivated = $doubly_freemius->is_paying();
        
        return($isActivated);
	}
	
    /**
     * check if edit mode
     */
    public static function isElementorEditMode(){
    	
    	$isInstalled = self::isElementorInstalled();
    	
    	if($isInstalled == false)
    		return(false);
    	
		if(isset($_GET["elementor-preview"]))
			return(true);
    	
    	$argPost = UniteFunctionsDOUBLY::getPostGetVariable("post", "", UniteFunctionsDOUBLY::SANITIZE_KEY);
    	$argAction = UniteFunctionsDOUBLY::getPostGetVariable("action", "", UniteFunctionsDOUBLY::SANITIZE_KEY);
		
    	if($argAction == "elementor_render_widget" || $argAction == "elementor_ajax")
    		return(true);
    	
    	
    	return(false);
    }
	
	
	/**
	 * get elementor global colors
	 */
	public static function getElementorGlobalTypography(){
		
		if(self::isElementorInstalled() == false)
			return(false);
		
		if(!empty(self::$arrElementorGlobalTypography))
			return(self::$arrElementorGlobalTypography);

		$dataManager = self::getElementorDataManager();
		if(empty($dataManager))
			return(false);
		
		$arrTypography = $dataManager->run("globals/typography");
		
		if(empty($arrTypography))
			return(false);
		
		//get saved ones
    	foreach($arrTypography as $typoID=>$typo){
    		    			
    		$value = UniteFunctionsDOUBLY::getVal($typo, "value");
    		
    		$arrTypography[$typoID] = $value;
    	}
    	
    	self::$arrElementorGlobalTypography = $arrTypography;
    	
    	return(self::$arrElementorGlobalTypography);
		
		
	}
	
	
	
	/**
	 * add debug
	 */
	public static function addDebug($str){
		
		self::$arrDebug[] = $str;
		
	}
	
	/**
	 * print debug
	 */
	public static function printDebug(){
		
		dmp(self::$arrDebug);
	}

	/**
	 * on php error message
	 */
	public static function onPHPErrorMessage($message, $error){
		
		$errorMessage = UniteFunctionsDOUBLY::getVal($error, "message");
		
		$file = UniteFunctionsDOUBLY::getVal($error, "file");
		$line = UniteFunctionsDOUBLY::getVal($error, "line");
		
		if(is_string($errorMessage))
			$message .= "Doubly Troubleshooting: \n<br><pre>{$errorMessage}</pre>";
		
		if(!empty($file))
			$message .= "in : <b>$file</b>";
		
		if(!empty($line))
			$message .= " on line <b>$line</b>";
		
		return($message);
	}
	
	
	/**
	 * get post type titles
	 * try first hard coded to avoid language issues
	 */
	public static function getPostTypeTitles($postType){
		
		if(empty($postType))
			$postType = "post";
		
		$single = $postType;
		$plural = $postType;
		
		$isOutput = true;
		
		switch($postType){
			case "post":
				$single = "Post";
				$plural = "Posts";
			break;
			case "page":
				$single = "Page";
				$plural = "Pages";
			break;
			case "product":
				$single = "Product";
				$plural = "Products";
			break;
			case "attachment":
				$single = "Image";
				$plural = "Images";
			break;
			case "snippet":
				$single = "Snippet";
				$plural = "Snippets";
			break;
			case "media":
				$single = "Media";
				$plural = "Medias";
			break;
			default:
				$isOutput = false;
			break;
		}
			
		$output = array();
		
		if($isOutput == true){
			
			$output["single"] = $single;
			$output["plural"] = $plural;
			
			return($output);
		}
		
		//get names by object
		
		$objType = get_post_type_object($postType);
		
		if(!empty($objType)){
			
			$arrLabels = $objType->labels;
			
			$arrLabels = (array)$arrLabels;
			
			$plural = UniteFunctionsDOUBLY::getVal($arrLabels, "name");
			$single = UniteFunctionsDOUBLY::getVal($arrLabels, "singular_name");
		}
		
		$output["single"] = $single;
		$output["plural"] = $plural;
		
		
		return($output);
	}
	
	
	
	/**
	 * check if url is local - inside base url
	 */
	public static function isUrlUnderBase($url){
		
		$pos = strpos($url, GlobalsDOUBLY::$urlBase);
		
		if($pos !== false)
			return(true);
		
		return(false);
	}
	
	/**
	 * try to get attachment id and site from url (include thumb url's)
	 */
	public static function getAttachmentDataFromUrl($url){
		
		$isUnderBase = self::isUrlUnderBase($url);
		
		if($isUnderBase == false)
			return(null);
		
		$postID = attachment_url_to_postid( $url );
		
		if(!empty($postID)){
			
			$output = array();
			$output["id"] = $postID;
			$output["size"] = UniteFunctionsWPDOUBLY::THUMB_FULL;
			
			return($output);
		}
		
		$arrImage = UniteFunctionsWPDOUBLY::getAttachmentIDFromImageUrl($url, true);
		
		if(empty($arrImage))
			return(null);
		
		
		return($arrImage);
	}
	
	
	/**
	 * try to get attachment id from attachment url
	 */
	public static function getAttachmentIDFromUrl($url){
		
		$isUnderBase = self::isUrlUnderBase($url);
		
		if($isUnderBase == false)
			return(null);
		
		$postID = attachment_url_to_postid( $url );
		
		if(empty($postID))
			return(null);
			
		return($postID);		
	}
	
	private static function __________PARSE_CONTENT__________(){}
	
	
	/**
	 * get url attachments from string
	 */
	public static function getArrAttachmentUrlsFromString($txt = '', $should_contain = ''){
		
	    $extensions = array('jpg', 'svg', 'png', 'gif', 'mp4', 'pdf', 'jpeg');
	    $links = array();
	    preg_match_all('/(http|https)\:\/\/(.*?)(\s|\'|\"|\,|\;|$)/', $txt, $matches);
	    
	    if(empty($matches[0]))
	    	return(null);
	    
	    $arrUlrs = $matches[0];
	    
        foreach($arrUlrs as $url){
            
        	$link = trim(str_replace(array('"', "'", ',', ';'), '', $url));
                    	
            $basename_arr = explode('.', basename($link));
            
            $link_extension = mb_strtolower(end($basename_arr));
            
            if(in_array($link_extension, $extensions) && (!$should_contain || mb_strpos($link, $should_contain) !== false)){
                $links[] = $link;
            }
            
        }
	    
        $links = array_unique($links);
        
	    return $links;
	}
	
	
	private static function __________GUTENBERG__________(){}
	
	
	/**
	 * modify blocks
	 */
	public static function modifyBlocksForShow($blocks){
		
		if(empty($blocks))
			return($blocks);
			
		foreach($blocks as $index => $block){
			
			$innerBlocks = UniteFunctionsDOUBLY::getVal($block, "innerBlocks");
			if(!empty($innerBlocks))
				$innerBlocks = self::modifyBlocksForShow($innerBlocks);
			
			$innerContent = UniteFunctionsDOUBLY::getVal($block, "innerContent");
			
			if(!empty($innerContent)){
				
				foreach($innerContent as $key=>$content)
					$innerContent[$key] = htmlspecialchars($content);
			}
			
			$block["innerHTML"] = htmlspecialchars($block["innerHTML"]);
			
			$block["innerContent"] = $innerContent;
			
			$block["innerBlocks"] = $innerBlocks;
			
			$blocks[$index] = $block;
		}
		
		
		return($blocks);
	}
	
	
	private static function __________ELEMENTOR__________(){}
	
	/**
	 * is elementor installed
	 */
	public static function isElementorInstalled(){
		
		if(defined("ELEMENTOR_VERSION"))
			return(true);
		
		return(false);
	}
	
	
	/**
	 * get elementor data manager
	 */
	private static function getElementorDataManager(){
		
		$plugin = \Elementor\Plugin::$instance;
		
		if(isset($plugin) == false)
			return(false);
		
		$dataManager = null;
		if(isset($plugin->data_manager))
			$dataManager = $plugin->data_manager;
					
		if(empty($dataManager) && isset($plugin->data_manager_v2)){
			$dataManager = $plugin->data_manager_v2;
		}
				
		if(empty($dataManager))
			return(false);
				
		if(method_exists($dataManager,"run") == false)
			return(false);

		if(method_exists($dataManager,"run") == false)
			return(false);
			
		return($dataManager);
	}
	
	
	/**
	 * get elementor global colors
	 */
	public static function getElementorGlobalColors(){
				
		if(self::isElementorInstalled() == false)
			return(false);
					
		if(!empty(self::$arrElementorGlobalColors))
			return(self::$arrElementorGlobalColors);
					
		$dataManager = self::getElementorDataManager();

		if(empty($dataManager))
			return(false);
							
		$arrColors = $dataManager->run("globals/colors");
		
		if(empty($arrColors))
			return(false);
		
		//get saved ones
    	foreach($arrColors as $colorID=>$color){
    		    			
    		$value = UniteFunctionsDOUBLY::getVal($color, "value");
    		 
    		$arrGlobalColors[$colorID] = $value;
    	}
    	
    	self::$arrElementorGlobalColors = $arrGlobalColors;
    	
    	return(self::$arrElementorGlobalColors);
		
		
	}
	
	
	/**
	 * get elementor section from elementor content
	 */
	public static function getElementorSectionFromContent($arrContent, $sectionID){
		
		if(empty($arrContent))
			return(null);
			
		if(is_array($arrContent) == false)
			return(null);
		
		$elType = UniteFunctionsDOUBLY::getVal($arrContent, "elType");
		$id = UniteFunctionsDOUBLY::getVal($arrContent, "id");
		
		if( ($elType == "section" || $elType == "container") && $id == $sectionID)
			return($arrContent);
		
		foreach($arrContent as $section){
			
			if(is_array($section) == false)
				continue;
			
			$arrSection = self::getElementorSectionFromContent($section, $sectionID);
			
			if(!empty($arrSection))
				return($arrSection);
		}
		
		
		return(null);
	}
	
	/**
	 * generate elementor id
	 * from numbers and letters, 0 not first
	 */
	public static function generateElementorID(){
		
		$first = UniteFunctionsDOUBLY::getRandomString(1, "hex_no_zero");
		
		$letters = UniteFunctionsDOUBLY::getRandomString(3, "hex_letters");
		$numbers = UniteFunctionsDOUBLY::getRandomString(3, "numbers");
		
		$mixed = $letters.$numbers;
		
		$mixed = str_shuffle($mixed);
		
		$final = $first.$mixed;
		
		return($final);
	}
	
	
	/**
	 * get elementor content from post
	 */
	public static function getElementorContent($postID){
		
		$jsonData = get_post_meta($postID,"_elementor_data",true);
		
		if(empty($jsonData))
			return(null);
		
		$arrData = UniteFunctionsDOUBLY::maybeJsonDecode($jsonData);
		
		return($arrData);
	}
	
	
	/**
	 * check if it's elementor post
	 */
	public static function isElementorPost($postID){
		
		if(self::isElementorInstalled() == false)
			return(false);
			
		$jsonData = get_post_meta($postID, "_elementor_data", true);
		
		if(empty($jsonData))
			return(false);
		
		return(true);
	}
	
	
	
	/**
	 * remove elementor cache file by post id
	 */
	public static function removeElementorPostCacheFile($postID){
		
		//remove post meta
		delete_post_meta($postID, "_elementor_css");
		
		$pathFiles = GlobalsDOUBLY::$pathUploads."elementor/css/";
		
		$filepath = $pathFiles."post-{$postID}.css";
		
		$fileExists = file_exists($filepath);
		
		if($fileExists == false)
			return(false);
			
		@unlink($filepath);
	}
	
	
	private static function __________URL_FUNCTIONS__________(){}
	
	
	/**
	 * return true if exits base url key in text
	 */
	public static function hasBaseUrl($text){
		
		$pos = strpos($text, Doubly_PluginExporterBase::KEY_BASE_URL);
		
		if($pos !== false)
			return(true);
		
		return(false);
	}
	
	/**
	 * remote base url from url's
	 */
	public static function removeBaseUrl($text){
		
		$text = str_replace(Doubly_PluginExporterBase::KEY_BASE_URL, GlobalsDOUBLY::$urlBase, $text);
		
		return($text);
	}
	
	
	/**
	 * convert some url to relative
	 */
	public static function URLtoRelative($url, $isAssets = false){
		
		$replaceString = GlobalsDOUBLY::$urlBase;
		if($isAssets == true)
			$replaceString = GlobalsDOUBLY::$urlBase;
		
		//change the protocol
		if(strpos($url, "http://") !== false && strpos($replaceString, "https://") !== false)
			$replaceString = str_replace("https://", "http://", $replaceString);
					
		//in case of array take "url" from the array
		if(is_array($url)){
			
			$strUrl = UniteFunctionsDOUBLY::getVal($url, "url");
			if(empty($strUrl))
				return($url);
			
			$url["url"] = str_replace($replaceString, "", $strUrl);
			
			return($url);
		}
		
		$url = str_replace($replaceString, "", $url);
	
		return($url);
	}
	
	
	/**
	 * convert url to path, if wrong path, return null
	 */
	public static function urlToPath($url){
		
		$urlRelative = self::URLtoRelative($url);
				
		$path = GlobalsDOUBLY::$pathBase.$urlRelative;
		if(file_exists($path) == false)
			return(null);
		
		return($path);
	}
	
	
	/**
	 * convert url to full url
	 */
	public static function URLtoFull($url, $urlBase = null){
		
		if(is_numeric($url))		//protection for image id
			return($url);
		
		if(getType($urlBase) == "boolean")
			UniteFunctionsDOUBLY::throwError("the url base should be null or string");
		
		if(is_array($url))
			UniteFunctionsDOUBLY::throwError("url can't be array");
		
		$url = trim($url);
		
		if(empty($url))
			return("");
			
		$urlLower = strtolower($url);
		
		if(strpos($urlLower, "http://") !== false || strpos($urlLower, "https://") !== false)
			return($url);
		
		if(empty($urlBase))
			$url = GlobalsDOUBLY::$urlBase.$url;
		else{
			
			$convertUrl = GlobalsDOUBLY::$urlBase;
			
			//preserve old format:
			$filepath = self::pathToAbsolute($url);
			if(file_exists($filepath) == false)
				$convertUrl = $urlBase;
			
			$url = $convertUrl.$url;
		}
		
		$url = UniteFunctionsDOUBLY::cleanUrl($url);
		
		return($url);
	}
	
	
	
	/**
	 * convert title to handle
	 */
	public static function convertTitleToHandle($title, $removeNonAscii = true){
		
		$handle = strtolower($title);
		
		$handle = str_replace(array("ä", "Ä"), "a", $handle);
		$handle = str_replace(array("å", "Å"), "a", $handle);
		$handle = str_replace(array("ö", "Ö"), "o", $handle);
		
		if($removeNonAscii == true){
			
			// Remove any character that is not alphanumeric, white-space, or a hyphen
			$handle = preg_replace("/[^a-z0-9\s\_]/i", " ", $handle);
		
		}
		
		// Replace multiple instances of white-space with a single space
		$handle = preg_replace("/\s\s+/", " ", $handle);
		// Replace all spaces with underscores
		$handle = preg_replace("/\s/", "_", $handle);
		// Replace multiple underscore with a single underscore
		$handle = preg_replace("/\_\_+/", "_", $handle);
		// Remove leading and trailing underscores
		$handle = trim($handle, "_");
		
		return($handle);
	}
	
	
	/**
	 * convert title to alias
	 */
	public static function convertTitleToAlias($title){
		
		$handle = self::convertTitleToHandle($title, false);
		$alias = str_replace("_", "-", $handle);
		
		return($alias);
	}
	
	/**
	 * get host url without extension
	 */
	public static function getUrlHostNoExtension(){
		
		$urlInfo = parse_url(GlobalsDOUBLY::$urlBase);
		$host = UniteFunctionsDOUBLY::getVal($urlInfo, "host");
		
		if(empty($host))
			return("");
					
		$host = UniteFunctionsDOUBLY::getDomainWithoutExtension($host);
		
		return($host);
	}
	
	
	private static function ___________VALIDATIONS___________(){}
	
	/**
	 * validate that copy enabled
	 */
	public static function validateCopyEnabled($action = ""){
		
		if(class_exists("FS_Options") == false)
			exit();
		
		$action = esc_html($action);
		
		if(GlobalsDOUBLY::$enableCopy == false){
			
			UniteFunctionsDOUBLY::throwError("Copy / Export action not allowed ($action)");
		}
	}

	/**
	 * validate that copy enabled
	 */
	public static function validateCopySectionFrontEnabled($action = ""){
		
		if(class_exists("FS_Options") == false)
			exit();
				
		if(GlobalsDOUBLY::$enableFrontCopy == false){
			
			UniteFunctionsDOUBLY::throwError("Copy front action not allowed");
		}
	}
	
	
	/**
	 * validate that copy enabled
	 */
	public static function validateCopySectionEnabled($action = ""){
		
		if(class_exists("FS_Options") == false)
			exit();
				
		if(GlobalsDOUBLY::$enableCopy == false){
			
			UniteFunctionsDOUBLY::throwError("Copy action not allowed");
		}
	}
	
	
	
	/**
	 * validate that copy enabled
	 */
	public static function validatePasteEnabled($action = ""){
		
		$action = esc_html($action);
		
		if(GlobalsDOUBLY::$enablePaste == false)
			UniteFunctionsDOUBLY::throwError("Paste / Import action not allowed ($action)");
		
	}
	
	
	/**
	 * check if user role alowed
	 */
	public static function isCurrentUserRoleAlowed($checkFront = false){

		$key = "allowed_roles";
		
		if($checkFront == true)
			$key = "allowed_roles_front";
		
		$options = self::getGeneralSettings();
		
		$roles = UniteFunctionsDOUBLY::getVal($options, $key);
		
		if(empty($roles))
			return(false);
					
		if(is_string($roles)){
			$roles = explode(",", $roles);
		}
		
		$userID = get_current_user_id();
		
		//if no role, search for quest
		if(empty($userID)){
			
			if(array_search("quest", $roles) !== false)
				return(true);
			
			return(false);
		}
		
		$user = get_userdata($userID);
		
		if(empty($user))
			return(false);
		
		$currentRoles = $user->roles;
		
		if(empty($currentRoles))
			return(false);
		
		foreach($currentRoles as $role){
			
			if(array_search($role, $roles) !== false)
				return(true);
			
		}
				
		return(false);
	}
	
	/**
	 * check if current post type allowed for doubly operations
	 */
	public static function isPostTypeAllowedForOperations($postType, $exportType = null){
		
		if(GlobalsDOUBLY::$isProActive == true)
			return(true);
		
			
		if($postType == "page")
			return(true);
		
		if($exportType == GlobalsDOUBLY::EXPORT_TYPE_SNIPPET)
			return(true);

		
		return(false);
	}
	
	/**
	 * check if front copy permitted for some post
	 */
	public static function isFrontCopyPermittedForPost($post){
		
		if(empty($post))
			return(false);
				
		$postType = $post->post_type;
		
		if($postType != "page")
			return(false);
		
		$postName = $post->post_name;
		
		$isExcluded = apply_filters("doubly_front_copy_page_excluded", $postName);
		
		if($isExcluded === true)
			return(false);
		
		//check excluded by options
		
		$options = HelperDOUBLY::getGeneralSettings();
		
		$strPages = UniteFunctionsDOUBLY::getVal($options, "front_copy_excluded_pages");
		
		if(!empty($strPages)){
			
			$arrPages = explode("\n", $strPages);
						
			foreach($arrPages as $excludedSlug){
				
				$excludedSlug = trim($excludedSlug);
				
				if($excludedSlug === $postName)
					return(false);
			}
			
		}

		
		return(true);
	}
	
	
	
	private static function ___________AJAX___________(){}
	
	/**
	 * output exception in a box
	 */
	public static function outputExceptionBox($e, $prefix=""){
		
		$message = $e->getMessage();
		
		if(!empty($prefix))
			$message = $prefix.":  ".$message;
		
		$trace = "";
		if(GlobalsDOUBLY::$showTrace || GlobalsDOUBLY::DEBUG_ERRORS == true)
			$trace = $e->getTraceAsString();
		
		$html = self::getErrorMessageHtml($message, $trace);
		
		echo esc_html($html);
	}
	
	/**
	 *
	 * echo json ajax response
	 */
	public static function ajaxResponse($success,$message,$arrData = null){
	
		$response = array();
		$response["success"] = $success;
		$response["message"] = $message;
	
		if(!empty($arrData)){
	
			if(gettype($arrData) == "string")
				$arrData = array("data"=>$arrData);
	
			$response = array_merge($response,$arrData);
		}
	
		$json = json_encode($response);
		
		echo UniteFunctionsDOUBLY::escapeField($json);
		exit();
	}
	
	/**
	 *
	 * echo json ajax response, without message, only data
	 */
	public static function ajaxResponseData($arrData){
		if(gettype($arrData) == "string")
			$arrData = array("data"=>$arrData);
	
		self::ajaxResponse(true,"",$arrData);
	}
	
	/**
	 *
	 * echo json ajax response
	 */
	public static function ajaxResponseError($message,$arrData = null){
	
		self::ajaxResponse(false,$message,$arrData,true);
	}
	
	/**
	 * echo ajax success response
	 */
	public static function ajaxResponseSuccess($message,$arrData = null){
	
		self::ajaxResponse(true,$message,$arrData,true);
	
	}
	
	/**
	 * echo ajax success response
	 */
	public static function ajaxResponseSuccessRedirect($message,$url){
		$arrData = array("is_redirect"=>true,"redirect_url"=>$url);
	
		self::ajaxResponse(true,$message,$arrData,true);
	}
	
	
	
}

