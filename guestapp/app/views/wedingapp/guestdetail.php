<?php
header('Content-type: application/javascript');
echo $jsonval;
?>
({
		
		"items": [
<?php
    echo json_encode($row);
?>

	   
	 ],

})	
