<?php
/**
 * @package Doubly
 * @author Unlimited Elements
 * @copyright (C) 2022 Unlimited Elements, All Rights Reserved. 
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 **/

if(!defined("DOUBLY_INC")) die("restricted access");

class Doubly_Integrations{
	
	private $lastImportedID;
	private $lastNumImported;
	private $lastImportedUrl;
	
	
	private function _____GENERAL______(){}
	
	
	/**
	 * get last imported id
	 */
	public function getLastImportedID(){
		
		return($this->lastImportedID);
	}
	
	/**
	 * get last number imported objects
	 */
	public function getLastNumImported(){
		
		return($this->lastNumImported);
	}
	
	
	/**
	 * get last number imported url
	 */
	public function getLastImportedUrl(){
		
		return($this->lastImportedUrl);
	}
	
	private function _____CODE_SNIPPETS_PLUGIN______(){}
	
	
	/**
	 * import snippet
	 */
	private function importSnippet($arrSnippetData, $importToId = null){
		
		$snippet = new Code_Snippets\Snippet( $arrSnippetData );
		
		if(!empty($importToId))
			$snippet->id = $importToId;
		
		global $wpdb;
		$table = Code_Snippets\code_snippets()->db->get_table_name( $snippet->network );
		
		// Update the last modification date if necessary.
		$snippet->update_modified();
		
		// Build array of data to insert.
		$data = array(
			'name'        => $snippet->name,
			'description' => $snippet->desc,
			'code'        => $snippet->code,
			'tags'        => $snippet->tags_list,
			'scope'       => $snippet->scope,
			'priority'    => $snippet->priority,
			'active'      => intval( $snippet->active ),
			'modified'    => $snippet->modified,
		);
	
		// Create a new snippet if the ID is not set.
		if ( 0 === $snippet->id ) {
			$wpdb->insert( $table, $data, '%s' ); // db call ok.
			$snippet->id = $wpdb->insert_id;
	
			do_action( 'code_snippets/create_snippet', $snippet->id, $table );
		} else {
	
			// Otherwise, update the snippet data.
			$wpdb->update( $table, $data, array( 'id' => $snippet->id ), null, array( '%d' ) ); // db call ok.
	
			do_action( 'code_snippets/update_snippet', $snippet->id, $table );
		}
		
		Code_Snippets\clean_snippets_cache( $table );
		
		return $snippet->id;
	}
	
	/**
	 * get url snippets
	 */
	private function getUrlSnippets(){
		
		$url = admin_url("admin.php?page=snippets");
		
		return($url);
	}
	
	/**
	 * get url snippet
	 */
	private function getUrlSnippet($id){
		
		$id = (int)$id;
		
		$url = admin_url("admin.php?page=edit-snippet&id=".$id);
		
		return($url);
	}
	
	
	/**
	 * import snippets
	 */
	public function importSnippets($arrSnippets, $importToId = null){
		
		if(empty($arrSnippets))
			return("no snippets found");
		
		if(is_array($arrSnippets) == false)
			UniteFunctionsDOUBLY::throwError("Wrong snippets import type");
		
		if(defined("CODE_SNIPPETS_FILE") == false)
			UniteFunctionsDOUBLY::throwError("Can't import, Code Snippets plugin don't installed.");
		
		//validate import to id mode
		
		if(!empty($importToId)){
			
			$importToId = (int)$importToId;
			
			$existingSnippet = $this->getSnippetById($importToId);
			
			if(empty($existingSnippet))
				UniteFunctionsDOUBLY::throwError("Existing snippet with id: $importToId not found");
				
			if(count($arrSnippets) > 1)
				UniteFunctionsDOUBLY::throwError("Should be only one snippet, not many");
			
		}
			
		$logText = "";
		
		foreach($arrSnippets as $snippetData){
			
			$id = $this->importSnippet($snippetData, $importToId);
			
			$this->lastImportedID = $id;
			
			$name = UniteFunctionsDOUBLY::getVal($snippetData, "name");
			
			$logText .= "imported snippet: ".$name."\n<br>";
			
		}
		
		$this->lastNumImported = count($arrSnippets);
		
		//set last url
		if($this->lastNumImported == 1)
			$this->lastImportedUrl = $this->getUrlSnippet($this->lastImportedID);
		else
			$this->lastImportedUrl = $this->getUrlSnippets();
		
		
		return($logText);
	}
	
