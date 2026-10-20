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
        $image_error = $_FILES['image']['error'];
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $failure_reason = null;

        // التحقق من نوع الملف المرفوع
        if (in_array($image_type, $allowed_types)) {
            $image_up = "images/" . basename($image_name);

            // تحديث البيانات في قاعدة البيانات
            $update = "UPDATE prod SET name='$NAME', price='$PRICE', image='$image_up' WHERE id='$ID'";
            
            if (mysqli_query($con, $update)) {
                if (move_uploaded_file($image_location, $image_up)) {
                    // Pendo: the edit is saved and the new image is stored.
                    // Raw $_POST values are sent because $ID, $NAME and $PRICE are SQL-escaped.
                    pendo_track('product_updated', [
                        'product_id' => $_POST['id'],
                        'product_name' => $_POST['name'],
                        'price' => is_numeric($_POST['price']) ? (float) $_POST['price'] : null,
                        'image_type' => $image_type,
                    ]);
                    echo "<script>alert('تم تحديث المنتج بنجاح')</script>";
                } else {
                    $failure_reason = 'image_move_failed';
                    echo "<script>alert('حدثت مشكلة، لم يتم رفع المنتج')</script>";
                }
            } else {
                $failure_reason = 'db_update_failed';
                echo "<script>alert('حدث خطأ أثناء إدخال البيانات في قاعدة البيانات')</script>";
            }
        } else {
            $failure_reason = 'invalid_image_type';
            echo "<script>alert('نوع الملف غير مسموح به. الرجاء رفع صورة بامتداد JPEG, PNG, أو GIF.')</script>";
        }

        if ($failure_reason !== null) {
            // Pendo: the edit was rejected. Every edit needs a new image, so upload_error_code 4
            // (no image picked) shows how often merchants only changed the name or price.
            pendo_track('product_update_failed', [
                'failure_reason' => $failure_reason,
                'product_id' => $_POST['id'],
                'image_type' => $image_type,
                'upload_error_code' => (string) $image_error,
            ]);
        }

        // إعادة التوجيه بعد الانتهاء
        header('Location: index.php');
        exit; // إيقاف تنفيذ السكربت بعد إعادة التوجيه
    } else {
        // Pendo: the edit was rejected because a required field was not posted.
        pendo_track('product_update_failed', [
            'failure_reason' => 'missing_fields',
            'product_id' => $_POST['id'] ?? null,
            'image_type' => $_FILES['image']['type'] ?? null,
            'upload_error_code' => isset($_FILES['image']['error']) ? (string) $_FILES['image']['error'] : null,
        ]);
        echo "<script>alert('يرجى ملء جميع الحقول المطلوبة.');</script>";
    }
}

?>
