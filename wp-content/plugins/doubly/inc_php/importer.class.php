<?php
/**
 * @package Doubly
 * @author Unlimited Elements
 * @copyright (C) 2022 Unlimited Elements, All Rights Reserved. 
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 **/

if(!defined("DOUBLY_INC")) die("restricted access");

class Doubly_PluginImporter extends Doubly_PluginExporterBase{
	
	const DEBUG_SHOW_SUMMARY = false;
	
	const DEBUG_SHOW_UNZIPPED_FILES = false;
	
	const DEBUG_CONVERT_STRINGS = false;
	const DEBUG_SHOW_GUTENBERG_BLOCKS = false;
	const DEBUG_META_STRING_BEFORE_AFTER = false;
	
	const DEBUG_META_ARRAYS = false;
	
	const DEBUG_META = false;
	const DEBUG_INSERTED_TERMS = false;
	const DEBUG_INSERTED_COMMENTS = false;
	
	const DEBUG_INSERTED_POST = false;
	
	const DEBUG_PHP_ERRORS = false;
	const DEBUG_IMPORTED_WIDGETS = false;
	
	private $pathImportContentFiles;
	private $filepathImportZipFile;
	private $pathImportImages;
	private $pathImportWidgets;
	private $pathImportBGWidgets;
	private $isElementorData = false;
	
	private $originalString;
	private $originalArray;
	private $arrSummary = array();
	
	private $arrContent;
	private $arrContent_terms;
	private $arrContent_wooAttributes;
	
	private $arrImportedImages = array();
	private $arrImportedTerms = array();
	
	private $param_importToPostID;
	private $param_importToSectionID;
	private $param_importToSectionPosition;
	private $param_isSameDomain;
	
	private $lastImportedType = "";
	private $numLastImportedPosts = 0;
	private $lastImportedPostID;
	private $lastImportedUrl = null;
	private $numLastImportedImages = 0;
	private $isImageConverted = false;
	private $imageIDAttribute = false;
	private $existingPostAction = "new";	//new, overwrite, skip
	
	
	//use as: array("from"=>"to");
	private $arrSpecialRename = array(); 	
	
	private $type;
	
	
	/**
	 * construct
	 */
	public function __construct(){
		
		parent::__construct();
				
	}
	
	/**
	 * print some test text
	 */
	private function printTestText(){
		
		//echo "<b>";
		
		dmp("all ok, post imported!");
				
		dmp($this->pathImportContentFiles);
		
		$this->printSummary();
		
		
		if(!empty($this->lastImportedPostID))
			$this->debug("show_inserted_post", $this->lastImportedPostID);
		
		//dmp("Unpack the zip file there");
		
		//echo "</b>";
	}
	
	/**
	 * init before import
	 */
	private function initBeforeImport(){
		
		$this->existingPostAction = HelperDOUBLY::getGeneralSetting("existing_post_action");
		
		$this->addSummary("Existing post action: ".$this->existingPostAction);
		
	}
	
	
	/**
	 * 
	 * create folders
	 */
	private function createFolders(){
		
		$pathCache = GlobalsDOUBLY::$pathCache;
		UniteFunctionsDOUBLY::mkdirValidate($pathCache, "Cache");
		
		//write index.html file
		UniteFunctionsDOUBLY::writeFileIfNotExists("", $pathCache."index.html");
		
		$pathImport = $pathCache."import/";
		UniteFunctionsDOUBLY::mkdirValidate($pathImport, "Import");
		
		//write index.html file
		UniteFunctionsDOUBLY::writeFileIfNotExists("", $pathImport."index.html");
		
		//create import content folder
		$pathImportContent = $pathImport."content/";
		UniteFunctionsDOUBLY::mkdirValidate($pathImportContent, "Import Content");
		UniteFunctionsDOUBLY::writeFileIfNotExists("", $pathImportContent."index.html");
		
		//clear import content
		UniteFunctionsDOUBLY::clearDirByTime($pathImportContent);
		
		//create import zips folder
		$pathImportZips = $pathImport."zips/";
		UniteFunctionsDOUBLY::mkdirValidate($pathImportZips, "Import Zips");
		UniteFunctionsDOUBLY::writeFileIfNotExists("", $pathImportZips."index.html");
		
		//clear import zips folder
		UniteFunctionsDOUBLY::clearDirByTime($pathImportZips);
				
		$randomString = UniteFunctionsDOUBLY::getRandomString(10);
		
		//set import zip file for copy
		$this->filepathImportZipFile = $pathImportZips."doubly_import_{$randomString}.zip";
		
		//create import content files folder
		$this->pathImportContentFiles = $pathImportContent."content_".$randomString."/";
		UniteFunctionsDOUBLY::mkdirValidate($this->pathImportContentFiles, "Import Content Folder");
		
		UniteFunctionsDOUBLY::writeFileIfNotExists("", $this->pathImportContentFiles."index.html");
		
		//set other paths:
		$this->pathImportImages = $this->pathImportContentFiles."images/";
		
		$this->pathImportWidgets = $this->pathImportContentFiles."widgets/";
		 
		$this->pathImportBGWidgets = $this->pathImportContentFiles."widgets_bg/"; 
		
	}
	
	
	/**
	 * debug meta string before
	 */
	private function debug($type, $arg1, $arg2 = null, $arg3 = null){

		switch($type){
			case "convert_strings_before":	//$key, $str
				
				dmp("convert: <span style='color:red;'>$arg1</span>");
				dmp(htmlspecialchars($arg2));
				
				$this->originalString = $arg2;
				
			break;
			case "convert_strings_after":
				
				echo "<span style='color:blue'>";
				
				if($arg2 != $this->originalString)
					dmp(htmlspecialchars($arg2));
				
				echo "</span>";
				
			break;
			case "meta_string_before":	//$key, $str
				
				dmp("before: $arg1 | $arg2");
				
				$this->originalString = $arg2;
			break;			
			case "meta_string_after":	//key, str, prefix
				
				dmp($arg3);
				
				if($arg2 != $this->originalString)
					dmp("<span style='color:blue'>after: $arg1 | $arg2</span>");
				else
					dmp("after: $arg1 | $arg2");				
				
			break;
			case "meta_array_before":	//key, str
			
				$this->originalArray = $arg2;
				
				echo "<div style='display:flex;border-bottom:1px solid red;padding-bottom:30px;padding-top:30px;'>";
			
				echo "<div style='overflow:scroll;width:500%;border:1px solid lightgray;padding:5px;'>";
				
				dmp("<b>----- BEFORE-------</b>");
				dmp("<b>{$arg1}</b>");
				dmp($arg2);
				echo "</div>";
				
			break;
			case "meta_array_after":
				
				$diff = "";
				$color = "blue";
				
				if($this->originalArray === $arg2){
					$arg2 = "no change";
					$color = "black";
				}else{
					$diff = UniteFunctionsDOUBLY::arrayRecursiveDiff($this->originalArray, $arg2);
				}
				
					
				echo "<div style='color:".esc_attr($color).";overflow:scroll;width:500%;border:1px solid lightgray;padding:5px;'>";
				
				dmp("<b>----- AFTER -------</b>");
				dmp("<b>{$arg1}</b>");
				
				//dmp($arrDiff);
				dmp($arg2);
								
				if(!empty($diff)){
					
					dmp("-------CHANGES---------");
					
					dmp($diff);
					
				}
								
				dmp("----------------------------------------------------");
				echo "</div>";
				
				echo "</div>";
				
				
			break;
			case "meta_before":		// arrMeta, title
				
				dmp("Meta Before: <b>$arg2</b>");
				dmp($arg1);
				
			break;
			case "meta_after":		// arrMeta, title
				
				dmp("After");
				dmp($arg1);
				
			break;
			case "term_meta_before":		// arrMeta, name
				
				dmp("Term Meta Before: <b>$arg2</b>");
				dmp($arg1);
			break;
			case "term_meta_after":		// arrMeta
				
				dmp("After");
				dmp($arg1);
				
			break;
			
			case "show_terms":	//postiD
				
				$post = get_post($arg1);
				
				dmp("DEBUG TERMS");
				
				dmp("terms of inserted post: ".$post->post_title);
				
				$arrTerms = UniteFunctionsWPDOUBLY::getPostTerms($post);
				
				
				dmp($arrTerms);
				
				dmp("stop importing...");
				exit();
				
			break;
			case "show_comments":
				
				$args = array();
				$args["post_id"] = $post->ID;
				
				$arrComments = get_comments($args);
				
				dmp("comments of inserted post: ".$post->post_title);
				
				dmp($arrComments);
				
			break;
			case "show_inserted_post":
				
				if(empty($arg1))
					return(false);
				
				dmp("------------- <b>DEBUG_INSERTED_POST </b>-------------");
				$post = get_post($arg1);
				
				$postContent = $post->post_content;
				
				$post->post_content = ".... [show later]";
				
				dmp($post);
				
				
				dmp("-------------<b>post content: </b>-------------");
				
				dmp(htmlspecialchars($postContent));
				
				dmp("-------------<b>post meta: </b>-------------");
				
				$postMeta = get_post_meta($arg1, null, true);
				
				dmp($postMeta);
				
				dmp("-------------<b>post terms: </b>-------------");
				
				$arrTerms = UniteFunctionsWPDOUBLY::getPostTerms($post);
				
				dmp($arrTerms);

				dmp("-------------<b>post comments: </b>-------------");

				$args = array();
				$args["post_id"] = $post->ID;
				
				$arrComments = get_comments($args);
				
				dmp($arrComments);

				//gutenberg
				
				if(has_blocks($postContent)){
					
					$arrBlocks = parse_blocks($postContent);
				
					
					dmp("-------------<b>gutenberg blocks: </b>-------------");
					
					dmp($arrBlocks);
					
				}
				
				
				dmp("stop importing...");
				exit();
				
			break;
			default:
				
				UniteFunctionsDOUBLY::throwError("Wrong debug type: $type");
				
			break;
		}
						
	}
	
