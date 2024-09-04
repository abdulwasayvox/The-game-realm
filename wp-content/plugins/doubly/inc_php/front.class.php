<?php
/**
 * @package Doubly
 * @author Unlimited Elements
 * @copyright (C) 2022 Unlimited Elements, All Rights Reserved. 
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 **/

if(!defined("DOUBLY_INC")) die("restricted access");

class Doubly_PluginFront extends Doubly_PluginCommon{
	
	
	/**
	 * call init
	 */
	public function __construct(){
		
		$this->init();
	}
	
	
	/**
	 * get current page info
	 */
	private function getPageInfo(){

		$postType = "";
		
		$obj = get_queried_object();
		
		$arrObj = array();
		if(!empty($obj))
		$arrObj = (array)$obj;
		
		$postType = UniteFunctionsDOUBLY::getVal($arrObj, "post_type");
		
		$arrInfo = array();
		$arrInfo["is_singular"] = is_singular();
		$arrInfo["post_type"] = $postType;
		$arrInfo["obj"] = $obj;
		
		return($arrInfo);
	}
	
	
	/**
	 * check if copy permitted to the page
	 */
	protected function isCopyPermittedForCurrentPost(){
		
		if($this->isOperationPermittedCache !== null)
			return($this->isOperationPermittedCache);
		
		$this->isOperationPermittedCache = false;
		
	    $arrPageInfo = $this->getPageInfo();
	   		    
	    $isSinglular = UniteFunctionsDOUBLY::getVal($arrPageInfo, "is_singular");
	    $postType = UniteFunctionsDOUBLY::getVal($arrPageInfo, "post_type");
	    $obj = UniteFunctionsDOUBLY::getVal($arrPageInfo, "obj");
	    
	    $isAllowed = true;
	    
	    if($isSinglular == false){
	    	$isAllowed = false;
	    }
	    
		$isAllowedForOperations = HelperDOUBLY::isPostTypeAllowedForOperations($postType, $this->specialExportType);
	    
		if($isAllowedForOperations == false)
			$isAllowed = false;
	    
	    //check if add to some post types
	    
	    if($isAllowed == false){
	    	$this->isOperationPermittedCache = false;
	    	GlobalsDOUBLY::$enableCopy = false;
	    	GlobalsDOUBLY::$enableFrontCopy = false;
	    	
	    	return(false);
	    }
	    	
		$this->post = $obj;	    
	    $this->postID = $obj->ID;
		$this->postTypeName = UniteFunctionsWPDOUBLY::getPostTypeName($this->post->post_type);
		$this->postType = $this->post->post_type;
	    
		
	    $this->isOperationPermittedCache = true;
	    
		return(true);
	}
	
	
	
	
	/**
	 * add admin bar buttons
	 */
	public function addAdminBarButtons(WP_Admin_Bar $admin_bar){
		
	    $this->isCopyPermittedForCurrentPost();
		
	    //add copy item
	    
	    if(GlobalsDOUBLY::$enableCopy){
		    $arrMeta = array();
		    $arrMeta["title"] = __( 'Copy this content for paste to another site', 'doubly' );
		    
		    $arrMenu = array();
		    $arrMenu["title"] = __('Copy',"doubly");
		    $arrMenu["id"] = __('doubly_copy',"doubly");
		    $arrMenu["href"] = 'javascript:void(0)';
		    $arrMenu["meta"] = $arrMeta;
		    
		    $admin_bar->add_menu($arrMenu);
	    }
	    	
	    
	    //----- paste
	    if(GlobalsDOUBLY::$enablePaste){
	    
		    $arrMenu = array();
		    $arrMenu["title"] = __('Paste',"doubly");
		    $arrMenu["id"] = __('doubly_paste',"doubly");
		    $arrMenu["href"] = 'javascript:void(0)';
		    
		    $admin_bar->add_menu($arrMenu);
		    
	    }
		
	    if(GlobalsDOUBLY::$showDebugMenu)
	    	$this->addAdminMenuBar_debug($admin_bar);
	    
	}
	

