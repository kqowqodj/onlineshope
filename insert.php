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
            if (move_uploaded_file($image_location, $image_up)) {
                // Track Event: the new product row was inserted and its image saved.
                pendo_track('product_created', [
                    'product_id' => mysqli_insert_id($con),
                    'product_name' => $_POST['name'], // $NAME is SQL-escaped
                    'price' => is_numeric($PRICE) ? (float) $PRICE : null,
                    'image_type' => $image_type,
                    'image_size' => $_FILES['image']['size'],
                ]);
                echo "<script>alert('تم رفع المنتج بنجاح')</script>";
            } else {
                // The row is already inserted, so the catalog now has a product with a broken image path.
                pendo_track('product_creation_failed', [
                    'failure_reason' => 'image_upload_failed',
                    'image_type' => $image_type,
                    'upload_error_code' => $_FILES['image']['error'],
                ]);
                echo "<script>alert('حدثت مشكلة، لم يتم رفع المنتج')</script>";
            }
        } else {
            pendo_track('product_creation_failed', [
                'failure_reason' => 'db_insert_failed',
                'image_type' => $image_type,
                'upload_error_code' => $_FILES['image']['error'],
            ]);
            echo "<script>alert('حدث خطأ أثناء إدخال البيانات في قاعدة البيانات')</script>";
        }
    } else {
        // Also reached when no image was chosen (empty image_type, upload_error_code 4).
        pendo_track('product_creation_failed', [
            'failure_reason' => 'invalid_file_type',
            'image_type' => $image_type,
            'upload_error_code' => $_FILES['image']['error'],
        ]);
        echo "<script>alert('نوع الملف غير مسموح به. الرجاء رفع صورة بامتداد JPEG, PNG, أو GIF.')</script>";
    }
    header('location: index.php');
}

?>
