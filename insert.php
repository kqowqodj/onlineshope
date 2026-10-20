<?php

include('config.php');
include('pendo_track.php');

if (isset($_POST['upload'])) {
    $NAME = mysqli_real_escape_string($con, $_POST['name']);
    $PRICE = mysqli_real_escape_string($con, $_POST['price']);
    
    $image_name = $_FILES['image']['name'];
    $image_location = $_FILES['image']['tmp_name'];
    $image_type = $_FILES['image']['type'];
    $image_error = $_FILES['image']['error'];
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    $failure_reason = null;

    // التحقق من نوع الملف المرفوع
    if (in_array($image_type, $allowed_types)) {
        $image_up = "images/" . basename($image_name);

        // إدخال البيانات في قاعدة البيانات
        $insert = "INSERT INTO prod (name, price, image) VALUES ('$NAME', '$PRICE', '$image_up')";
        
        if (mysqli_query($con, $insert)) {
            $product_id = mysqli_insert_id($con);

            if (move_uploaded_file($image_location, $image_up)) {
                // Pendo: the product row is saved and its image is stored.
                // Raw $_POST values are sent because $NAME and $PRICE are SQL-escaped.
                pendo_track('product_created', [
                    'product_id' => (string) $product_id,
                    'product_name' => $_POST['name'],
                    'price' => is_numeric($_POST['price']) ? (float) $_POST['price'] : null,
                    'image_type' => $image_type,
                ]);
                echo "<script>alert('تم رفع المنتج بنجاح')</script>";
            } else {
                $failure_reason = 'image_move_failed';
                echo "<script>alert('حدثت مشكلة، لم يتم رفع المنتج')</script>";
            }
        } else {
            $failure_reason = 'db_insert_failed';
            echo "<script>alert('حدث خطأ أثناء إدخال البيانات في قاعدة البيانات')</script>";
        }
    } else {
        $failure_reason = 'invalid_image_type';
        echo "<script>alert('نوع الملف غير مسموح به. الرجاء رفع صورة بامتداد JPEG, PNG, أو GIF.')</script>";
    }

    if ($failure_reason !== null) {
        // Pendo: the product was not created. upload_error_code tells a missing image (4) or an
        // oversized one (1), which both arrive with an empty image_type, from a real format rejection.
        pendo_track('product_creation_failed', [
            'failure_reason' => $failure_reason,
            'image_type' => $image_type,
            'upload_error_code' => (string) $image_error,
        ]);
    }
    header('location: index.php');
}

?>
