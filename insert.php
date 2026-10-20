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
                pendo_track('product_created', [
                    'product_id' => (string) mysqli_insert_id($con),
                    'product_name' => $_POST['name'],
                    'price' => pendo_track_number($_POST['price']),
                    'image_type' => $image_type,
                    'image_size' => $_FILES['image']['size'],
                ]);
                echo "<script>alert('تم رفع المنتج بنجاح')</script>";
            } else {
                // The product row was already inserted, but its image was not saved.
                pendo_track('product_creation_failed', [
                    'failure_reason' => 'image_move_error',
                    'image_type' => $image_type,
                    'upload_error_code' => $_FILES['image']['error'],
                ]);
                echo "<script>alert('حدثت مشكلة، لم يتم رفع المنتج')</script>";
            }
        } else {
            pendo_track('product_creation_failed', [
                'failure_reason' => 'db_insert_error',
                'image_type' => $image_type,
                'upload_error_code' => $_FILES['image']['error'],
            ]);
            echo "<script>alert('حدث خطأ أثناء إدخال البيانات في قاعدة البيانات')</script>";
        }
    } else {
        // upload_error_code 4 means no image was chosen, 1 or 2 means the image was too large.
        pendo_track('product_creation_failed', [
            'failure_reason' => 'invalid_file_type',
            'image_type' => $image_type,
            'upload_error_code' => $_FILES['image']['error'] ?? null,
        ]);
        echo "<script>alert('نوع الملف غير مسموح به. الرجاء رفع صورة بامتداد JPEG, PNG, أو GIF.')</script>";
    }
    header('location: index.php');
}

?>
