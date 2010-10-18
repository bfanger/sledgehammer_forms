<?php
/**
 * Een groep invoervelden die achter elkaar ge-renderd worden.
 * Zet geen html voor, tussen of na de velden.
 *
 * @package Forms
 */

class Fields extends Object implements Component, Import{

	public 
		$fields;

	function __construct($fields = array()) {
		$this->fields = $fields;
	}

	function initial($values) {
		foreach($this->fields as $key => $null) {
			if (isset($values[$key])) {
				$this->fields[$key]->initial($values[$key]);
			}
		}
	}

	function import(&$error_messages, $source = array()) {
		$values = array();
		foreach($this->fields as $key => $Field) {
			$error_message = false;
			$values[$key] = $this->fields[$key]->import($error_message, $source);
			if ($error_message) {
				$error_messages[$key] = $error_message;
			}
		}
		return $values;
	}

	function render() {
		foreach ($this->fields as $field) {
			$field->render();
		}
	}
}
?>
