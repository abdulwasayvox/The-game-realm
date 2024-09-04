<?php
/**
 * @package Doubly
 * @author Unlimited Elements
 * @copyright (C) 2022 Unlimited Elements, All Rights Reserved. 
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 **/

if(!defined("DOUBLY_INC")) die("restricted access");

class Doubly_PluginCommon{
		
	protected $isAdmin;
	protected $isOperationPermittedCache = null;
	protected $post;
	protected $postID;
	protected $postTypeName;
	protected $postType;
	protected $specialExportType = null;
	
	
	/**
	 * init common for the admin and front
	 */
	protected function initCommon(){
				
	}
	
	
	/**
	 * add others menu item to menu bar
	 */
	protected function addAdminMenuBar_debug($admin_bar){
		
	    $post = get_post();
	    if($post == false)
	    	return(false);
		
	    $arrMenu = array();
	    $arrMenu["title"] = __('Others',"doubly");
	    $arrMenu["id"] = __('doubly_others',"doubly");
	    $arrMenu["href"] = 'javascript:void(0)';
	    
	    $admin_bar->add_menu($arrMenu);
	   	
	    //------ show post data
	   	
	    $postID = $post->ID;
	    
	    $urlShowData = HelperDOUBLY::getUrlAjax("show_post_data","postid=$postID");

	    $urlGetContent = HelperDOUBLY::getUrlAjax("get_copied_content","postid=$postID");
	    
	    $urlTestImport = HelperDOUBLY::getUrlAjax("import_content_test");
	    
	    
	    $arrMenu = array();
	    $arrMenu["title"] = __('Show Post Data',"doubly");
	    $arrMenu["id"] = __('doubly_show_data',"doubly");
	    $arrMenu["href"] = $urlShowData;
	    $arrMenu["parent"] = "doubly_others";
	    
	    $admin_bar->add_menu($arrMenu);		
		
	    
	    //other content
	    
	    $arrMenu = array();
	    $arrMenu["title"] = __('Get Content Link',"doubly");
	    $arrMenu["id"] = __('doubly_get_content',"doubly");
	    $arrMenu["href"] = $urlGetContent;
	    $arrMenu["parent"] = "doubly_others";
	    
	    $admin_bar->add_menu($arrMenu);		
	    
	    
	    //import content
	    
	    $arrMenu = array();
	    $arrMenu["title"] = __('Test Import Link',"doubly");
	    $arrMenu["id"] = __('doubly_get_import_content_test',"doubly");
	    $arrMenu["href"] = $urlTestImport;
	    $arrMenu["parent"] = "doubly_others";
	    
	    $admin_bar->add_menu($arrMenu);
	    
	}
	
	
	/**
	 * put html help button
	 */
	protected function putHTML_helpButton(){
		?>
	  		<a id="doubly_top_panel_button_help" class="doubly-front-top-panel__button_help" href="javascript:void(0)" style="display:none" >
	  		
				<svg width="24px" height="24px" viewBox="0 0 24 24" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
				    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
				        <g transform="translate(-1219.000000, -13.000000)">
				            <g transform="translate(1220.000000, 14.000000)">
				                <path d="M11,0 C17.075,0 22,4.925 22,11 C22,17.075 17.075,22 11,22 C4.925,22 0,17.075 0,11 C0,4.925 4.925,0 11,0 Z" stroke="inherit" stroke-width="2" stroke-linecap="square"></path>
				                <path d="M11,16 C11.553,16 12,16.447 12,17 C12,17.553 11.553,18 11,18 C10.448,18 10,17.553 10,17 C10,16.447 10.448,16 11,16" fill="inherit"></path>
				                <path d="M8.853,5.5601 C10.833,4.6851 13.395,4.7891 14.27,6.2051 C15.145,7.6211 14.541,9.2681 13.041,10.5391 C11.541,11.8101 11,12.5001 11,13.5001" stroke="inherit" stroke-width="2" stroke-linecap="square"></path>
				            </g>
				        </g>
				    </g>
				</svg>
	  			
			</a>
		<?php 
	}
	