	/**
	* add the summary
	*/
	private function addSummary($text){
		
		$this->arrSummary[] = $text;
	}
	
	/**
	* print the summary
	*/
	private function printSummary(){
		
		dmp("Import Summary");
		dmp($this->arrSummary);
		
		if(empty($this->arrSummary))
			dmp("no summary :( ");
		
	}
	
	private function _________UE_INTEGRATE________(){}
	
	/**
	 * convert elementor ue post list
	 */
	private function convertMeta_handleElementorUEPostList($arr){
		
		//check if same domain - no need to convert nothing if do
		if($this->param_isSameDomain == true)
			return($arr);
		
		$alias = $this->getUEWidgetAlias($arr);
				
		if(empty($alias))
			return($arr);
		
		//get the special key - post list name
		
		$postListName = UniteFunctionsDOUBLY::getVal($arr, "ue_post_list_name");
				
		if(empty($postListName))
			return($arr);
		
		unset($arr["ue_post_list_name"]);
		
		$arrSettings = UniteFunctionsDOUBLY::getVal($arr, "settings");
		
		if(empty($arrSettings))
			return($arr);
		
		$arrKeysToClear = array("category");
		
		foreach($arrSettings as $key => $value){
			
			if(strpos($key, $postListName) !== 0)
				continue;
			
			if(empty($value))
				continue;
			
			//clear id's list
			$isIDsList = UniteFunctionsDOUBLY::isValidIDsArray($value);
			
			if($isIDsList == true){
				unset($arrSettings[$key]);
				continue;
			}
			
			$shortKey = str_replace($postListName."_", "", $key);
			
			$isClear = in_array($shortKey, $arrKeysToClear);
			
			if($isClear == true)
				unset($arrSettings[$key]);
		}
		
		//change manual to custom
		
		$source = UniteFunctionsDOUBLY::getVal($arrSettings, $postListName."_source");
		if($source == "manual")
			unset($arrSettings[$postListName."_source"]);
		
		$arr["settings"] = $arrSettings;
		
		return($arr);					
	}
	
	
	private function _________CONVERT_STRINGS________(){}
	
	
	/**
	 * convert image key with size to image url
	 */
	private function convertString_imageSize($str, $index, $imageID){
		
		if($index === false)
			UniteFunctionsDOUBLY::throwError("wrong index for image size");

		$indexEnd = strpos($str, self::KEY_LOCAL_IMAGE_SIZE_END, $index);
			
		if($indexEnd === false)
			UniteFunctionsDOUBLY::throwError("wrong end index for image size");
		
		//find content
			
		$postStart = $index + strlen(self::KEY_LOCAL_IMAGE_SIZE);
		$length = $indexEnd - $postStart;
		
		$content = substr($str, $postStart, $length);
		
		if(empty($content))
			UniteFunctionsDOUBLY::throwError("wrong image size content");
		
		//find size
			
		$arrContent = explode("||", $content);
		
		if(count($arrContent) !== 2)
			UniteFunctionsDOUBLY::throwError("wrong image size content count");
		
		$size = $arrContent[1];
		
		if(empty($size))
			UniteFunctionsDOUBLY::throwError("empty image size value");
		
		
		//get image url
			
		$urlImage = UniteFunctionsWPDOUBLY::getUrlAttachmentImage($imageID, $size);
		
		if(empty($urlImage))
			UniteFunctionsDOUBLY::throwError("no image found by size");
		
		//replace tags by image
		
		$posEnd = $indexEnd + strlen(self::KEY_LOCAL_IMAGE_SIZE_END);

		$replaceLength = $posEnd - $index;
		
		$str = substr_replace($str, $urlImage, $index,$replaceLength);

		
		return($str);
	}
	
	
	/**
	 * convert string image
	 */
	private function convertString_image($str, $key = null){
		
		$index = strpos($str, self::KEY_LOCAL);
		
		if($index === false)
			$index = strpos($str, self::KEY_LOCAL_IMAGE);

		if($index === false)
			$index = strpos($str, self::KEY_LOCAL_IMAGE_SIZE);
		
		if($index === false)
			return($str);
		
		foreach($this->arrImportedImages as $key => $imageID){
			
			//search for ID
			
			$index = strpos($str, self::KEY_LOCAL.$key);
			if($index !== false){
				$str = str_replace(self::KEY_LOCAL.$key, $imageID, $str);
			}
			
			//search for image url
			
			$index = strpos($str, self::KEY_LOCAL_IMAGE.$key);
			if($index !== false){
				
				$urlImage = UniteFunctionsWPDOUBLY::getUrlAttachmentImage($imageID);
				
				if(!empty($urlImage))
					$str = str_replace(self::KEY_LOCAL_IMAGE.$key, $urlImage, $str);
			}
			
			//search for some size image
			$index = strpos($str, self::KEY_LOCAL_IMAGE_SIZE.$key);
			if($index !== false){
				
				//counter is protection
				$counter = 0;
				do{
					$str = $this->convertString_imageSize($str, $index, $imageID);
					$index = strpos($str, self::KEY_LOCAL_IMAGE_SIZE.$key);
					
					$counter++;
					
				}while($index !== false && $counter < 10);
				
			}			
			
		}
		
		//validation
		
		$index = strpos($str, self::KEY_LOCAL);
		
		if($index !== false){
			
			$strToShow = UniteFunctionsDOUBLY::sanitizeFilenameForOutput($str);
			
			UniteFunctionsDOUBLY::throwError("images: <b>$strToShow</b> not imported well");
		}

		$str = trim($str);
				
		return($str);
	}
	
