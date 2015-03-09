<?php
header('Content-type: application/javascript');
echo $jsonval;

?>
({
		
		"items": [{"count":"<?=$count;?>","deviceid":"<?=$deviceid;?>","name":"<?=$name;?>","email":"<?=$email;?>"}]
})