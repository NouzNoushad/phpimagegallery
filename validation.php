<?php

class ImageValidation {

	private $data;
	private $errors = [];
	private static $fields = ['imageName', 'imageDescription'];

	public function __construct($post_data){
		$this->data = $post_data;
	}

	public function validation(){

		foreach(self::$fields as $field){
			echo $field;
			print_r($this->data);
			if(!array_key_exists($field, $this->data)){
				trigger_error("$field is not present in data");
				return;
			}
		}
		$this->validateImageName();
		$this->validateImageDescription();
		return $this->errors;
	}

	private function validateImageName(){
		$name = trim($this->data['imageName']);
		if(empty($name)){
			$this->Error('imageName', 'Please provide an image name');
		}
		else{
			if(!preg_match('/^([a-zA-Z0-9\s]*)$/', $name)){
				$this->Error('imageName', 'Image name do not allow special characters');
			}
		}
	}

	private function validateImageDescription(){
		$description = trim($this->data['imageDescription']);
		if(empty($description)){
			$this->Error('imageDescription', 'Please provide an image description');
		}
	}

	private function Error($key, $value){
		$this->errors[$key] = $value;
	}
}

?>