	/**
	 * get snippets by id
	 */
	private function getSnippetsByIDs($arrIDs){

		$tableName = Code_Snippets\code_snippets()->db->get_table_name();
		
		$db = HelperDOUBLY::getDB();
		
		$arrIDs = implode(",", $arrIDs);

		$sql = "select * from $tableName where id in($arrIDs)";
		
		$arrSnippetsData = $db->fetchSql($sql);
		
		if(empty($arrSnippetsData))
			return(array());
			
		$arrSnippets = array();
		
		foreach($arrSnippetsData as $snippetData){
			
			$snippet = new Code_Snippets\Snippet($snippetData);
			
			$arrSnippets[] = $snippet;
		}
			
		
		return($arrSnippets);
	}
	
	/**
	 * get code snippet by id
	 */
	public function getSnippetById($id){
		
		UniteFunctionsDOUBLY::validateNumeric($id,"snippet id");
		
		$arrIDs = array($id);
		
		$arrSnippets = $this->getSnippetsByIDs($arrIDs);
		
		if(empty($arrSnippets))
			return(null);
			
		$snippet = $arrSnippets[0];
		
		return($snippet);
	}
	
	
	/**
	 * get snippets plugin export data
	 */
	public function getSnippetsExportData($id){
		
		if(defined("CODE_SNIPPETS_FILE") == false)
			UniteFunctionsDOUBLY::throwError("Can't export, Code Snippets plugin don't installed.");
					
		//get arrIDs
		
		if(is_array($id))
			$arrIDs = $id;
		else 
			if(is_numeric($id) == true)
				$arrIDs = array($id);
		else{
			
			$isListIDs = UniteFunctionsDOUBLY::isIDsListString($id);
			
			if($isListIDs == false)
				UniteFunctionsDOUBLY::throwError("Snippet id wrong format");
			
			$arrIDs = explode(",", $id);
		}
		
		UniteFunctionsDOUBLY::validateNotEmpty($arrIDs, "arr id's");
		
		$arrSnippets = $this->getSnippetsByIDs($arrIDs);
		
		if(empty($arrSnippets))
			UniteFunctionsDOUBLY::throwError("No snippets fetched.");
		
		$snippets = array();
		
		foreach ( $arrSnippets as $snippet ) {
			$fields = array( 'name', 'desc', 'tags', 'scope', 'code', 'priority' );
			$final_snippet = array();

			foreach ( $fields as $field ) {
				if ( ! empty( $snippet->$field ) ) {
					$final_snippet[ $field ] = str_replace( "\r\n", "\n", $snippet->$field );
				}
			}
			
			if ( $final_snippet ) {
				$snippets[] = $final_snippet;
			}
		}
		
		
		return($snippets);
	}
	
	
	/**
	 * get exported filename from exported snippets
	 */
	public function snippets_getExportFilename($arrSnippets){
		
		if(empty($arrSnippets))
			UniteFunctionsDOUBLY::throwError("No snippets found");
		
		$num = count($arrSnippets);
			
		if($num > 1){
			$name = "{$num}_snippets";
			
			return($name);
		}
		
		$arrSnippet = $arrSnippets[0];
		
		$name = UniteFunctionsDOUBLY::getVal($arrSnippet,"name");
		
		if(empty($name))
			return("snippet");
			
		$name = UniteFunctionsDOUBLY::truncateString($name, 10, true, "");
		
		if(empty($name))
			return("snippet");
		
		$name = trim($name);
		
		$name = HelperDOUBLY::convertTitleToHandle($name);
		
		$name = str_replace("_", "", $name);
		
		if(empty($name))
			return("snippet");
		
		$name = "snippet_".$name;
		
		return($name);
	}
	
	
	
}