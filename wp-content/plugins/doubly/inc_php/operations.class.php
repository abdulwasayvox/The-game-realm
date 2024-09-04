<?php
/**
 * @package Doubly
 * @author Unlimited Elements
 * @copyright (C) 2022 Unlimited Elements, All Rights Reserved. 
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 **/

if(!defined("DOUBLY_INC")) die("restricted access");

class Doubly_Operations{
	
	private $isSameDomain = false;
	

	/**
	 * validate post ids
	 */
	private function validatePostIDs($postIDs){
		
		if(empty($postIDs))
			return(true);
		
		if(is_numeric($postIDs) == true)
			return(true);
			
		if(is_array($postIDs) == false)
			UniteFunctionsDOUBLY::throwError("post ids should be array");
		
		$isValid = UniteFunctionsDOUBLY::isValidIDsArray($postIDs);
		
		if($isValid == false)
			UniteFunctionsDOUBLY::throwError("The id's array is not valid");
		
	}
	
	/**
	 * validate the copy content
	 */
	private function validateCopyContent($arrCopy){
		
		//validate type
		
		$type = UniteFunctionsDOUBLY::getVal($arrCopy, "type");
		
		switch($type){
			case GlobalsDOUBLY::EXPORT_TYPE_ELEMENTOR_SECTION:
			case GlobalsDOUBLY::EXPORT_TYPE_MEDIA:
			case GlobalsDOUBLY::EXPORT_TYPE_POSTS:
			case GlobalsDOUBLY::EXPORT_TYPE_SNIPPET:
			break;
			default:
				$type = esc_html($type);
				UniteFunctionsDOUBLY::throwError("Wrong export type: $type");
			break;
		}
		
		//validate post id
		
		$postID = UniteFunctionsDOUBLY::getVal($arrCopy, "postid");
		
		$this->validatePostIDs($postID);
		
		//validate keys
	}
	
	
	
	
	private function _____COPY________(){}

	/**
	 * get and validate post from data
	 */
	private function getValiadatePostFromData($data){
		
		$postID = UniteFunctionsDOUBLY::getVal($data, "postid");
		
		if(is_numeric($postID) == false && UniteFunctionsDOUBLY::isIDsListString($postID) == true)
			return(null);
		
		$postID = (int)$postID;
		
		UniteFunctionsDOUBLY::validateNotEmpty($postID,"post id");
	
		$post = get_post($postID);
		if(empty($post))
			UniteFunctionsDOUBLY::throwError("Wrong post id given");
		
		return($post);
	}
	
	/**
	 * get post type multiple lower case from posts
	 */
	private function getPostTypeFromArrPosts($arrPosts){
		
		//get post type
		$postType = null;
		$isTheSame = true;
		
		if(is_array($arrPosts) == false)
			$arrPosts = array($arrPosts);
		
		foreach($arrPosts as $post){
		
			$type = $post->post_type;
						
			if(empty($postType)){
				$postType = $type;
				continue;
			}
			
			if($postType != $type)
				$isTheSame = false;
		}
		
		if($isTheSame == false)
			return("posts");
					
		$objType = get_post_type_object($postType);
		
		if(empty($objType))
			return($postType);
			
		$name = $objType->label;
		
		$name = strtolower($name);
		
		$name = HelperDOUBLY::convertTitleToHandle($name);
		
		if(empty($name))
			$name = $postType;
		
		return($name);
	}
	
	
	/**
	 * get export filename from posts array
	 */
	private function getExportFilenameFromPosts($arrPosts){
		
		if(empty($arrPosts))
			UniteFunctionsDOUBLY::throwError("No exported posts found for name");
		
		$numPosts = count($arrPosts);
		
		if($numPosts == 1){
			
			$post = $arrPosts[0];
			$filename = $this->getExportFilenameFromPost($post);
			
			return($filename);
		}
		
		$postTypeName = $this->getPostTypeFromArrPosts($arrPosts);
		
		$ending = $this->getFilenameEnding();
		
		$filename = "doubly_{$numPosts}_{$postTypeName}{$ending}";
				
		return($filename);
	}
	
	
	
