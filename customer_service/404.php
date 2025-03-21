<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 Not Found</title>
</head>
<body>
    <?php 
        include "../session/session_login.php";
    ?>

    <div class="d-flex" id="wrapper">
        <?php 
            include "sidebar.php";
            echo '<div id="page-content-wrapper">';
            include "navbar.php";
        ?>
            <!-- Page content-->
            <div class="container-fluid text-center">
                <h1 class="mt-4">Halaman Yang Anda Cari Tidak Tersedia</h1>
                <a href="index.php">
                    <button class="mt-3 btn btn-info border-black border-2">KEMBALI</button>
                </a>
            </div>
        </div>
    </div>
</body>
</html>