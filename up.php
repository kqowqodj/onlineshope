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
                    echo "<script>alert('تم تحديث المنتج بنجاح')</script>";
                    // Pendo: the edit was saved and the new image stored
                    pendo_queue_event('product_updated', [
                        'product_id' => (int) $_POST['id'],
                        'product_name' => $_POST['name'],
                        'price' => (float) $_POST['price'],
                        'image_type' => $image_type,
                    ]);
                } else {
                    echo "<script>alert('حدثت مشكلة، لم يتم رفع المنتج')</script>";
                    // Pendo: the UPDATE was already applied but the new image was not stored
                    pendo_queue_event('product_update_failed', [
                        'product_id' => (int) $_POST['id'],
                        'failure_reason' => 'image_move_failed',
                        'image_type' => $image_type,
                        'image_provided' => !empty($_FILES['image']['name']),
                    ]);
                }
            } else {
                echo "<script>alert('حدث خطأ أثناء إدخال البيانات في قاعدة البيانات')</script>";
                // Pendo: the UPDATE failed, so the edit was not saved
                pendo_queue_event('product_update_failed', [
                    'product_id' => (int) $_POST['id'],
                    'failure_reason' => 'db_update_failed',
                    'image_type' => $image_type,
                    'image_provided' => !empty($_FILES['image']['name']),
                ]);
            }
        } else {
            echo "<script>alert('نوع الملف غير مسموح به. الرجاء رفع صورة بامتداد JPEG, PNG, أو GIF.')</script>";
            // Pendo: every edit needs a new image, so image_provided=false means none was chosen
            pendo_queue_event('product_update_failed', [
                'product_id' => (int) $_POST['id'],
                'failure_reason' => 'invalid_image_type',
                'image_type' => $image_type,
                'image_provided' => !empty($_FILES['image']['name']),
            ]);
        }

        // إعادة التوجيه بعد الانتهاء
        header('Location: index.php');
        exit; // إيقاف تنفيذ السكربت بعد إعادة التوجيه
    } else {
        echo "<script>alert('يرجى ملء جميع الحقول المطلوبة.');</script>";
        // Pendo: this branch does not redirect, so the event is sent on the next index.php or card.php load
        pendo_queue_event('product_update_failed', [
            'product_id' => (int) ($_POST['id'] ?? 0),
            'failure_reason' => 'missing_fields',
            'image_type' => $_FILES['image']['type'] ?? '',
            'image_provided' => !empty($_FILES['image']['name']),
        ]);
    }
}

?>