	/**
	 * check if the copy content is multiple
	 */
	private function isCopyContentMultiple($arrCopy){
		
		$type = UniteFunctionsDOUBLY::getVal($arrCopy, "type");
		
		switch($type){
			case GlobalsDOUBLY::EXPORT_TYPE_SNIPPET:
				
				$arrIDs = UniteFunctionsDOUBLY::getVal($arrCopy, "id");
				
			break;
			case GlobalsDOUBLY::EXPORT_TYPE_POSTS:
				
				$arrIDs = UniteFunctionsDOUBLY::getVal($arrCopy, "postid");
				
			break;
			default:
				return(false);
			break;
		}
		
		if(empty($arrIDs))
			return(false);
			
		if(is_array($arrIDs) == false)
			return(false);
			
		if(count($arrIDs) > 1)
			return(true);
	}
	
	/**
	 * copy the content
	 */
	private function copyContent($arrCopy, $postType = null){
		
		$type = UniteFunctionsDOUBLY::getVal($arrCopy, "type");
		
		$randomKey = UniteFunctionsDOUBLY::getRandomString();
		
		$transientName = "doubly_copy_{$randomKey}";
		
		$this->validateCopyContent($arrCopy);
		
		$isMultiple = $this->isCopyContentMultiple($arrCopy);
		
		
		$success = set_transient($transientName, $arrCopy, GlobalsDOUBLY::COPY_EXPIRATION_TIME);
		if($success == false)
			UniteFunctionsDOUBLY::throwError("Unable to copy the content");
		
		$arrReturn = array();
		$arrReturn["url"] = GlobalsDOUBLY::$urlAjax;
		$arrReturn["key"] = $randomKey;
		
		$prefix = "doubly_";
		if($type == "elementor_section")
			$prefix = "doubly_section_";
		else
			if($isMultiple == true)
				$prefix = "doubly_multiple_";
		
		$returnDataEncoded = $prefix.UniteFunctionsDOUBLY::encodeContent($arrReturn);
		
		$output = array();
		$output["copy_text"] = $returnDataEncoded;
		
		$postTypeName = "Post";
		
		if(!empty($postType)){
			
			$arrNames = HelperDOUBLY::getPostTypeTitles($postType);
			
			if($isMultiple == true)
				$postTypeName = UniteFunctionsDOUBLY::getVal($arrNames, "plural");
			else
				$postTypeName = UniteFunctionsDOUBLY::getVal($arrNames, "single");
		}
		
		$postTypeName = esc_html($postTypeName);
		
		$textSuccess = $postTypeName.__(" Copied to Clipboard!","doubly");
		
		if($type == "elementor_section")
			$textSuccess = __("Section Copied to Clipboard!","doubly");
		
		
		HelperDOUBLY::ajaxResponseSuccess($textSuccess,$output);
	}
	
	/**
	 * get filename ending
	 */
	private function getFilenameEnding(){
		
		$host = HelperDOUBLY::getUrlHostNoExtension();
		
		if(!empty($host))
			$host = $host."_";

		$time = date("d-m-y")."--".date("h-i");
		
		$ending = "_{$host}{$time}.zip";
		
		return($ending);
	}
	
