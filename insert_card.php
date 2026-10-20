<?php

include('config.php');
include('pendo_track.php');

if(isset($_POST['add'])){
    $NAME = $_POST['name'];
    $PRICE = $_POST['price'];

   $insert ="INSERT INTO addcard (name, price) VALUES ('$NAME','$PRICE')";
   if (mysqli_query($con,$insert)) {
       pendo_track('product_added_to_cart', [
           'product_id' => $_POST['id'] ?? null,
           'product_name' => $NAME,
           'price' => pendo_track_number($PRICE),
       ]);
   }

    header('location: card.php');
}
?>
