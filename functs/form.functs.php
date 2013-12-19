<?php

function generateFormSelector($options, $elementID,
			$textAttribute, $selectedID = null) {
	$selector = '<select name="' . $elementID . '" required>';
	$selector .= generateFormSelectorChoices($options, $textAttribute, $selectedID);
	$selector .= '</select>';
	return $selector;
}
	
function generateFormSelectorChoices($options, $textAttribute, $selectedID) {
	$choices = '';
	foreach ($options as $optionID => $optionData) {
		$choices .= '<option value="' . $optionID . '"';
		if (!empty($selectedID) && $selectedID === $optionID) {
			$choices .= ' selected';
		}
		$choices .= '>' . $optionData[$textAttribute] . '</option>';
	}
	return $choices;
}