	/**
	 * get export filename from post
	 */
	private function getExportFilenameFromPost($post, $sectionID = null){
		
		$postTitle = $post->post_title;
		$postName = $post->post_name;
		
		if(strlen($postName) < strlen($postTitle))
			$postTitle = $postName;
		
		$ending = $this->getFilenameEnding();
		
		$postType = $post->post_type;

		$postTitle = HelperDOUBLY::convertTitleToHandle($postTitle);
		
		if(!empty($sectionID))
			$filename = "doubly_section_{$postTitle}_{$sectionID}";
		else
			$filename = "doubly_{$postType}_{$postTitle}";
				
		$filename .= $ending;
		
		return($filename);
	}
	
	
	/**
	 * export object from data
	 */
	public function exportObjectFromData($data){
		
		$objType = UniteFunctionsDOUBLY::getVal($data, "objtype");
		$objID = UniteFunctionsDOUBLY::getVal($data, "id");
				
		
		UniteFunctionsDOUBLY::validateNotEmpty($objType,"object type");
		
		//check for multiple
		if(is_numeric($objID) == false){

			$isIDList = UniteFunctionsDOUBLY::isIDsListString($objID);
			
			if($isIDList == false)
				UniteFunctionsDOUBLY::throwError("Object id's not valid type");
		}
		
		
		$exportData = array();
		$exportData["type"] = "objects";
		$exportData["objtype"] = $objType;
		$exportData["id"] = $objID;
				
		$objExporter = new Doubly_PluginExporter();
		$filepathZip = $objExporter->exportPostFromData($exportData);
		
		$filename = $objExporter->getExportedFilename();
		
		$ending = $this->getFilenameEnding();
		
		$filename = "doubly_".$filename.$ending;
		
		
		UniteFunctionsDOUBLY::downloadFile($filepathZip, $filename);
		exit();
	}
	
	
	/**
	 * export post from data
	 */
	public function exportPostFromData($data){
		
		$post = $this->getValiadatePostFromData($data);
		
		if(!empty($post))
			$postID = $post->ID;
		else{
			$postID = UniteFunctionsDOUBLY::getVal($data, "postid");
			
			UniteFunctionsDOUBLY::validateIDsList($postID, "post ids");
		}
		
		$exportData = array();
		$exportData["type"] = "posts";
		$exportData["postid"] = $postID;

		$objExporter = new Doubly_PluginExporter();
		$filepathZip = $objExporter->exportPostFromData($exportData);
		
		if(!empty($post))
			$filename = $this->getExportFilenameFromPost($post);
		else{
			$arrPosts = $objExporter->getExportedPosts();
			$filename = $this->getExportFilenameFromPosts($arrPosts);
		}
		
		
		if(GlobalsDOUBLY::DEBUG_ERRORS == true){
			
			dmp("no file download - debug turn off the debug errors");
			exit();
		}
		
		UniteFunctionsDOUBLY::downloadFile($filepathZip, $filename);
		
		exit();
	}
	
	
	/**
	 * export elementor section from data
	 */
	public function exportElementorSectionFromData($data){
		
		$post = $this->getValiadatePostFromData($data);
		$postID = $post->ID;
		
		$sectionID = UniteFunctionsDOUBLY::getVal($data, "sectionid");
		UniteFunctionsDOUBLY::validateAlphaNumeric($sectionID);
		
		
		$exportData = array();
		$exportData["type"] = "elementor_section";
		$exportData["postid"] = $postID;
		$exportData["sectionid"] = $sectionID;
		
		$objExporter = new Doubly_PluginExporter();
		$filepathZip = $objExporter->exportPostFromData($exportData);
		
		$filename = $this->getExportFilenameFromPost($post, $sectionID);
		
		UniteFunctionsDOUBLY::downloadFile($filepathZip, $filename);
		exit();
	}
	
	/**
	 * copy elementor section
	 */
	public function copyElementorSectionFromData($data, $isFront = false){

		$postID = UniteFunctionsDOUBLY::getVal($data, "postid");
		$postID = (int)$postID;
		
		$sectionID = UniteFunctionsDOUBLY::getVal($data, "sectionid");
		
		UniteFunctionsDOUBLY::validateNotEmpty($sectionID,"section id");
		
		$post = get_post($postID);
		
		if(empty($post))
			UniteFunctionsDOUBLY::throwError("Post not found");
		
		
		//validate if front permitted - in case that it's front
		
		if($isFront == true){
			
			$isFrontCopyPermitted = HelperDOUBLY::isFrontCopyPermittedForPost($post);
			
			if($isFrontCopyPermitted == false)
				UniteFunctionsDOUBLY::throwError("Front copy not permitted for this post");
		
		}
		
		$arrCopy = array();
		$arrCopy["type"] = "elementor_section";
		$arrCopy["postid"] = $postID;
		$arrCopy["sectionid"] = $sectionID;
		
		$this->copyContent($arrCopy);
	}
	
	
	/**
	 * copy post action from data, for ajax request
	 * return the copy key
	 */
	public function copyPostFromData($data){
		
		$postID = UniteFunctionsDOUBLY::getVal($data, "postid");
				
		if(is_array($postID) == false)
			$postID = (int)$postID;
		else{
			$isValidArray = UniteFunctionsDOUBLY::isValidIDsArray($postID);
			if($isValidArray == false)
				UniteFunctionsDOUBLY::throwError("Not valid id's array");
		}
		
		UniteFunctionsDOUBLY::validateNotEmpty($postID,"post id");
		
		$postType = UniteFunctionsDOUBLY::getVal($data, "posttype");
		
		$copyMode = UniteFunctionsDOUBLY::getVal($data, "copymode");
		
		if(empty($copyMode))
			$copyMode = "posts";
		
		
		$arrCopy = array();
		$arrCopy["type"] = $copyMode;
		
		switch($copyMode){
			case "posts":
				$arrCopy["postid"] = $postID;
			break;
			case GlobalsDOUBLY::EXPORT_TYPE_SNIPPET:
				$arrCopy["id"] = $postID;
			break;
			default:
				UniteFunctionsDOUBLY::throwError("Wrong copy mode: ".$copyMode);
			break;
		}
		
		
		$this->copyContent($arrCopy, $postType);
				
	}
	
	
	private function _____PASTE________(){}


