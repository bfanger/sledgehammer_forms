<?php
/**
 * Een groep velden binnen een webformulier 
 * Bied ondersteuning voor een Fieldset binnen een Fieldset
 *
 * @package Forms
 */
namespace SledgeHammer;
class Fieldset extends Fields {

	public 
		$legend,
		$parameters,
		$template;

	function __construct($legend, $fields = array(), $parameters = array(), $template = 'Fieldset.html') {
		parent::__construct($fields);
		$this->legend = $legend;
		$this->parameters = $parameters;
		$this->template = $template;
	}

	function render() {
		$template = new Template($this->template, array(
			'parameters' => implode_xml_parameters($this->parameters),
			'legend' => $this->legend,
			'fields' => $this->fields,
		));
		$template->render();
	}
}
?>
