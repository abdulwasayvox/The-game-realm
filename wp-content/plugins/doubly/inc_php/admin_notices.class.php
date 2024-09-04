<?php
/**
 * @package Doubly
 * @author Unlimited Elements
 * @copyright (C) 2022 Unlimited Elements, All Rights Reserved. 
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 **/

if(!defined("DOUBLY_INC")) die("restricted access");


class Doubly_AdminNotices{
	
	const NOTICES_LIMIT = 1;
	const TYPE_ADVANCED = "advanced";
	const TYPE_BANNER = "banner";
	
	private static $isInited = false;
	private static $arrNotices = array();
	
	
	/**
	 * set notice
	 */
	public function setNotice($text, $id, $params = array()){
		
		//don't let to add more then limited notices
		if(count(self::$arrNotices) >= self::NOTICES_LIMIT)
			return(false);
		
			
		$type = UniteFunctionsDOUBLY::getVal($params, "type");
		
		if(empty($text) && $type != self::TYPE_BANNER)
			return(false);
			
		if(empty($id))
			return(false);
		
		$arrNotice = array();
		$arrNotice["text"] = $text;
		$arrNotice["id"] = $id;
		
		if(!empty($params)){
			unset($params["text"]);
			unset($params["id"]);
			unset($params["expire"]);
			
			$arrNotice = array_merge($arrNotice, $params);
		}
		
		if(isset(self::$arrNotices[$id]))
			return(false);
		
		self::$arrNotices[$id] = $arrNotice;
		
		$this->init();
	}
	
	
	/**
	 * get notice html
	 */
	private function getNoticeHtml($text, $id, $isDissmissable = true, $params = array()){
		
				
		$type = UniteFunctionsDOUBLY::getVal($params, "type");
		
		$isNoWrap = UniteFunctionsDOUBLY::getVal($params, "no-notice-wrap");
		$isNoWrap = UniteFunctionsDOUBLY::strToBool($isNoWrap);
				
		$classWrap = "notice ";
		
		if($isNoWrap == true)
			$classWrap = "";
		
		$typeClass = "notice-info";
		
		$typeClass = "notice-error";
			
		$class = "notice doubly-admin-notice $typeClass";	//doubly-banner-type-banner
		
		if($type == self::TYPE_ADVANCED)
			$class .= " doubly-notice-advanced";
				
		if($type == self::TYPE_BANNER){
			$class = "notice doubly-admin-notice";
			
			if($isNoWrap == true)
				$class .= " doubly-admin-notice--nowrap";
				
		}
		
		$classDissmissable = "is-dismissible";
		$classDissmissable = "";
		
		$htmlDissmiss = "";
		
		if($isDissmissable == true){
			
			$textDissmiss = __("Dismiss", "unlimited-elements-for-elementor");
			$textDissmissLabel = __("Dismiss unlimited elements message","unlimited-elements-for-elementor");
			
			$textDissmiss = esc_attr($textDissmiss);
			$textDissmissLabel = esc_attr($textDissmissLabel);
			
			$addClassDissmiss = "";
			
			$urlDissmiss = GlobalsDOUBLY::$urlCurrentPage;
			
			$urlDissmiss = UniteFunctionsDOUBLY::addUrlParams($urlDissmiss, "doubly_dismiss_notice=$id");
			
			$htmlDissmiss = "\n<a class=\"doubly-notice-dismiss\" href=\"{$urlDissmiss}\" aria-label=\"$textDissmissLabel\">$textDissmiss</a>\n";
			
			if(self::TYPE_BANNER)
				$htmlDissmiss = "\n<a class=\"doubly-notice-dismiss-banner\" href=\"{$urlDissmiss}\" title=\"{$textDissmiss}\" aria-label=\"$textDissmissLabel\">X</a>\n";
			
		}
		
		switch($type){
			
			case self::TYPE_ADVANCED:
				
				$buttonText = UniteFunctionsDOUBLY::getVal($params, "button_text");
				$buttonLink = UniteFunctionsDOUBLY::getVal($params, "button_link");
							
				$urlLogo = GlobalsDOUBLY::$urlImages."logo-circle.svg";
				
				$htmlButton = "";
				
				if(!empty($buttonText)){
					
					$htmlButton = "<a class='button button-primary' href='{$buttonLink}' target='_blank'>{$buttonText}</a>";
				}
				
				$text = "<div class='doubly-notice-advanced-wrapper'>
					<span class='doubly-notice-advanced__item-logo'>
						<img class='doubly-image-logo-ue' width=\"40\" src='$urlLogo'>
					</span>
					<span class='doubly-notice-advanced__item-text'>".$text.$htmlButton."</span>
				</div>";
				
			break;
			case self::TYPE_BANNER:
				
				$filename = UniteFunctionsDOUBLY::getVal($params, "banner");
				
				if(empty($filename))
					return(false);
				
				$urlBanner = GlobalsDOUBLY::$urlImages.$filename;
				
				$buttonLink = UniteFunctionsDOUBLY::getVal($params, "button_link");
				
				$text = "<a class='doubly-notice-banner-link' href='{$buttonLink}' target='_blank'>
					<img class='doubly-notice-banner' src='{$urlBanner}'>
				</a>";
				
			break;
			
		}
		
		$html = "<div class=\"$class $classDissmissable\"><p>";
			$html .= $text."\n";
			$html .= $htmlDissmiss;
		$html .= "</p></div>";
		

		return($html);
	}
	
	
	