	/**
	 * put html close button
	 */
	protected function putHTML_closeButton(){
		?>
	  		<a id="doubly_top_panel_button_close" class="doubly-front-top-panel__button_close" href="javascript:void(0)" >

				<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="16px" height="16px" viewBox="0 0 16 16"><g transform="translate(0, 0)">
					<path fill="#ffffff" d="M14.7,1.3c-0.4-0.4-1-0.4-1.4,0L8,6.6L2.7,1.3c-0.4-0.4-1-0.4-1.4,0s-0.4,1,0,1.4L6.6,8l-5.3,5.3
					c-0.4,0.4-0.4,1,0,1.4C1.5,14.9,1.7,15,2,15s0.5-0.1,0.7-0.3L8,9.4l5.3,5.3c0.2,0.2,0.5,0.3,0.7,0.3s0.5-0.1,0.7-0.3
					c0.4-0.4,0.4-1,0-1.4L9.4,8l5.3-5.3C15.1,2.3,15.1,1.7,14.7,1.3z"></path></g>
				</svg>
		  						  			
			</a>
		
		<?php 
	}
	
	
	/**
	 * put copy post button
	 */
	protected function putHtml_copyPostButton(){
		
		if(empty($this->postID))
			return(false);
		
		
		$copyButtonText = __("Copy ","doubly").$this->postTypeName;
		
		$exportButtonTitle = __("Export ","doubly").$this->postTypeName;
		$exportButtonTitle = esc_attr($exportButtonTitle);
		
		$postID = $this->postID;
		
		switch($this->specialExportType){
			case GlobalsDOUBLY::EXPORT_TYPE_SNIPPET:
				
				$urlExport = HelperDOUBLY::getUrlAjax("export_object","objtype=snippet&id=$postID");
				
			break;
			default:		//for posts
	    		
				$urlExport = HelperDOUBLY::getUrlAjax("export_post","postid=$postID");
				
			break;
		}
		
		
		
		?>
		  		<a id="doubly_top_panel_button_copy" class="doubly-front-top-panel__button_copy" href="javascript:void(0)">
					
					<svg class="doubly-button-copy__icon" width="16px" height="16px" viewBox="0 0 16 16" version="1.1">
					    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
					        <g transform="translate(-1223.000000, -181.000000)" fill="#FFFFFF">
					            <g transform="translate(1231.000000, 189.000000) scale(1, -1) translate(-1231.000000, -189.000000) translate(1223.000000, 181.000000)">
					                <path d="M11,12 L1,12 C0.447,12 0,11.553 0,11 L0,1 C0,0.448 0.447,0 1,0 L11,0 C11.553,0 12,0.448 12,1 L12,11 C12,11.553 11.553,12 11,12"></path>
					                <path d="M15,16 L4,16 L4,14 L14,14 L14,4 L16,4 L16,15 C16,15.553 15.553,16 15,16"></path>
					            </g>
					        </g>
					    </g>
					</svg>
					
		  			<span class="doubly-button-copy__regular">
		  				<?php echo esc_html($copyButtonText)?>
		  			</span>
		  			
					<svg class="doubly-button-copy__loading" style="width:30px;" version="1.1" id="L5" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"  viewBox="0 0 100 100" enable-background="new 0 0 0 0" xml:space="preserve">  <circle fill="#fff" stroke="none" cx="6" cy="50" r="6">    <animateTransform        attributeName="transform"        dur="1s"        type="translate"        values="0 15 ; 0 -15; 0 15"        repeatCount="indefinite"        begin="0.1"/>  </circle>  <circle fill="#fff" stroke="none" cx="30" cy="50" r="6">    <animateTransform        attributeName="transform"        dur="1s"        type="translate"        values="0 10 ; 0 -10; 0 10"        repeatCount="indefinite"        begin="0.2"/>  </circle>  <circle fill="#fff" stroke="none" cx="54" cy="50" r="6">    <animateTransform        attributeName="transform"        dur="1s"        type="translate"        values="0 5 ; 0 -5; 0 5"        repeatCount="indefinite"        begin="0.3"/>  </circle></svg>
		  			
		  			<span class="doubly-button-copy__loading" >
		  				<?php _e("Copying...","doubly") ?>
		  			</span>
		  			
		  			<span class="doubly-button-copy__ask" style="display:none">
		  				<?php _e("Press for Copy To Clipboard","doubly")?>
		  			</span>
		  			
				</a>
				
		  		<a id="doubly_top_panel_button_export" class="doubly-front-top-panel__button_export" title="<?php echo esc_attr($exportButtonTitle)?>" href="<?php echo esc_attr($urlExport) ?>" style="display:none">

					<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="16px" height="16px" viewBox="0 0 16 16">
						<g transform="translate(0, 0)">
						<path fill="#ffffff" d="M8,12c0.3,0,0.5-0.1,0.7-0.3L14.4,6L13,4.6l-4,4V0H7v8.6l-4-4L1.6,6l5.7,5.7C7.5,11.9,7.7,12,8,12z"></path>
						<path fill="#ffffff" d="M14,14H2v-3H0v4c0,0.6,0.4,1,1,1h14c0.6,0,1-0.4,1-1v-4h-2V14z"></path></g>
					</svg>
					
					<span class="doubly-front-top-panel__button_export-text"><?php _e("export","doubly")?></span>
					
		  		</a>
				
			
		<?php 
	}
	
