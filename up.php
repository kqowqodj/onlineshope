<?php

include('config.php');
include('pendo_track.php');

if (isset($_POST['update'])) {
    // التأكد من أن جميع الحقول مطلوبة قد تم تعبئتها
    if (isset($_POST['id'], $_POST['name'], $_POST['price'], $_FILES['image'])) {
        $ID = mysqli_real_escape_string($con, $_POST['id']);
        $NAME = mysqli_real_escape_string($con, $_POST['name']);
        $PRICE = mysqli_real_escape_string($con, $_POST['price']);
        
        $image_name = $_FILES['image']['name'];
        $image_location = $_FILES['image']['tmp_name'];
        $image_type = $_FILES['image']['type'];
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];

        // التحقق من نوع الملف المرفوع
        if (in_array($image_type, $allowed_types)) {
            $image_up = "images/" . basename($image_name);

            // تحديث البيانات في قاعدة البيانات
            $update = "UPDATE prod SET name='$NAME', price='$PRICE', image='$image_up' WHERE id='$ID'";
            
            if (mysqli_query($con, $update)) {
                if (move_uploaded_file($image_location, $image_up)) {
                    pendo_track('product_updated', [
                        'product_id' => $_POST['id'],
                        'product_name' => $_POST['name'],
                        'price' => pendo_track_number($_POST['price']),
                        'image_type' => $image_type,
                    ]);
                    echo "<script>alert('تم تحديث المنتج بنجاح')</script>";
                } else {
                    // The product row was already updated, but its new image was not saved.
                    pendo_track('product_update_failed', [
                        'product_id' => $_POST['id'],
                        'failure_reason' => 'image_move_error',
                        'image_type' => $image_type,
                        'upload_error_code' => $_FILES['image']['error'],
                    ]);
                    echo "<script>alert('حدثت مشكلة، لم يتم رفع المنتج')</script>";
                }
            } else {
                pendo_track('product_update_failed', [
                    'product_id' => $_POST['id'],
                    'failure_reason' => 'db_update_error',
                    'image_type' => $image_type,
                    'upload_error_code' => $_FILES['image']['error'],
                ]);
                echo "<script>alert('حدث خطأ أثناء إدخال البيانات في قاعدة البيانات')</script>";
            }
        } else {
            // Editing only the name or price also lands here, because no new image
            // was chosen: upload_error_code is then 4.
            pendo_track('product_update_failed', [
                'product_id' => $_POST['id'],
                'failure_reason' => 'invalid_file_type',
                'image_type' => $image_type,
                'upload_error_code' => $_FILES['image']['error'],
            ]);
            echo "<script>alert('نوع الملف غير مسموح به. الرجاء رفع صورة بامتداد JPEG, PNG, أو GIF.')</script>";
        }

        // إعادة التوجيه بعد الانتهاء
        header('Location: index.php');
        exit; // إيقاف تنفيذ السكربت بعد إعادة التوجيه
    } else {
        pendo_track('product_update_failed', [
            'product_id' => $_POST['id'] ?? null,
            'failure_reason' => 'missing_fields',
        ]);
        echo "<script>alert('يرجى ملء جميع الحقول المطلوبة.');</script>";
    }
}

?>
