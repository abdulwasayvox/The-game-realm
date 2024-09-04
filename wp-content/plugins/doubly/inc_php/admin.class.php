<?php
/**
 * @package Doubly
 * @author Unlimited Elements
 * @copyright (C) 2022 Unlimited Elements, All Rights Reserved. 
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 **/

if(!defined("DOUBLY_INC")) die("restricted access");

class Doubly_PluginAdmin extends Doubly_PluginCommon{
	
   	private static $arrMenuPages = array();
   	private static $arrSubMenuPages = array();
	public static $view;
	public static $isInsidePlugin = false;
	private $screen;
	
	
	const DEBUG_SCREEN_ID = false;
	
	
	/**
	 * call init
	 */
	public function __construct(){
		
		$this->init();
	}

	
	/**
	 * add admin menus from the list.
	 */
	public function addAdminMenu(){
		
		$pageTitle = "Doubly";
		
		if(GlobalsDOUBLY::$isProVersion == true)
			$pageTitle = "Doubly Pro";
				
		$menuTitle = $pageTitle;
		$menuSlug = "doubly";
		$function = array($this, "adminPages");
		
		add_menu_page($pageTitle, $pageTitle, GlobalsDOUBLY::$capability, $menuSlug, $function,GlobalsDOUBLY::$urlImages."doubly-menu-icon.svg");
		
		//add sub menu page
		
		add_submenu_page( "doubly", "Welcome", "Welcome", GlobalsDOUBLY::$capability, "doubly", $function);
		add_submenu_page( "doubly", "Settings", "Settings", GlobalsDOUBLY::$capability, "doubly_settings", $function);
		
	}
		
	
	/**
	 * init view
	 */
	private function initView(){
		
		$defaultView = GlobalsDOUBLY::DEFAULT_VIEW;
		
		//set view
		$viewInput = UniteFunctionsDOUBLY::getGetVar("view","",UniteFunctionsDOUBLY::SANITIZE_KEY);
		$page = UniteFunctionsDOUBLY::getGetVar("page","",UniteFunctionsDOUBLY::SANITIZE_KEY);
				
		if(strpos($page, GlobalsDOUBLY::PLUGIN_SLUG) === 0)
			self::$isInsidePlugin = true;

		//get the view out of the page
		if(!empty($viewInput)){
			self::$view = $viewInput;
			return(false);
		}
		
		//check bottom devider
		$deviderPos = strpos($page,"_");
				
		if($deviderPos !== false){
			
			self::$view = substr($page, $deviderPos+1);
			return(false);
		}
		
		//check middle devider
		$deviderPos = strpos($page, "-");
		if($deviderPos !== false){
			self::$view = substr($page, $deviderPos+1);
			
			return(false);
		}
		
		self::$view = $defaultView;
		
	}
	
	
	/**
	 * open admin pages
	 */
	public function adminPages(){
				
		try{
			
			$pathView = HelperDOUBLY::getPathView(self::$view);
			
			require $pathView;
			
		}catch(Exception $e){
			
			echo "<br>";
			
			HelperDOUBLY::outputExceptionBox($e, GlobalsDOUBLY::PLUGIN_TITLE." error");
			
		}
	}
	
	
	/**
	 * add inside scripts
	 */
	public function onAddScripts(){
		
		//---- add js scripts
		
		switch(self::$view){
			case GlobalsDOUBLY::VIEW_WELCOME:
			case GlobalsDOUBLY::VIEW_SETTINGS:
								
				HelperDOUBLY::addStyle("doubly_admin");
				HelperDOUBLY::addStyle("doubly_styles");
				
				HelperDOUBLY::addScript("doubly_provider_admin");
				HelperDOUBLY::addScript("doubly_admin");
				HelperDOUBLY::addScript("doubly_settings");
				
				HelperDOUBLY::addScript("doubly_view_settings");
				
			break;
		}
		
		//---- add css styles
		
		if(GlobalsDOUBLY::$enableCopy == true || GlobalsDOUBLY::$enablePaste == true)
			$this->onIncludeFrontScripts();
		
		
	}
	
	
	/**
	 * debug show screen id
	 */
	private function debugScreenID(){
		
		$currentScreen = get_current_screen();
		
		dmp("screen id: ".$currentScreen->id);
		exit();		
	}
	
	/**
	 * add outside scripts
	 */
	public function onAddOutsideScripts(){
		
		$this->onIncludeFrontScripts();
		
		if(self::DEBUG_SCREEN_ID)
			$this->debugScreenID();
	}
	
	
	/**
	 * register settings for the settings page
	 */
	public function onAdminInit(){
		
		//register settings to save
		register_setting( GlobalsDOUBLY::OPTIONS_GROUP_NAME, 'doubly_general_settings' );
		
		
	}
	