	/**
	 * put middle paste panel
	 */
	protected function putHtml_middlePastePanel(){
		
		$importZipTooltip = __("Import exported content zip file","doubly");
		
		$pasteInputPlaceholder = __("Paste from the clipboard here...", "doubly");
		
		?>
		
		  	<div class="doubly-front-top-panel__middle-paste" style="display:none">
		  	
		  		<input id="doubly_top_panel_paste_input" type="text" class="doubly-front-top-panel__paste_input" placeholder="<?php echo esc_attr($pasteInputPlaceholder)?>" style="display:none">
		  		
				<form id="doubly_from_import" method="post" class="doubly-front-top-panel__form-import" enctype="multipart/form-data" >
				
				    <input type="file" id="doubly_top_panel_input_import" name="doubly_import_file" class="doubly-front-top-panel__input_import" />
					
			  		<a id="doubly_top_panel_button_import" class="doubly-front-top-panel__button_import" title="<?php echo esc_attr($importZipTooltip)?>" style="display:none">
			  			
						<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="16px" height="16px" viewBox="0 0 16 16">
							<g transform="translate(0, 0)">
							<path fill="#ffffff" d="M8,12c0.3,0,0.5-0.1,0.7-0.3L14.4,6L13,4.6l-4,4V0H7v8.6l-4-4L1.6,6l5.7,5.7C7.5,11.9,7.7,12,8,12z"></path>
							<path fill="#ffffff" d="M14,14H2v-3H0v4c0,0.6,0.4,1,1,1h14c0.6,0,1-0.4,1-1v-4h-2V14z"></path></g>
						</svg>
						
						<span class="doubly-front-top-panel__button_import-text"><?php _e("import zip","doubly")?></span>
			  			
			  		</a>
			  		
				</form>
		  				  	
		  	</div>
		
		<?php 
	}
	
	/**
	 * put html dialog
	 */
	protected function putHtml_dialog(){
		
		?>
		  
		  <div id="doubly_front_dialog" class="doubly-front-popup doubly-front-popup__dialog" style="display:none">
		  	  		  	  
		  	  <div class="doubly-front-popup__dialog-middle">
		  	  				  	  		
		  	  		<input id="doubly_dialog_import_radio_new" type="radio" name="doubly_radio_import_post" value="overwrite">
		  	  		<label for="doubly_dialog_import_radio_new">
		  	  			
		  	  			<?php _e("As New","doubly")?>
		  	  			
		  	  		</label>
		  	  		
		  	  		<input id="doubly_dialog_import_radio_overwrite" type="radio" name="doubly_radio_import_post" value="overwrite" checked>
		  	  		
		  	  		<label for="doubly_dialog_import_radio_overwrite">
		  	  			
		  	  			<?php _e("Overwrite Current","doubly")?>
		  	  					  	  			
		  	  		</label>
		  	  
		  	  </div>
		  	  
		  	  <a id="doubly_button_dialog_import" href="javascript:void(0)" class="doubly-front-popup__dialog-button-import" ><?php _e("Import ","doubly")?><?php echo esc_html($this->postTypeName)?></a>
		  	  
		  </div>
		  
		<?php 
		
	}
	
