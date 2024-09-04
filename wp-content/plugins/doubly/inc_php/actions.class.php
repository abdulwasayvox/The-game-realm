<?php
/**
 * @package Doubly
 * @author Unlimited Elements
 * @copyright (C) 2022 Unlimited Elements, All Rights Reserved. 
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 **/

if(!defined("DOUBLY_INC")) die("restricted access");

class Doubly_AjaxActions{
	
	
	/**
	 * get data array from request
	 */
	private function getDataFromRequest(){
		
		$data = UniteFunctionsDOUBLY::getPostGetVariable("data","",UniteFunctionsDOUBLY::SANITIZE_NOTHING);
		
		if(empty($data))
			$data = $_REQUEST;
		
		if(is_string($data)){
						
			$arrData = (array)json_decode($data);
			
			if(empty($arrData)){
				$arrData = stripslashes(trim($data));
				$arrData = (array)json_decode($arrData);
			}
						
			$data = $arrData;
		}
		
		$data = UniteFunctionsDOUBLY::convertStdClassToArray($data);
		$data = UniteFunctionsDOUBLY::normalizeAjaxInputData($data);
		
		return($data);
	}
	
	/**
	 * run this function on exception
	 */
	private function onException($e, $prefix = ""){
		
		$message = $e->getMessage();
		
		if(!empty($prefix))
			$message = $prefix.$message;
		
		if(GlobalsDOUBLY::DEBUG_ERRORS == true)
			HelperDOUBLY::outputExceptionBox($e);
		
		if(GlobalsDOUBLY::$showTrace){
			$trace = $e->getTraceAsString();
			$message .= "<pre>".$trace."</pre>";
		}
		
		HelperDOUBLY::ajaxResponseError($message);
		
	}
	
	/**
	 * return is verify needed or not
	 */
	private function isVerifyNeeded($action){
		
		switch($action){
			case "get_copied_content":
			case "copy_elementor_section_front":
				return(false);
			break;
		}
		
		return(true);
	}
	