	/**
	 * put admin notices
	 */
	public function putAdminNotices(){
		
		if(empty(self::$arrNotices))
			return(false);
		
		foreach(self::$arrNotices as $notice){
			
			$text = UniteFunctionsDOUBLY::getVal($notice, "text");
			$id = UniteFunctionsDOUBLY::getVal($notice, "id");
			
			//$isDissmissed = $this->isNoticeDissmissed($id);
			//if($isDissmissed == true)
				//continue;
			
			$htmlNotices = $this->getNoticeHtml($text, $id, false, $notice);			
			echo $htmlNotices;
		}
		
	}
	
	/**
	 * put styles
	 */
	public function putAdminStyles(){
		
		?>
		<!--  unlimited elements notices styles -->
		<style type="text/css">
			
			.doubly-admin-notice{
				position:relative;
			}
			
			.doubly-admin-notice.doubly-notice-advanced{
				font-size:16px;
			}
			
			.doubly-admin-notice--nowrap{
				padding:0px !important;
				border:none !important;
				background-color:transparent !important;
			}
			
			.doubly-admin-notice .doubly-notice-advanced-wrapper span{
				display:table-cell;
				vertical-align:middle;
			}
			
			.doubly-admin-notice .doubly-notice-advanced-wrapper .button{
				vertical-align:middle;
				margin-left:10px;
			}
			
			.doubly-admin-notice .doubly-notice-advanced__item-logo{
				padding-right:15px;
			}
			
			.doubly-admin-notice .doubly-notice-dismiss{
				position: absolute;
				top: 0px;
				right: 10px;
				padding: 10px 15px 10px 21px;
				font-size: 13px;
				text-decoration: none;			
			}
			
			.doubly-admin-notice .doubly-notice-dismiss::before{
				position: absolute;
				top: 10px;
				left: 0px;
				transition: all .1s ease-in-out;
				
				background: none;
				color: #72777c;
				content: "\f153";
				display: block;
				font: normal 16px/20px dashicons;
				speak: none;
				height: 20px;
				text-align: center;
				width: 20px;
			}
			
			.doubly-admin-notice .doubly-notice-dismiss:focus::before,
			.doubly-admin-notice .doubly-notice-dismiss:hover::before{
				color: #c00;			
			}
			
			.doubly-notice-banner-link{
				display:block;
			}
			.doubly-notice-banner{
				width:100%;
			}
 			
 			.doubly-notice-dismiss-banner{
 				background-color:#000000;
 				position:absolute;
 				top:20px;
 				right:23px;
 				height:20px;
 				width:20px;
 				border-radius:20px;
 				font-size:12px;
 				text-decoration:none;
 				color:#ffffff;
 				text-align:center;
 			}
 			
 			.doubly-notice-dismiss-banner:hover{
 				color:#ffffff;
				background-color:#c00;
 			}
 			
 			.doubly-notice-dismiss-banner:focus,
 			.doubly-notice-dismiss-banner:visited{
 				color:#ffffff; 				
 			}
 			
		</style>
		<?php 
	}
	
	/**
	 * check if some notice dissmissed
	 */
	private function isNoticeDissmissed($key){

		$userID = get_current_user_id();
		if(empty($userID))
			return(false);

		$value = get_user_meta($userID, "doubly_notice_dissmissed_".$key, true);

		$value = UniteFunctionsDOUBLY::strToBool($value);
		
		return($value);
	}


	/**
	* check dissmiss action
	*/
	public function checkDissmissAction(){
		
		$dissmissKey = UniteFunctionsDOUBLY::getPostGetVariable("doubly_dismiss_notice","", UniteFunctionsDOUBLY::SANITIZE_KEY);
		if(empty($dissmissKey))
			return(false);
		
		$userID = get_current_user_id();
		
		if(empty($userID))
			return(false);
		
		$metaKey = "doubly_notice_dissmissed_".$dissmissKey;
		
		delete_user_meta($userID, $metaKey);
		add_user_meta($userID, $metaKey, "true", true);		
	}
	

	/**
	 * init
	 */
	private function init(){
		
		if(self::$isInited == true)
			return(false);	
	
		if(GlobalsDOUBLY::$isAdmin == false)
			return(false);
				
		add_action("admin_init", array($this, "checkDissmissAction"));
		
		add_action("admin_notices", array($this, "putAdminNotices"),10,3);
		
		add_action("admin_print_styles", array($this, "putAdminStyles"));
		
		self::$isInited = true;
	}
	
}
