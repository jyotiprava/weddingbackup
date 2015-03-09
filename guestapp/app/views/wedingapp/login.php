<?php
header('Content-type: application/javascript');
echo $jsonval;
if($row==1)
{
?>
({
		
		"items": [{"item":"1","name":"<?=$name;?>"},]
})
<?php
}
else
{
    ?>
({    
    "items":[{"item":"0","name":""},]
})
    <?php
 
}
?>