	/**
	 * apply special renames if not empty
	 */
	private function convertString_applySpecialRenames($str){
		
		if(empty($this->arrSpecialRename))
			return($str);

		foreach($this->arrSpecialRename as $from=>$to){
			
			if($str == $from){
				
				//dmp("special rename: $from to $to");
				
				$str = $to;
				
				
			}
		}
			
		return($str);
	}
	
	
	/**
	 * convert string
	 */
	private function convertString($str, $key){
		
		if(is_string($str) == false)
			return($str);
		
		if(empty($str))
			return($str);
					
		if(self::DEBUG_CONVERT_STRINGS == true)
			$this->debug("convert_strings_before",$key, $str);
		
		if($key == "post_content"){
			
			$str = $this->convertGutenbergContent($str);
			
			//$str = str_replace("\\u002d", "-", $str);
		}
		
		//---- convert base url -----
		
		$index = strpos($str, self::KEY_BASE_URL);
		
		if($index !== false)
			$str = str_replace(self::KEY_BASE_URL, GlobalsDOUBLY::$urlBase, $str);
		
		//-------- local image url to id ---------
		
		$str = $this->convertString_image($str, $key);
		
		//-------- speial renames ---------
		
		$str = $this->convertString_applySpecialRenames($str);
		

		if(self::DEBUG_CONVERT_STRINGS == true)
			$this->debug("convert_strings_after",$key, $str);
		
			
		return($str);
	}
		
	
	/**
	 * convert strings array
	 * may be recursive
	 */
	private function convertStringsArray($arr){
		
		if($this->isElementorData == true){
			
			$arr = $this->convertMeta_handleElementorUEPostList($arr);
		}
		
		if(is_array($arr) == false)
			return($arr);
		
		$wasImageChange = false;
		
		foreach($arr as $key => $value){
			
			if($key == "url" && $wasImageChange == true)
				continue;
			
			if(is_string($value)){
				
				$isImageID = false;
				
				//check the image convert
				
				if($key == "id" && strpos($value, self::KEY_LOCAL) !== false){
					$isImageID = true;
					$wasImageChange = true;
				}
				
				$value = $this->convertString($value, $key);
				
				$arr[$key] = $value;
				
				//put elementor url
				if($isImageID == true){
					
					if(array_key_exists("url", $arr) && empty($arr["url"])){
						
						$url = UniteFunctionsWPDOUBLY::getImageUrlByID($value);
						
						$arr["url"] = $url;
					}
					
				}
				
				
				continue;
			}				
			
			if(is_array($value))
				$arr[$key] = $this->convertStringsArray($value);
		}
		
		
		return($arr);
	}
	
	
	/**
	 * modify the meta string
	 */
	private function modifyMetaString($key, $str){
		
		$isDebugStrings = self::DEBUG_META_STRING_BEFORE_AFTER;
		$isDebugArrays = self::DEBUG_META_ARRAYS;
		
		
		if($isDebugStrings)
			$this->debug("meta_string_before", $key, $str);
		
		//----- serialize -----
		
		$arr = UniteFunctionsDOUBLY::maybeUnserialize($str);
		
		if(is_array($arr)){
			
			if($isDebugArrays)
				$this->debug("meta_array_before",$key, $arr);
			
			$arr = $this->convertStringsArray($arr);
			
			if($isDebugArrays)
				$this->debug("meta_array_after",$key, $arr);
			
			$str = serialize($arr);
			
			if($isDebugStrings)
				$this->debug("meta_string_after", $key, $str, "after serualize");
			
			return($str);
		}

		//----- json -----
		
		$arr = UniteFunctionsDOUBLY::maybeJsonDecode($str);
		if(is_array($arr)){

			if($isDebugArrays)
				$this->debug("meta_array_before",$key, $arr);
			
			$arr = $this->convertStringsArray($arr);
			
			if($isDebugArrays)
				$this->debug("meta_array_after",$key, $arr);
			
			$str = json_encode($arr);

			if($isDebugStrings)
				$this->debug("meta_string_after", $key, $str, "after json");
			
			return($str);
		}

		// ------------ regular -------------
		
		$str = $this->convertString($str, $key);
		
		if($isDebugStrings)
			$this->debug("meta_string_after", $key, $str);
		
		
		return($str);
	}
	
	/**
	 * modify meta array, check for json or serialization
	 */
	private function modifyMetaArray($arrMeta){
		
		if(empty($arrMeta))
			return($arrMeta);
			
		if(is_array($arrMeta) == false)
			return($arrMeta);
		
		foreach($arrMeta as $key=>$value){
			
			$this->isElementorData = ($key == "_elementor_data");
			
			$value = $this->modifyMetaString($key, $value);
			
			$arrMeta[$key] = $value;
		}
				
		return($arrMeta);
	}
	
	
	/**
	 * import post comments
	 */
	private function importPostComments($postID, $arrComments){
		
		if(empty($arrComments))
			return(false);
		
		$arrParentIDs = array();

		$arrChildrenIDs = array();

		//insert all the comments
		
		foreach($arrComments as $commentData){
			
			$comment = $commentData;
			
			$comment["comment_post_ID"] = $postID;
			
			$oldID = UniteFunctionsDOUBLY::getVal($comment, "doubly_id");
			$parentID = UniteFunctionsDOUBLY::getVal($comment, "doubly_parentid");

			unset($comment["doubly_id"]);
			
			//add to children array
			
			$needUpdateChild = false;
			
			if(!empty($parentID)){
				
				if(isset($arrParentIDs[$parentID]))
					$comment["comment_parent"] = $arrParentIDs[$parentID];
					
				else{		//if parent not fount - add to children array
					$needUpdateChild = true;
				}
			}
			
			unset($comment["doubly_parentid"]);
			
			$commentID = wp_insert_comment($comment);
			
	        $isError = is_wp_error( $commentID );
	        if($isError){
	        	
				$message = $imageID->get_error_message();
				UniteFunctionsDOUBLY::throwError("Comment not imported: $message");
	        }

			if(!empty($oldID))
				$arrParentIDs[$oldID] = $commentID;
			
			//save those that need to update id's later
			
			if($needUpdateChild == true)
				$arrChildrenIDs[$commentID] = $parentID;
				
		}
		
		if(empty($arrChildrenIDs))
			return(false);
		
		
		// --------  update parent id's
		 
		foreach($arrChildrenIDs as $commentID => $oldParentID){
			
			$parentCommentID = UniteFunctionsDOUBLY::getVal($arrParentIDs, $oldParentID);
			
			if(empty($parentCommentID))
				continue;

			$arrUpdate = array();
			$arrUpdate["comment_ID"] = $commentID;
			$arrUpdate["comment_parent"] = $parentCommentID;
			
			$response = wp_update_comment($arrUpdate);
			
	        $isError = is_wp_error( $response );
	        if($isError){
	        	
				$message = $imageID->get_error_message();
				UniteFunctionsDOUBLY::throwError("Could not update comment parent: $message");
	        }
			
		}
		
		
	}
	
	
	private function _________IMPORT_TERMS________(){}
	
	/**
	 * insert term meta
	 */
	private function insertTermMeta($termID, $arrMeta, $name){
		
		if(empty($arrMeta))
			return(false);
		
		if(self::DEBUG_META == true)
			$this->debug("term_meta_before",$arrMeta,$name);
		
		$arrMeta = $this->modifyMetaArray($arrMeta);
					
		if(self::DEBUG_META == true)
			$this->debug("term_meta_after",$arrMeta);
		
		UniteFunctionsWPDOUBLY::updateTermMetaBulk($termID, $arrMeta);
					
	}

	/**
	 * check and import taxonomy - from woo meanwhile
	 */
	private function checkImportTaxonomy($taxonomy){
		
		//check in woo attributes
		$wooAttribute = UniteFunctionsDOUBLY::getVal($this->arrContent_wooAttributes, $taxonomy);
		
		if(empty($wooAttribute))
			return(false);
			
		$slug = UniteFunctionsDOUBLY::getVal($wooAttribute, "attribute_name");
		$name = UniteFunctionsDOUBLY::getVal($wooAttribute, "attribute_label");
		$type = UniteFunctionsDOUBLY::getVal($wooAttribute, "attribute_type");
				
		//insert the woo attribute to db
		UniteFunctionsWPDOUBLY::insertWooAttribute($slug, $name, $type);
		
		//create the taxonomy
		register_taxonomy("pa_".$slug, array("product"));
		
		$this->addSummary("Inserted taxonomy: ".$slug);
		
	}
	
	
	/**
	 * insert the term, return the id inserted
	 */
	private function insertTerm($arrTerm){
		
		$data = UniteFunctionsDOUBLY::getVal($arrTerm, "term");
		
		$meta = UniteFunctionsDOUBLY::getVal($arrTerm, "meta");
		
		
		$name = UniteFunctionsDOUBLY::getVal($data, "name");
		
		$slug = UniteFunctionsDOUBLY::getVal($data, "slug");
		
		$taxonomy = UniteFunctionsDOUBLY::getVal($data, "taxonomy");
		
		$description = UniteFunctionsDOUBLY::getVal($data, "description");
		
		$parentKey = UniteFunctionsDOUBLY::getVal($data, "parent");
		
		//try to get parent id
		$parentID = null;
		
		if(!empty($parentKey)){
			$parentTerm = UniteFunctionsDOUBLY::getVal($this->arrImportedTerms, $parentKey);
			
			if(empty($parentTerm))
				UniteFunctionsDOUBLY::throwError("parent term $parentKey for $slug should be found");
				
			$parentID = $parentTerm->term_id;
		}
		
		if(!empty($description))
			$description = $this->convertString($description, "term_description");
		
		if($name === "")
			return(false);
						
		if($slug === "")
			return(false);
		
		if($taxonomy === "")
			return(false);
		
		$args = array();
		$args["slug"] = $slug;
		$args["description"] = $description;
		
		if(!empty($parentID))
			$args["parent"] = $parentID;
		
		$isTaxonomyExists = taxonomy_exists($taxonomy);
		
		if($isTaxonomyExists == false)
			$this->checkImportTaxonomy($taxonomy);
		
		//check still if exists
		$isTaxonomyExists = taxonomy_exists($taxonomy);
			
		if($isTaxonomyExists == false){
			
			$name = UniteFunctionsDOUBLY::sanitizeFilenameForOutput($name);
			$taxonomy = UniteFunctionsDOUBLY::sanitizeFilenameForOutput($taxonomy);
			UniteFunctionsDOUBLY::throwError("Failed to insert term: $name, taxonomy not found: $taxonomy. <br> Please make sure you have same plugins installed on the source and destanation sites");
		}
		
		$response = wp_insert_term($name, $taxonomy, $args);
		
		if(is_wp_error($response) || empty($response)){
			
			UniteFunctionsDOUBLY::throwError("failed to insert term: $name");
		}
		
		$termID = UniteFunctionsDOUBLY::getVal($response, "term_id");
		
		//insert the term meta
		if(!empty($meta))
			$this->insertTermMeta($termID, $meta, $name);
		
		$this->addSummary("Inserted Term: $slug");
		
		$objTerm = get_term($termID);
		
		return($objTerm);		
	}
	
