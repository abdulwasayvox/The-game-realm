<?php	
/**
 * @package Doubly
 * @author Unlimited Elements
 * @copyright (C) 2022 Unlimited Elements, All Rights Reserved. 
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 **/

if(!defined("DOUBLY_INC")) die("restricted access");

class Doubly_PluginViewWelcome{
	
	/**
	 * constructor
	 */
	public function __construct(){
		
		$this->putViewHtml();
		
	}
	
	
	
	/**
	 * put free version text
	 */
	private function putTextFreeVersion(){
		?>
		
			<h3>Upgrade to Doubly Pro</h3>
			
			<p class="doubly-welcome__column-text">	
				You are currently using the FREE version of Doubly, Upgrade to Doubly Pro, and unlock Copy Paste for WordPress Posts, WooCommerce Products, Media Files and more.
			</p>
			
			<p>
				<a href="https://doubly.pro/#pricing" target="_blank">Go Pro</a>
			</p>
			
		<?php 
	}
	
	
	
	
	/**
	 * put view html
	 */
	private function putViewHtml(){

		$title = __("Doubly","doubly");
		
		if(GlobalsDOUBLY::$isProVersion == true)
			$title = __("Doubly Pro","doubly");
		
		
		?>
		
		<div class="wrap doubly-view-welcome" id="doubly_welcome_page_wrapper">
	  
		    <div id="div_debug" class="unite-div-debug" style="display:none"></div>
			
			<div class="doubly-welcome__logo-wrapper">
				
				<?php if(GlobalsDOUBLY::$isProVersion == false):?>
			
					<img src="<?php echo GlobalsDOUBLY::$urlImages."doubly-logo-black.svg"?>" width="150px">
					
				<?php else:?>
					
					<img src="<?php echo GlobalsDOUBLY::$urlImages."doubly-pro-logo-black.svg"?>" width="200px">
					
				<?php endif?>
			</div>
			
			<?php if(GlobalsDOUBLY::$isProVersion == true && GlobalsDOUBLY::$isProActive == false):?>
				<p class="doubly-error-message"><?php _e("Doubly Pro is not active, please activate from plugins list view","doubly")?></p>
			<?php endif?>
			
			
			<div class="doubly-welcome__video-wrapper">
			
				<h3>Welcome to <?php echo $title?></h3>
				
				<?php if(GlobalsDOUBLY::$isProVersion == false):?>
					<p>Use Doubly Free to copy-paste pages and Elementor sections between websites.</p>
				
				<?php else:?>
					<p>Increase productivity and get the job done faster by easily moving content & designs between your WordPress websites in seconds.</p>
				
				<?php endif?>
				
				<br>
				<br>
				
				<iframe class="doubly-welcome__logo-video" width="940" height="550" src="https://www.youtube.com/embed/yTqWr6YNQhQ" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>			
			</div>
			
			
			<div class="doubly-welcome__text-wrapper">
				
				<?php if(GlobalsDOUBLY::$isProVersion == false):?>
					
				<div class="doubly-welcome__column doubly-welcome__column1">
				
						<?php $this->putTextFreeVersion()?>
				
				</div>
				
				<?php endif?>
				
				<div class="doubly-welcome__column doubly-welcome__column2">
				
					<h3>How to Use Doubly?</h3>
					
					<p class="doubly-welcome__column-text">	
						Just getting started? Don't worry we got you covered. Visit the documentation on our website and learn how you can take advantage of Doubly.						
					</p>
					
					<p>
						<a href="https://doubly.pro/docs/" target="_blank">View Docs</a>
					</p>
										
									
					
				</div>
				<div class="doubly-welcome__column doubly-welcome__column3">
					<h3>Need Help?</h3>
					
					<p class="doubly-welcome__column-text">	
						Each and every one of our customers receives personalized assistance from our dedicated support team.						
					</p>
					
					<p>
						<a href="https://unitecms.ticksy.com/" target="_blank">Get Help</a>
					</p>
				
				
				</div>
				
				<div class="doubly-welcome__column doubly-welcome__column4">
				
					<h3>Like Doubly?</h3>
					
					<p class="doubly-welcome__column-text">	
						If you like using Doubly please leave us a 5-star rating on our plugin page in the WordPress plugin directory. It will help us a lot!					
					</p>
				
					<p>
						<a href="https://wordpress.org/support/plugin/doubly/reviews/?filter=5" target="_blank">Rate Us</a>
					</p>
				
				</div>
							
				
			
			</div>
			
			
		</div>
		
		
		<?php 
		
	}
	
}


new Doubly_PluginViewWelcome();

