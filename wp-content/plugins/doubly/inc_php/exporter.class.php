<?php
/**
 * @package Doubly
 * @author Unlimited Elements
 * @copyright (C) 2022 Unlimited Elements, All Rights Reserved. 
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 **/

if(!defined("DOUBLY_INC")) die("restricted access");

class Doubly_PluginExporter extends Doubly_PluginExporterBase{
	
	const DEBUG_META_ARRAY = false;		//show meta content
	
	const DEBUG_AFTER_EXPORT = false;	//must for show the data
	
	const DEBUG_SHOW_GUTENBERG_BLOCKS = false;
	const DEBUG_META_STRING_BEFORE_AFTER = false;
	const DEBUG_CONVERT_STRINGS = false;
	
	const DEBUG_GUTENBERG_BLOCKS = false;
	const DEBUG_GUTENBERG_BLOCKS_BLOCKNAME = "kadence/image";
	
	const DEBUG_CONVERT_BASE_URL = false;
	const DEBUG_CONVERT_IMAGES = false;
	const DEBUG_CHECK_WIDGETS = false;
	const DEBUG_FILES_DELETE = false;
	const DEBUG_PHP_ERRORS = false;
	
	const CLEAR_TEMP_FOLDER_SECONDS = 1800;
	
	private $arrIDsLikeKeys = array("thumb","url","img","image","gallery","photo");
	private $arrPostFields = array("post_title","post_content","post_excerpt","post_name","post_content_filtered","post_type","post_mime_type","post_date","post_date_gmt","post_modified","post_modified_gmt","post_status");
	private $arrTermsFields = array("name","slug","taxonomy","filter","description","parent");
	
	private $arrExcludeMetaKeys = array(
		"_edit_lock","_wp_old_slug","_wc_attachment_source",
		"_wc_average_rating","_wc_review_count","_upsell_ids","_edit_last","total_sales");
	
	private $arrExcludeMetaKeysPrefix = array(
		"_oembed_","_wpml_"
	);
	
	private $arrExcludeMetaKeysTerms = array("order");
	private $arrExcludeMetaKeysTermsPrefixes = array("product_count_");
	
	private $arrIncludeAttachmentKeys = array("_wp_attachment_image_alt");
	private $arrProductKeys = array();
	
	private $db;
	
	private $exportPost;
	private $exportPostID;
	private $exportType;
	private $arrExportPostData;
	private $arrWooAttributeTaxonomies = array();
	
	private $arrExportImages = array();
	private $arrCollectConvertedImages = null;		//when not null - the images id's will adding to it
	
	private $arrCacheImageFilenames = array();
	private $arrExportContent = array();
	
	private $arrExportUEWidgets = array();
	private $arrExportUEBackgrounds = array();
	
	private $pathExportContents;
	private $pathExportContent;
	private $pathExportContentImages;
	private $pathExportContentWidgets;
	private $pathExportContentWidgetsBG;
	private $pathExportZip;
	private $filepathZip;
	private $exportedFilename;
	
	private $isElementorMeta = false;
	private $isElementorSectionExport = false;
	private $arrElementorGlobalColors = null;
	private $arrElementorGlobalTypography = null;
	
	private $isImageConverted = false;
	
	private $arrExportedPosts = array();
	
	
	/**
	 * constructor
	 */
	public function __construct(){
		
		parent::__construct();
		
		$this->db = new Doubly_PluginDB();
	}

	/**
	 * print post content
	 */
	private function testPrintText_postContent(){
		
		$posts = UniteFunctionsDOUBLY::getVal($this->arrExportContent, "posts");
		
		if(empty($posts))
			return(false);
			
		foreach($posts as $post){
			
			$postData = UniteFunctionsDOUBLY::getVal($post, "post");
			
			$title = UniteFunctionsDOUBLY::getVal($postData, "post_title");
			
			$content = UniteFunctionsDOUBLY::getVal($postData, "post_content");
			
			
			dmp("--------------------------------------------------");
			
			dmp("<b>Post Content: $title</b>");
			
			dmp(htmlspecialchars($content));
			
		}
		
	}
	
	/**
	 * print some test phrases
	 */
	private function testPrintText(){
		
		echo "<b>";
		
		dmp("Export Elementor Section - width widgets and images");
		
		dmp("output the zip file");
		
		dmp($this->filepathZip);
				
		echo "</b>";
		
		dmp("---------------------");
		
		dmp($this->arrExportContent);
		
		$this->testPrintText_postContent();
		
		exit();
		
	}
	
	
	/**
	 * clear export layout data
	 */
	protected function clearExportPostData(){
		
		$this->exportPost = null;
		$this->exportPostID = null;
		
		$this->arrExportContent = array();
		
		$this->arrExportImages = array();
		$this->arrCacheImageFilenames = array();
		
		$this->arrExportUEWidgets = array();
		$this->arrExportUEBackgrounds = array();
		
		$this->pathExport = "";
		$this->pathExportContent = "";
		$this->pathExportContentImages = "";
		$this->pathExportContentWidgets = "";
		$this->pathExportContentWidgetsBG = "";
		$this->pathExportContents = "";
		
		$this->pathExportZip = "";
		$this->filepathZip = "";
	}
	
	
	/**
	 * prepare global export path
	 */
	protected function prepareExportFolders_globalExport(){
	
		$pathCache = GlobalsDOUBLY::$pathCache;
		UniteFunctionsDOUBLY::mkdirValidate($pathCache, "Cache");
		
		//write index.html file
		UniteFunctionsDOUBLY::writeFileIfNotExists("", $pathCache."index.html");
		
		$pathExport = $pathCache."export/";
		UniteFunctionsDOUBLY::mkdirValidate($pathExport, "Export");
	
		$this->pathExport = $pathExport;
		
		//write index.html file		
		UniteFunctionsDOUBLY::writeFileIfNotExists("", $this->pathExport."index.html");
		
	}
	
	
	/**
	 * prepare export folders
	 */
	private function prepareFolders(){
				
		$this->prepareExportFolders_globalExport();
		
		//make posts folder
		$this->pathExportContents = $this->pathExport."contents/";
		UniteFunctionsDOUBLY::mkdirValidate($this->pathExportContents, "Contents");
		
		//clean posts folder
		UniteFunctionsDOUBLY::clearDirByTime($this->pathExportContents, self::CLEAR_TEMP_FOLDER_SECONDS, self::DEBUG_FILES_DELETE);
		
		//make zip folder
		$this->pathExportZip = $this->pathExport."zips/";
		UniteFunctionsDOUBLY::mkdirValidate($this->pathExportZip, "Export Zip");

		//create index.html - in posts
		UniteFunctionsDOUBLY::writeFileIfNotExists("", $this->pathExportContents."index.html");
		
		//clear zip folder
		UniteFunctionsDOUBLY::clearDirByTime($this->pathExportZip, self::CLEAR_TEMP_FOLDER_SECONDS, self::DEBUG_FILES_DELETE);
		
		$randomStrings = UniteFunctionsDOUBLY::getRandomString(10);
		
		//create index.html - in zips
		UniteFunctionsDOUBLY::writeFileIfNotExists("", $this->pathExportZip."index.html");
		
		$this->filepathZip = $this->pathExportZip."content_{$randomStrings}.zip";
		
		//prepare export post folder
				
		$this->pathExportContent = $this->pathExportContents."content_{$randomStrings}/";
		UniteFunctionsDOUBLY::mkdirValidate($this->pathExportContent, "Export Post");
		
		$this->pathExportContentImages = $this->pathExportContent."images/";
		
		$this->pathExportContentWidgets = $this->pathExportContent."widgets/";
		$this->pathExportContentWidgetsBG = $this->pathExportContent."widgets_bg/";
				
	}
	
	
	/**
	 * convert urls
	 */
	private function convertExportUrl($string){
		
		//numbers
		
		if(is_numeric($string)){
			
			return($string);
		}
		
		//strings
		
		if(is_string($string)){
						
			$index = strpos($string, GlobalsDOUBLY::$urlBase);
			
			if(self::DEBUG_CONVERT_BASE_URL == true){
				dmp("convert string url:");
				dmp(htmlspecialchars($string));
				dmp("index: $index");
			}
			
			if($index !== false){
				
				$string = str_replace(GlobalsDOUBLY::$urlBase, self::KEY_BASE_URL, $string);
				
				if(self::DEBUG_CONVERT_BASE_URL == true){
					dmp("after replace: ");
					dmp(htmlspecialchars($string));
				}
				
			}
			
			
			return($string);
		}
		
		
		return($string);				
	}
	
