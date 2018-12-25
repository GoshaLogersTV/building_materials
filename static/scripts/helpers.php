<?php
function unsetValues(&$content, $values){
	foreach ($values as $value) {
		if(isset($content[$value]))
			unset($content[$value]);
	}
}
?>