	/**
	 * get import file content from improted files 
	 */
	private function getImportFileContent(){
		
		if(empty($_FILES))
			UniteFunctionsDOUBLY::throwError("No import file found");
		
		$arrFile = UniteFunctionsDOUBLY::getVal($_FILES, "file");
		
		$size = UniteFunctionsDOUBLY::getVal($arrFile, "size");
		
		if($size == 0)
			UniteFunctionsDOUBLY::throwError("Wrong import file");
		
		$pathFile = UniteFunctionsDOUBLY::getVal($arrFile, "tmp_name");
		
		$zipContent = file_get_contents($pathFile);
		
		if(empty($zipContent))
			UniteFunctionsDOUBLY::throwError("No import file content found");
		
		return($zipContent);
	}
	
	
	/**
	 * get zip content from data
	 */
	private function pastePostFromData_getZipContent($data, $isSection){
		
		$copyContent = UniteFunctionsDOUBLY::getVal($data, "copy_text");
				
		//extract data		
		
		//replace the multiple to regular if exists		
		$keyContent = "doubly_";
		
		if($isSection == true)
			$keyContent = "doubly_section";
		else
			$copyContent = str_replace("doubly_multiple_", "doubly_", $copyContent);
		
		if(strpos($copyContent, $keyContent) === false)
			UniteFunctionsDOUBLY::throwError("Wrong copy post data","doubly");
		
		$copyContent = str_replace($keyContent, "", $copyContent);
		
		$arrContent = UniteFunctionsDOUBLY::decodeContent($copyContent);

		if(empty($arrContent))
			UniteFunctionsDOUBLY::throwError("The copy data is invalid","doubly");
		
		$url = UniteFunctionsDOUBLY::getVal($arrContent, "url");
		$key = UniteFunctionsDOUBLY::getVal($arrContent, "key");
		
		if($url == GlobalsDOUBLY::$urlAjax)
			$this->isSameDomain = true;
		
		// call the server, get the zip
		
		$urlAjaxRemote = HelperDOUBLY::getUrlRemoteAjax($url, "get_copied_content","key=".$key);
				
		$zipContent = UniteFunctionsDOUBLY::getUrlContents($urlAjaxRemote);
		
		if(empty(trim($zipContent)))
			UniteFunctionsDOUBLY::throwError("No Content Found");
		
		$length = strlen($zipContent);
		
		//check for errors
		
		if($length < 2000){
			
			//in case that it's json
			
			$arrJson = UniteFunctionsDOUBLY::maybeJsonDecode($zipContent);
			
			if(is_array($arrJson)){
				echo UniteFunctionsDOUBLY::escapeField($zipContent);
				exit();
			}
		}
		
		return($zipContent);
	}
	
	
	
	/**
	 * import post from some file
	 */
	public function importPostFromData($data){
		
		$zipContent = $this->getImportFileContent();
		
		$this->pastePostFromData($data, false, $zipContent);
	}
	
	/**
	 * import section from data
	 */
	public function importElementorSectionFromData($data){
		
		$zipContent = $this->getImportFileContent();
		
		$this->pastePostFromData($data, true, $zipContent);
	}
	
	
	/**
	 * paste elementor section
	 */
	public function pasteElementorSectionFromData($data){
		
		$this->pastePostFromData($data, true);
		
	}
	