	/**
	 * print debug strings after convert
	 */
	private function debugStringsAfterConvert($str, $strNew, $key){
				
		$isChange = ($strNew !== $str);
		
		$str = htmlspecialchars($str);
		$strNew = htmlspecialchars($strNew);
		
		$text = "<b>$key</b>&nbsp;&nbsp;&nbsp;{$str}";
		if($isChange == true)
			$text .= "<br> <span style='color:blue'>$strNew</span>";
		
		dmp($text);
		
	}
	
	
	private function ________EXPORT_IMAGES______________(){}

	/**
	 * get export image key
	 */
	private function getImageKey($filename, $type, $options = null){
		
		switch($type){
			case self::KEY_LOCAL:
			case self::KEY_LOCAL_IMAGE:
			case self::KEY_LOCAL_IMAGE_LINK:
				$output = $type.$filename;
			break;
		}
		
		
		return($output);
	}
	
	
	/**
	 * get array of export images for save
	 */
	private function getExportImagesArrayForSave(){
		
		if(empty($this->arrExportImages))
			return(array());
		
		$arrOutput = array();
		foreach($this->arrExportImages as $arrImage){
			
			$pathImage = UniteFunctionsDOUBLY::getVal($arrImage, "path");
			
			$size = filesize($pathImage);
						
			$filename = UniteFunctionsDOUBLY::getVal($arrImage, "save_filename");
			$originalFilename = UniteFunctionsDOUBLY::getVal($arrImage, "original_filename");
			
			$item = array();
			$item["filename"] = $filename;
			$item["filename_original"] = $originalFilename;
			$item["size"] = $size;
			
			$post = UniteFunctionsDOUBLY::getVal($arrImage, "post");
			$meta = UniteFunctionsDOUBLY::getVal($arrImage, "meta");
						
			if(!empty($post))
				$item["post"] = $post;
			
			if(!empty($meta))
				$item["meta"] = $meta;										
			
			$arrOutput[] = $item;
			
		}
		
		return($arrOutput);
	}
	
	
	/**
	 * get save filename, image should not exists
	 */
	private function getExportImageSaveFilename($pathImage, $forceSaveFilename = null){
		
		$info = pathinfo($pathImage);
				
		$filename = UniteFunctionsDOUBLY::getVal($info, "basename");
				
		if(empty($filename))
			return(null);
		
		if(empty($forceSaveFilename)){
			$isFileExists = array_key_exists($filename, $this->arrCacheImageFilenames);
			if($isFileExists == false){
				$this->arrCacheImageFilenames[$filename] = true;
				return($filename);
			}				
		}
		
		$saveFilename = $filename;
		
		$basename = $info["filename"];
		$ext = $info["extension"];
		
		if(!empty($forceSaveFilename)){
			$saveFilename = $forceSaveFilename.".".$ext;
			
			$this->arrCacheImageFilenames[$saveFilename] = true;
			return($saveFilename);
		}
		
		$counter = 0;
		$textPortion = UniteFunctionsDOUBLY::getStringTextPortion($basename);
		if(empty($textPortion))
			$textPortion = $basename."_";
		
		do{
			$counter++;
			$saveFilename = $textPortion.$counter.".".$ext;
			$isFileExists = array_key_exists($saveFilename, $this->arrCacheImageFilenames);
		
		}while($isFileExists == true);
		
		$this->arrCacheImageFilenames[$saveFilename] = true;
		
		return($saveFilename);
	}
	
	
	/**
	 * get post text only records
	 */
	private function getArrPostTextRecords($post){
		
		$keys = array(
			"post_title","post_content","post_excerpt","post_name"
		);
		
		$arrPost = (array)$post;
		
		$arrOutput = array();
		foreach($keys as $key){
			
			$value = UniteFunctionsDOUBLY::getVal($arrPost, $key);
			$arrOutput[$key] = $value;
		
		}
		
		return($arrOutput);
	}
	
	
	/**
	 * get image data
	 */
	private function getImageAttachmentData($post, $imageID){
						
		if(empty($post))
			return(null);
		
		if($post->post_type != "attachment")
			return(null);
		
		$arrMeta = UniteFunctionsWPDOUBLY::getPostMetaRecords($imageID);
		$arrPostRecords = $this->getArrPostTextRecords($post);
		
		$arrMeta = $this->filterArrayFields($arrMeta, $this->arrIncludeAttachmentKeys);
		
		$arrOutput = array();
		$arrOutput["post"] = $arrPostRecords;
		$arrOutput["meta"] = $arrMeta;
				
		return($arrOutput);
	}
	
	
	/**
	 * convert single export image id to local image path
	 * or any other attachment
	 */
	private function convertExportImageID($str, $returnOnlyFilename = false){
		
		if(is_numeric($str) == false)
			return($str);
		
		$imageID = $str;
		
		$post = get_post($imageID);
		
		if(empty($post))
			return($str);
		
		if($post->post_type != "attachment")
			return($str);
		
		//get image url
		$urlImage = UniteFunctionsWPDOUBLY::getImageUrlByID($str);
		
		if(empty($urlImage))
			$urlImage = $post->guid;
		
		if(empty($urlImage))
			return($str);
					
		if(strpos($urlImage, self::KEY_LOCAL) !== false)
		    return($str);
		 
		$urlImage = HelperDOUBLY::URLtoFull($urlImage);
		$pathImage = HelperDOUBLY::urlToPath($urlImage);
		
				
		if(empty($pathImage))
			return($str);
		
		if(file_exists($pathImage) == false || is_file($pathImage) == false)
			return null;
					
		$handlePath = HelperDOUBLY::convertTitleToHandle($pathImage, false);
				
		if(isset($this->arrExportImages[$handlePath])){
			$localFilename = $this->arrExportImages[$handlePath]["save_filename"];
			
			$localFilenameOutput = self::KEY_LOCAL.$localFilename;
			
			if($returnOnlyFilename == true)
				return($localFilename);
			
			return($localFilenameOutput);
		}
		
		$localFilename = $this->getExportImageSaveFilename($pathImage);
		
		if(empty($localFilename))
			return($str);
		
		$originalFilename = basename($pathImage);
			
		//save the image to array
		
		$arrImage = array();
		$arrImage["save_filename"] = $localFilename;
		$arrImage["original_filename"] = $originalFilename;
		$arrImage["url"] = $urlImage;
		$arrImage["path"] = $pathImage;

		$arrImageData = $this->getImageAttachmentData($post, $imageID);
		
		$arrImage = array_merge($arrImage, $arrImageData);
						
		$this->arrExportImages[$handlePath] = $arrImage;
				
		//output the saved local filename
		
		$localFilenameOutput = self::KEY_LOCAL.$localFilename;
		
		//collect image paths by demand, the variables should be array
		
		if(is_array($this->arrCollectConvertedImages)){
			
			$this->arrCollectConvertedImages[] = array(
				"key"=>$localFilename,
				"imageid"=>$imageID
			);
			
		}
				
		if($returnOnlyFilename == true)
			return($localFilename);
		
		
		return($localFilenameOutput);
	}
	
	
	/**
	 * check and modify image url to id
	 */
	private function checkExportImagesByUrls_modifyImage($str, $urlImage){
				
		$dataAttachment = HelperDOUBLY::getAttachmentDataFromUrl($urlImage);
		
		if(empty($dataAttachment))
			return($str);
		
		$imageID = UniteFunctionsDOUBLY::getVal($dataAttachment, "id");
		
		if(empty($imageID))
			return($str);
		
		$size = UniteFunctionsDOUBLY::getVal($dataAttachment, "size");

		if(empty($size))
			$size = UniteFunctionsWPDOUBLY::THUMB_FULL;
		
		$imageFilename = $this->convertExportImageID($imageID, true);
			
		if(empty($imageFilename))
			return($str);
		
			
		// ------ convert text --------
		
		//convert simple if full size
			
		if($size == UniteFunctionsWPDOUBLY::THUMB_FULL){
			
			$convertKey = $this->getImageKey($imageFilename, self::KEY_LOCAL_IMAGE);
	
			$str = str_replace($urlImage, $convertKey, $str);
			
			return($str);
		}
		
		//convert with size complex key
		
		$convertKey = self::KEY_LOCAL_IMAGE_SIZE.$imageFilename."||".$size.self::KEY_LOCAL_IMAGE_SIZE_END;
		
		$str = str_replace($urlImage, $convertKey, $str);
		
		
		return($str);
	}
	
	
	/**
	 * check and export images by url's
	 */
	private function checkExportImagesByUrls($str, $key, $type = null){
		
		$arrImages = HelperDOUBLY::getArrAttachmentUrlsFromString($str);
		
		if(empty($arrImages))
			return($str);
		
		foreach($arrImages as $urlImage){
			
			$str = $this->checkExportImagesByUrls_modifyImage($str, $urlImage);
		}
		
		
		return($str);
	}
	
	
	/**
	 * get export images from wpbakery page builder
	 */
	private function checkExportImagesFromWPBakery($str, $pattern){
				
		if(empty($str))
			return($str);

		//$pattern - image or images
			
		preg_match_all('/'.$pattern.'="([^"]+)"/', $str, $arrMatches);
		
		if(empty($arrMatches) || count($arrMatches) < 2)
			return($str);
		
		$arrMatchesStrings = $arrMatches[0];
		$arrMathcesNumbers = $arrMatches[1];
		
		
		if(empty($arrMatchesStrings))
			return($str);
		
		foreach($arrMatchesStrings as $key => $strMatch){
						
			$strNumbers = $arrMathcesNumbers[$key];

			$strNumbesNew = $this->convertImageIDsList($strNumbers);
			
			$strMatchNew = str_replace($strNumbers, $strNumbesNew, $strMatch);
			
			$str = str_replace($strMatch, $strMatchNew, $str);
						
		}
		
		return($str);
	}
	
