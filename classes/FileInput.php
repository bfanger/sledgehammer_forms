<?php
/**
 * Een component waarmee <input type='file'> tags kunt genereren en importeren
 * 
 * @todo Ondersteuning voor een multidimentionale identifier. name="foto[1]"
 * @package Forms
 */

class FileInput extends Input {

	function __construct($name, $parameters = array(), $Validator = NULL) {
		parent::__construct('file', $name, $parameters, $Validator);
	}

	function initial($value) {
		$this->parameters['value'] = str_replace('\"', '&quot;', $value);
	}

	function import(&$error_message, $source = array()) {
		if (count($_FILES) == 0) {
			if (!array_key_exists('_FILES', $source)) {
				return null; // Het formulier is nog niet gepost 
			}
			notice('$_FILES is empty, check for <form enctype="multipart/form-data">');
			return null;
		}
		if (!array_key_exists($this->parameters['name'], $_FILES)) {
			$error_message = 'Invalid name';
			return null;
		}
		$file = $_FILES[$this->parameters['name']];
		switch ($file['error']) {

			case UPLOAD_ERR_OK:
				unset($file['error']);
				return $file;

			case UPLOAD_ERR_NO_FILE:
				if ($this->Validator !== null) {
					$this->Validator->validate(null, $error_message);
				}
				break;
				
			case UPLOAD_ERR_INI_SIZE:
				$error_message = 'De grootte van het bestand is groter dan de in php.ini ingestelde waarde voor upload_max_filesize';
				break;

			case UPLOAD_ERR_FORM_SIZE:
				$error_message = 'De grootte van het bestand is groter dan de in html gegeven MAX_FILE_SIZE';
				break;

			case UPLOAD_ERR_PARTIAL:
				$error_message = "Het bestand is maar gedeeltelijk geupload";
				break;

			
				
			default:
				$error_message = 'Unknown errorcode: "'.$file['error'].'"';
		}
		if ($error_message) {
			$this->parameters['class'] = ' error';
		}
		return null; // Er is geen (volledig) bestand ge-upload
	}

	function render() {
		echo '<input type="hidden" name="_FILES" value="" />';
		parent::render();
	}
}
?>
