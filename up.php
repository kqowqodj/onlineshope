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
                    // Track Event: the product row was updated and its new image saved.
                    pendo_track('product_updated', [
                        'product_id' => (int) $ID,
                        'product_name' => $_POST['name'], // $NAME is SQL-escaped
                        'price' => is_numeric($PRICE) ? (float) $PRICE : null,
                        'image_type' => $image_type,
                        'image_size' => $_FILES['image']['size'],
                    ]);
                    echo "<script>alert('تم تحديث المنتج بنجاح')</script>";
                } else {
                    // The row is already updated, but the new image file was not saved.
                    pendo_track('product_update_failed', [
                        'failure_reason' => 'image_upload_failed',
                        'product_id' => (int) $ID,
                        'image_type' => $image_type,
                        'upload_error_code' => $_FILES['image']['error'],
                    ]);
                    echo "<script>alert('حدثت مشكلة، لم يتم رفع المنتج')</script>";
                }
            } else {
                pendo_track('product_update_failed', [
                    'failure_reason' => 'db_update_failed',
                    'product_id' => (int) $ID,
                    'image_type' => $image_type,
                    'upload_error_code' => $_FILES['image']['error'],
                ]);
                echo "<script>alert('حدث خطأ أثناء إدخال البيانات في قاعدة البيانات')</script>";
            }
        } else {
            // Also reached by every edit that keeps the current image (empty image_type, upload_error_code 4).
            pendo_track('product_update_failed', [
                'failure_reason' => 'invalid_file_type',
                'product_id' => (int) $ID,
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
            'failure_reason' => 'missing_fields',
            'product_id' => isset($_POST['id']) ? (int) $_POST['id'] : null,
        ]);
        echo "<script>alert('يرجى ملء جميع الحقول المطلوبة.');</script>";
    }
}

?>
