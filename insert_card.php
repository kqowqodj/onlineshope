<?php

include('config.php');
include('pendo_track.php');

if(isset($_POST['add'])){
    $NAME = $_POST['name'];
    $PRICE = $_POST['price'];

   $insert ="INSERT INTO addcard (name, price) VALUES ('$NAME','$PRICE')";
   if(mysqli_query($con,$insert)){
       // Track Event: the shopper confirmed the product on val.php and it is now in the cart.
       pendo_track('product_added_to_cart', [
           'product_id' => isset($_POST['id']) ? (int) $_POST['id'] : null,
           'product_name' => $NAME,
           'price' => is_numeric($PRICE) ? (float) $PRICE : null,
       ]);
   }

    header('location: card.php');
}
?>
