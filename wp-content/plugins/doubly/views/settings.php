<?php	
/**
 * @package Doubly
 * @author Unlimited Elements
 * @copyright (C) 2022 Unlimited Elements, All Rights Reserved. 
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 **/

if(!defined("DOUBLY_INC")) die("restricted access");

class Doubly_PluginViewSettings{

	
	/**
	 * constructor
	 */
	public function __construct(){
		
		$this->putViewHtml();
		
	}

	/**
	 * draw save settings button
	 */
	protected function drawSaveSettingsButton(){
				
		$prefix="doubly_settings";
		
		$buttonText = esc_html__("Save Settings", "doubly");
		
		$addParams = "";		
		
		?>
			<div class="uc-button-action-wrapper">
			
				<a id="<?php echo esc_attr($prefix)?>_button_save_settings" data-prefix="<?php echo esc_attr($prefix)?>" <?php echo UniteFunctionsDOUBLY::escapeField($addParams)?> class="unite-button-primary doubly-button-save-settings" href="javascript:void(0)"><?php echo esc_html($buttonText)?></a>
				
				<div style="padding-top:6px;">
					
					<span id="<?php echo esc_attr($prefix)?>_loader_save" class="loader_text" style="display:none"><?php esc_html_e("Saving...", "doubly")?></span>
					<span id="<?php echo esc_attr($prefix)?>_message_saved" class="unite-color-green" style="display:none"></span>
					
				</div>
			</div>
			
			<div class="unite-clear"></div>
			
			<div id="<?php echo esc_attr($prefix)?>_save_settings_error" class="unite_error_message" style="display:none"></div>
			
		<?php 
	}
	
	
	/**
	 * put view html
	 */
	private function putViewHtml(){
		
		$nonce = HelperDOUBLY::getNonce();
		
		$settingsName = GlobalsDOUBLY::OPTIONS_GROUP_NAME;
				
		$settings = HelperDOUBLY::getGeneralSettingsObject();
		
		$formID = "doubly_general_settings";
		
		$output = new UniteSettingsOutputWideDOUBLY();
		$output->init($settings);
		$output->setFormID($formID);
		
		//$output->setShowSaps();
		
		$title = __("Doubly Settings","doubly");
				
		if(GlobalsDOUBLY::$isProVersion == true)
			$title = __("Doubly Pro Settings","doubly");
		
		
		?>
			<div class="wrap" id="uc_settings_page_wrapper">
		  
		    <div id="div_debug" class="unite-div-debug" style="display:none"></div>
			
			<h1><?php echo esc_html($title)?></h1>		
				
				<br><br>
				
				<?php if(GlobalsDOUBLY::$isProVersion == true && GlobalsDOUBLY::$isProActive == false):?>
				<p class="doubly-error-message"><?php _e("Doubly Pro is not active, please activate from plugins list view","doubly")?></p>
				<?php endif?>
				
				<div class="doubly-main-settings-wrapper">
					<?php 
					$output->draw("doubly_main_settings",true);
					?>
				</div>				
				
				<br>
				<?php
				$this->drawSaveSettingsButton();
				?>
				
			</div>
<script>

var g_doublyNonce = "<?php echo UniteFunctionsDOUBLY::escapeField($nonce) ?>";
var g_urlAjaxActionsDOUBLY = "<?php echo GlobalsDOUBLY::$urlAjax?>";

jQuery(document).ready(function(){
	
	var objSettingsView = new DOUBLY_SettingsView();
	objSettingsView.init();
	
});

</script>

<?php 
	}
	
}

new Doubly_PluginViewSettings();