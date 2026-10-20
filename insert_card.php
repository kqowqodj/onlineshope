<?php

include('config.php');
include('pendo_track.php');

if(isset($_POST['add'])){
    $NAME = $_POST['name'];
    $PRICE = $_POST['price'];

   $insert ="INSERT INTO addcard (name, price) VALUES ('$NAME','$PRICE')";
    if (mysqli_query($con, $insert)) {
        // Pendo: the product is now in the cart. val.php posts the price from a hidden input that
        // the browser can edit, so the catalog price from prod is reported when it can be read.
        $product_id = $_POST['id'] ?? '';
        $price = $PRICE;
        try {
            $ID = mysqli_real_escape_string($con, $product_id);
            $result = mysqli_query($con, "SELECT price FROM prod WHERE id='$ID'");
            $product = $result ? mysqli_fetch_array($result) : null;
            if ($product) {
                $price = $product['price'];
            }
        } catch (Throwable $e) {
            // Keep the posted price: a failed lookup must not block the redirect to card.php.
        }

        pendo_track('product_added_to_cart', [
            'product_id' => $product_id,
            'product_name' => $NAME,
            'price' => is_numeric($price) ? (float) $price : null,
        ]);
    }

    header('location: card.php');
}
?>
