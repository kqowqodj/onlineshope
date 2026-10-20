<?php
include('config.php');
include('pendo_track.php');

$ID =$_GET['id'];
// Pendo: read the item before it is deleted so cart_item_removed can say what was removed
$item_result = mysqli_query($con, "SELECT name, price FROM addcard WHERE id=" . (int) $ID);
$item = $item_result ? mysqli_fetch_assoc($item_result) : null;
mysqli_query($con, "DELETE FROM addcard WHERE id=$ID");
if ($item && mysqli_affected_rows($con) > 0) {
    pendo_queue_event('cart_item_removed', [
        'cart_item_id' => (int) $ID,
        'product_name' => $item['name'],
        'price' => (float) $item['price'],
    ]);
}
header('location: card.php');
?>