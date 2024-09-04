<?php
/**
 * @package Doubly
 * @author Unlimited Elements
 * @copyright (C) 2022 Unlimited Elements, All Rights Reserved. 
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 **/

if(!defined("DOUBLY_INC")) die("restricted access");

class Doubly_PluginGeneralSettings{
	
		
	/**
	 * get general settings
	 */
	public static function getSettingsObject(){
		
		$settings = new UniteSettingsDOUBLY();
				
		$arrItems = array(
			"all"=>__("Copy and Paste","doubly"),
			"copy"=>__("Copy Only","doubly"),
			"paste"=>__("Paste Only","doubly"),
			"none"=>__("None (plugin disabled)","doubly")
		);
		
		$arrItems = array_flip($arrItems);
		
		$settings->addSelect("enabled_func", $arrItems, __("Enabled Functionality","doubly"), "all");
		
		
		//---- roles
		
		$arrRoles = UniteFunctionsWPDOUBLY::getRolesNames();
		$arrRoles = array_flip($arrRoles);
		
		$params = array();
		$params["description"] = __("Select user roles that will be allowed to use the plugin","doubly");
		
		$settings->addMultiSelect("allowed_roles", $arrRoles, __("Allowed Roles","doubly"), "administrator", $params);

		
		//---- existing post action
				
		$arrItems = array(
			"new"=>__("Add New Post","doubly"),
			"overwrite"=>__("Overwrite Existing","doubly"),
			"skip"=>__("Skip","doubly")
		);
		
		$arrItems = array_flip($arrItems);
		
		$settings->addSelect("existing_post_action", $arrItems, __("When Similar Post Found on Import","doubly"), "new");
		
		
		/**
		 * option reserved only for pro
		 */
		if(GlobalsDOUBLY::$isProVersion == true)
			$settings = Doubly_Pro::addProGeneralSettings($settings, $arrRoles);
			
		
		//----- unlimited elements integration
		
		$isUEInstalled = HelperDOUBLY::isUEInstalled();
		
		if($isUEInstalled == true){
			
			$arrItems = array(
				"add"=>__("Yes","doubly"),
				"not_add"=>"No",
			);
			
			$arrItems = array_flip($arrItems);
			
			$params = array(
				"description"=>__("Unlimited Elements plugin Integration. Choose if add widgets together with the copy content, or not to add. In case that they are not added, doubly will try to install them on paste from the catalog.", "doubly")
			);
			
			$settings->addSelect("unlimited_elements_add_widgets", $arrItems, "Unlimited Elements Integration <br>Include Widgets with copy content", "add", $params);
		}
		
		// ------ Front End Copy free ------------
		
		if(GlobalsDOUBLY::$isProVersion == false){
		
			$params = array(
				"description"=>__("Enable front end section copy button for site quests. In pro version only", "doubly"),
				"disabled" => true
			);
			
			$arrItems = array(
				__("No","doubly")=>"no",
			);
			
			
			$settings->addSelect("enabled_elementor_front_copy_free", $arrItems, __("Enable Elementor Front Section Copy (pro)","doubly"), "no", $params);
		}
		
		
		return($settings);
	}
	
}