	/**
	 * convert images in image id's list
	 */
	private function convertImageIDsList($strIDs){
		
		if(empty($strIDs))
			return($strIDs);
		
		$arrIDs = explode(",", $strIDs);

		$isValid = UniteFunctionsDOUBLY::isValidIDsArray($arrIDs);

		if($isValid == false)
			return($strIDs);
		
		//convert and create new array of converted
		
		foreach($arrIDs as $key=>$id){
						
			$strID = $this->convertExportImageID($id);
			
			$arrIDs[$key] = $strID;
		}
		
		$strIDsNew = implode(",", $arrIDs);
			
		return($strIDsNew);
	}
	
	
	/**
	 * check export attachment url's in content
	 */
	private function checkExportWPImagesInContent($str, $key, $type=""){
				
		if(empty($str))
			return($str);
		
		//check for wp images in content
			
		$arrImages = UniteFunctionsWPDOUBLY::getWPImagesFromContent($str);
		
		if(empty($arrImages))
			return($str);
		
		foreach($arrImages as $image){
			
			$imageID = UniteFunctionsDOUBLY::getVal($image, "id");
			
			$imageKey = $this->convertExportImageID($imageID, true);
			
			if(empty($imageKey))
				continue;
			
			//replace id class
			$replaceID = self::KEY_LOCAL.$imageKey;
			
			$str = str_replace("wp-image-$imageID", "wp-image-".$replaceID, $str);
			
			//replace image url
			
			$imageUrl = UniteFunctionsDOUBLY::getVal($image, "src");
			
			$size = UniteFunctionsDOUBLY::getVal($image, "size");
			
			$replaceUrl = self::KEY_LOCAL_IMAGE_SIZE.$imageKey."||".$size.self::KEY_LOCAL_IMAGE_SIZE_END;
			
			$str = str_replace($imageUrl, $replaceUrl, $str);
		}
		
				
		return($str);
	}
	
	
	/**
	 * check export image by url
	 * single meanwhile
	 */
	private function checkExportImage_url($str, $key, $type){
				
		$isHasUrls = $hasBaseUrl = HelperDOUBLY::hasBaseUrl($str);
		
		if($isHasUrls == false)
			return($str);
			
		$strNew = HelperDOUBLY::removeBaseUrl($str);
		
		$arrImages = HelperDOUBLY::getArrAttachmentUrlsFromString($strNew);
		
		if(count($arrImages) != 1)
			return($str);
			
		$urlImage = $arrImages[0];
		
		if($urlImage != $strNew)
			return($str);
		
		$imageID = HelperDOUBLY::getAttachmentIDFromUrl($urlImage);
		
		if(empty($imageID))
			return($str);
		
		$imageFilename = $this->convertExportImageID($imageID, true);
		
		if($imageFilename == $imageID)
			return($str);

		$convertKey = $this->getImageKey($imageFilename, self::KEY_LOCAL_IMAGE);
		
		if(self::DEBUG_CONVERT_IMAGES){
			dmp("convert key: $convertKey");
		}
		
		return($convertKey);
	}
	
	
	/**
	 * check and export image if exists
	 */
	private function checkExportImage_id($str, $key, $type = null){
		
		$isThumbLike = $this->isKeyThumbLike($key, $str, $type);
		
		if($isThumbLike == false){
			
			if(self::DEBUG_CONVERT_IMAGES == true){
				dmp("---- key not thumb like: $key");
			}
			
			return($str);
		}
		
		
		if(self::DEBUG_CONVERT_IMAGES == true){
			dmp("---- key thumb like: $key");
			dmp("input str: ".$str);
		}
				
		$isIDsList = UniteFunctionsDOUBLY::isIDsListString($str);
		
		if($isIDsList == true){
		
			$arrIDs = explode(",", $str);
			
			foreach($arrIDs as $idKey=>$idVal){
				$arrIDs[$idKey] = $this->convertExportImageID($idVal);
			}
			
			$strIDs = implode(",", $arrIDs);
			
			return($strIDs);
		}
		
		if(is_numeric($str) == false)
			return($str);

		if($str === 0)
			return($str);
		
		//str is image id
		$str = $this->convertExportImageID($str);

		if(self::DEBUG_CONVERT_IMAGES == true){
			dmp("converted str: ".$str);
		}
		
		
		$this->isImageConverted = true;
		
		return($str);
	}

	/**
	 * check export image id
	 */
	private function checkExportImage($str, $key, $type = null){
		
		//check image id
		
		$strNew = $this->checkExportImage_id($str, $key, $type);
		
		if($strNew != $str)
			return($strNew);
		
		//check image url
		
		$str = $this->checkExportImage_url($str, $key, $type);
		
		return($str);
	}
	
	
	/**
	 * copy all images
	 */
	private function copyExportImages(){
		
		if(empty($this->arrExportImages))
			return(false);
		
		UniteFunctionsDOUBLY::mkdirValidate($this->pathExportContentImages, "export images folder");
		
		//copy images
		foreach($this->arrExportImages as $arrImage){
			$sourceFilepath = $arrImage["path"];
			
			if(is_file($sourceFilepath) == false)
				UniteFunctionsDOUBLY::throwError("Image file: $sourceFilepath not found!");
			
			$filename = $arrImage["save_filename"];
			$destFilepath = $this->pathExportContentImages.$filename;
			
			copy($sourceFilepath, $destFilepath);
		}
		
	}

	private function ________EXPORT_UE_WIDGETS______________(){}
		
	
	/**
	 * check for regular widget
	 */
	private function checkElementorUEWidget_regular($arrWidget){
		
		if(self::DEBUG_CHECK_WIDGETS == true){
			dmp("check widget array");
			dmp($arrWidget);
		}
		
		$alias = $this->getUEWidgetAlias($arrWidget);
		
		if(empty($alias))
			return(false);
		
		$this->arrExportUEWidgets[$alias] = true;
		
		
		if(self::DEBUG_CHECK_WIDGETS == true){
			dmp("widget found - $alias");
		}
		
		
	}
	
	/**
	 * check for ue background widget array
	 */
	private function checkElementorUEWidget_BG($arrSettings){
		
		$backgroundType = UniteFunctionsDOUBLY::getVal($arrSettings, "uc_background_type");
		
		if(empty($backgroundType))
			return(false);
			
		if(is_numeric($backgroundType))
			return(false);
		
		if($backgroundType == "__none__")
			return(false);
		
		$this->arrExportUEBackgrounds[$backgroundType] = true;
		
	}
	
	
	/**
	 * check and add to list UE widget if available
	 */
	private function checkElementorUEWidget($arrValues){
		
		if(self::DEBUG_CHECK_WIDGETS == true)
			dmp("check widgets - start");
					
		if($this->isUEInstalled == false)
			return(false);
		
		$this->checkElementorUEWidget_regular($arrValues);
		
		$this->checkElementorUEWidget_BG($arrValues);
		
		if(self::DEBUG_CHECK_WIDGETS == true)
			dmp("check widgets - end");
		
	}
	
	
	/**
	 * export addon by alias
	 */
	private function exportAddonByAlias($alias, $isBG = false){
		
		//set addon type
		$addonType = GlobalsUC::ADDON_TYPE_ELEMENTOR;
		$pathExport = $this->pathExportContentWidgets;
		
		if($isBG == true){
			$addonType = GlobalsUC::ADDON_TYPE_BGADDON;
			$pathExport = $this->pathExportContentWidgetsBG;
		}
		
		//init addon
		
		$objAddon = new UniteCreatorAddon();
		$objAddon->initByAlias($alias, $addonType);
		
		//export
		
		$objAddonExporter = new UniteCreatorExporter();
		$objAddon->setType($addonType);
		
		$objAddonExporter->initByAddon($objAddon);
		$objAddonExporter->export($pathExport, true);
		
	}
	
