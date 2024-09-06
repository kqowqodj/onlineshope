<?php

include('config.php');

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
                echo "<script>alert('تم رفع المنتج بنجاح')</script>";
            } else {
                echo "<script>alert('حدثت مشكلة، لم يتم رفع المنتج')</script>";
            }
        } else {
            echo "<script>alert('حدث خطأ أثناء إدخال البيانات في قاعدة البيانات')</script>";
        }
    } else {
        echo "<script>alert('نوع الملف غير مسموح به. الرجاء رفع صورة بامتداد JPEG, PNG, أو GIF.')</script>";
    }
    header('location: index.php');
}

?>
