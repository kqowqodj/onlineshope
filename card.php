<!DOCTYPE html>
<html lang="en">
<head>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" crossorigin="anonymous">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Products | My Cart</title>
    <style>
        h3 {
            font-size: 30px;
            font-weight: bold;
        }
        main {
            width: 40%;
        }
        table {
            box-shadow: 1px 1px 10px silver;
        }
        thead {
            background-color: red;
            color: white;
        }
    </style>
</head>
<body>
    <center>
        <h3>منتجاتك المحجوزه</h3>
    </center>

    <center>
        <main>
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">Product Name</th>
                        <th scope="col">Product Price</th>
                        <th scope="col">Delete Product</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    include('config.php');
                    $result = mysqli_query($con, "SELECT * FROM addcard");
                    while ($row = mysqli_fetch_array($result)) {
                        echo "
                        <tr>
                            <td>{$row['name']}</td>
                            <td>{$row['price']}</td>
                            <td><a href='del_card.php?id={$row['id']}' class='btn btn-danger'>Delete</a></td>
                        </tr>";
                    }
                    ?>
                </tbody>
            </table>
        </main>
    </center>
    <center>
        <a href="shop.php">الرجوع الي صفحه المنتجات</a>
    </center>


</body>
</html>