	/**
	 * copy regular widgets
	 */
	private function copyExportUEWidgets_regular(){
		
		if(empty($this->arrExportUEWidgets))
			return(false);
		
		$arrWidgets = array_keys($this->arrExportUEWidgets);
		
		UniteFunctionsDOUBLY::mkdirValidate($this->pathExportContentWidgets, "export post widgets");
		
		foreach($arrWidgets as $alias)
			$this->exportAddonByAlias($alias);
		
	}

	/**
	 * copy UE background widgets
	 */
	private function copyExportUEWidgets_BG(){
		
		if(empty($this->arrExportUEBackgrounds))
			return(false);
		
		$arrWidgetsBG = array_keys($this->arrExportUEBackgrounds);
				
		UniteFunctionsDOUBLY::mkdirValidate($this->pathExportContentWidgetsBG, "export post BG widgets");
		
		foreach($arrWidgetsBG as $alias)
			$this->exportAddonByAlias($alias,true);
		
	}
	
	
	/**
	 * copy UE widgets if exists
	 * using the unlimited elements plugin
	 */
	private function copyExportUEWidgets(){
		
		if($this->isUEInstalled == false)
			return(false);
			
		$isAddWidgets = HelperDOUBLY::getGeneralSetting("unlimited_elements_add_widgets");
		
		if($isAddWidgets == "not_add"){
			
			return(false);
		}
		
		$this->copyExportUEWidgets_regular();
		
		$this->copyExportUEWidgets_BG();
		
	}
	
	

	private function ________EXPORT_STRING______________(){}
	
	
	/**
	 * check if key maybe thumb like
	 */
	private function isKeyThumbLike($key, $str, $type = ""){
		
		if($this->exportType == GlobalsDOUBLY::EXPORT_TYPE_MEDIA)
			return(true);
		
		if(is_numeric($key))
			return(false);
		
		//check the type
		if($type == GlobalsDOUBLY::META_FIELD_TYPE_IMAGE)
			return(true);
		
		//exact word
		switch($key){
			case "ids":
			case "id":
			case "_thumbnail_id":
				return(true);
			break;
		}
		
		$arrStrings = $this->arrIDsLikeKeys;
				
		foreach($arrStrings as $string){
			
			$pos = strpos($key, $string);
			
			if($pos !== false)
				return(true);
		}
				
		$isIDsList = UniteFunctionsDOUBLY::isIDsListString($str);
			
		if($isIDsList == true)
			return(true);
		

		
		return(false);
	}
	
	
	/**
	 * convert export string
	 */
	private function convertExportString($str, $key = null, $type = null){
		
		if(is_array($str))
			return($str);
		
		//export image from content (in post content only)
		
		if($key == "post_content"){
			
			if(has_blocks($this->exportPost) == false){
								
				$str = $this->checkExportWPImagesInContent($str, $key, $type);
				
				$str = $this->checkExportImagesByUrls($str, $key, $type);

				//wpbakery page builder
				if(defined("WPB_VC_VERSION")){
					$str = $this->checkExportImagesFromWPBakery($str, "image");
					$str = $this->checkExportImagesFromWPBakery($str, "images");
				}
				
			}
			
		}
		
		//convert url's
			
		$strNew = $this->convertExportUrl($str);
		
		//check for single image
		
		$strNew = $this->checkExportImage($strNew, $key, $type);
				
		if(self::DEBUG_CONVERT_STRINGS == true)
			$this->debugStringsAfterConvert($str, $strNew, $key);			
		
		
		return($strNew);
	}
	
	
	/**
	 * convert strings array - 
	 */
	private function convertArrStringsRecursive($arr, $parentKey = null, $type = null){
				
		if(empty($arr))
			return($arr);
		
		foreach($arr as $key=>$item){
			
			if(is_array($item)){
				$arr[$key] = $this->convertArrStringsRecursive($item, $key);
				continue;
			}
			
			$convertKey = $key;			
			if(is_numeric($convertKey))
				$convertKey = $parentKey;
			
			$item = $this->convertExportString($item, $convertKey);
			
			$arr[$key] = $item;
		}
				
		return($arr);
	}
			
	
	private function ___________EXPORT_TERMS______________(){}
	
	/**
	 * filter array item by fields
	 * convert string on the way
	 */
	private function filterArrayItemFields($arrItems, $arrFields){
		
		if(empty($arrItems))
			return(array());
		
		$arrItemsOutput = array();
		foreach($arrItems as $item){
						
			$item = (array)$item;
			
			
			
			$arrItemsOutput[] = $arrOutput;
			
		}
		
		return($arrItemsOutput);
	}
	
	/**
	 * filter array fields
	 */
	private function filterArrayFields($arrItem, $arrFields){
		
		$arrOutput = array();
		foreach($arrFields as $key){
			
			$value = UniteFunctionsDOUBLY::getVal($arrItem, $key);
			
			$value = $this->convertExportString($value);
			
			$arrOutput[$key] = $value;
		}
		
		return($arrOutput);
	}
	
	
	/**
	 * get term export array
	 */
	private function getArrExportTerm($term){
		
		$term = (array)$term;
		$termID = UniteFunctionsDOUBLY::getVal($term, "term_id");
		
		$arrTerm = $this->filterArrayFields($term, $this->arrTermsFields);
		
		$arrTermOutput = array();
		$arrTermOutput["term"] = $arrTerm;
		
		//get term meta
		
		$arrMeta = UniteFunctionsWPDOUBLY::getTermMetaRecords($termID);
		$arrMeta = $this->modifyMetaValuesForExport($arrMeta, $this->arrExcludeMetaKeysTerms, $this->arrExcludeMetaKeysTermsPrefixes);
		
		$arrTermOutput["meta"] = $arrMeta;
		
				
		return($arrTermOutput);
	}
	
	/**
	 * check and throw wp error if exists
	 */
	private function checkWpError($obj){

		$isError = is_wp_error($obj);
		
		if($isError == false)
			return(false);
			
		$message = $obj->get_error_message();
				
		if(empty($message))
			$message = "Error Occured";
		
		UniteFunctionsDOUBLY::throwError($message);
		
	}
	
	/**
	 * get terms slugs
	 */
	private function setParentTermSlugs($arrTerms){
		
		if(empty($arrTerms))
			return($arrTerms);
		
		//create slugs array
		$arrSlugs = array();
		$arrUniqueTerms = array();
		
		foreach($arrTerms as $term){
			
			$id = $term->term_id;
			$slug = $term->slug;
			$taxonomy = $term->taxonomy;
			
			$termKey = $taxonomy."_".$slug;
			
			$arrSlugs[$id] = $termKey;
			$arrUniqueTerms[$id] = $term;
		}
		
		//replace parent id by slug
				
		foreach($arrUniqueTerms as $key => $term){
			
			$parent = $term->parent;
			if(empty($parent))
				continue;
				
			$slug = UniteFunctionsDOUBLY::getVal($arrSlugs, $parent);
			if(empty($slug))
				continue;
			
			$term->parent = $slug;
						
			$arrUniqueTerms[$key] = $term;
		}
		
		$arrUniqueTerms = UniteFunctionsDOUBLY::assocToArray($arrUniqueTerms);
		
		return($arrUniqueTerms);
	}
	
	
	/**
	 * get export terms keys
	 */
	private function getExportPostTerms_keys($arrTerms){
		
		$arrKeys = array();
		
		foreach($arrTerms as $term){
			
			$key = $term->taxonomy."_".$term->slug;
			
			$arrKeys[] = $key;
		}
		
		return($arrKeys);
	}
	
	
	/**
	 * get export post terms
	 */
	private function getExportPostTerms(){
		
		$output = array();
		$output["post_terms_keys"] = array();
		$output["terms"] = array();
		
		$arrTerms = UniteFunctionsWPDOUBLY::getPostTerms($this->exportPost);
		
		if(empty($arrTerms))
			return($output);
		
		$arrTermsKeys = $this->getExportPostTerms_keys($arrTerms);
		
		$arrParents = UniteFunctionsWPDOUBLY::getArrTermsParents($arrTerms);
		
		$arrCombined = array_merge($arrTerms, $arrParents);
		
		$arrCombined = $this->setParentTermSlugs($arrCombined);
		
		//prepare terms output
			
		$arrTermsOutput = array();
		foreach($arrCombined as $term){
			
			$arrTerm = $this->getArrExportTerm($term);
			
			$key = $term->taxonomy."_".$term->slug;
			
			$arrTermsOutput[$key] = $arrTerm;
		}
		
		$output = array();
		$output["post_terms_keys"] = $arrTermsKeys;
		$output["terms"] = $arrTermsOutput;
		
		
		return($output);
	}
	
	private function ________EXPORT_POST_META___________(){}