	/**
	 * on ajax actions
	 */
	public function onAjaxActions(){
		
		$objActions = new Doubly_AjaxActions();
		$objActions->onAjaxActions();		
	}
	
	
	/**
	 * register terms bulk actions
	 */
	public function registerTermsBulkActions($arrActions){
		
		if(GlobalsDOUBLY::$inDev == false)
			return($arrActions);
				
		//in free version - limit to post only
		if(GlobalsDOUBLY::$isProActive == false){
			
			$postType = $this->screen->post_type;
			
			if($postType !== "post")
				return($arrActions);
		}
		
		$arrActions["doubly_combine"] = __("Combine");
		
		
		return($arrActions);
	}
		
	
	/**
	 * register bulk actions
	 */
	public function registerBulkActions($arrActions){
			
		if(GlobalsDOUBLY::$enableCopy == false)
			return($arrActions);

	  switch($this->specialExportType){
	  	 case GlobalsDOUBLY::EXPORT_TYPE_SNIPPET:
	  	 	  
			  $arrActions['doubly_copy'] = __( 'Copy by Doubly', 'doubly');
			  
			  //$arrActions['doubly_export'] = __( 'Export Zip - Remove Me', 'doubly');
			  
	  	 break;
	  	 default:
	  	 	
			  $arrActions['doubly_copy'] = __( 'Copy', 'doubly');
			  $arrActions['doubly_export'] = __( 'Export Zip', 'doubly');
	  	 	  
	  	 break;
	  }
			
				  
	  return $arrActions;
	}		

	
	/**
	 * add admin bar buttons
	 */
	public function addAdminBarButtons(WP_Admin_Bar $admin_bar){
		
		if(GlobalsDOUBLY::$enableCopy == false && GlobalsDOUBLY::$enablePaste == false)
			return(false);
		
	    //add copy item
		
	    if(!empty($this->postID) && GlobalsDOUBLY::$enableCopy){
	    	
		    // -----  add copy
		   	
		    $arrMeta = array();
		    $arrMeta["title"] = __( 'Cross Domain Copy Post', 'doubly' );
		    
		    $arrMenu = array();
		    $arrMenu["title"] = __('Copy',"doubly");
		    $arrMenu["id"] = __('doubly_copy',"doubly");
		    $arrMenu["href"] = 'javascript:void(0)';
		    $arrMenu["meta"] = $arrMeta;
		    
		    $admin_bar->add_menu($arrMenu);
	    }
	   		    		
	    
	    // -----  add paste
	    
	    if(GlobalsDOUBLY::$enablePaste){
	    	
		    $arrMeta = array();
		    $arrMeta["title"] = __( 'Cross Domain Paste / Import Content', 'doubly' );
		    
		    $arrMenu = array();
		    $arrMenu["title"] = __('Paste',"doubly");
		    $arrMenu["id"] = __('doubly_paste',"doubly");
		    $arrMenu["href"] = 'javascript:void(0)';
		    $arrMenu["meta"] = $arrMeta;
		    
		    $admin_bar->add_menu($arrMenu);
	    }
	    
	    //add debug
	    
	    if(GlobalsDOUBLY::$showDebugMenu)
	    	$this->addAdminMenuBar_debug($admin_bar);
	    
	    
	}
	
