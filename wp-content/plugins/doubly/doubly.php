<?php
/*
Plugin Name: Doubly
Plugin URI: http://doubly.pro
Description: Copy, Paste, Export, Import pages and elementor sections between domains
Author: Unlimited Elements
Version: 1.0.44
*/

if(!defined("DOUBLY_INC"))
	define("DOUBLY_INC", true);

if(defined("DOUBLY_VERSION")){
	
	if(!defined("DOUBLY_BOTH_VERSIONS_INSTALLED"))
		define("DOUBLY_BOTH_VERSIONS_INSTALLED", true);

}else{
	
	define("DOUBLY_VERSION","1.0.44");
	
	if ( ! function_exists( 'doubly_freemius' ) ) {
	    // Create a helper function for easy SDK access.
	    function doubly_freemius() {
	        global $doubly_freemius;
	
	        if ( ! isset( $doubly_freemius ) ) {
	            // Include Freemius SDK.
	            require_once dirname(__FILE__) . '/vendor/freemius/start.php';
				
	            $doubly_freemius = fs_dynamic_init( array(
					'id'                  => '9579',
	                'slug'                => 'doubly',
	                'premium_slug'        => 'doubly-pro',
	                'type'                => 'plugin',
	                'public_key'          => 'pk_b682eb54fedb56bad60782fc78e2e',
	                'is_premium'          => false,
					'premium_suffix'      => '(Pro)',            
	                'is_premium_only'     => false,
	                'has_addons'          => false,
	                'has_paid_plans'      => true,
	                'menu'                => array(
	                    'slug'           => 'doubly',
	                    'support'        => false,
	                )
	            ));
	        }
	
	        return $doubly_freemius;
	    }
	
	    // Init Freemius.
	    doubly_freemius();
	    // Signal that SDK was initiated.
	    do_action( 'doubly_freemius_loaded' );
	}
	
	$mainFilepath = __FILE__;
	$currentFolder = dirname($mainFilepath);
	
	
	try{
		
		require_once $currentFolder.'/includes.php';
	
		if(GlobalsDOUBLY::$isAdmin == true)
			new Doubly_PluginAdmin();
		else
			new Doubly_PluginFront();
				
	}catch(Exception $e){
		
		$message = $e->getMessage();
		echo esc_html($message);
	}

	
}