	/**
	 * check if the array is image and delete the url of it
	 */
	private function modifyElementorDataArrayImage($arr){
		
		if(is_array($arr) == false)
			return($arr);

		//just in case
		
		if(count($arr) > 10)
			return($arr);
		
		if(isset($arr["id"]) == false || isset($arr["url"]) == false)
			return($arr);
		
		$id = $arr["id"];
		
		if(empty($id) || is_numeric($id) == false)
			return($arr);
		
		$arr["url"] = "";
		
		return($arr);
	}
	
	/**
	 * handle the post list, add the widget post list name
	 */
	private function modifyMeta_handleElementorUEPostList($arr){
		
		if($this->isUEInstalled == false)
			return($arr);
			
		$alias = $this->getUEWidgetAlias($arr);
		
		if(empty($alias))
			return($arr);
			
		try{
			
			$objAddon = new UniteCreatorAddon();
			$objAddon->initByAlias($alias, GlobalsUC::ADDON_TYPE_ELEMENTOR);
			
			$postListParam = $objAddon->getParamByType(UniteCreatorDialogParam::PARAM_POSTS_LIST);
			
			if(empty($postListParam))
				return($arr);
			
			$name = UniteFunctionsDOUBLY::getVal($postListParam, "name");
			
			$arr["ue_post_list_name"] = $name;
			
		}catch(Exception $e){
			
			return($arr);
		}

		return($arr);
	}
	
	/**
	 * modify meta array for export - recursive function
	 */
	private function modifyMetaArrayForExport_recursive($arr, $key){
		
		if($this->isElementorMeta == true){
			
			$this->checkElementorUEWidget($arr);
			
			$arr = $this->modifyElementorDataArrayImage($arr);
			
			$arr = $this->modifyMeta_handleElementorGlobalColors($arr);
			
			if($this->isUEInstalled == true)
				$arr = $this->modifyMeta_handleElementorUEPostList($arr);
				
		}	
		
		if($this->isElementorSectionExport == true)
			$arr = $this->modifyElementorElementIDs($arr);
		else
			$arr = $this->modifyElementorElementIDs($arr, true);	//convert only numeric
			
		foreach($arr as $arrKey => $value){
							
			if(is_array($value)){
				$arr[$arrKey] = $this->modifyMetaArrayForExport_recursive($value, $key);
				continue;
			}
			
			$this->isImageConverted = false;
			
			//key that sent to export string function
			
			$stringKey = $arrKey;
			if(is_numeric($arrKey) && is_numeric($key) == false)
				$stringKey = $key;
			
			$arr[$arrKey] = $this->convertExportString($value, $stringKey);
			
			if($this->isImageConverted == true){
				
				if($this->isElementorMeta == true && isset($arr["url"]))
					$arr["url"] = "";
				
			}
			
		}
		
		return($arr);		
		
	}
		
	
	/**
	 * modify meta array for export
	 */
	private function modifyMetaArrayForExport($arr, $key = null){
		
		if(self::DEBUG_META_ARRAY == true){
			
			echo "<div style='display:flex;border-bottom:1px solid red;padding-bottom:30px;padding-top:30px;'>";
			
			echo "<div style='overflow:scroll;width:500%;border:1px solid lightgray;padding:5px;'>";
			dmp("<b>----- BEFORE-------</b>");
			dmp("<b>{$key}</b>");
			dmp($arr);
			echo "</div>";
		}
		
		$arr = $this->modifyMetaArrayForExport_recursive($arr, $key);
		
		if(self::DEBUG_META_ARRAY == true){
			
			echo "<div style='color:blue;overflow:scroll;width:500%;border:1px solid lightgray;padding:5px;'>";
						
			dmp("<b>----- AFTER -------</b>");
			dmp("<b>{$key}</b>");
			dmp($arr);
			dmp("----------------------------------------------------");
			echo "</div>";
			
			echo "</div>";
		}
		
		return($arr);		
	}
	
	
	/**
	 * export elementor global colors
	 */
	private function modifyMeta_handleElementorGlobalColors($arr){
		
		if(empty($arr))
			return($arr);
		
		if($this->arrElementorGlobalColors === null)
			$this->arrElementorGlobalColors = HelperDOUBLY::getElementorGlobalColors();
				
		if($this->arrElementorGlobalTypography === null)
				$this->arrElementorGlobalTypography = HelperDOUBLY::getElementorGlobalTypography();
				
		if(empty($this->arrElementorGlobalColors) && empty($this->arrElementorGlobalTypography))
			return($arr);
		
		$isGlobalsExists = array_key_exists("__globals__", $arr);
		
		if($isGlobalsExists == false)
			return($arr);
				
		$arrGlobals = UniteFunctionsDOUBLY::getVal($arr, "__globals__");
		
		foreach($arrGlobals as $globalKey=>$value){
			
			if(empty($value)){
				unset($arrGlobals[$globalKey]);
				continue;
			}
			
			if(strpos($value, "globals/typography") !== false){
			
				$value = str_replace("globals/typography?id=", "", $value);
				
				$arrValues = UniteFunctionsDOUBLY::getVal($this->arrElementorGlobalTypography, $value);
				
				if(!empty($arrValues) && is_array($arrValues))
					$arr = array_merge($arr, $arrValues);
			}
			
			if(strpos($value, "globals/colors") !== false){
				$value = str_replace("globals/colors?id=", "", $value);
				$color = UniteFunctionsDOUBLY::getVal($this->arrElementorGlobalColors, $value);
				
				if(!empty($color))
					$arr[$globalKey] = $color;
			}
						
			unset($arrGlobals[$globalKey]);
		}
		
		if(empty($arrGlobals))
			unset($arr["__globals__"]);	
		else
			$arr["__globals__"] = $arrGlobals;
		
		
		return($arr);
	}
	
	/**
	 * modify the meta value for export
	 */
	private function modifyMetaValueForExport($str, $key, $type){
		
		if(self::DEBUG_META_STRING_BEFORE_AFTER == true){
			dmp("before: $key | $str");
			$originalStr = $str;
		}
		
		//serialize
		$arr = UniteFunctionsDOUBLY::maybeUnserialize($str);
				
		if(is_array($arr)){
					
			$arr = $this->modifyMetaArrayForExport($arr, $key);
			
			$str = serialize($arr);
			
			if(self::DEBUG_META_STRING_BEFORE_AFTER == true){
				
				$style = "";
				if($originalStr != $str)
					dmp("<div style='color:blue'>after unserialize: $key | $str</div>");
				else
					dmp("after unserialize: $key | $str");
			}
			
			return($str);
		}
			
		//json
		$arr = UniteFunctionsDOUBLY::maybeJsonDecode($str);
		if(is_array($arr)){
			
			$arr = $this->modifyMetaArrayForExport($arr, $key);
			
			$str = json_encode($arr);
			
			if(self::DEBUG_META_STRING_BEFORE_AFTER == true){
				
				if($originalStr != $str)
					dmp("<div style='color:blue'>after json: $key | $str</div>");
				else
					dmp("after json: $key | $str");
				
			}
			
			return($str);
		}
		
		//string
		$str = $this->convertExportString($str, $key, $type);

		if(self::DEBUG_META_STRING_BEFORE_AFTER == true){
			
			if($originalStr != $str)
				dmp("<div style='color:blue'>after : $key | $str</div>");
			else
				dmp("after : $key | $str");			
		}
		
		
		return($str);
	}
	
	/**
	 * modify meta values
	 */
	private function modifyMetaValuesForExport($arrMeta, $arrExcludeKeys = array(), $arrExcludePrefixes = array(), $arrTypes = array()){
		
		if(empty($arrMeta))
			return(array());
		
		foreach($arrMeta as $key => $value){
			
			$this->isElementorMeta = false;
			
			if($key == "_elementor_data")
				$this->isElementorMeta = true;
			
			if(array_search($key, $arrExcludeKeys) !== false){
				unset($arrMeta[$key]);
				continue;
			}

			$type = UniteFunctionsDOUBLY::getVal($arrTypes, $key);
			
			$value = $this->modifyMetaValueForExport($value, $key, $type);
			
			$arrMeta[$key] = $value;
			
			
			//unset by prefixes
			if(empty($arrExcludePrefixes))
				continue;
				
			foreach($arrExcludePrefixes as $prefix)
				if(strpos($key, $prefix) === 0)
					unset($arrMeta[$key]);
							
		}
		
		return($arrMeta);
	}
	
	
	/**
	 * get post meta content
	 */
	private function getPostMetaContent($postID){
		
		$arrMeta = UniteFunctionsWPDOUBLY::getPostMetaRecords($postID);
				
		$arrTypes = array();
		if(!empty($arrMeta))
			$arrTypes = UniteFunctionsWPDOUBLY::getPostMetaTypes($postID);
				
		$arrMeta = $this->modifyMetaValuesForExport($arrMeta, $this->arrExcludeMetaKeys, $this->arrExcludeMetaKeysPrefix, $arrTypes);
		
				
		return($arrMeta);		
	}

