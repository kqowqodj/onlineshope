<?php

include('config.php');

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
                } else {
                    echo "<script>alert('حدثت مشكلة، لم يتم رفع المنتج')</script>";
                }
            } else {
                echo "<script>alert('حدث خطأ أثناء إدخال البيانات في قاعدة البيانات')</script>";
            }
        } else {
            echo "<script>alert('نوع الملف غير مسموح به. الرجاء رفع صورة بامتداد JPEG, PNG, أو GIF.')</script>";
        }

        // إعادة التوجيه بعد الانتهاء
        header('Location: index.php');
        exit; // إيقاف تنفيذ السكربت بعد إعادة التوجيه
    } else {
        echo "<script>alert('يرجى ملء جميع الحقول المطلوبة.');</script>";
    }
}

?>
