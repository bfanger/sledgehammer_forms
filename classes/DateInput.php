<?php
/**
 * Een component waarmee een datum via 3 keuzelijsten kunt selecteren
 * @todo: Een calender toevoegen (datepicker)
 * @package Forms
 */

class DateInput extends Object implements Component, Import{

	public 
		$Validator,
		$parameters;

	private 
		$SelectBoxes,
		$timestamp;

	function __construct($identifier, $parameters = array(), $Validator = NULL) {
		$this->parameters =	$parameters;
		$this->Validator = $Validator;
		$this->SelectBoxes = array(
			'day' => new SelectBox( $identifier.'[day]', $this->generate_options(1, 31), $this->parameters, $Validator),
			'month' => new SelectBox( $identifier.'[month]',$this->generate_months(), $this->parameters, $Validator),
			'year' => new SelectBox($identifier.'[year]', $this->generate_options(date('Y') + 2, 1900, -1), $this->parameters, $Validator)
		);
	}

	function initial($value) {
		$this->timestamp = $value;
	}

	function import(&$error_message, $source = array()) {
		$day = $this->SelectBoxes['day']->import($error_message, $source);
		$month = $this->SelectBoxes['month']->import($error_message, $source);
		$year = $this->SelectBoxes['year']->import($error_message, $source);
		if (!$day || !$month || !$year) {
			$this->timestamp = NULL;
		} else {
			$this->timestamp = mktime(0, 0, 0, $month, $day, $year);
		}
		return $this->timestamp;
	}

	function render() {
		if ($this->timestamp !== NULL) {
			$this->SelectBoxes['day']->initial(date('d', $this->timestamp));
			$this->SelectBoxes['month']->initial(date('n', $this->timestamp));
			$this->SelectBoxes['year']->initial(date('Y', $this->timestamp));
		}
		$this->SelectBoxes['day']->render();
		$this->SelectBoxes['month']->render();
		$this->SelectBoxes['year']->render();
	}

	function generate_options($from, $to, $step = 1) {
		$options = array();
		for($i = $from; ($step < 0 ? ($i >= $to) : ($i <= $to)); $i+= $step) {
			$options[] = $i;
		}
		return $options;
	}
	function generate_months() {
		$options = array();
    $year = date("Y");
    for($month = 1; $month <= 12; $month++) {
    	$timestamp = mktime (0, 0, 0, $month, 1, $year);
      $options[$month] = strftime('%B', $timestamp);
		}
		return $options;
	}
}
?>