	/**
	 * get action title
	 */
	private function getActionTitle($action){
		
		$title = "";
		
		switch($action){
			case "paste_elementor_section":
				$title = __("Paste Elementor Section","doubly");
			break;
		}
		
		
		return($title);
	}
	
	
	/**
	 * on ajax action
	 */
	public function onAjaxActions(){
		
		if(GlobalsDOUBLY::DEBUG_ERRORS == true){
			
			dmp("DEBUG ERROR WORKING, PLEASE TURN IT OFF!");
			ini_set("display_errors","on");
		}
				
		add_filter("wp_php_error_message", array("HelperDOUBLY","onPHPErrorMessage"),100,2);
	    
		@set_time_limit(240);	//set time limit - 240 seconds - 4 minutes
		
		$actionType = UniteFunctionsDOUBLY::getPostGetVariable("action","",UniteFunctionsDOUBLY::SANITIZE_KEY);
		
		if($actionType != GlobalsDOUBLY::PLUGIN_SLUG."_ajax_actions")
			return(false);
				
		$action = UniteFunctionsDOUBLY::getPostGetVariable("client_action","",UniteFunctionsDOUBLY::SANITIZE_KEY);
		
		//check front actions
		switch($action){
			case "front_action_demo":
									
				$this->onAjaxFrontAction();
				exit();
			break;
		}
		
		$data = $this->getDataFromRequest();
		
		//verify nonce
		
		$isVerifyNeeded = $this->isVerifyNeeded($action);
		
		//if no verification - set the enabled copy or paste only by general setting
		if($isVerifyNeeded == false)
			GlobalsDOUBLY::setEnabledCopyPasteBySetting();
		
		$operations = new Doubly_Operations();
		
		if(class_exists("FS_Api") == false)
			die();
		
		try{
			
			if($isVerifyNeeded == true){
				
				$nonce = UniteFunctionsDOUBLY::getPostGetVariable("nonce","",UniteFunctionsDOUBLY::SANITIZE_NOTHING);
				HelperDOUBLY::verifyNonce($nonce);
				
				//verify logged in, all actions except front copy
									
				$userID = get_current_user_id();
				if(empty($userID))
					UniteFunctionsDOUBLY::throwError("Operation not permitted");
				
			}
			
			switch($action){
				case "export_object":
					
					HelperDOUBLY::validateCopyEnabled($action);
					
					$operations->exportObjectFromData($data);
					
				break;
				case "export_post":
					
					HelperDOUBLY::validateCopyEnabled($action);
					
					$operations->exportPostFromData($data);
					exit();
				break;
				case "export_elementor_section":
					
					HelperDOUBLY::validateCopyEnabled($action);
					
					$operations->exportElementorSectionFromData($data);
					exit();
				break;
				case "show_post_data":
					
					HelperDOUBLY::validateCopyEnabled($action);
					
					$operations->showPostData($data);
					exit();
				break;
				case "copy_post":
					
					HelperDOUBLY::validateCopyEnabled($action);
					
					$response = $operations->copyPostFromData($data);
					
					HelperDOUBLY::ajaxResponseData($response);
					
				break;
				case "copy_elementor_section_front":
					
					HelperDOUBLY::validateCopySectionFrontEnabled();
					
					$response = $operations->copyElementorSectionFromData($data, true);
					
					HelperDOUBLY::ajaxResponseData($response);
					
				break;
				case "copy_elementor_section":
					
					HelperDOUBLY::validateCopySectionEnabled();
										
					$response = $operations->copyElementorSectionFromData($data);
					
					HelperDOUBLY::ajaxResponseData($response);
					
				break;
				case "paste_post":
					
					HelperDOUBLY::validatePasteEnabled();
					
					$response = $operations->pastePostFromData($data);
					
				break;
				case "import_post":
					
					HelperDOUBLY::validatePasteEnabled();
					
					$response = $operations->importPostFromData($data);
					
				break;
				case "import_elementor_section":
					
					HelperDOUBLY::validatePasteEnabled($action);
					
					$response = $operations->importElementorSectionFromData($data);
					
				break;
				case "paste_elementor_section":
					
					HelperDOUBLY::validatePasteEnabled($action);
					
					$operations->pasteElementorSectionFromData($data);
										
				break;
				case "get_copied_content":
					
					HelperDOUBLY::validateCopyEnabled($action);
					
					//get copied zip content, the output is from inside the function
					$operations->getCopiedZipContentFromData($data);
					
					exit();
					
				break;
				case "save_general_settings":
					
					$operations->saveGeneralSettingsFromData($data);
					
					$urlRedirect = HelperDOUBLY::getViewUrl(GlobalsDOUBLY::VIEW_SETTINGS);
					
					HelperDOUBLY::ajaxResponseSuccessRedirect( __("Settings Saved... Refreshing...","doubly"), $urlRedirect);
					
				break;
				case "import_content_test":
					
					if(GlobalsDOUBLY::$showDebugMenu == false)
						UniteFunctionsDOUBLY::throwError("function not avialable");
					
					$operations->importContentTest();
										
				break;
				default:
						HelperDOUBLY::ajaxResponseError("wrong ajax action: <b>$action</b> ");
				break;
			}
		
		}
		catch(Exception $e){
						
			$actionTitle = $this->getActionTitle($action);
			
			$prefix = null;
			if(!empty($actionTitle))
				$prefix = "$actionTitle error: ";
			
			$this->onException($e, $prefix);
		}
		
		//it's an ajax action, so exit
		HelperDOUBLY::ajaxResponseError("No response output on <b> $action </b> action. please check with the developer.");
		exit();
		
	}
	
	
	/**
	 * on ajax action
	 */
	public function onAjaxFrontActions(){
		
		if(GlobalsDOUBLY::DEBUG_ERRORS == true){
			dmp("DEBUG ERROR WORKING, PLEASE TURN IT OFF!");
		}
		
		$actionType = UniteFunctionsDOUBLY::getPostGetVariable("action","",UniteFunctionsDOUBLY::SANITIZE_KEY);
		
		if($actionType != GlobalsDOUBLY::PLUGIN_SLUG."_ajax_actions")
			return(false);
		
		$action = UniteFunctionsDOUBLY::getPostGetVariable("client_action","",UniteFunctionsDOUBLY::SANITIZE_KEY);
		$data = $this->getDataFromRequest();
		
		try{
			
			switch($action){
				default:
					HelperDOUBLY::ajaxResponseError("wrong ajax action: <b>$action</b> ");
				break;
			}
			
		}
		catch(Exception $e){
			$this->onException($e);
		}
		
		
		//it's an ajax action, so exit
		HelperDOUBLY::ajaxResponseError("No response output on <b> $action </b> action. please check with the developer.");
		exit();		
	}
	
	
	
}