<?php
header('Content-type: application/javascript');
echo $jsonval;
if($row==1)
{
?>
({
		
		"items":"1"
})
<?php
}
else
{
    ?>
({ 
   "items":"0"
   
})
    <?php
    
}
?>