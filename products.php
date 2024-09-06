<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" crossorigin="anonymous">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>products |المنتجات </title>
    <link rel="stylesheet" href="index.css">
    <style>
        h3{
            font-weight: bold;
        }
        .card{
            margin-top: 20px;
            float: right;
            margin-left: 10px;
            margin-right: 10px;
        }
        .card img{
            
            height: 200px;

        }
        main{
            width: 60%;
        }


    </style>
</head>
<body>
    <center>
        <h3>جميع المنتجات المتوفره </h3>
    </center>
    <?php
    include('config.php');
    $result = mysqli_query($con, "SELECT * FROM prod");
    while($row = mysqli_fetch_array($result)){
        echo "
        <center>
          <main>
   <div class='card' style='width: 15rem;'>
        <img src='$row[image]' class='card-img-top'>
        <div class='card-body'>
           <h5 class='card-title'>$row[name]</h5>
           <p class=card-text'>$row[price]</p>
           <a href='delete.php? id=$row[id]' class='btn btn-danger'> حذف منتج</a>
           <a href='update.php? id=$row[id]' class='btn btn-primary'> تعديل منتج</a>
       </div>
   </div>
   </main>
        <center>
        ";
    }
    
    
    ?>





 
   
</body>
</html>