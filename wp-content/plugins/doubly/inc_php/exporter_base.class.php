<?php
/**
 * @package Doubly
 * @author Unlimited Elements
 * @copyright (C) 2022 Unlimited Elements, All Rights Reserved. 
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 **/

if(!defined("DOUBLY_INC")) die("restricted access");

class Doubly_PluginExporterBase{
	
	const KEY_LOCAL = "[uc_local]";
	const KEY_LOCAL_IMAGE = "[uc_local_image]";
	const KEY_LOCAL_IMAGE_SIZE = "[uc_local_image_size]";
	const KEY_LOCAL_IMAGE_SIZE_END = "[/uc_local_image_size]";
	const KEY_LOCAL_IMAGE_LINK = "[uc_local_image_link]";
	const KEY_BASE_URL = "[uc_baseurl]";
	
	protected $isUEInstalled = false;
	protected $isElementorInstalled = false;
	
	
	/**
	 * is UE installed
	 */
	public function __construct(){
		
		$this->isUEInstalled = HelperDOUBLY::isUEInstalled();
		
		$this->isElementorInstalled = HelperDOUBLY::isElementorInstalled();
		
	}
	
	
	/**
	 * change elementor element id's 
	 */
	protected function modifyElementorElementIDs($arr, $checkIsNumeric = false){
		
		if(is_array($arr) == false)
			return($arr);
		
		if(isset($arr["elType"]) == false)
			return($arr);
			
		if(isset($arr["id"]) == false)
			return($arr);
		
		if($checkIsNumeric == true && is_numeric($arr["id"]) == false)
			return($arr);
		
		$arr["id"] = HelperDOUBLY::generateElementorID();
		
		return($arr);
	}
	
	/**
	 * get UE widget alias. if the array is not wiget, skip
	 */
	protected function getUEWidgetAlias($arrWidget){
		
		if($this->isUEInstalled == false)
			return(false);
		
		$type = UniteFunctionsDOUBLY::getVal($arrWidget, "elType");
		
		if(empty($type))
			return(false);
			
		if($type !== "widget")
			return(false);
		
		$widgetType = UniteFunctionsDOUBLY::getVal($arrWidget, "widgetType");
		
		if(strpos($widgetType, "ucaddon_") === false)
			return(false);
		
		$alias = str_replace("ucaddon_", "", $widgetType);
		
		return($alias);
	}
	
	/**
	 * convert blocks
	 */
	protected function convertGutenbergBlocks($arrBlocks){
		
		if(empty($arrBlocks))
			return($arrBlocks);
			
		if(is_array($arrBlocks) == false)
			return($arrBlocks);
		
		foreach($arrBlocks as $index => $block){
			
			$innerBlocks = UniteFunctionsDOUBLY::getVal($block, "innerBlocks");
			
			if(!empty($innerBlocks)){
				
				$newInnerBlocks = $this->convertGutenbergBlocks($innerBlocks);
								
				$block["innerBlocks"] = $newInnerBlocks;
			}
			
			$newBlock = $this->convertGutenbergBlock($block);
						
			if(!empty($block) && empty($newBlock))
				UniteFunctionsDOUBLY::throwError("Gutenberg block convert error: return emtpy block");
						
			$arrBlocks[$index] = $newBlock;
		}
		
		return($arrBlocks);
	}
	
	
	/**
	 * show gutenberg blocks before after
	 */
	protected function showGutenbergBlocksBeforeAfter($arrBlocks, $arrBlocksNEW){
		
		dmp("---- old blocks ----");
		
		$arrBlocks = HelperDOUBLY::modifyBlocksForShow($arrBlocks);
		dmp($arrBlocks);
					
		echo "<div style='background-color:yellow'>";
		
		dmp("---- new blocks ----");
		
		$arrBlocksNEW = HelperDOUBLY::modifyBlocksForShow($arrBlocksNEW);
		dmp($arrBlocksNEW);
		
		echo "</div>";
		
	}
	
	
}