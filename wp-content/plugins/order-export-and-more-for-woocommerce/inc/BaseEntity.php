<?php
class BaseEntity{
	
	public $fields;			//The field list
	public $id;				//What entity is this
	public $enabled;		//is it enabled?
	public $fieldsToExport; //What fields does the user want to export
	public $filters;		//Filters for the query
	public $settings;		//The plugin settings for this user
	public $fieldSequence;	//what order are the fields in?

	/**
	 * outputs the HTML for the fields for this entity
	 */
	public function render_fields(){
	
	}
	
	
	/**
	 * gets the overrides from the settings and updates the fields array
	 */
	private function get_label_overrides(){
	
	}

	/**
	 * Generates the export sequence option if it doesn't exist
	 */
	public function generate_export_order_sequence(){

		error_log('order sequence');
		//build the option name - TODO need to refactor it is hardcoded everywhere
		$name = strtolower($this->id) . "_option";

		//does it exist?
		$fields = array();
		$i = 1;

		if(false == get_option($name)){
			//need to create it!

			//loop thru all the fields
			foreach($this->fields as $key => $val){
				$fields[$val['placeholder']] = $i;
				$i++;
			}

			//now save it
			update_option($name,  json_encode($fields) );
		}


	}

	/**
	 * Generates the HTML for the filter screen for this product. Gets overriden in the appropriate entity class
	 * 
	 */
	public function generate_filters(){
		
	}
	
	/**
	 * This actually runs the query!  Gets overriden in the appropriate entity class
	 */
	public function run_query($file){
		
	}
	
	
	/**
	 * Takes the form POST as input and gets the filter params from it
	 * They are entity specific 
	 * @param unknown $post
	 */
	public function extract_filters($post){
		
	}
	
	
	/**
	 * Creates the header row for the CSV file...
	 * This is common across all entities so is in the base class
	 */
	protected function create_header_row(){
		
		//lets get the options for these labels
		$labels = get_option( JEM_EXP_DOMAIN . '_' . $this->id . '_labels');
		
		$data = array();
		
		foreach($this->fieldsToExport as $key => $field){
			
			//do we have a custum label for this one?
			$val = ( isset($labels[ $key ] ) ) ? $labels[ $key ] : $this->fields[$key]['placeholder'];
			array_push($data, $val);	
			
				
		}
		
		return $data;
	}
	
}

?>