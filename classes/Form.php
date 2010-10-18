<?php
/**
 * Een webformulier
 * 
 * @package Forms
 */

class Form extends Object implements Component, Import {

	public 
		$Fieldsets;

	private
		$parameters,
		$template;

	function __construct($parameters = array(), $Fieldsets = array()) {
		$this->parameters = $parameters;
		if(empty($this->parameters['method'])) {
			$this->parameters['method'] = 'post';
		}
		if(empty($this->parameters['action'])) {
			$this->parameters['action'] = URL::uri();
		}
		$this->Fieldsets = $Fieldsets;
	}

	function initial($values) {
		foreach($this->Fieldsets as $key => $null) {
			if (isset($values[$key])) {
				$this->Fieldsets[$key]->initial($values[$key]);
			}
		}
	}

	function import(&$error_messages, $source = NULL) {
		if ($source === NULL) {
			if($this->parameters['method'] == 'post') {
				$source = &$_POST;
			} elseif ($this->parameters['method'] == 'get') {
				$source = &$_GET;
			} else {
				warning('Invalid import method');
			}
		}
		if (count($source) == 0) {
			$error_messages = false;
			return NULL;
		}
		$values = array();
		foreach($this->Fieldsets as $key => $null) {
			$fieldset_error_messages = false;
			$value = $this->Fieldsets[$key]->import($fieldset_error_messages, $source);
			if(!$fieldset_error_messages) {
				$values[$key] = $value;
			} else {
				$error_messages[$key] = $fieldset_error_messages;
			}
		}
		if (count($error_messages)) {
			return NULL;
		}
		return $values;
	}
	
	function render() {
		echo '<form'.implode_xml_parameters($this->parameters).'>';
		$fieldset_names = array_keys($this->Fieldsets);
		foreach ($fieldset_names as $fieldset) {
			$this->Fieldsets[$fieldset]->render();
		}
		echo '</form>';
	}
}
?>