	private function ________COMMENTS___________(){}
	
	
	/**
	 * get export post comments
	 * add fields: doubly_id, doubly_parentid if needed
	 */
	private function getExportPostComments(){
		
		$args = array();
		$args["post_id"] = $this->exportPostID;
		
		$arrComments = get_comments($args);
		
		if(empty($arrComments))
			return(array());
		
		$arrExportComments = array();
		
		$arrIDs = array();
		
		$hasChildren = false;
		$arrParentIDs = array();
		
		foreach($arrComments as $comment){
			
			$commentID = $comment->comment_ID;
												
			$arrComment = UniteFunctionsDOUBLY::convertStdClassToArray($comment);
			unset($arrComment["comment_ID"]);
			unset($arrComment["user_id"]);
			unset($arrComment["comment_post_ID"]);
			
			$arrComment["doubly_id"] = "comment_{$commentID}";
			
			$arrComment["comment_author_url"] = "";
			
			//parent
			$parentID = $arrComment["comment_parent"];
			
			if(!empty($parentID)){
				
				$doublyParentID = "comment_{$parentID}";
				
				$arrParentIDs[$doublyParentID] = true;
				$arrComment["doubly_parentid"] = $doublyParentID;
			}
				
			unset($arrComment["comment_parent"]);
			
			$arrExportComments[] = $arrComment;
		}
		
		
		//delete ids if not parents
		
		foreach($arrExportComments as $index => $comment){
			
			$id = UniteFunctionsDOUBLY::getVal($comment, "doubly_id");
			
			if(isset($arrParentIDs[$id]) == false)
				unset($arrExportComments[$index]["doubly_id"]);
		}
				
		return($arrExportComments);
	}
	
	
	private function ________GUTENBERG_POST___________(){}

	/**
	 * convert gutenberg image html
	 */
	private function convertGutenbergBlock_htmlImage($html, $innerContent, $item, $attributes){
		
		$imageID = UniteFunctionsDOUBLY::getVal($item, "imageid");
		$key = UniteFunctionsDOUBLY::getVal($item, "key");
		
		$htmlKeyID = self::KEY_LOCAL.$key;
		$htmlKeyImage = self::KEY_LOCAL_IMAGE.$key;
		$htmlKeyImageLink = self::KEY_LOCAL_IMAGE_LINK.$key;
		
		$arrReplace = array();
		$arrReplace["html"] = $html;
		foreach($innerContent as $index=>$content){
			$arrReplace["inner_".$index] = $content;
		}
		
		
		$size = "full";
		$sizeSlug = UniteFunctionsDOUBLY::getVal($attributes, "sizeSlug");
		if(!empty($sizeSlug))
			$size = $sizeSlug;
		
		$urlImage = UniteFunctionsWPDOUBLY::getUrlAttachmentImage($imageID, $size);
		$link = get_attachment_link( $imageID );
		
		foreach($arrReplace as $key=>$html){
				
			//replace the image id in all html
			
			$html = str_replace("id=\"$imageID\"", "id=\"$htmlKeyID\"", $html);
			$html = str_replace("image-{$imageID}", "image-{$htmlKeyID}", $html);
			
			//replace the image url by size
							
			if(!empty($urlImage))
				$html = str_replace($urlImage, $htmlKeyImage, $html);
			
			//replace the link
			
			$html = str_replace($link, $htmlKeyImageLink, $html);
			
			$arrReplace[$key] = $html;
		}
		
		//combine back
		$index = 0;
		foreach($arrReplace as $key => $content){
			
			if($key == "html"){
				$html = $content;
				continue;
			}
			
			$innerContent[$index] = $content;
			$index++;
		}
		
		//response
		$response = array();
		$response["html"] = $html;
		$response["innerContent"] = $innerContent;
		
		return($response);
	}
	
	
	/**
	 * convert gutenberg block html with images and url's
	 */
	private function convertGutenbergBlock_htmlImages($block, $attributes){
		
		//convert html images
		
		if(empty($this->arrCollectConvertedImages))
			return($block);
					
		$html = UniteFunctionsDOUBLY::getVal($block, "innerHTML");
		$innerContent = UniteFunctionsDOUBLY::getVal($block, "innerContent");
		
		foreach($this->arrCollectConvertedImages as $item){
			
			$response = $this->convertGutenbergBlock_htmlImage($html, $innerContent, $item, $attributes);
			
			$html = $response["html"];
			$innerContent = $response["innerContent"];
			
		}
		
		$block["innerHTML"] = $html;
		$block["innerContent"] = $innerContent;
		
		return($block);
	}
	
	/**
	 * collect images from gutenberg
	 */
	private function collectImagesFromGutenbergBlock($block){
		
		$html = UniteFunctionsDOUBLY::getVal($block, "innerHTML");
		
		$arrContent = UniteFunctionsDOUBLY::getVal($block, "innerContent");
		
		if(empty($arrContent))
			$arrContent = array();
			
		$arrContent[] = $html;
		
		foreach($arrContent as $contentHTML){
			
			$this->checkExportWPImagesInContent($contentHTML, "gutenberg");
			
			$this->checkExportImagesByUrls($contentHTML, "gutenberg");
		}
		
	}
	
	
	/**
	 * convert gutenberg block
	 */
	protected function convertGutenbergBlock($block){
		
		
		$name = UniteFunctionsDOUBLY::getVal($block, "blockName");
		
		if(self::DEBUG_GUTENBERG_BLOCKS == true){
			
			if(!self::DEBUG_GUTENBERG_BLOCKS_BLOCKNAME || self::DEBUG_GUTENBERG_BLOCKS_BLOCKNAME == $name){
				dmp("input block");
				dmp($block);
			}
			
		}
		
		
		$attributes = UniteFunctionsDOUBLY::getVal($block, "attrs");
		
		$this->arrCollectConvertedImages = array();
		
		$attributes = $this->convertArrStringsRecursive($attributes);
		
		$this->collectImagesFromGutenbergBlock($block);
		
		$block = $this->convertGutenbergBlock_htmlImages($block, $attributes);
				
		$block["attrs"] = $attributes;

		$this->arrCollectConvertedImages = null;
		
		
		if(self::DEBUG_GUTENBERG_BLOCKS == true){
			
			if(!self::DEBUG_GUTENBERG_BLOCKS_BLOCKNAME || self::DEBUG_GUTENBERG_BLOCKS_BLOCKNAME == $name){
			dmp("output block");
			dmp($block);
			}
			
		}
			
		
		return($block);
	}
	
	
	/**
	 * convert gutenberg content
	 */
	private function convertGutenbergContent($content){
		
		if(has_blocks($this->exportPost) == false)
			return($content);
		
		$arrBlocks = parse_blocks($content);
		
		
		if(empty($arrBlocks))
			return($content);
		
		$arrBlocksNEW = $this->convertGutenbergBlocks($arrBlocks);
		
		if(self::DEBUG_SHOW_GUTENBERG_BLOCKS == true){
			
			$this->showGutenbergBlocksBeforeAfter($arrBlocks, $arrBlocksNEW);
			exit();
		}

		if(self::DEBUG_GUTENBERG_BLOCKS == true){
			
			dmp("end debug blocks");
			exit();
		}
		
		$contentForSave = serialize_blocks($arrBlocksNEW);
		
				
		return($contentForSave);
	}
	

	private function ________WOOCOMMERCE___________(){}
	
	/**
	 * export woo attributes
	 */
	private function exportWooAttributes(){

		$attributes = get_post_meta($this->exportPostID, "_product_attributes", true);
		
		if(empty($attributes))
			return(false);
			
		if(is_array($attributes) == false)
			return(false);
				
		foreach($attributes as $item){
			
			$name = UniteFunctionsDOUBLY::getVal($item, "name");
			
			$isTaxonomy = UniteFunctionsDOUBLY::getVal($item, "is_taxonomy");
			$isTaxonomy = UniteFunctionsDOUBLY::strToBool($isTaxonomy);
			
			if($isTaxonomy == false)
				return(false);
			
			if(isset($this->arrWooAttributeTaxonomies[$name]))
				return(false);
			
			//maybe this attribute not exists in db (happends)
			
			$attributeData = UniteFunctionsWPDOUBLY::getWooAttributeTaxonomyData($name);
			if(empty($attributeData))
				return(false);
			
			$this->arrWooAttributeTaxonomies[$name] = true;
			
			$this->addToExportData("woo_attributes", array($name=>$attributeData));
		}
		
	}
	
	
	/**
	 * export variations
	 */
	private function exportWooVariations(){
		
		if(function_exists("wc_get_product") == false)
			return(null);
		
		//wc_get_ac
    	$objInfo = wc_get_product($this->exportPostID);
		
    	if(empty($objInfo))
    		return(null);
				
    	$arrData = $objInfo->get_data();
		$type = $objInfo->get_type();
		
		if($type != "variable")
			return(false);
			
		$arrVariationIDs = $objInfo->get_children();
		
		if(empty($arrVariationIDs))
			return(false);
		
		$arrVariations = array();
		
		foreach($arrVariationIDs as $variationID){
			
			$postVariation = get_post($variationID);
			
			$arrVariation = $this->exportPost($postVariation, true);
			
			$arrVariations[] = $arrVariation;
			
		}
		
		$this->arrExportPostData["variations"] = $arrVariations;
		
	}
	
	
	/**
	 * export woo content - add taxonomies for attributes
	 */
	private function exportWooProductContent(){
		
		$this->exportWooAttributes();
		
		$this->exportWooVariations();
		
	}
	