	/**
	 * put html popups
	 */
	protected function putHtml_popups(){
		
		?>
		  
		  <div id="doubly_front_loading" class="doubly-front-popup doubly-front-popup__loading" style="display:none">
		  	  
		  	  <div class="doubly-front-popup__icon">
				
					<svg xmlns:svg="http://www.w3.org/2000/svg" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.0" width="32px" height="32px" viewBox="0 0 128 128" xml:space="preserve"><rect x="0" y="0" width="100%" height="100%" fill="#000000" /><path fill="#ffffff" d="M64.4 16a49 49 0 0 0-50 48 51 51 0 0 0 50 52.2 53 53 0 0 0 54-52c-.7-48-45-55.7-45-55.7s45.3 3.8 49 55.6c.8 32-24.8 59.5-58 60.2-33 .8-61.4-25.7-62-60C1.3 29.8 28.8.6 64.3 0c0 0 8.5 0 8.7 8.4 0 8-8.6 7.6-8.6 7.6z"><animateTransform attributeName="transform" type="rotate" from="0 64 64" to="360 64 64" dur="1800ms" repeatCount="indefinite"></animateTransform></path></svg>
				
		  	  </div>
		  	  
		  	  <div class="doubly-front-popup__content">
					<?php _e("Copying...","doubly")?>
		  	  </div>
		  	  
		  </div>
		  
		  <div id="doubly_front_success" class="doubly-front-popup doubly-front-popup__success" style="display:none">
		  	  <div class="doubly-front-popup__icon">
				
				<svg width="22px" height="22px" viewBox="0 0 22 22" version="1.1" >
				    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
				        <g transform="translate(-785.000000, -584.000000)" stroke="#FFFFFF">
				            <g transform="translate(785.000000, 585.000000)">
				                <path d="M20.2002,7.8999 C20.4002,8.6999 20.5002,9.5999 20.5002,10.4999 C20.5002,15.9999 16.0002,20.4999 10.5002,20.4999 C5.0002,20.4999 0.5002,15.9999 0.5002,10.4999 C0.5002,4.9999 5.0002,0.4999 10.5002,0.4999 C12.4002,0.4999 14.2002,0.9999 15.8002,1.9999" id="Stroke-1"></path>
				                <polyline stroke-linecap="square" points="6.5 8.5 10.5 12.5 21.5 1.5"></polyline>
				            </g>
				        </g>
				    </g>
				</svg>

		  	  </div>
		  	  <div class="doubly-front-popup__content">
		  	  	<?php _e(" Copied Successfully", "doubly")?><?php echo esc_html($this->postTypeName)?>
		  	  </div>
		  </div>
		  
		  <div id="doubly_front_error" class="doubly-front-popup doubly-front-popup__error" style="display:none">
		  	  
		  	  <div class="doubly-front-popup__icon">
				
				  <svg width="23px" height="23px" viewBox="0 0 23 23" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
				    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
				        <g transform="translate(-784.000000, -742.000000)">
				            <g transform="translate(784.000000, 742.000000)">
				                <path d="M11.5,0.5 C17.575,0.5 22.5,5.425 22.5,11.5 C22.5,17.575 17.575,22.5 11.5,22.5 C5.425,22.5 0.5,17.575 0.5,11.5 C0.5,5.425 5.425,0.5 11.5,0.5 Z" stroke="#FFFFFF" stroke-linecap="square"></path>
				                <line x1="11.5" y1="6.5" x2="11.5" y2="12.5" id="Stroke-3" stroke="#FFFFFF" stroke-linecap="square"></line>
				                <path d="M11.5,15.5 C12.053,15.5 12.5,15.947 12.5,16.5 C12.5,17.053 12.053,17.5 11.5,17.5 C10.948,17.5 10.5,17.053 10.5,16.5 C10.5,15.947 10.948,15.5 11.5,15.5" fill="#FFFFFF"></path>
				                <path d="M11.5,15.5 C12.053,15.5 12.5,15.947 12.5,16.5 C12.5,17.053 12.053,17.5 11.5,17.5 C10.948,17.5 10.5,17.053 10.5,16.5 C10.5,15.947 10.948,15.5 11.5,15.5 Z" stroke="#FFFFFF"></path>
				            </g>
				        </g>
				    </g>
				   </svg>
				
		  	  </div>
		  	  
		  	  <div class="doubly-front-popup__content">
		  	  	<?php _e("Error Occured", "doubly")?>
		  	  </div>
		  	  
		  </div>
		  
		  <div id="doubly_front_pastesection" class="doubly-front-popup doubly-front-popup__pastesection" style="display:none">
		  	  
		  	  <?php _e("Select Section Position To Paste","doubly")?>
		  		
		  		<br>
		  		
		  		<a href="javascript:void(0)" class="doubly-button-stop-copy doubly-button-action__close-state"><?php _e("End Paste Setion Mode","doubly")?></a>
		  				  		
		  </div>
		
		<?php 
		
	}
	