	/**
	 * paste post from data
	 */
	public function pastePostFromData($data, $isSection = false, $zipContent = null){

		$isAdmin = UniteFunctionsDOUBLY::getVal($data, "isadmin");
		$isAdmin = UniteFunctionsDOUBLY::strToBool($isAdmin);
		
		$postID = UniteFunctionsDOUBLY::getVal($data, "postid");
		
		$pasteMode = UniteFunctionsDOUBLY::getVal($data, "paste_mode");
		
		$isObject = false;
		
		if($pasteMode == true){
			$isObject = true;
			$objType = $pasteMode;
		}
		
		if(empty($postID))
			UniteFunctionsDOUBLY::throwError(__("Destanation post not found","doubly"));
		
		if($isSection == true){
			
			$insertToSectionID = UniteFunctionsDOUBLY::getVal($data, "sectionid");
			$insertToSectionPosition = UniteFunctionsDOUBLY::getVal($data, "position");
			
			UniteFunctionsDOUBLY::validateNotEmpty($insertToSectionID, "insert to section id");
			UniteFunctionsDOUBLY::validateNotEmpty($insertToSectionPosition, "insert to section position");
		}
		
		
		if($postID !== "new"){
			
			if($isObject == true){	//objects - validate exists
			
				$object = new Doubly_Object();
				$object->init($objType, $postID);
				
			}else{		//posts
			
				$post = get_post($postID);
				
				if(empty($post))
					UniteFunctionsDOUBLY::throwError("No post destanation found","doubly");
			}
			
		}else
			$postID = null;
		
		
		if(empty($zipContent))
			$zipContent = $this->pastePostFromData_getZipContent($data, $isSection);
		
		
		$arrParams = array();
		
		if(!empty($postID))
			$arrParams["import_to_postid"] = $postID;
		
		if($isSection == true){
			$arrParams["import_to_sectionid"] = $insertToSectionID;
			$arrParams["import_to_section_position"] = $insertToSectionPosition;
		}
		
		$arrParams["is_same_domain"] = $this->isSameDomain;
		
		$objImporter = new Doubly_PluginImporter();
		$objImporter->setImportParams($arrParams);
		
		if(function_exists("fs_request_get") == false)
			die;
		
		try{
			
			$objImporter->importFromZipContent($zipContent);
			
		}catch(Exception $e){
						
			$message = $e->getMessage();
			
			$length = strlen($zipContent);
			
			//some debug output
			if($length < 2000){
				
				dmp($message);
				dmp($zipContent);
				exit();
				
			}
			else
				throw $e;
			
		}
		
		$arrLastData = $objImporter->getNumLastImportedData();
		
		
		$numLastPosts = UniteFunctionsDOUBLY::getVal($arrLastData, "num_imported");
		$importedPostType = UniteFunctionsDOUBLY::getVal($arrLastData, "post_type");
				
		$arrPostTypeTitles = HelperDOUBLY::getPostTypeTitles($importedPostType);
		
		$titleSingle = UniteFunctionsDOUBLY::getVal($arrPostTypeTitles, "single");
		$titlePlural = UniteFunctionsDOUBLY::getVal($arrPostTypeTitles, "plural");
		
		$urlImported = UniteFunctionsDOUBLY::getVal($arrLastData, "url_imported");
		
		
		$successText = $titleSingle.__(" Imported Successfully. Refreshing...","doubly");
		
		if($isSection == true)
			$successText = __("Section Imported Successfully. Refreshing...","doubly");
		
		$urlPost = "";
		
		if(!empty($urlImported))
			$urlPost = $urlImported;		//set by importer class, like in objects
		
		//multiple
		if($isSection == false && is_numeric($numLastPosts) && $numLastPosts > 1){		//posts import
			
			$successText = $numLastPosts ." ".$titlePlural. __(" Imported Successfully. Refreshing...","doubly");

		}
		
		//get url for single post
		if($isSection == false && $numLastPosts == 1 && empty($urlPost))
			$urlPost = $objImporter->getLastImportedPostUrl($isAdmin);
		
		
		$arrOutput = array();
		$arrOutput["url_post"] = $urlPost;
		
		HelperDOUBLY::ajaxResponseSuccess($successText, $arrOutput);
	}
	
		
	
	private function _____OTHERS________(){}
	