	/**
	 * export woo order related - order items
	 */
	private function exportOrderRelated(){
		
		$order = new WC_Order( $this->exportPostID );
		
		//export order items
		
		$order_items = $order->get_items();
		
		foreach ( $order_items as $item ) {
			
			$productID = $item->get_product_id();
			
			$product = get_post( $productID ); 
			
			$productData = $item->get_data();
			
			$slug = $product->post_name;
			
			$productData['slug'] = $slug;
			
			unset($productData["order_id"]);
			unset($productData["product_id"]);
			unset($productData["id"]);
			unset($productData["variation_id"]);
						
			$this->arrExportPostData = UniteFunctionsDOUBLY::addMergeArrayAsItem($this->arrExportPostData, "order_related", array($productData));
						
		}
		
	}
	
	private function __________RELATED_POSTS___________(){}
	
	
	/**
	 * export posts from another post type that related to this post by post parent
	 */
	private function exportRelatedPosts($relatedType){
		
		$postData = UniteFunctionsDOUBLY::getVal($this->arrExportPostData, "post");
		
		if(empty($postData))
			return(false);
		
		//get related posts
		
		$args = array();
		$args["post_type"] = $relatedType;
		$args["posts_per_page"] = -1;
		$args["post_status"] = "publish";
		$args["post_parent"] = $this->exportPostID;
				
		$arrRelated = get_posts($args);
						
		if(empty($arrRelated))
			return(false);

		$arrPosts = array();
				
		foreach($arrRelated as $post){
						
			$arrPostData = $this->exportPost($post, true);
			
			$arrPosts[] = $arrPostData;
		}
		
		
		if(empty($arrPosts))
			return(false);
		
		//save related post
		
		$this->arrExportPostData = UniteFunctionsDOUBLY::addMergeArrayAsItem($this->arrExportPostData, "related_posts", $arrPosts);
		
		
	}
	
	
	private function ________EXPORT_POST___________(){}
	
	
	
	/**
	 * fill the export post content
	 */
	private function getPostRecordContent($postID){
		
		$postRecord = UniteFunctionsWPDOUBLY::getPostRecord($postID);
		
		if(empty($postRecord))
			return(false);
		
		$arrExportRecord = array();
		
		foreach($this->arrPostFields as $field){

			$value = UniteFunctionsDOUBLY::getVal($postRecord, $field);
			
			if($field == "post_content"){
				
				$value = $this->convertGutenbergContent($value);
			}
			
			$value = $this->convertExportString($value, $field);
			
			$arrExportRecord[$field] = $value;
		}
		
		
		return($arrExportRecord);
	}
	
	
	/**
	 * add some data to export data
	 */
	private function addToExportData($key, $arrData){
		
		
		switch($key){
			case "posts":
				
				if(isset($this->arrExportContent[$key]) == false)
					$this->arrExportContent[$key] = array();
				
				$this->arrExportContent[$key][] = $arrData;
				return(false);
			break;
			default:
				
				if(empty($arrData))
					$arrData = array();
				
				$arrCurrent = UniteFunctionsDOUBLY::getVal($this->arrExportContent, $key, array());
				
				if(empty($arrCurrent))
					$arrCurrent = array();
				
				$arrExport = array_merge($arrCurrent, $arrData);
				
				if(empty($arrExport))
					return(false);	
				
				if(isset($this->arrExportContent[$key]) == false)
					$this->arrExportContent[$key] = array();
				
				$this->arrExportContent[$key] = $arrExport;
				
			break;
		}
		
	}
	
	
	
	/**
	 * make special exports by post type 
	 */
	private function checkSpecialExports(){
		
		$postType = $this->exportPost->post_type;
		
		switch($postType){
			case "product_variation":
				
				$this->exportWooAttributes();
				
			break;
			case "product":
				
				$this->exportWooProductContent();
				
			break;
			case "acf-field-group":
				
				$this->exportRelatedPosts("acf-field");
				
			break;
			case "shop_order":
				
				$this->exportOrderRelated();
				
			break;
			default:
								
			break;
		}
		
		
	}
	
	
	/**
	 * fill post content
	 */
	private function fillExportPostContent($isReturnData = false){
		
		$arrPost = array();
		
		//post record
		$postRecord = $this->getPostRecordContent($this->exportPostID);
		$arrPost["post"] = $postRecord;
		
		
		//post meta
		$arrPostMeta = $this->getPostMetaContent($this->exportPostID);
		
		$arrPost["meta"] = $arrPostMeta;
		
		//terms
		$arrTermsOutput = $this->getExportPostTerms();
				
		$arrPostTermKeys = $arrTermsOutput["post_terms_keys"];
		$arrTerms = $arrTermsOutput["terms"];
				
		$arrPost["terms"] = $arrPostTermKeys;
		
		//comments
		
		$arrCommentsOutput = $this->getExportPostComments();
		
		$arrPost["comments"] = $arrCommentsOutput;
		
		
		if($isReturnData == false)
			$this->arrExportPostData = $arrPost;
		
		//add the terms
		$this->addToExportData("terms", $arrTerms);
		
		//add the special stuff
		$this->checkSpecialExports();
		
		if($isReturnData == false)
			$this->addToExportData("posts", $this->arrExportPostData);
		
		
		return($arrPost);
	}
	
	
	private function ________EXPORT_MULTIPLE_POSTS___________(){}
	
	/**
	 * start export
	 */
	private function startExport($type){
		
		if(empty($type))
			UniteFunctionsDOUBLY::throwError("start export error - no type given");
		
		$this->exportType = $type;
			
		$this->clearExportPostData();
		$this->prepareFolders();
		
		$this->arrExportContent["type"] = $type;
		
		//$this->arrExportContent["site"] = GlobalsDOUBLY::$currentSiteUrl;
		
	}
	
	
	/**
	 * end export
	 */
	private function endExport(){
		
		//add to export all the images
		
		$arrImages = $this->getExportImagesArrayForSave();
		
		$this->addToExportData("images", $arrImages);
		
		//prepare the widgets
		
		$isExportTypeSupportElementor = $this->exportType == GlobalsDOUBLY::EXPORT_TYPE_POSTS || $this->exportType == GlobalsDOUBLY::EXPORT_TYPE_ELEMENTOR_SECTION;
		
		if($isExportTypeSupportElementor && $this->isUEInstalled == true){
			
			$arrWidgets = array_keys($this->arrExportUEWidgets);
			$arrWidgetsBG = array_keys($this->arrExportUEBackgrounds);

			$this->addToExportData("widgets", $arrWidgets);
			$this->addToExportData("widgets_bg", $arrWidgetsBG);
		}
		
		
		//copy the collected files
		$this->copyExportImages();		
		$this->copyExportUEWidgets();
		
		//fix the export type, in case that there are only images
		$arrPosts = UniteFunctionsDOUBLY::getVal($this->arrExportContent, "posts");
		$arrImages = UniteFunctionsDOUBLY::getVal($this->arrExportContent, "images");
		$type = $this->arrExportContent["type"];
		
		if($type == GlobalsDOUBLY::EXPORT_TYPE_POSTS && empty($arrPosts) && !empty($arrImages))
			$this->arrExportContent["type"] = GlobalsDOUBLY::EXPORT_TYPE_MEDIA;
		
		//write a content file
		$contentText = serialize($this->arrExportContent);
		$filepathContent = $this->pathExportContent."content.txt";
		
		UniteFunctionsDOUBLY::writeFile($contentText, $filepathContent);
		
		if(file_exists($filepathContent) == false)
			UniteFunctionsDOUBLY::throwError("Content file didn't wrote");
		
		//make zip
		$zip = new UniteZipDOUBLY();
		$zip->makeZip($this->pathExportContent, $this->filepathZip);
		
		//check the zip created
		if(file_exists($this->filepathZip) == false)
			UniteFunctionsDOUBLY::throwError("Content file didn't wrote");
		
		//delete the content folder
		UniteFunctionsDOUBLY::deleteDir($this->pathExportContent, true);
		
		if(self::DEBUG_AFTER_EXPORT == true)
			$this->testPrintText();
		
	}
	
	
	/**
	 * expost single post within
	 */
	private function exportPost($post, $isReturnData = false){
		
		if(empty($this->exportType))
			UniteFunctionsDOUBLY::throwError("Export post not available , export not started");
		
		$this->exportPost = $post;
		$this->exportPostID = $post->ID;
		
		$postType = $this->exportPost->post_type;
		
		if($isReturnData == false)
			$this->arrExportedPosts[] = $post;
		
		if($postType == "attachment")
			$this->checkExportImage($this->exportPostID, "image");
		else
			$arrPost = $this->fillExportPostContent($isReturnData);
		
		if($isReturnData == true)
			return($arrPost);
	}
	
	
	