	/**
	 * put floating buttons
	 */
	private function putHTML_floatingButtons(){
		
		$this->isCopyPermittedForCurrentPost();
		
		if(GlobalsDOUBLY::$enableCopy == false && GlobalsDOUBLY::$enablePaste == false)
			return(false);
		
		$classAdd = "";
		
		$isAdminBarShowing = is_admin_bar_showing();
		
		if($isAdminBarShowing == true)
			$classAdd = "position-under-bar";
		
		if(GlobalsDOUBLY::$isAdmin == true)
			$classAdd .= " position-center";
		
		$enableCopy = GlobalsDOUBLY::$enableCopy;
		
		if(GlobalsDOUBLY::$isAdmin == true && empty($this->postID))
			$enableCopy = false;
			
		?>
		
		<div id="doubly_floating_buttons_wrapper" class="doubly-floating-buttons-wrapper <?php echo $classAdd?>">
			
			<?php if($enableCopy == true):?>
			<a href="javascript:void(0)" class="doubly-floating-button doubly-floating-button__copy">
				<?php _e("Copy","doubly")?>
			</a>
			<?php endif?>
			
			<div class="doubly-floating-button__logo">
				<img class="doubly-floating-button__logo-icon" src="<?php echo GlobalsDOUBLY::$urlImages."front-button-icon.svg"?>" width="22px" >
			</div>
						
			<?php if(GlobalsDOUBLY::$enablePaste == true):?>
				<a href="javascript:void(0)" class="doubly-floating-button doubly-floating-button__paste">
					<?php _e("Paste","doubly")?>
				</a>
			<?php endif?>
			
		</div>
		
		<?php 		
	}
	
	
	/**
	 * get copy instructions
	 */
	private function getTextCopyInstructions(){
		
		if(GlobalsDOUBLY::$enableCopy == false){
			$text = "no instructions";			//will never appear
			return($text);
		}
		
		$text = __("Copy / Export ","doubly").$this->postTypeName;
		
		return($text);
	}
	
	
	/**
	 * get the texts
	 */
	protected function getArrText(){
		
		$textCopyInstructions = $this->getTextCopyInstructions();
		
		$arrTexts = array();
		$arrTexts["copying"] = __("Copying...","doubly");
		$arrTexts["instructions_copy"] = __("Copy / Export ","doubly").$this->postTypeName;
		$arrTexts["instructions_copy_elementor_section"] = __(" or Elementor Section","doubly");
		$arrTexts["instructions_paste"] = __("Paste the copied clipboard text, or import zip","doubly");
		$arrTexts["middle_text_paste"] = __("Choose a place for paste section","doubly");
		$arrTexts["middle_text_pasting_section"] = __("Pasting Elementor Section...","doubly");
		$arrTexts["middle_text_copying_section"] = __("Copying Elementor Section...","doubly");
		$arrTexts["middle_text_pasting_post"] = __("Pasting Content...","doubly");
		$arrTexts["loader_pasting_post_text"] = __("Importing Content... <br> Please don't close the browser","doubly");
		$arrTexts["middle_text_paste_dialog"] = __("Choose location for the pasted post","doubly");
		$arrTexts["copy_section_success_message"] = __("Section Copied to Clipboard","doubly");
		
		
		return($arrTexts);
	}
	
