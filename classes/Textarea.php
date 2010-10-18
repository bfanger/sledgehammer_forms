<?php
/**
 * Een tekstvak <textarea>
 * @package Forms
 */

class Textarea extends Object implements Component, Import{

	public 
		$Validator,
		$parameters;

	private
		$value;

	function __construct($name = NULL, $parameters = array(), $Validator = NULL) {
		$this->parameters =	$parameters;
		if($name !== NULL) {
			$this->parameters['name'] = $name;
		}
		$this->Validator = $Validator;
	}

	function initial($value) {
		$this->value = $value;
	}

	function import(&$error_message, $source = array()) {
		if (!array_key_exists('name', $this->parameters)) {
			return NULL; // De naam is niet opgegeven. 
		}
		if (extract_element($source, $this->parameters['name'], $value)) {
			$value = str_replace("\r\n", "\n", $value);
			$this->value = $value;
			if ($this->Validator !== NULL) {
				if (!$this->Validator->validate($value, $error_message)) {
					$this->parameters['class'] = ' error';
				}
		 	}
			return $value;
		} else {
			$error_message = 'Import failed';
			return NULL;
		}
	}

	function render() {
		echo '<textarea'.implode_xml_parameters($this->parameters).'>';
		echo htmlspecialchars($this->value);
		echo '</textarea>';
	}
}
?>
