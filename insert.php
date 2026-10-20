<?php

include('config.php');
include('pendo_track.php');

if (isset($_POST['upload'])) {
    $NAME = mysqli_real_escape_string($con, $_POST['name']);
    $PRICE = mysqli_real_escape_string($con, $_POST['price']);
    
    $image_name = $_FILES['image']['name'];
    $image_location = $_FILES['image']['tmp_name'];
    $image_type = $_FILES['image']['type'];
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];

    // التحقق من نوع الملف المرفوع
    if (in_array($image_type, $allowed_types)) {
        $image_up = "images/" . basename($image_name);

        // إدخال البيانات في قاعدة البيانات
        $insert = "INSERT INTO prod (name, price, image) VALUES ('$NAME', '$PRICE', '$image_up')";
        
        if (mysqli_query($con, $insert)) {
            $product_id = mysqli_insert_id($con);
            if (move_uploaded_file($image_location, $image_up)) {
                echo "<script>alert('تم رفع المنتج بنجاح')</script>";
                // Pendo: the product row was inserted and its image stored
                pendo_queue_event('product_created', [
                    'product_id' => $product_id,
                    'product_name' => $_POST['name'],
                    'price' => (float) $_POST['price'],
                    'image_type' => $image_type,
                ]);
            } else {
                echo "<script>alert('حدثت مشكلة، لم يتم رفع المنتج')</script>";
                // Pendo: the row was already inserted, so the product now has a broken image
                pendo_queue_event('product_creation_failed', [
                    'failure_reason' => 'image_move_failed',
                    'product_id' => $product_id,
                    'image_type' => $image_type,
                    'image_provided' => !empty($_FILES['image']['name']),
                ]);
            }
        } else {
            echo "<script>alert('حدث خطأ أثناء إدخال البيانات في قاعدة البيانات')</script>";
            // Pendo: the INSERT failed, so no product was created
            pendo_queue_event('product_creation_failed', [
                'failure_reason' => 'db_insert_failed',
                'image_type' => $image_type,
                'image_provided' => !empty($_FILES['image']['name']),
            ]);
        }
    } else {
        echo "<script>alert('نوع الملف غير مسموح به. الرجاء رفع صورة بامتداد JPEG, PNG, أو GIF.')</script>";
        // Pendo: image_provided=false means no image was chosen (PHP then reports an empty type)
        pendo_queue_event('product_creation_failed', [
            'failure_reason' => 'invalid_image_type',
            'image_type' => $image_type,
            'image_provided' => !empty($_FILES['image']['name']),
        ]);
    }
    header('location: index.php');
}

?>