	/**
	 * function for override
	 */
	protected function isCopyPermittedForCurrentPost(){}
	
	
	/**
	 * get options array
	 */
	private function getArrOptions(){
		
		$nonce = HelperDOUBLY::getNonce();
		
		$arrOptions = array();
		$arrOptions["nonce"] = $nonce;
		$arrOptions["ajaxurl"] = GlobalsDOUBLY::$urlAjax;
		$arrOptions["isadmin"] = $this->isAdmin;
		
		if(!empty($this->postID))
			$arrOptions["postid"] = $this->postID;
		
		if(!empty($this->postType))
			$arrOptions["posttype"] = $this->postType;
		
		if(!empty($this->specialExportType))
			$arrOptions["copymode"] = $this->specialExportType;
		
			
		return($arrOptions);
	}
	
	
	/**
	 * print some debug window
	 */
	public function printDebug($str){
		
		?>
		
		<style>
			.doubly-debug-popup{
				position:fixed;
				min-height:50px;
				width:300px;
				background-color:lightgray;
				border:1px solid gray;
				padding:10px;
				direction:ltr;
				top:50px;
				right:10px;
				z-index:99999;
			}
		</style>
		
		<div class="doubly-debug-popup">
			<?php dmp($str)?>
		</div>
		
		<?php 
		
	}
	
	/**
	 * action on include front scripts
	 */
	public function onIncludeFrontScripts(){

		$this->isCopyPermittedForCurrentPost();
		
		if(GlobalsDOUBLY::$enableFrontCopy == true)
			GlobalsDOUBLY::$enableFrontCopy = $this->isFrontCopyPermitted();
		
		if(GlobalsDOUBLY::$enableCopy == false && GlobalsDOUBLY::$enablePaste == false && GlobalsDOUBLY::$enableFrontCopy == false)
			return(false);
		
		//add the jquery script if not included
		wp_enqueue_script("jquery");
		
		HelperDOUBLY::addScript("doubly_front");
		HelperDOUBLY::addStyle("doubly_front");
		
	}
	
	/**
	 * check if front copy permitted
	 */
	private function isFrontCopyPermitted(){
		
		$isHome = is_home();
				
		if($isHome == true)
			return(false);
			
		$isFront = is_front_page();
		
		if($isFront == true)
			return(false);
		
		$isArchive = is_archive();
		
		if($isArchive == true)
			return(false);
			
		$post = get_post();
		
		$isPermited = HelperDOUBLY::isFrontCopyPermittedForPost($post);
		
		return($isPermited);
	}
	
	
	
