<?php
header('Content-type: application/javascript');
echo $jsonval;

?>
({
		
		"items": [{"item":"<?=$row;?>","name":"<?=$name;?>"}]
})