	/**
	 * set bulk actions
	 */
	public function onCurrentScreenInit(){
		
		$objScreen = get_current_screen();
		
		$this->screen = $objScreen;
		
		$base = $objScreen->base;
		
		switch($base){
			case "post":
				
				$postID = UniteFunctionsDOUBLY::getGetVar("post","",UniteFunctionsDOUBLY::SANITIZE_ID);
				
				if(!empty($postID)){
					$this->postID = $postID;
					$this->post = get_post($postID);
					
					if(!empty($this->post)){
						$this->postType = $this->post->post_type;
						$this->postTypeName = UniteFunctionsWPDOUBLY::getPostTypeName($this->post->post_type);
					}
				}
				
			break;
			case "snippets_page_edit-snippet":		//code snippets plugin integration
				
				$postID = UniteFunctionsDOUBLY::getGetVar("id","",UniteFunctionsDOUBLY::SANITIZE_ID);
				
				if(!empty($postID)){
					
					$this->postID = $postID;
					$this->specialExportType = GlobalsDOUBLY::EXPORT_TYPE_SNIPPET;
					$this->postType = "snippet";
					$this->postTypeName = __("Snippet","doubly");
				}
				
				
			break;
			case "toplevel_page_snippets":
					$this->specialExportType = GlobalsDOUBLY::EXPORT_TYPE_SNIPPET;
					$this->postType = "snippet";
					$this->postTypeName = __("Snippet","doubly");
			break;
			default:
				
				//dmp($base);exit();	//for debug
				
			break;
		}
				
		if(empty($this->postType)){
			
			$postType = $this->screen->post_type;
			
			if(!empty($postType))
				$this->postType = $postType;
			
		}
		
		$screenID = $objScreen->id;
		
		switch($base){
			case "upload":
			case "edit":		//edit posts
				add_filter( 'bulk_actions-'.$screenID, array($this, 'registerBulkActions') );
			break;
			case "edit-tags":
			
				add_filter( 'bulk_actions-'.$screenID, array($this, 'registerTermsBulkActions') );
				
				$screenID = $objScreen->id;
						
				//add the action
				add_filter( 'handle_bulk_actions-'.$screenID, array($this,"onBulkActionsEditTags"), 10, 3 );		
				
				
			break;
			case "toplevel_page_snippets":
				
				add_filter( 'bulk_actions-'.$screenID, array($this, 'registerBulkActions') );
				
			break;
		}
				
		
	}
	
	
	/**
	 * check if copy permitted to the page
	 */
	protected function isCopyPermittedForCurrentPost(){
		
		if($this->isOperationPermittedCache !== null)
			return($this->isOperationPermittedCache);
		
		$this->isOperationPermittedCache = false;
		
		$postType = $this->screen->post_type;		
		$base = $this->screen->base;
		
		$isAllowed = true;
		
		switch($base){
			case "edit":
			case "post":
			case "upload":
			break;
			default:
				
				if(empty($this->specialExportType))
					$isAllowed = true;
				
			break;
		}
				
		$isAllowedForOperations = HelperDOUBLY::isPostTypeAllowedForOperations($postType, $this->specialExportType);
		
		if($isAllowedForOperations == false)
			$isAllowed = false;
		
	    if($isAllowed == false){
	    	
	    	$this->isOperationPermittedCache = false;
	    	GlobalsDOUBLY::$enableCopy = false;
	    	GlobalsDOUBLY::$enableFrontCopy = false;
	    	
	    	return(false);
	    }
		
	    $this->isOperationPermittedCache = true;
		
	    
		return(true);
	}
	
	
	/**
	 * put both versions installed admin notice
	 */
	private function putBothVersionsInstalledNotice(){
		
		$objNotices = new Doubly_AdminNotices();
		
		$objNotices->setNotice("Doubly Pro and Free versions are active. Please deactivate and delete the free version of the plugin", "both_versions");
		
		
	}
	
	/**
	 * init admin notices
	 */
	public function initAdminNotices(){
		
		if(defined("DOUBLY_BOTH_VERSIONS_INSTALLED"))
			$this->putBothVersionsInstalledNotice();
		
	}
	
	/**
	 * on edit tags - check and make the combine action
	 */
	public function onBulkActionsEditTags($redirect, $doaction, $object_ids){
		
		
		dmp($redirect);
		dmp($doaction);
		dmp($object_ids);
		
		dmp("do action");
		exit();
		
		return($redirect);
	}
	
	
	/**
	 * init the class
	 */
	public function init(){

		if(function_exists("fs_is_plugin_page") == false)
			return(false);
		
		$this->isAdmin = true;
		
		$this->initView();
				
		add_action("init",array($this,"onWPInit"));
		
		add_action("init",array($this,"initAdminNotices"));
		
		add_action("admin_menu", array($this, "addAdminMenu"));
		
		if(self::$isInsidePlugin == true)
			add_action("admin_enqueue_scripts", array($this,"onAddScripts"), true);
		else
			add_action("admin_enqueue_scripts", array($this,"onAddOutsideScripts"), true);
		
		//register settings
		add_action("admin_init", array($this,"onAdminInit"));
		
		//register ajax
		add_action('wp_ajax_'.GlobalsDOUBLY::PLUGIN_SLUG."_ajax_actions"."", array($this,"onAjaxActions"), true);
		add_action('wp_ajax_nopriv_'.GlobalsDOUBLY::PLUGIN_SLUG."_ajax_actions", array($this,"onAjaxActions"), true);
		
		//bulk actions
		
		//set bulk actions for each current screen
		add_action("current_screen", array($this, "onCurrentScreenInit"));
		
		add_action( 'admin_footer', array($this, 'addFooterScripts') );
		
		//init admin bar menu actions - add the buttons menu there
		add_action( 'admin_bar_menu', array($this, "addAdminBarButtons"), 500 );
		
	}
	
}