	/**
	 * decide if to show floating buttons
	 */
	private function isShowFloatingButtons(){
						
		if(GlobalsDOUBLY::$isWordpressCom == true)
			return(true);

		if(GlobalsDOUBLY::$isAdmin == true)
			return(false);
		
		$isAdminBarShowing = is_admin_bar_showing();
		
		if($isAdminBarShowing == false)
			return(true);
		
			
		return(false);
	}
	
	
	/**
	 * add footer scripts
	 */
	public function addFooterScripts(){
		
		$isElementorEditMode = HelperDOUBLY::isElementorEditMode();
				
		if($isElementorEditMode == true)
			return(false);
		
		//set permissions
		
		if(GlobalsDOUBLY::$enableFrontCopy == true)
			GlobalsDOUBLY::$enableFrontCopy = $this->isFrontCopyPermitted();
		
		$this->isCopyPermittedForCurrentPost();
		
		
		if(GlobalsDOUBLY::$enableCopy == false && GlobalsDOUBLY::$enablePaste == false && GlobalsDOUBLY::$enableFrontCopy == false)
			return(false);
		
			
		if(empty($this->postTypeName))
			$this->postTypeName = "Post";
			
		$arrOptions = $this->getArrOptions();
				
		$strFrontOptions = UniteFunctionsDOUBLY::jsonEncodeForClientSide($arrOptions);
		
		$arrTexts = $this->getArrText();
				
		$strTexts = UniteFunctionsDOUBLY::jsonEncodeForClientSide($arrTexts);

		$showFloating = $this->isShowFloatingButtons();
		
		
		  ?>

		  <img src="<?php echo GlobalsDOUBLY::$urlImages?>toolbar-icon-paste-hover.svg" style="display:none">
		  <img src="<?php echo GlobalsDOUBLY::$urlImages?>toolbar-icon-copy-hover.svg" style="display:none">
		  
		  <div id="doubly_front_debug" class="doubly-front-debug" style="display:none"></div>
		  
		  <?php if(GlobalsDOUBLY::DEBUG_SHOW_INPUT == true):?>
		  
			  <div class="doubly-front-input-wrapper doubly-front-input--visible" >
			  		<input id="doubly_copy_input" type="text" value="">
			  </div>
			  
		  <?php else:?>
		  
			  <div class="doubly-front-input-wrapper" style="opacity:0">
			  		<input id="doubly_copy_input" type="text" value="">
			  </div>
		  
		  <?php endif?>
		  
		  <div id="doubly_fron_top_panel" class="doubly-front-top-panel" style="display:none">
		  	
		  	<div class="doubly-front-top-panel__left">
		  	
		  		<a href="<?php echo GlobalsDOUBLY::URL_WEBSITE?>" target="_blank" class="doubly-front-top-panel__logo">
		  			<img src="<?php echo GlobalsDOUBLY::$urlImages."logo.svg"?>" class="doubly-front-top-panel__logo-image">
		  			<img src="<?php echo GlobalsDOUBLY::$urlImages."logo-hover.svg"?>" class="doubly-front-top-panel__logo-image-hover" style="display:none">
		  		</a>
			  	
			  	<div id="doubly_top_panel_text" class="doubly-front-top-panel--text" style="display:none">Top Panel Text</div>
		  		
		  	</div>
			
		    <div id="doubly_top_panel_middle_text" class="doubly-front-top-panel--middle-text" style="display:none">
		  			Middle Text
		    </div>
		  	
		  	<?php $this->putHtml_middlePastePanel() ?>
		  	
		  	<div class="doubly-front-top-panel__right">
		  		
		  		<?php $this->putHtml_copyPostButton() ?>
		  		
		  		<?php $this->putHTML_helpButton(); ?>
		  		
		  		<?php $this->putHTML_closeButton()?>
		  		
		  	</div>
		  	
		  </div>
		  
		  <div class="doubly-front-load-overlay" style="display:none"></div>
		  
		  <?php $this->putHtml_popups() ?>
		  
		  <?php $this->putHtml_dialog() ?>
		  
		  <?php 
		  	if($this->isAdmin == false){
		  		
		  		$this->putHTML_ElementorSectionOverlay();
		  		
		  		if(GlobalsDOUBLY::$enableFrontCopy == true)
		  			$this->putHTML_ElementorFrontCopySectionOverlay();
		  			
		  	}
		  	
	  		if($showFloating == true)
	  			$this->putHTML_floatingButtons();
		  	
		  ?>
		  
		  <script>
		  	var g_doublyTexts = <?php echo UniteFunctionsDOUBLY::escapeField($strTexts)?>;
		  	var g_strDOUBLYOptions = <?php echo UniteFunctionsDOUBLY::escapeField($strFrontOptions)?>;
		  </script>
		  <?php
	}
	
	/**
	 * on plugins loaded - run the global set action
	 */
	public function onWPInit(){
		
		GlobalsDOUBLY::onWPInit();
		
	}
	
	
}