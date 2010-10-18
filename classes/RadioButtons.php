<?php
/**
 * Een component waarmee <input type='radio'> tags kunt genereren en importeren
 *
 * Er word een extra 'hidden' input gegenereerd, omdat zodra er geen keuzerondje gekozen wordt, er geen get/post variabele wordt verstuurd.
 * Dankzij het 'hidden' veld kun je toch nagaan of het keuzevakje ook daadwerkelijk verstuurd is.
 *
 * @package Forms
 */

class RadioButtons extends Object implements Component, Import{

	public 
		$Iterator,
		$parameters,
		$Validator,
		$label_parameters = array();

	private
		$selected;

	function __construct($name, $Iterator, $parameters = array(), $Validator = NULL) {
		$this->Iterator = $Iterator;
		$this->parameters =	$parameters;
		if($name !== NULL) {
			$this->parameters['name'] = $name;
		}
		$this->Validator = $Validator;
	}

	function initial($value) {
		$this->selected = $value;
	}

	function import(&$error_message, $source = array()) {
		if (!array_key_exists('name', $this->parameters)) {
			return NULL; // De naam is niet opgegeven. 
		}
		if (extract_element($source, $this->parameters['name'], $value)) {
			if (isset($value['value'])) {
				$this->selected = $value['value'];
			} else {
				$this->selected = NULL;
			}
			if ($this->Validator !== NULL) {
				if (!$this->Validator->validate($this->selected, $error_message)) {
					$this->parameters['class'] = 'error';
					$this->label_parameters['class'] = 'error';
				}
			}
			return $this->selected;
		} else {
			$error_message = 'Import failed';
			return NULL;
		}
	}

	function render() {
		$name = $this->parameters['name'];
		echo '<input type="hidden" name="'.$name.'[hidden]" />';
		$this->parameters['name'] = $name.'[value]';
		$indexed = is_indexed($this->Iterator);
		if ($this->selected !== NULL && ($indexed && is_numeric($this->selected))) {
			$this->selected = (int) $this->selected;
		}
		$label_parameters = implode_xml_parameters($this->label_parameters);
		foreach ($this->Iterator as $key => $value ) {
			$parameters = $this->parameters;
			if ($indexed) {
				$label = $value;
			} else {
				$label = $value;
				$value = $key;
			}
			$parameters['value'] = $value;
			if (equals($value, $this->selected)) {
				$parameters['checked'] = 'checked';
			}
			if (trim($label) != '') {
				$label = str_replace('<', '&lt;', $label);
				$label = str_replace('>', '&gt;', $label);
				$parameters['id'] = tidy_id($name.'_'.$value);
				echo '<input type="radio"'.implode_xml_parameters($parameters).' />';
				echo '<label for="'.$parameters['id'].'"'.$label_parameters.'>'.$label.'</label>';
			} else {
				echo '<input type="radio"'.implode_xml_parameters($parameters).' />';
			}
		}
		$this->parameters['name'] = $name;
	}
}
?>