	/**
	 * import the term, return term id
	 */
	private function importTerm($key){
		
		if(empty($key))
			UniteFunctionsDOUBLY::throwError("can't import empty term");
		
		$arrTerms = $this->arrContent_terms;
		
		$term = UniteFunctionsDOUBLY::getVal($arrTerms, $key);
				
		if(empty($term))
			UniteFunctionsDOUBLY::throwError("Term not found: $key");
		
		$termData = UniteFunctionsDOUBLY::getVal($term, "term");
		
		//find existing term
		
		$termSlug = UniteFunctionsDOUBLY::getVal($termData, "slug");
		$termTaxonomy = UniteFunctionsDOUBLY::getVal($termData, "taxonomy");
		
		$objTerm = UniteFunctionsWPDOUBLY::getTermBySlug($termTaxonomy, $termSlug);
		
		if(empty($objTerm))
			$objTerm = $this->insertTerm($term);
		else
			$this->addSummary("skip term: ".$termSlug);
		
		if(empty($objTerm)){
			
			UniteFunctionsDOUBLY::throwError("Term not inserted: {$termTaxonomy} - {$termSlug}");
		}
		
		$this->arrImportedTerms[$key] = $objTerm;	//save the cache
		
		return($objTerm);
	}
	
	/**
	 * get existing term id - only the first term - uncategorized
	 */
	private function getExistingTermID_uncategorized($postID){
		
		$post = get_post($postID);
		
		if(empty($post))
			return(null);
		
		//check the uncategorized category
		$arrExistingTerms = UniteFunctionsWPDOUBLY::getPostTerms($post);
		
		if(empty($arrExistingTerms))
			return(null);

		$term = $arrExistingTerms[0];
				
		return($term);
	}
	
	/**
	 * get all term parent keys
	 */
	private function getTermParentsKeys($key, $arrKeys = array()){
		
		if(empty($key))
			return($arrKeys);
		
		$arrTermData = UniteFunctionsDOUBLY::getVal($this->arrContent_terms, $key);
		
		if(empty($arrTermData))
			return($arrKeys);
				
		$arrTerm = UniteFunctionsDOUBLY::getVal($arrTermData, "term");
		
		$parentKey = UniteFunctionsDOUBLY::getVal($arrTerm, "parent");

		//recursion, add the main parent first
		$arrKeys = $this->getTermParentsKeys($parentKey);
		
		if(empty($parentKey))
			return($arrKeys);
		
		$arrKeys[] = $parentKey;
		
		return($arrKeys);
	}
	
	
	/**
	 * check and import term parents
	 */
	private function checkImportTermParents($key){

		//get all the parent keys
		
		$arrParentKeys = $this->getTermParentsKeys($key);
		
		if(empty($arrParentKeys))
			return(false);
		
		//import all the parent terms
		
		foreach($arrParentKeys as $termKey)
			$this->importTerm($termKey);
		
	}
	
	/**
	 * import post terms
	 */
	private function importPostTerms($postID, $arrTerms){
		
		if(empty($arrTerms))
			return(false);
			
		if(empty($postID))
			UniteFunctionsDOUBLY::throwError("Should be postid for importing terms: $postID");
		
		//set term id to delete from post after insert, if not exists
		$uncategorizedTerm = $this->getExistingTermID_uncategorized($postID);
		
		$uncategorizedTermID = null;
		
		if(!empty($uncategorizedTerm))
			$uncategorizedTermID = $uncategorizedTerm->term_id;
		
		foreach($arrTerms as $termKey){
			
			$parentID = $this->checkImportTermParents($termKey);
			
			$objTerm = $this->importTerm($termKey);
			
			if(empty($objTerm))
				UniteFunctionsDOUBLY::throwError("Term not imported: $termKey");
			
			$termID = $objTerm->term_id;
			$taxonomy = $objTerm->taxonomy;
			
			if(!empty($uncategorizedTermID) && $termID == $uncategorizedTermID)
				$uncategorizedTermID = null;
									
			$response = wp_set_post_terms($postID, array($termID), $taxonomy, true);
			
			if(is_wp_error($response) || empty($response))
				UniteFunctionsDOUBLY::throwError("failed to set post terms");			
		}
		
		//remove the uncategorized from post, if not exists inside all the terms
		
		if(!empty($uncategorizedTermID))
			UniteFunctionsWPDOUBLY::removeTermFromPost($postID, $uncategorizedTerm);
		
	}
	
	private function _________IMPORT_POST_ADDITIONS________(){}
	
