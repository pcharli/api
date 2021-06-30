<?php if(MODE == "dev") :?>
<section class="section debug">
<?php 
echo "Session : <br>";
echo "<pre>";
    print_r($_SESSION);
echo "</pre>";
echo "Get : <br>";
echo "<pre>";
    print_r($_GET);
echo "</pre>";
echo "Post : <br>";
echo "<pre>";
    print_r($_POST);
echo "</pre>";
?>
</section>  
<?php endif;?>