	/**
	 * get copied content from data
	 */
	public function getCopiedZipContentFromData($data){
		
		$key = UniteFunctionsDOUBLY::getVal($data, "key");
		UniteFunctionsDOUBLY::validateNotEmpty($key, "Key");
		
		$transientName = "doubly_copy_{$key}";
				
		$copyData = get_transient($transientName);
		
		if(empty($copyData))
			UniteFunctionsDOUBLY::throwError("No copy data available");
		
		//delete the transiend, don't allow to copy twice
		delete_transient($transientName);
		
		$type = UniteFunctionsDOUBLY::getVal($copyData, "type");
		
		//modify copy data for objects
		
		switch($type){
			
			case GlobalsDOUBLY::EXPORT_TYPE_SNIPPET:
				
				$newCopyData = array();
				$newCopyData["type"] = "objects";
				$newCopyData["objtype"] = $type;
				$newCopyData["id"] = UniteFunctionsDOUBLY::getVal($copyData, "id");
				
				$copyData = $newCopyData;
				
				$type = "objects";
				
			break;
			
			
		}
		
		
		switch($type){
			case "posts":
			case "elementor_section":
			case "objects":
			break;
			default:
				UniteFunctionsDOUBLY::throwError("The type: $type not supported for copy");
			break;
		}
		
		$objExporter = new Doubly_PluginExporter();
		$filepathZip = $objExporter->exportPostFromData($copyData);
			
		if(empty($filepathZip))
			UniteFunctionsDOUBLY::throwError("Error generating copy content");
		
		if(file_exists($filepathZip) == false)
			UniteFunctionsDOUBLY::throwError("The copy content not generated");
		
		$content = file_get_contents($filepathZip);
		
		header("Content-Type: text/plain");
		echo UniteFunctionsDOUBLY::escapeField($content);
		exit();
	}
	
	
	/**
	 * make some test task
	 */
	public function importContentTest(){
		
		$objImporter = new Doubly_PluginImporter();
		
		$postID = UniteFunctionsDOUBLY::getGetVar("postid","",UniteFunctionsDOUBLY::SANITIZE_ID);
		
		if(empty($postID))
			$postID = "new";
		
		$arrParams = array();
		$arrParams["import_to_sectionid"] = "new";
		$arrParams["import_to_section_position"] = "after";
		
		$objImporter->setImportParams($arrParams);
			
		$objImporter->importFromTestFile($postID);
				
	}
	
	
	/**
	 * save the settings from data
	 */
	public function saveGeneralSettingsFromData($data){
		
		$arrValues = UniteFunctionsDOUBLY::getVal($data, "settings_values");
		
		if(empty($arrValues))
			$arrValues = array();
		
		update_option(GlobalsDOUBLY::OPTION_GENERAL_SETTINGS, $arrValues);
		
	}
	
	
	/**
	 * show post data from data
	 */
	public function showPostData($data){
		
		if(GlobalsDOUBLY::$showDebugMenu == false)
			UniteFunctionsDOUBLY::throwError("function not available");
		
		$postID = UniteFunctionsDOUBLY::getVal($data, "postid");
		UniteFunctionsDOUBLY::validateNotEmpty($postID,"post id");
		
		$this->showPost($postID);
		
	}
	
	
	
	
	/**
	 * show post data
	 */
	private function showPost($showPostID){
	
		$post = get_post($showPostID);
		
		dmp($post);
		
		//------- meta ------- 
		
		echo "<div style='background-color:lightgray'>";
		
		$meta = get_post_meta($showPostID);
		
		dmp("Meta: ");
		dmp($meta);
		
		$elementorData = UniteFunctionsDOUBLY::getVal($meta, "_elementor_data");
		if(!empty($elementorData)){
			$elementorData = $elementorData[0];
			$elementorData = UniteFunctionsDOUBLY::jsonDecode($elementorData);
			
			dmp($elementorData);
		}
		
		echo "</div>";
		
		//------- terms  ------- 
		
		echo "<div style='background-color:#F0E9B2'>";
		
		$arrTerms = UniteFunctionsWPDOUBLY::getPostTerms($post);
		
		
		dmp("Terms:");
		dmp($arrTerms);
		
		echo "</div>";
		
		//------- blocks ------- 
		
		echo "<div style='background-color:#B8EFB3'>";
		
		if(has_blocks($post)){
			
			dmp("----------------------------");
			
			dmp("gutenberg:");
			$content = $post->post_content;
			dmp(htmlspecialchars($content));
			
			dmp("----------------------------");
			
			$blocks = parse_blocks($content);
			
			$blocks = HelperDOUBLY::modifyBlocksForShow($blocks);
			
			dmp($blocks);
		}
		
		echo "</div>";
		
		
		if($post->post_type == "product"){
			
			echo "<div style='background-color:#BAD7E8'>";
			
	    	$objInfo = wc_get_product($showPostID);
	    	$arrData = $objInfo->get_data();
			$type = $objInfo->get_type();
	    	
			if($type == "variable"){
				$arrVariations = $objInfo->get_available_variations();
				dmp("Product Variations");
				
				dmp($arrVariations);
			}

			echo "</div>";
			
			
		}
		
		exit();
	}
	
	
	
}