<?php
/**
 * Implementatie voor een Import structuur. 
 * Komt voor in Formulier objecten en ook in DialogBox
 *
 * @package Forms
 */
namespace SledgeHammer;
interface Import {

	/**
	 * Hiermee stel je de beginwaarde(n) (initial value(s)) in, zodat je een formulier vooraf kunt invullen met gegevens (uit bijvoorbeeld de database).
	 *
	 * @return void
	 */
	function initial($value);

	/**
	 * Retourneert de geimporteerde waarde(s).
	 * 
	 * @param mixed $error_message Als er fouten voordoen tijden het importeren wordt melding in $error_message gezet
	 * @param mixed $source De bron waaruit geimporteerd moet worden bestandsnaam, $_POST, $_GET etc.
	 * @return mixed
	 */
	function import(&$error_message, $source = NULL);
}

?>
