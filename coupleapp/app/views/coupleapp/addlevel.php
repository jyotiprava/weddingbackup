<?php
header('Content-type: application/javascript');
echo $jsonval;

?>
({
		
		"items": [{"item":"<?=$count;?>","name":"<?=$lid;?>"}]
})