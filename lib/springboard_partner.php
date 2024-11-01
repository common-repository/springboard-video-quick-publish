<?php

class Springboard_Partner {
	var $id = null;
	var $website;
	
	public function toArray() {
		$properties = get_object_vars($this);
		$fArray = array();
		foreach($properties as $key=>$value){
			if(isset($value) && $value!=null && $value != '')
				$fArray[$key] = $value;
		}
		return $fArray;
	}
}

?>