	/**
	 * put elementor section overlay html
	 */
	protected function putHTML_ElementorFrontCopySectionOverlay(){
		
		if(defined("DOUBLY_HIDE_FRONT_BUTTONS"))
			return(false);
		
		$showState = false;

		$options = HelperDOUBLY::getGeneralSettings();
		
		$link = UniteFunctionsDOUBLY::getVal($options, "front_copy_link");
		
		
		?>
		  <div id="doubly_copy_section_front_overlay_template" class="doubly-front-copy-section-button-overlay" style="display:none">
		  		
		  		<?php if(!empty($showState)):?>
		  		<div class="<?php echo esc_attr($showState)?>">
		  		<?php endif?>
		  		
				<a href="javascript:void(0)" class="doubly-button-copy-section-front" >
					
					<span class="doubly-button-copy-section-front__icon">
						<img class="doubly-button-copy__icon-regular" src="<?php echo GlobalsDOUBLY::$urlImages."front-button-icon.svg"?>" width="22px" >
						<img class="doubly-button-copy__icon-info" src="<?php echo GlobalsDOUBLY::$urlImages."icon-info.svg"?>" width="18px" >
						<img class="doubly-button-copy__icon-loading" src="<?php echo GlobalsDOUBLY::$urlImages."icon-loading.svg"?>" width="22px" style="display:none">
						<img class="doubly-button-copy__icon-success" src="<?php echo GlobalsDOUBLY::$urlImages."icon-check.svg"?>" xwidth="22px" style="display:none">
					</span>
					
					<span class="doubly-button-copy-section-front__text">
						<span class="doubly-button-copy__regular"><?php _e("Copy","doubly")?></span>
						<span class="doubly-button-copy__loading" style="display:none"><?php _e("Copying...","doubly")?></span>
						<span class="doubly-button-copy__success" style="display:none"><?php _e("Copied","doubly")?></span>
						<span class="doubly-button-copy__ask" style="display:none"><?php _e("Press for Copy To Clipboard","doubly")?></span>
					</span>
					
				</a>
				
				<div class="doubly-button-copy-section-info" style="display:none">
					
		  		<?php _e("Click to Copy the Section and paste to your site using ","doubly")?>
		  			
		  			<br>
		  			
			  		<a href="https://wordpress.org/plugins/doubly/" target="_blank" title="<?php _e("Download the free plugin from wordpress.org","doubly")?>" class="doubly-button-copy-section-info__plugin-link">
			  			<?php _e("Doubly Plugin","doubly")?></a>
					&nbsp;
					<a href="<?php echo $link?>" target="_blank" class="doubly-button-copy-section-info__link"><?php _e("Learn More","doubly")?></a>
				</div>
								
				
		  		<?php if(!empty($showState)):?>
		  		</div> 
		  		<?php endif?>
		  		
		  </div>
		<?php 
		
	}
	
	
	/**
	 * put elementor section overlay html
	 */
	protected function putHTML_ElementorSectionOverlay(){
		
		$copySectionTooltipText = __("Copy Elementor Section","doubly");
		
		$exportSectionText = __("Export Elementor Section","doubly");
		$exportSectionText = esc_attr($exportSectionText);
		
		
		?>
		
		  <div id="doubly_copy_section_overlay_template" class="doubly-front-copy-section-overlay" style="display:none">
		  				  		
		  		<a href="javascript:void(0)" class="doubly-button-copy-section" title="<?php echo esc_attr($copySectionTooltipText)?>">
					
					<svg class="doubly-button-copy-section__loading" style="width:30px;" version="1.1" id="L5" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"  viewBox="0 0 100 100" enable-background="new 0 0 0 0" xml:space="preserve">  <circle fill="#fff" stroke="none" cx="6" cy="50" r="6">    <animateTransform        attributeName="transform"        dur="1s"        type="translate"        values="0 15 ; 0 -15; 0 15"        repeatCount="indefinite"        begin="0.1"/>  </circle>  <circle fill="#fff" stroke="none" cx="30" cy="50" r="6">    <animateTransform        attributeName="transform"        dur="1s"        type="translate"        values="0 10 ; 0 -10; 0 10"        repeatCount="indefinite"        begin="0.2"/>  </circle>  <circle fill="#fff" stroke="none" cx="54" cy="50" r="6">    <animateTransform        attributeName="transform"        dur="1s"        type="translate"        values="0 5 ; 0 -5; 0 5"        repeatCount="indefinite"        begin="0.3"/>  </circle></svg>
					
					<svg width="16px" height="16px" viewBox="0 0 16 16" version="1.1" class="doubly-button-copy-section__regular">
					    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
					        <g transform="translate(-1223.000000, -181.000000)" fill="#FFFFFF">
					            <g transform="translate(1231.000000, 189.000000) scale(1, -1) translate(-1231.000000, -189.000000) translate(1223.000000, 181.000000)">
					                <path d="M11,12 L1,12 C0.447,12 0,11.553 0,11 L0,1 C0,0.448 0.447,0 1,0 L11,0 C11.553,0 12,0.448 12,1 L12,11 C12,11.553 11.553,12 11,12"></path>
					                <path d="M15,16 L4,16 L4,14 L14,14 L14,4 L16,4 L16,15 C16,15.553 15.553,16 15,16"></path>
					            </g>
					        </g>
					    </g>
					</svg>
					
					<span class="doubly-button-copy-section__regular">
						<?php _e("Copy","doubly")?>
					</span>
					
					<span class="doubly-button-copy-section__loading">
						<?php _e("Copying...","doubly")?>
					</span>
					
					<span class="doubly-button-copy-section__success">
						<?php _e("Section Copied!","doubly")?>
					</span>
					
		  		</a>
		  		
		  		<a href="javascript:void(0)" class="doubly-button-export-section" title="<?php echo esc_attr($exportSectionText)?>">
		  		
					<svg width="16px" height="16px" viewBox="0 0 16 16" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
					    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
					        <g transform="translate(-1272.000000, -425.000000)" fill="#FFFFFF">
					            <g transform="translate(1272.000000, 425.000000)">
					                <path d="M8,12 C8.3,12 8.5,11.9 8.7,11.7 L14.4,6 L13,4.6 L9,8.6 L9,0 L7,0 L7,8.6 L3,4.6 L1.6,6 L7.3,11.7 C7.5,11.9 7.7,12 8,12" ></path>
					                <path d="M14,14 L2,14 L2,11 L0,11 L0,15 C0,15.6 0.4,16 1,16 L15,16 C15.6,16 16,15.6 16,15 L16,11 L14,11 L14,14 Z"></path>
					            </g>
					        </g>
					    </g>
					</svg>
		  			
		  		</a>
		  				  		
		  </div>
		  
		  <div id="doubly_paste_section_overlay_template" class="doubly-paste-section-overlay" data-position='before' style="display:none">
		  		
		  		<a href="javascript:void(0)" class="doubly-paste-section-overlay__button">
		  		
					<svg width="16px" height="16px" viewBox="0 0 16 16" version="1.1" class="doubly-button-paste-section__regular">
					    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
					        <g transform="translate(-1223.000000, -181.000000)" fill="#FFFFFF">
					            <g transform="translate(1231.000000, 189.000000) scale(1, -1) translate(-1231.000000, -189.000000) translate(1223.000000, 181.000000)">
					                <path d="M11,12 L1,12 C0.447,12 0,11.553 0,11 L0,1 C0,0.448 0.447,0 1,0 L11,0 C11.553,0 12,0.448 12,1 L12,11 C12,11.553 11.553,12 11,12"></path>
					                <path d="M15,16 L4,16 L4,14 L14,14 L14,4 L16,4 L16,15 C16,15.553 15.553,16 15,16"></path>
					            </g>
					        </g>
					    </g>
					</svg>
		  			
		  			<span class="doubly-button-paste-section__regular">
		  				
		  				<?php _e("Paste Section Here", "doubly")?>
		  				
		  				<span class="doubly-paste-section-overlay__text-footer" style="display:none">
		  					 - (Footer)
		  				</span>
		  				
		  				<span class="doubly-paste-section-overlay__text-header" style="display:none">
		  					 - (Header)
		  				</span>
		  				
		  			</span>
		  			
					<svg class="doubly-button-paste-section__loading" style="width:30px;" version="1.1" id="L5" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"  viewBox="0 0 100 100" enable-background="new 0 0 0 0" xml:space="preserve">  <circle fill="#fff" stroke="none" cx="6" cy="50" r="6">    <animateTransform        attributeName="transform"        dur="1s"        type="translate"        values="0 15 ; 0 -15; 0 15"        repeatCount="indefinite"        begin="0.1"/>  </circle>  <circle fill="#fff" stroke="none" cx="30" cy="50" r="6">    <animateTransform        attributeName="transform"        dur="1s"        type="translate"        values="0 10 ; 0 -10; 0 10"        repeatCount="indefinite"        begin="0.2"/>  </circle>  <circle fill="#fff" stroke="none" cx="54" cy="50" r="6">    <animateTransform        attributeName="transform"        dur="1s"        type="translate"        values="0 5 ; 0 -5; 0 5"        repeatCount="indefinite"        begin="0.3"/>  </circle></svg>
		  			
		  			<span class="doubly-button-paste-section__loading">
		  				<?php _e("Pasting Section...", "doubly")?>
		  			</span>
		  			
		  			
		  		</a>
		  		
		  </div>

		<?php 
				
	}

	
	/**
	 * init all front actions
	 */
	private function initAllAction(){

		
		//init admin bar menu actions - add the buttons menu there
		add_action( 'admin_bar_menu', array($this, "addAdminBarButtons"), 500 );
		
		add_action("wp_enqueue_scripts",array($this,"onIncludeFrontScripts"));
		
		add_action( 'wp_footer', array($this, 'addFooterScripts') );
		
	}
		
	
	/**
	 * on plugins loaded - init all other actions
	 */
	public function onWPInit(){
				
		parent::onWPInit();
		
		if(GlobalsDOUBLY::$enableCopy == false && GlobalsDOUBLY::$enablePaste == false && GlobalsDOUBLY::$enableFrontCopy == false)
			return(false);

		
		$this->initCommon();
		
		//if both are false, no operation is enabled as well
		if(GlobalsDOUBLY::$enableCopy == false && GlobalsDOUBLY::$enablePaste == false && GlobalsDOUBLY::$enableFrontCopy == false)
			return(false);
					
		$this->initAllAction();
				
	}
	
	
	/**
	 * init the class
	 */
	public function init(){

		if(function_exists("fs_is_plugin_page") == false)
			return(false);
		
		$this->isAdmin = false;
		
		add_action("plugins_loaded",array($this,"onWPInit"));
						
	}
	
}
