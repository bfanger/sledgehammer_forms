<?php
/**
 * Omschrijving : Een component waarmee een checkbox/keuzevakje kunt genereren en importeren
 * Opmerkingen  : Er word een extra 'hidden' input gegenereerd, omdat een leeg keuzevakje geen get/post variabele verstuurd.
 *                Dankzij het 'hidden' veld kun je toch nagaan of het keuzevakje ook daadwerkelijk verstuurd is.
 * @package Forms
 */
namespace SledgeHammer;
class Checkbox extends Object implements View, Import {

	public
		$parameters,
		$label;

	private
		$no_hidden_input;

	function __construct($name = NULL, $label = NULL, $parameters = array(), $no_hidden_input = false) {
		$this->parameters =	$parameters;
		$this->parameters['type'] = 'checkbox';
		if($name !== NULL) {
			$this->parameters['name'] = $name;
		}
		$this->label = $label;
		$this->no_hidden_input = $no_hidden_input;
	}

	function initial($value) {
		if ($value) {
			$this->parameters['checked'] = 'checked';
		} else {
			unset($this->parameters['checked']);
		}
	}

	function import(&$error_message, $source = array()) {
		if (!array_key_exists('name', $this->parameters)) {
			return NULL; // De naam is niet opgegeven.
		}
		if (extract_element($source, $this->parameters['name'], $value)) {
			if (isset($this->parameters['value'])) {
				$checked = $this->parameters['value'];
				$returnvalue = $checked;
			} else {
				$checked = 'on';
				$returnvalue = true;
			}
			if ($this->no_hidden_input == false && @$value['value'] == $checked) { // Als er een hidden input is meegestuurd zit de waarde in $value['value'] mits aangevinkt
				$this->parameters['checked'] = 'checked';
				return $returnvalue;
			} elseif ($this->no_hidden_input && $value == $checked) { // Bij alleen een <input checkbox> zit de waarde direct in $value
				$this->parameters['checked'] = 'checked';
				return $returnvalue;
			} else {
				unset($this->parameters['checked']);
				return false;
			}
		} elseif ($this->no_hidden_input) { // Is er geen extra hidden meegestuurd, dan is de checked niet aangevinkt.(als deze getoond werd)
				unset($this->parameters['checked']);
				return false;
		} else {
			$error_message = 'Import failed';
			return NULL;
		}
	}

	function render() {
		if ($this->label != '' && !isset($this->parameters['id'])) {
			$this->parameters['id'] = tidy_id($this->parameters['name']);
		}
		if ($this->no_hidden_input == false) {
			$name = $this->parameters['name'];
			echo '<input type="hidden" name="'.$name.'[hidden]" />';
			$this->parameters['name'] = $name.'[value]';
			echo '<input'.implode_xml_parameters($this->parameters).' />';
			$this->parameters['name'] = $name;
		} else {
			echo '<input'.implode_xml_parameters($this->parameters).' />';
		}
		if ($this->label != '') {
			echo '<label for="'.$this->parameters['id'].'">'.$this->label.'</label>';
		}
	}
}
?>
