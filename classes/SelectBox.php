<?php
/**
 * Een component waarmee <select> tags kunt genereren en importeren
 *
 * @todo Ondersteuning voor <optiongroup>
 * @package Forms
 */
namespace SledgeHammer;
class SelectBox extends Object implements Component, Import {

	public 
		$Iterator,
		$parameters, // xhtml parameters voor binnen de <select> tag
		$Validator;

	private
		$selected, // De geselecteerde waarde.
		$empty_option, // Eerste optie met import waarde NULL. false: nee, true: Ja, lege optie (&nbsp;), string: tekst die in de optie getoont wordt.
		$empty_flag;

	function __construct($name, $Iterator, $parameters = array(), $Validator = NULL, $empty_option = false, $empty_flag = '__NULL__') {
		$this->Iterator = $Iterator;
		$this->parameters =	$parameters;
		if($name !== NULL) {
			$this->parameters['name'] = $name;
		}
		$this->Validator = $Validator;
		$this->empty_option = $empty_option;
		$this->empty_flag = $empty_flag;
	}

	function initial($value) {
		if ($value == $this->empty_flag) {
			$value = NULL;
		}
		$this->selected = $value;
	}

	function get_value(){
		return $this->selected;
	}
	function import(&$error_message, $source = array()) {
		if (!array_key_exists('name', $this->parameters)) {
			return NULL; // De naam is niet opgegeven. 
		}
		if (extract_element($source, $this->parameters['name'], $value)) {
			if ($value == $this->empty_flag) {
				$value = NULL;
			}
			$this->selected = $value;
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
		$indexed = is_indexed($this->Iterator);
		echo '<select'.implode_xml_parameters($this->parameters).">\n";
		if ($this->selected === NULL || $this->empty_option) {
			$label = (is_bool($this->empty_option)) ? '&nbsp;' : $this->empty_option; // Is er tekst opgegeven voor de NULL optie?
			echo '<option value="'.$this->empty_flag.'">'.$this->label($label).'</option>'."\n";
		} elseif ($indexed && is_numeric($this->selected)) {
			$this->selected = (int) $this->selected;
		}
		$nothing_selected = true;
		foreach ($this->Iterator as $key => $value ) {
			echo "\t".'<option';
			if ($indexed) {
				$label = $value;
			} else {
				$label = $value;
				$value = $key;
			}
			$parameters = array('value' => $value);
			if (equals($value, $this->selected)) {
				$parameters['selected'] = 'selected';
				$nothing_selected = false;
			}
			if (count($parameters)) {
				echo implode_xml_parameters($parameters);
			}
			echo '>'.$this->label($label).'</option>'."\n";
		}
		echo '</select>';
		if ($nothing_selected && $this->selected !== NULL) {
			notice('Selected value '.syntax_highlight($this->selected).' not found.');
		}
	}

	// Zorgt voor correcte html voor de labels <option>$label</option>
	// "blabla & bla" wordt "blabla &amp; bla", maar "&euro;" blijft "&euro;"
	private function label($label) {
		$label = str_replace(' & ', ' &amp; ', $label);
		if (trim($label) == '') {
			$label = '&nbsp;';
		}
		return $label;
	}
}
?>
