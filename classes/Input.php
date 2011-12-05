<?php
/**
 * Een component waarmee <input> tags kunt genereren en importeren
 *
 * @package Forms
 */
namespace SledgeHammer;
class Input extends Object implements View, Import {

	public
		$Validator,
		$parameters;

	function __construct($type, $name = NULL, $parameters = array(), $Validator = NULL) {
		$this->parameters =	$parameters;
		$this->parameters['type'] = $type;
		if($name !== NULL) {
			$this->parameters['name'] = $name;
		}
		$this->Validator = $Validator;
	}

	/**
	 * De <input value="*"> een waarde geven, zodat bij de eerste vertoning de waarde gevuld is.
	 * Deze waarde zal worden overschreven zodra de import() is aangeroepen.
	 *
	 * @param string $value
	 * @return void
	 */
	function initial($value) {
		$this->parameters['value'] = str_replace('\"', '&quot;', $value);
	}

	/**
	 * De waarde vanuit de $source array uitlezen
	 * Als er NULL als $name is opgegevens zal de import (zonder fouten) NULL teruggeven
	 *
	 * @param reference $error_message Deze waarde zal gevult worden met een foutmelding, zodra er een fout optreed.
	 * @param array $source De gegevensbron waaruit de waarde wordt ingelezen, zal om de $_GET of $_POST array gaan.
	 */
	function import(&$error_message, $source = array()) {
		if (!array_key_exists('name', $this->parameters)) {
			return NULL; // De naam is niet opgegeven.
		}
		if (extract_element($source, $this->parameters['name'], $value)) {
			$this->parameters['value'] = str_replace('\"', '&quot;', $value);
			if ($this->Validator !== NULL) {
				if (!$this->Validator->validate($value, $error_message)) {
					append_class_to_parameters('error', $this->parameters);
				}
		 	}
			return $value;
		} else {
			$error_message = 'Import failed';
			return NULL;
		}
	}

	/**
	 * De tag genereren en echo-en
	 *
	 * @return void
	 */
	function render() {
		echo "<input".implode_xml_parameters($this->parameters)." />\n";
	}

	/**
	 * Een veel voorkomende input bouwen. (Heeft de validators module nodig)
	 *
	 * @param string $type Zoals "number" & "required text"
	 * @param string|NULL $name De <input name="*"> waarde
	 * @param array $parameters Extra parameters zoals ''size', id', 'class' & 'style'
	 * @return Input
	 */
	static function build($type, $name = NULL, $parameters = array()) {
		$available_types = array('number', 'required text');
		if (!in_array($type, $available_types)) {
			error('Unexpected type: "'.$type.'", expecting "'.implode('", "', $available_types).'"'); // Zou een "throw new Exception" moeten zijn i.p.v. een error(), maar deze geeft "Segmentation fault" in php 5.2.9
		}
		$Validator = NULL;
		switch ($type) {

			case 'number':
				$type = 'text';
				$parameters['size'] = 5;
				append_class_to_parameters('number', $parameters);
				$Validator = new NumberValidator();
				break;

			case 'required text':
				$type = 'text';
				append_class_to_parameters('required', $parameters);
				$Validator = new Validators(array(new NotEmptyValidator(), new XSSValidator()));
				break;
		}
		return new Input($type, $name, $parameters, $Validator);
	}
}
?>
