<?php

include('config.php');
include('pendo_track.php');

if(isset($_POST['add'])){
    $NAME = $_POST['name'];
    $PRICE = $_POST['price'];

   $insert ="INSERT INTO addcard (name, price) VALUES ('$NAME','$PRICE')";
   if (mysqli_query($con,$insert)) {
       // Pendo: the product is now saved in the cart (card.php sends the event)
       pendo_queue_event('product_added_to_cart', [
           'product_id' => (int) ($_POST['id'] ?? 0),
           'product_name' => $NAME,
           'price' => (float) $PRICE,
           'cart_item_id' => mysqli_insert_id($con),
       ]);
   }

    header('location: card.php');
}
?>