	/**
	 * insert the variations
	 */
	private function insertWooVariations($arrVariations, $postID){
		
		if(empty($arrVariations))
			return(false);
		
		$post = get_post($postID);

		$postType = $post->post_type;
		
		if($postType !== "product")
			UniteFunctionsDOUBLY::throwError("Can't insert variations of non product post");
		
		foreach($arrVariations as $postVariation){
			
			$variationID = $this->importPost($postVariation);
			
			//update post parent
			$arrUpdate = array();
			$arrUpdate["ID"] = $variationID;
			$arrUpdate["post_parent"] = $postID;
			
			wp_update_post($arrUpdate);			
			
			//debug
			$this->addSummary("insert product variation: ".$postVariation["post"]["post_name"]);
			
		}
					
	}
	
	
	/**
	 * insert related posts
	 */
	private function insertRelatedPosts($relatedPosts, $parentID){
				
		if(empty($parentID))
			UniteFunctionsDOUBLY::throwError("insertRelatedPosts: parent id not given");
		
		foreach($relatedPosts as $arrPost){
			
			$arrPost["post"]["post_parent"] = $parentID;
			
			$this->importPost($arrPost);
		}
		
	}
	
	
	/**
	 * insert ordre item
	 */
	private function insetOrderItem($postID, $order, $productData){
		
		$productPost = null;
		
		$slug = UniteFunctionsDOUBLY::getVal($productData, "slug");
			
		if(!empty($slug))
		   $productPost = UniteFunctionsWPDOUBLY::getPostByPostName($slug, 'product');
		
		$quantity = $productData["quantity"];
			
		$name = UniteFunctionsDOUBLY::getVal($productData, "name");
		
		//if product exists - add by wp function
		
		$this->addSummary("insert order item: $name");
		
		if(!empty($productPost)){
				
			$productID = $productPost->ID;
			
			$product = wc_get_product( $productID );
			
			$args = array(
				'name' => $name,
				'tax_class' => $productData["tax_class"],
				'variation_id' => $productData["variation_id"],
				'variation' => $productData["variation"],
				'subtotal' => $productData["subtotal"],
				'total' => $productData["total"],
				'subtotal_tax' => $productData["subtotal_tax"],
				'total_tax' => $productData["total_tax"],
			);
						
			$order->add_product( $product, $quantity, $args );
			
			return(false);
		}
				
		//product not exists - add by db
				
        global $wpdb;
        $wpdb->insert(GlobalsDOUBLY::$dbPrefix.'woocommerce_order_items', array('order_item_name'   => $productData["name"], 
                      'order_item_type'  => 'line_item', 
                      'order_id'=> $postID
         )); 

         //order item meta
         
         $lastid = $wpdb->insert_id;

         $tableMeta = GlobalsDOUBLY::$dbPrefix."woocommerce_order_itemmeta";
         
         $arrTaxData = array();
         $arrTaxData["subtotal"] = array();
         $arrTaxData["total"] = array();
         
         $strTaxData = serialize($arrTaxData);
         
         $db = HelperDOUBLY::getDB();
         
         $db->runSql("INSERT INTO $tableMeta
             (`order_item_id`, `meta_key`, `meta_value`)
                 	VALUES
             (".$lastid.", '_product_id', 0),
             (".$lastid.", '_variation_id', 0),
             (".$lastid.", '_qty', ".$quantity."),
             (".$lastid.", '_line_subtotal', ".$productData['subtotal']."),
             (".$lastid.", '_line_subtotal_tax', ".$productData['subtotal_tax']."),
             (".$lastid.", '_line_total', ".$productData['total']."),
             (".$lastid.", '_line_tax', ".$productData['total_tax']."),
             (".$lastid.", '_line_tax_data',  '".$strTaxData."')");
         
	}
	
	
	/**
	 * insert order related
	 */
	private function insertOrderRelated($postID, $orderItems){
		
		//$line = 'a:2:{s:8:"subtotal";a:0:{}s:5:"total";a:0:{}}';
		//$arr = unserialize($line);
				
		if(function_exists("wc_get_product") == false)
			return(false);
		
		$order = new WC_Order( $postID );
		
		if(empty($order))
			UniteFunctionsDOUBLY::throwError("wrong order type");
		
		foreach($orderItems as $productData){
			
			$this->insetOrderItem($postID, $order, $productData);
		}
		
	}
	
	/**
	 * import post additions
	 */
	private function importPostAdditions($post, $postID){
		
		$arrVariations = UniteFunctionsDOUBLY::getVal($post, "variations");
		
		if(!empty($arrVariations))
			$this->insertWooVariations($arrVariations, $postID);

		
		//related posts
		
		$relatedPosts = UniteFunctionsDOUBLY::getVal($post, "related_posts");

		if(!empty($relatedPosts))
			$this->insertRelatedPosts($relatedPosts, $postID);
		
		//order related
		
		$orderRelated = UniteFunctionsDOUBLY::getVal($post, "order_related");
		
		if(!empty($orderRelated))
			$this->insertOrderRelated($postID, $orderRelated);
			
	}
	
	private function _________IMPORT_POST________(){}
	
	/**
	 * check and modify post name before insert, by post type
	 */
	private function importPost_checkPostName($arrPost){
		
		$postType = UniteFunctionsDOUBLY::getVal($arrPost, "post_type");
		
		$newName = null;
		
		switch($postType){
			case "acf-field-group":
				
				//generate new post name
				$newName = "group_".UniteFunctionsDOUBLY::getRandomString(13);
				
			break;
		}
		
		if(!empty($newName))
			$arrPost["post_name"] = $newName;
		
		
		return($arrPost);
	}
	
	
	/**
	 * import post
	 */
	private function importPost($post){
				
		//---- prepare post vars
		
		$arrPost = UniteFunctionsDOUBLY::getVal($post, "post");

		$postType = UniteFunctionsDOUBLY::getVal($arrPost, "post_type");

		$postSlug = UniteFunctionsDOUBLY::getVal($arrPost, "post_name");
		
		$postTitle = UniteFunctionsDOUBLY::getVal($arrPost, "post_title");
		
		
		//remove attachment date fields - get it to the top
		
		if($postType == "attachment"){
			
			unset($arrPost["post_date"]);
			unset($arrPost["post_date_gmt"]);
			unset($arrPost["post_modified"]);
			unset($arrPost["post_modified_gmt"]);
		}
		
		
		if(GlobalsDOUBLY::$isProActive == false && $postType != "page"){
			UniteFunctionsDOUBLY::throwError("Doubly free version can't import post type: $postType, only Pages");
		}
		
		if(!isset($arrPost["post_status"]))
			$arrPost["post_status"] = "publish";
		
		if(empty($arrPost))
			UniteFunctionsDOUBLY::throwError("no post found");
		
		$arrPost = $this->convertStringsArray($arrPost);
		
		
		//---- insert the post, or update the current
		
		if(!empty($this->param_importToPostID)){
			
			$currentPost = get_post($this->param_importToPostID);
			if(empty($currentPost))
				UniteFunctionsDOUBLY::throwError("Current post not found");
			
			unset($arrPost["post_name"]);
			
			$arrPost["ID"] = $this->param_importToPostID;
			
			wp_update_post($arrPost);
			
			$postID = $this->param_importToPostID;
		}else{
		
			switch ($this->existingPostAction) {
				case "skip":
					
					$existingPost = UniteFunctionsWPDOUBLY::getPostByPostName($postSlug, $postType);
					
					if(!empty($existingPost)) {
						
						//add summary
						$postID = $existingPost->ID;
						$postTitleExisting = $existingPost->post_title;
						
						$this->addSummary("skipped post: ".$postTitle." | found post:  $postTitleExisting");
						
						return(false);
					}
				case "overwrite":
					
					$existingPost = UniteFunctionsWPDOUBLY::getPostByPostName($postSlug, $postType);
					
					if (!empty($existingPost)) {
						$existingPostID = $existingPost->ID;
					}
					break;
				case "new":
				default:
					$existingPostID = 0;
				break;
			}

			$postID = $this->importPost_save($this->existingPostAction, $arrPost, $existingPostID);
		}
		
		if(empty($postID))
			UniteFunctionsDOUBLY::throwError("The post is not inserted");
				
		//add summary
		
		//---- insert post meta
					
		$arrMeta = UniteFunctionsDOUBLY::getVal($post, "meta");
				
		if(self::DEBUG_META == true)
			$this->debug("meta_before",$arrMeta, $arrPost["post_title"]);
		
		$arrMeta = $this->modifyMetaArray($arrMeta);
		
		if(self::DEBUG_META == true)
			$this->debug("meta_after",$arrMeta);
		
			
		UniteFunctionsWPDOUBLY::updatePostMetaBulk($postID, $arrMeta);
		
		//------ insert the terms
		
		$arrTerms = UniteFunctionsDOUBLY::getVal($post, "terms");
		
		$this->importPostTerms($postID, $arrTerms);
		
		//debug the terms
		if(self::DEBUG_INSERTED_TERMS == true)
			$this->debug("show_terms", $postID);
		

		//------  insert the comments
		
		$arrComments = UniteFunctionsDOUBLY::getVal($post, "comments");
		
		$this->importPostComments($postID, $arrComments);
		
		if(self::DEBUG_INSERTED_COMMENTS == true)
			$this->debug("show_comments", $postID);
		
			
		//------- special insert -----------
		
		$this->importPostAdditions($post, $postID);
		
		if(self::DEBUG_INSERTED_POST == true)
			$this->debug("show_inserted_post", $postID);
		
		if(empty($this->lastImportedPostID))
			$this->lastImportedPostID = $postID;
		
		//in case of elementor post
		HelperDOUBLY::removeElementorPostCacheFile($postID);
		
		return($postID);
	}

	/**
	 * save imported post
	 */
	private function importPost_save($action, $postData, $postID){
			
		$postTitle = $postData["post_title"];
		
		if ($action === "overwrite" && $postID) {
			
			$urlPost = get_post_permalink($postID);
			
			unset($postData["post_name"]);

			$postData["ID"] = $postID;

			wp_update_post($postData);
		
			//add summary
			
			$this->addSummary("overwrited post: ".$postTitle." | $postID | $urlPost");
			
			return $postID;
		}

		$postID = wp_insert_post($postData);
		
		//add summary
		
		$urlPost = get_post_permalink($postID);
		$this->addSummary("inserted post: ".$postTitle." | $postID | $urlPost");
		
		return $postID;
	}
	
	
	/**
	 * import by posts
	 */
	private function importByPosts(){
		
		$arrPosts = UniteFunctionsDOUBLY::getVal($this->arrContent, "posts");
		
		if(empty($arrPosts))
			UniteFunctionsDOUBLY::throwError("posts not found");
		
		if(is_array($arrPosts) == false)
			UniteFunctionsDOUBLY::throwError("Wrong posts format");
		
		$this->numLastImportedPosts = count($arrPosts);
		
		foreach($arrPosts as $post){
						
			$this->importPost($post);
			
			//get post type
			if(empty($this->lastImportedType)){
				$arrPost = UniteFunctionsDOUBLY::getVal($post, "post");
				$this->lastImportedType = UniteFunctionsDOUBLY::getVal($arrPost, "post_type");
			}
		}

		
		//update all term counts, in case that something wrong
		UniteFunctionsWPDOUBLY::updateTermsCounts($this->arrImportedTerms);
		
	}
	
	private function _________IMPORT_ELEMENTOR_SECTION___________(){}
	
	
	/**
	 * insert elementor section to elementor layout
	 * insert after first section first
	 */
	private function insertElementorSectionToLayout($arrPageLayout, $arrSection){

		
		if(empty($arrPageLayout))
			$arrPageLayout = array();
		
		if(empty($this->param_importToSectionPosition))
			UniteFunctionsDOUBLY::throwError("Import position (before / after) not found");
				
		$arrPageLayoutNEW = array();
		$isInserted = false;
		
		
		//insert new section
		if($this->param_importToSectionID == "new" && empty($arrPageLayout)){
			
			$arrPageLayoutNEW = array($arrSection);
			$isInserted = true;
		}
		else
		foreach($arrPageLayout as $index=>$sectionExisting){
			
			$sectionID = UniteFunctionsDOUBLY::getVal($sectionExisting, "id");
			
			$isInsertHere = false;
			if($sectionID == $this->param_importToSectionID)
				$isInsertHere = true;
			
			if($this->param_importToSectionID == "new" && $index == 0)	
				$isInsertHere = true;
			
			
			//add before
			
			if($isInsertHere == true && $this->param_importToSectionPosition == "before"){
				$arrPageLayoutNEW[] = $arrSection;
				$isInserted = true;
			}
			
			//add existing 
			
			$arrPageLayoutNEW[] = $sectionExisting;
			
			//add after
			
			if($isInsertHere == true && $this->param_importToSectionPosition == "after"){
				$arrPageLayoutNEW[] = $arrSection;
				$isInserted = true;
			}
			
		}
		
		
		if($isInserted == false)
			UniteFunctionsDOUBLY::throwError("Section not inserted, before or after section not found");
		
		
		return($arrPageLayoutNEW);
	}
	
	
	/**
	 * import by elementor section
	 */
	private function importByElementorSection(){
		
		if(empty($this->param_importToPostID))
			UniteFunctionsDOUBLY::throwError("no post id for import to found");
		
		if(empty($this->param_importToSectionID))
			UniteFunctionsDOUBLY::throwError("no 'import to section id' found");
		
		if(empty($this->param_importToSectionPosition))
			UniteFunctionsDOUBLY::throwError("no 'import to section position' found");
		
		$postID = $this->param_importToPostID;
		
		UniteFunctionsDOUBLY::validateNumeric($postID, "post id");
				
		$post = get_post($postID);
				
		UniteFunctionsDOUBLY::validateNotEmpty($post, "post import to");
		
		$this->isElementorData = true;
		
		$arrSection = UniteFunctionsDOUBLY::getVal($this->arrContent, "content");
		
		UniteFunctionsDOUBLY::validateNotEmpty($arrSection,"section content");
		
		$arrSection = $this->modifyElementorElementIDs($arrSection);
		
		$arrPageLayout = HelperDOUBLY::getElementorContent($postID);
				
		if(empty($arrPageLayout) || is_array($arrPageLayout) == false){
			
			if($this->param_importToSectionID == "new")
				$arrPageLayout = array();
			else
				UniteFunctionsDOUBLY::throwError("No page elementor layout found");
		}
		
		//convert all images etc
		
		$arrSection = $this->convertStringsArray($arrSection);
		
		//insert section
		$arrPageLayout = $this->insertElementorSectionToLayout($arrPageLayout, $arrSection);
		
		//update elementor page
		UniteFunctionsWPDOUBLY::updateElementorDataMeta($postID, $arrPageLayout);
				
		$this->addSummary("Elementor Section Added");
		
		$this->lastImportedPostID = $postID;
		
		//clear cache
		HelperDOUBLY::removeElementorPostCacheFile($postID);
		
	}
	
	private function _________IMPORT_OBJECTS________(){}
	
	/**
	 * import object
	 */
	private function importByObject(){
		
		$objType = UniteFunctionsDOUBLY::getVal($this->arrContent, "objtype");
		
		$objIntegrations = new Doubly_Integrations();
		
		$arrObjects = UniteFunctionsDOUBLY::getVal($this->arrContent, "objects");
				
		switch($objType){
			case GlobalsDOUBLY::EXPORT_TYPE_SNIPPET:
								
				$logText = $objIntegrations->importSnippets($arrObjects, $this->param_importToPostID);
				
				$this->addSummary($logText);
				
				$this->lastImportedType = "snippet";
				
			break;
			default:
				
				UniteFunctionsDOUBLY::throwError("Wrong object import type: $objType");
				
			break;
		}
		
		$this->lastImportedPostID = $objIntegrations->getLastImportedID();
		
		$this->numLastImportedPosts = $objIntegrations->getLastNumImported();
		
		$this->lastImportedUrl = $objIntegrations->getLastImportedUrl();
		
	}
		
	
	private function _________IMPORT_WIDGETS________(){}
	
	/**
	 * import widgets if UE exists and there is some widgets
	 */
	private function importWidgets(){
		
		if($this->isUEInstalled == false)
			return(false);
		
		//check if widgets available by content
			
		$arrWidgets = UniteFunctionsDOUBLY::getVal($this->arrContent, "widgets");
			
		if(empty($arrWidgets))
			return(false);
			
			
		//import all widgets in the folder
		
		if(class_exists("UniteCreatorExporter") == false)
			return(false);
		
		$exporterAddons = new UniteCreatorExporter();
		
		$addonType = GlobalsUC::ADDON_TYPE_ELEMENTOR;
		
		//crate temp cat
		$exporterAddons->setMustImportAddonType($addonType);
		
		if(is_dir($this->pathImportWidgets) == false){
			
			$objAddons = new UniteCreatorAddons();
			
			if(method_exists($objAddons, "installMultipleAddons") == false)
				UniteFunctionsDOUBLY::throwError("Can't import widgets. The Unlimited Elements plugin version is old version. Please update it to the latest version.");
			
			$logText = $objAddons->installMultipleAddons($arrWidgets, $addonType);
			
		}else
			$logText = $exporterAddons->importAddonsFromFolder($this->pathImportWidgets, null, true);
		
		$this->addSummary($logText);
		
		if(self::DEBUG_IMPORTED_WIDGETS == true){
			dmp("imported widgets");
			dmp($logText);
		}
		
		return($logText);
	}

	/**
	 * import widgets if UE exists and there is some widgets
	 */
	private function importWidgetsBG(){
		
		if($this->isUEInstalled == false)
			return(false);
		
		
		//check if widgets available by content
			
		$arrWidgets = UniteFunctionsDOUBLY::getVal($this->arrContent, "widgets_bg");
		
		if(empty($arrWidgets))
			return(false);
			
		//import all widgets in the folder
		
		if(class_exists("UniteCreatorExporter") == false)
			return(false);
		
		$exporterAddons = new UniteCreatorExporter();
		
		$addonType = GlobalsUC::ADDON_TYPE_BGADDON;
		
		//crate temp cat
		$exporterAddons->setMustImportAddonType($addonType);
		
		if(is_dir($this->pathImportWidgets) == false){
			
			$objAddons = new UniteCreatorAddons();
			
			if(method_exists($objAddons, "installMultipleAddons") == false)
				UniteFunctionsDOUBLY::throwError("Can't import widgets. The Unlimited Elements plugin version is old version. Please update it to the latest version.");
			
			$logText = $objAddons->installMultipleAddons($arrWidgets, $addonType);
			
		}else{
			$logText = $exporterAddons->importAddonsFromFolder($this->pathImportBGWidgets, null, true);
		}
		
		$this->addSummary($logText);
		
		if(self::DEBUG_IMPORTED_WIDGETS == true){
			dmp("imported BG widgets");
			dmp($logText);
		}
		
		return($logText);
	}
	
	
	private function _________IMPORT_IMAGES________(){}
	
	
	/**
	 * insert attachment
	 */
	protected function insertAttachmentByImage($arrImage){
		
		$filepath = $arrImage["source"];
		$filename = $arrImage["filename"];
		$filepathDest = $arrImage["dest"];
		$url = $arrImage["url"];
		
		//get filetype
		$arrType = wp_check_filetype_and_ext($filepath, $filename);
		$type = UniteFunctionsDOUBLY::getVal($arrType, "type");
		if(empty($type))
			$type = "image/jpeg";
		
		//get name
		$name_parts = pathinfo($filename);
		$name = trim( substr( $filename, 0, -(1 + strlen($name_parts['extension'])) ) );
		
		$name .= "_image";
		
		//get full url
		$urlFull = HelperDOUBLY::URLtoFull($url);
				
		//check for existing image id
		$imageID = UniteFunctionsWPDOUBLY::getAttachmentIDFromImageUrl($urlFull);
		
		if(!empty($imageID)){
		
			$urlExistingImage = UniteFunctionsWPDOUBLY::getUrlAttachmentImage($imageID);
			if($urlExistingImage == $urlFull)
				return($imageID);
		}
						
		
		//get image title
		$title = $name;
		$excerpt = "";
		
		if ( 0 === strpos( $type, 'image/' ) && $image_meta = @wp_read_image_metadata( $filepath ) ) {
			if ( trim( $image_meta['title'] ) && ! is_numeric( sanitize_title( $image_meta['title'] ) ) ) {
				$title = $image_meta['title'];
			}
		
			if ( trim( $image_meta['caption'] ) ) {
				$excerpt = $image_meta['caption'];
			}
		}
		
		if(empty($title))
			$title = $name;
		
		$attachment = array(
				'post_mime_type' => $type,
				'guid' => $urlFull,
				'post_title' => $title,
				'post_excerpt' => $excerpt,
		);
		
		$id = @wp_insert_attachment($attachment, $filepathDest);
		if(is_wp_error($id))
			return(null);
		
		if(is_array($id))
			return(null);
		
		//not must
		$metaData = @wp_generate_attachment_metadata( $id, $filepathDest );
		
		if(!empty($metaData))
			@wp_update_attachment_metadata( $id, $metaData );
		
		return($id);
	}
	
	
	/**
	 * insert attachment
	 */
	private function importImage_insertAttachment($image, $pathImage){
		
		$filename = UniteFunctionsDOUBLY::getVal($image, "filename");
		
		$arrFile = array();
		$arrFile['name']     = $filename;
		$arrFile['tmp_name'] = $pathImage;
		
		$imageID = media_handle_sideload($arrFile);
		
		$isError = is_wp_error($imageID);
		
		if($isError == true){
			
			$message = $imageID->get_error_message();
			UniteFunctionsDOUBLY::throwError("Image $filename not imported: $message");
		}
		
		if(empty($imageID))
			UniteFunctionsDOUBLY::throwError("Image $filename not imported");
		
		//update the post content
		
		$postFields = UniteFunctionsDOUBLY::getVal($image, "post");
		
		if(!empty($postFields)){
			
			$postFields["ID"] = $imageID;
			
			$success = wp_update_post($postFields);
			
			if($success == false)
				UniteFunctionsDOUBLY::throwError("Updating attachment text failed, $filename");			
		}
		
		//update the post meta
		
		$metaFields = UniteFunctionsDOUBLY::getVal($image, "meta");
		
		if(!empty($metaFields))
			UniteFunctionsWPDOUBLY::updatePostMetaBulk($imageID, $metaFields);
		
		return($imageID);
	}
	
	/**
	 * find and rename image from the list to the attached
	 */
	private function findAndRenameImage($arrImage, $pathDestImage){
		
		if(file_exists($pathDestImage) == true)
			return(false);
		
		$size = UniteFunctionsDOUBLY::getVal($arrImage, "size");
		
		if(empty($size))
			return(false);
		
		$arrFiles = UniteFunctionsDOUBLY::getFileList($this->pathImportImages);
		
		foreach($arrFiles as $file){
			
			$pathFile = $this->pathImportImages.$file;
			
			$fileSize = filesize($pathFile);
			
			if($fileSize != $size)
				continue;

			//rename the file
			@rename($pathFile, $pathDestImage);
			
			return(false);
		}
				
	}
	
	
	/**
	 * import image
	 */
	private function importImage($image){
		
		$filename = UniteFunctionsDOUBLY::getVal($image, "filename");
		
		$pathImage = $this->pathImportImages.$filename;
		
		//try to find and rename image from the list - maybe some unicode file names issue
		if(file_exists($pathImage) == false)
			$this->findAndRenameImage($image, $pathImage);
		
		
		//validate filepath
		
		if(file_exists($pathImage) == false){
						
			$arrFiles = UniteFunctionsDOUBLY::getFileList($this->pathImportImages);
			
			if(empty($arrFiles))
				UniteFunctionsDOUBLY::throwError("No image files found. Something with zip extraction. Please check hosting permissions, rules or securety plugins");
			else{
				
				$strImages = implode(",", $arrFiles);
				
				UniteFunctionsDOUBLY::throwError("image not found: $filename <br> the images found are: $strImages. <br> Please turn to support.");
				
			}
				
		}
		
		//get original filename
		$originalFilename = UniteFunctionsDOUBLY::getVal($image, "filename_original");
		
		//check image not found in wp
		$imageID = UniteFunctionsWPDOUBLY::searchSimilarImage($pathImage, $originalFilename);

		//image exists, add to array and exit, dont' import it
		if(!empty($imageID)){
			
			$this->arrImportedImages[$filename] = $imageID;
			
			$this->addSummary("skipped image: $filename, image ID: $imageID");
			
			return(false);
		}
		
		$imageID = $this->importImage_insertAttachment($image, $pathImage);
		
		if(empty($imageID) || is_wp_error($imageID) || is_numeric($imageID) == false){
			
			if(is_wp_error($imageID) == true){
				
				$error = $imageID->get_error_message();
				
				UniteFunctionsDOUBLY::throwError("Image: $filename not imported: $error");
			}
			
			UniteFunctionsDOUBLY::throwError("Image: $filename not imported");
		}
		
		$this->arrImportedImages[$filename] = $imageID;
		
		$this->addSummary("Inserted Image: $filename, $imageID");
	}
	
	
	/**
	 * import images if exists
	 */
	private function importImages(){
		
		$arrImages = UniteFunctionsDOUBLY::getVal($this->arrContent, "images");
		
		if(empty($arrImages)){
			
			$this->addSummary("no images to import");
			
			return(false);
		}
		
		//allow import svg
		UniteFunctionsWPDOUBLY::allowImportSVG();
		
		foreach($arrImages as $image){
			
			$this->importImage($image);
			
		}
		
		$this->numLastImportedImages = count($arrImages);
		
	}
	
	private function _________GUTENBERG________(){}
	
	/**
	 * print block
	 */
	private function printBlock($block){
		
		dmp("gutenberg block");
		
		$html = UniteFunctionsDOUBLY::getVal($block, "innerHTML");
				
		$block["innerHTML"] = htmlspecialchars($html);
		
		$block["innerContent"] = htmlspecialchars($html);
		
		dmp($block);
	}
	
	
	
	/**
	 * check images in a block
	 */
	private function convertGutenbergBlock_checkImages2($block){
		
		$html = UniteFunctionsDOUBLY::getVal($block, "html");
		
		$attributes = UniteFunctionsDOUBLY::getVal($block, "attrs");
		
		$innerContent = UniteFunctionsDOUBLY::getVal($block, "innerContent");

		//prepare the content
		$arrReplace = array();
		$arrReplace["html"] = $html;
		foreach($innerContent as $index=>$content){
			$arrReplace["inner_".$index] = $content;
		}
		
		
		foreach($attributes as $key=>$value){
			
			if(is_array($value))
				continue;
			
			$newValue = $this->convertString_image($value);
			
			if($newValue == $value)
				continue;
			
			if(is_numeric($newValue) == false)
				continue;

			$imageID = $newValue;
				
			$attributes[$key] = $imageID;

			if(array_key_exists("url", $attributes))
				$attributes["url"] = $urlImage;
			
			//convert the image
						
			$imageKey = str_replace(self::KEY_LOCAL, "", $value);
			
			
			$size = "full";
			$sizeSlug = UniteFunctionsDOUBLY::getVal($attributes, "sizeSlug");
			if(!empty($sizeSlug))
				$size = $sizeSlug;
			
			$urlImage = UniteFunctionsWPDOUBLY::getUrlAttachmentImage($imageID, $size);
					
			$link = get_attachment_link( $imageID );
		
			//replace the html
			
			foreach($arrReplace as $index=>$html){
				
				$html = str_replace(self::KEY_LOCAL_IMAGE.$imageKey, $urlImage, $html);
				$html = str_replace(self::KEY_LOCAL.$imageKey, $imageID, $html);
				$html = str_replace(self::KEY_LOCAL_IMAGE_LINK.$imageKey, $link, $html);
				
				$arrReplace[$index] = $html;
			}
		
			//set the attribute values
			$attributes[$key] = $imageID;
			
			if(array_key_exists("url", $attributes))
				$attributes["url"] = $urlImage;
			
			$this->isImageConverted = true;
		
		
		} //end attributes

		if($this->isImageConverted == false)
			return($block);
		
	 //set back the data
	
	 	$block["attrs"] = $attributes;
		
		
		//combine back
		$index = 0;
		foreach($arrReplace as $indexKey => $content){
			
			if($indexKey == "html"){
				$html = $content;
				continue;
			}
			
			$innerContent[$index] = $content;
			$index++;
		}
		
		
		return($block);
	}
	
	
	/**
	 * 
	 * convert gutenberg block
	 */
	protected function convertGutenbergBlock($block){
		
		$isDebug = false;
		
		$attributes = UniteFunctionsDOUBLY::getVal($block, "attrs");
		
		//--- debug
		
		if($isDebug == true){
			
			dmp("---- old block------");
			$this->printBlock($block);
			
		}
		
		if(empty($attributes))
			return($block);
				
		$this->isImageConverted = false;
		
		$block = $this->convertGutenbergBlock_checkImages2($block);
		
		//--- debug
		
		if($isDebug == true){
			
			dmp("---- new block------");
			$this->printBlock($block);
			
		}
		
		/*
		if($this->isImageConverted == true){
			
			dmp("image converted");
			$this->printBlock($block);
			exit();
			
		}
		*/
		
		return($block);
	}
	
	
	/**
	 * convert gutenberg content
	 */
	private function convertGutenbergContent($content){
		
		if(has_blocks($content) == false)
			return($content);
		
		$arrBlocks = parse_blocks($content);

		
		$arrBlocksNew = $this->convertGutenbergBlocks($arrBlocks);
				
		$contentForSave = serialize_blocks($arrBlocksNew);
		
		if(self::DEBUG_SHOW_GUTENBERG_BLOCKS == true){
			
			$this->showGutenbergBlocksBeforeAfter($arrBlocks, $arrBlocksNew);
			exit();
		}
		
		
		return($contentForSave);
	}
	
	private function _________IMPORT_CONTENT________(){}
	
	
	/**
	 * import by content array
	 */
	private function importByContentArray($arrContent){
		
		$this->arrContent = $arrContent;
		
		$this->arrContent_terms = UniteFunctionsDOUBLY::getVal($this->arrContent, "terms",array());
				
		$this->arrContent_wooAttributes = UniteFunctionsDOUBLY::getVal($this->arrContent, "woo_attributes",array());
		
		$type = UniteFunctionsDOUBLY::getVal($arrContent, "type");
		
		//import media (images)
		
		$this->importImages();
				
		//import widgets
		
		if($type != GlobalsDOUBLY::EXPORT_TYPE_MEDIA){
			
			$this->importWidgets();
			$this->importWidgetsBG();
			
		}
		
		switch($type){
			case GlobalsDOUBLY::EXPORT_TYPE_POSTS:
				
				$this->importByPosts();
			
			break;
			case GlobalsDOUBLY::EXPORT_TYPE_MEDIA:
				
				$this->lastImportedType = "media";
				
				$this->numLastImportedPosts = $this->numLastImportedImages;
				
				
				//do nothing
			break;
			case GlobalsDOUBLY::EXPORT_TYPE_ELEMENTOR_SECTION:
			
				$this->importByElementorSection();
			break;
			case GlobalsDOUBLY::EXPORT_TYPE_OBJECTS:
				
				$this->importByObject();
				
			break;
			default:
				UniteFunctionsDOUBLY::throwError("Wrong import type: $type");
			break;
		}
		
	}
	
	
	/**
	 * import content file
	 */
	private function importContentFile(){
		
		$filepathContent = $this->pathImportContentFiles."content.txt";
		
		if(file_exists($filepathContent) == false)
			UniteFunctionsDOUBLY::throwError("content file not found");

		$content = file_get_contents($filepathContent);
		
		if(empty($content))
			UniteFunctionsDOUBLY::throwError("no content found");
		
		$arrContent = UniteFunctionsDOUBLY::maybeUnserialize($content);
		
		if(empty($arrContent))
			UniteFunctionsDOUBLY::throwError("wrong content file");
		
		if(is_array($arrContent) == false)
			UniteFunctionsDOUBLY::throwError("wrong content file - not array");
		
		
		$this->importByContentArray($arrContent);
		
	}
	
	/**
	 * import from zip file
	 */
	private function importFromZipFile($filepathZip, $deleteSourceFile = false){
		
		UniteFunctionsDOUBLY::validateFilepath($filepathZip,"import zip file");
		
		//unpack the zip
		$zip = new UniteZipDOUBLY();
		$success = $zip->extract($filepathZip, $this->pathImportContentFiles);
		
		if($success == false)
			UniteFunctionsDOUBLY::throwError("Import failed - file not extracted");
					
		//delete source zip file
		if($deleteSourceFile == true)
			@unlink($filepathZip);
		
		if(self::DEBUG_SHOW_UNZIPPED_FILES == true){
			
			$arrFiles = UniteFunctionsDOUBLY::getFileListTree($this->pathImportContentFiles);
			
			dmp("debug unzipped files");
			
			dmp($arrFiles);
			exit();
			
		}
			
		$this->importContentFile();
		
		//delete the import folder
		UniteFunctionsDOUBLY::deleteDir($this->pathImportContentFiles, true);
				
	}
	
	
	/**
	 * set import params
	 */
	public function setImportParams($arrParams){
		
		$importToPostID = UniteFunctionsDOUBLY::getVal($arrParams, "import_to_postid");
		
		if(!empty($importToPostID) && is_numeric($importToPostID))
			$this->param_importToPostID = $importToPostID;
		
		$importToSectionID = UniteFunctionsDOUBLY::getVal($arrParams, "import_to_sectionid");
		if(!empty($importToSectionID))
			$this->param_importToSectionID = $importToSectionID;
		
		$importToSectionPosition = UniteFunctionsDOUBLY::getVal($arrParams, "import_to_section_position");
		
		if(!empty($importToSectionPosition))
			$this->param_importToSectionPosition = $importToSectionPosition;
		
		$this->param_isSameDomain = UniteFunctionsDOUBLY::getVal($arrParams, "is_same_domain");
		$this->param_isSameDomain = UniteFunctionsDOUBLY::strToBool($this->param_isSameDomain);
		
	}
	
	
	/**
	 * get last imported post url
	 */
	public function getLastImportedPostUrl($isAdmin = false){
		
		if(empty($this->lastImportedPostID)){			
			return("");
		}
		
		if($isAdmin == false){
			
			$post = get_post($this->lastImportedPostID);
			
			$url = $post->guid;
			
		}else{		//link to edit post
						
			$url = get_edit_post_link( $this->lastImportedPostID ,false);
		}
		
		return($url);
	}
	
	/**
	 * get number of last imported posts
	 */
	public function getNumLastImportedData(){
		
		$output = array();
		$output["num_imported"] = $this->numLastImportedPosts;
		$output["post_type"] = $this->lastImportedType;
		$output["summary"] = $this->arrSummary;
		
		if(!empty($this->lastImportedUrl))
			$output["url_imported"] = $this->lastImportedUrl;
		
		
		return($output);
	}
	
	/**
	 * import from zip content
	 */
	public function importFromZipContent($zipContent){
		
		//disable php errors display
		
		if(self::DEBUG_PHP_ERRORS == true || GlobalsDOUBLY::DEBUG_ERRORS == true)
			ini_set("display_errors",1);
		else
			ini_set("display_errors",0);
		
		$this->initBeforeImport();
		
		$this->createFolders();
		
		if(empty($zipContent))
			UniteFunctionsDOUBLY::throwError("Wrong zip content");
		
		UniteFunctionsDOUBLY::writeFile($zipContent, $this->filepathImportZipFile);
		
		if(file_exists($this->filepathImportZipFile) == false)
			UniteFunctionsDOUBLY::throwError("Can't create import zip file");
		
		$this->importFromZipFile($this->filepathImportZipFile, true);
		
		if(self::DEBUG_SHOW_SUMMARY == true)
			$this->printSummary();
		
	}
	
	
	/**
	 * test function. import from some test file
	 */
	public function importFromTestFile($postID){
		
		if(self::DEBUG_PHP_ERRORS == true)
			ini_set("display_errors","on");
		
		if($postID != "new")
			UniteFunctionsDOUBLY::validateNumeric($postID,"postid");
		
		$pathZipFile = GlobalsDOUBLY::$pathCache."import/sample.zip";
		
		if(file_exists($pathZipFile) == false)
			UniteFunctionsDOUBLY::throwError("Test file not exists: $pathZipFile");
		
		$arrParams = array();
		
		if($postID != "new")
			$arrParams["import_to_postid"] = $postID;
		
		//$arrParams["import_to_sectionid"] = "1e5e4a3";
		//$arrParams["import_to_section_position"] = "before";
		
		$this->initBeforeImport();
		
		$this->setImportParams($arrParams);
		
		$this->createFolders();
		
		$this->importFromZipFile($pathZipFile);
		
		if(self::DEBUG_SHOW_SUMMARY == true)
			$this->printSummary();
		
		$this->printTestText();
		exit();
		
	}
	
}