	/**
	 * export multiple posts
	 */
	private function exportMultiplePosts($arrPosts, $exportType = null){
		
		$this->arrExportedPosts = array();
				
		if(empty($exportType))
			$exportType = GlobalsDOUBLY::EXPORT_TYPE_POSTS;
		
		$this->startExport($exportType);
		
		foreach($arrPosts as $post){
			
			$this->exportPost($post);
			
		}
		
		$this->endExport();
		
	}
	
	private function ________EXPORT_ELEMENTOR_SECTION___________(){}
	
		
	
	/**
	 * export elementor section
	 */
	private function exportElementorSection($post, $sectionID){
		
		UniteFunctionsDOUBLY::validateNotEmpty($post,"post");
		
		if(!isset($post->ID))
			UniteFunctionsDOUBLY::throwError("No post id found");
		
		$postID = $post->ID;
		
		$arrContent = HelperDOUBLY::getElementorContent($postID);
		
		if(empty($arrContent))
			UniteFunctionsDOUBLY::throwError("Elementor Content Not Found");
		
		$arrSection = HelperDOUBLY::getElementorSectionFromContent($arrContent, $sectionID);
		
		if(empty($arrSection)){
			
			UniteFunctionsDOUBLY::throwError("Section $sectionID not found in layout: $postID");
		}
		
		//start the export
		
		$this->startExport("elementor_section");
		
		$this->isElementorMeta = true;
		$this->isElementorSectionExport = true;
		
		$arrSection = $this->modifyMetaArrayForExport($arrSection);
		
		$this->arrExportContent["content"] = $arrSection;
		
		$this->endExport();
	}
	
	private function ________EXPORT_OBJECT___________(){}
	
	/**
	 * export object
	 */
	private function exportObjects($type, $id){
		
		$objIntegrations = new Doubly_Integrations();
		
		$this->startExport("objects");
		
		$this->arrExportContent["objtype"] = $type;
		
		switch($type){
			
			case GlobalsDOUBLY::EXPORT_TYPE_SNIPPET:
				
				$arrSnippets = $objIntegrations->getSnippetsExportData($id);
				
				$this->exportedFilename = $objIntegrations->snippets_getExportFilename($arrSnippets);
				
				$this->addToExportData("objects", $arrSnippets);
				
			break;
			default:
				UniteFunctionsDOUBLY::throwError("Wrong object type: $type");
			break;
			
		}
				
		$this->endExport();
		
	}
	
	/**
	 * get last exported filename
	 */
	public function getExportedFilename(){
		
		return($this->exportedFilename);
	}
	
	
	private function ________EXPORT_FROM_DATA___________(){}
	
	
	/**
	 * export post from slug
	 */
	private function exportPostBySlugFromData($data){
		
		$slug = UniteFunctionsDOUBLY::getVal($data, "slug");
		$slug = sanitize_title_with_dashes($slug);
		
		UniteFunctionsDOUBLY::validateNotEmpty($slug, "post name");
		
		$type = UniteFunctionsDOUBLY::getVal($data, "type", UniteFunctionsDOUBLY::SANITIZE_KEY);
		UniteFunctionsDOUBLY::validateNotEmpty($type,"Post Type");
		
		$post = UniteFunctionsWPDOUBLY::getPostByName($slug, $type);
		
		if(empty($post))
			UniteFunctionsDOUBLY::throwError("post not found");
		
		$arrPosts = array($post);
		
		$this->exportMultiplePosts($arrPosts);
	}
	
	
	/**
	 * clear the keys of some array
	 */
	private function clearExportContentKeys($key){
		
		$arr = UniteFunctionsDOUBLY::getVal($this->arrExportContent, $key);
		if(empty($arr))
			return(false);
			
		$this->arrExportContent[$key] = array_values($arr);
		
	}
	
	
	/**
	 * export multiple posts by data
	 */
	private function exportMultiplePostsByData($data, $exportType = ""){
		
		$key = "postid";
		$name = "post";
		
		if($exportType == "media"){
			$key = "thumbid";
			$name = "thumb";
		}
		
		$strIDs = UniteFunctionsDOUBLY::getVal($data, $key);
		
		if(is_array($strIDs)){
			
			$arrIDs = $strIDs;
			
		}else{	//convert from string id's
			
			UniteFunctionsDOUBLY::validateIDsList($strIDs,"$name ids");
			
			$arrIDs = explode(",", $strIDs);
		}
				
		$arrPosts = UniteFunctionsWPDOUBLY::getPostsObjectsFromArrIDs($arrIDs);
				
		if(empty($arrPosts))
			UniteFunctionsDOUBLY::throwError("no {$name}s found");

		
		$this->exportMultiplePosts($arrPosts, $exportType);
				
	}
	
	
	/**
	 * export post id from data
	 */
	private function exportPostByIDFromData($data){
		
		$postID = UniteFunctionsDOUBLY::getVal($data, "postid");
				
		if(is_array($postID)){
			
			$isValid = UniteFunctionsDOUBLY::isValidIDsArray($postID);
			if(!$isValid)
				UniteFunctionsDOUBLY::throwError("Not valid id's array");
			
			$this->exportMultiplePostsByData($data);
			return(false);
		}
		
		$isIDsList = UniteFunctionsDOUBLY::isIDsListString($postID);
				
		if($isIDsList == true){
			$this->exportMultiplePostsByData($data);
			return(false);
		}

		
		$postID = (int)$postID;
		UniteFunctionsDOUBLY::validateNotEmpty($postID, "post id");
		$post = get_post($postID);
		
		if(empty($post))
			UniteFunctionsDOUBLY::throwError("Post not found");
		
		$arrPosts = array($post);
		
		$this->exportMultiplePosts($arrPosts);
		
	}
	
	/**
	 * export elementor section
	 */
	private function exportElementorSectionFromData($data){
		
		$sectionID = UniteFunctionsDOUBLY::getVal($data, "sectionid");
		
		UniteFunctionsDOUBLY::validateNotEmpty($sectionID,"section id");
		
		$postID = UniteFunctionsDOUBLY::getVal($data, "postid");

		$postID = (int)$postID;
		
		UniteFunctionsDOUBLY::validateNotEmpty($postID, "post id");
		
		$post = get_post($postID);
		
		if(empty($post))
			UniteFunctionsDOUBLY::throwError("Post not found");
		
		$this->exportElementorSection($post, $sectionID);
		
	}
	
	
	/**
	 * export objects from data
	 */
	public function exportObjectFromData($data){
		
		$objType = UniteFunctionsDOUBLY::getVal($data, "objtype");
		$objID = UniteFunctionsDOUBLY::getVal($data, "id");
		
		UniteFunctionsDOUBLY::validateNotEmpty($objType,"objtype");
		UniteFunctionsDOUBLY::validateNotEmpty($objID,"objid");
		
		$this->exportObjects($objType, $objID);
		
		return($this->filepathZip);
	}
	
	/**
	 * get exported posts
	 */
	public function getExportedPosts(){
		
		return($this->arrExportedPosts);
	}
	
	
	/**
	 * export post from data
	 */
	public function exportPostFromData($data){
		
		if(self::DEBUG_PHP_ERRORS == true)
			ini_set("display_errors","on");
		
		$type = UniteFunctionsDOUBLY::getVal($data, "type");
		
		switch($type){
			case "elementor_section":
				$this->exportElementorSectionFromData($data);
			break;
			case "posts":
				
				//export post/posts
				
				if(isset($data["slug"])){
					$this->exportPostBySlugFromData($data);
					return(false);
				}
								
				//by post id
				$this->exportPostByIDFromData($data);
				
			break;
			case "objects":
				
				$this->exportObjectFromData($data);
				
			break;
			default:
				UniteFunctionsDOUBLY::throwError("Wrong Export Type: $type");
			break;
		}
		
		return($this->filepathZip);
	}
	
	
	
	
}