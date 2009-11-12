<?php
require 'lib/forms/forms.php';

function display_errors(Form $form, $fieldName = null) {
	if ($fieldName === null)
		$fieldName = Form::NonFieldErrors;
	
	$errors = $form->getErrors($fieldName);
	
	if (!($errors))
		return;
		
	print '<ul class="errorlist">';
	
	foreach ($errors as $error)
		print '<li>'.$error.'</li>';
	
	print '</ul>';
}
?>
