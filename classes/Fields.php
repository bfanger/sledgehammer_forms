<?php
/**
 * Een groep invoervelden die achter elkaar ge-renderd worden.
 * Zet geen html voor, tussen of na de velden.
 *
 * @package Forms
 */
namespace SledgeHammer;
class Fields extends Object implements View, Import, \ArrayAccess {

	protected
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

	//	ArrayAccess implementatie
	function offsetExists($key) {
		return array_key_exists($key, $this->fields);
	}
	function offsetGet($key) {
		return $this->fields[$key];
	}
	function offsetSet($key, $value) {
		$this->fields[$key] = $value;
	}
	function offsetUnset($key) {
		unset($this->fields[$key]);
	}
}
?>
