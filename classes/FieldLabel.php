<?php
/**
 * Een component waarmee een invoerveld van een label wordt voorzien.
 *
 * @package Forms
 */
namespace SledgeHammer;
class FieldLabel extends Object implements View, Import {

	public
		$label,
		$Field,
		$parameters;

	function __construct($label, $Field, $parameters = array()) {
		if (!array_key_exists('class', $parameters)) {
			$parameters['class'] = 'label';
		}
		$this->label = $label;
		$this->Field =	$Field;
		$this->parameters = $parameters;
	}

	function initial($value) {
		return $this->Field->initial($value);
	}

	function import(&$error_message, $source = array()) {
		$value = $this->Field->import($error_message, $source);
		if ($error_message) {
			$this->parameters['class'] .= ' error';
			if (is_array($error_message)) {
				$this->parameters['title'] = implode(', ', $error_message);
			} else {
				$this->parameters['title'] = $error_message;
			}
		}
		return $value;
	}

	function render() {
		$span_parameters  = $this->parameters;
		unset($span_parameters['for']);
		$label_parameters = $this->parameters;
		unset($label_parameters['class']);
		echo '<span'.implode_xml_parameters($span_parameters).'><label'.implode_xml_parameters($label_parameters).'>';
		if (trim($this->label) == '') {
			echo '&nbsp;';
		} else {
			echo $this->label;
		}
		echo '</label></span>';
		$this->Field->render();
	}
}
?>
