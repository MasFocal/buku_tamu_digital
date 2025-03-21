<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
</head>
<body>
    <?php 
        include "../session/session_login.php";
        $sql = mysqli_query ($konek_db, "SELECT * FROM customer_service WHERE username='".$_SESSION['username']."'");
        $data = mysqli_fetch_array ($sql);
    ?>

    <div class="d-flex" id="wrapper">
        <?php 
            include "sidebar.php";
            echo '<div id="page-content-wrapper">';
            include "navbar.php";
        ?>
        <!-- Page content-->
        <div class="container-fluid">
            <h1 class="mt-4 text-center">Profile</h1>
            <div class="col-xl-9 ms-2">
                <div class="card-body">
                    <div class="row align-items-center pt-4 pb-3">
                        <div class="col-md-3">
                            <h6 class="mb-0">Nama CS :</h6>
                        </div>
                        <div class="col-md-9 pe-5">
                            <h6 class="fw-normal"><?php echo $data['nama'] ?></h6>
                        </div>
                    </div>
                    <div class="row align-items-center py-3">
                        <div class="col-md-3">
                            <h6 class="mb-0">Username :</h6>
                        </div>
                        <div class="col-md-9 pe-5">
                            <h6 class="fw-normal"><?php echo $data['username'] ?></h6>
                        </div>
                    </div>
                    <div class="row align-items-center py-3">
                        <div class="col-md-3">
                            <h6 class="mb-0">No Handphone :</h6>
                        </div>
                        <div class="col-md-9 pe-5">
                            <h6 class="fw-normal"><?php echo $data['no_hp'] ?></h6>
                        </div>
                    </div>
                    <div class="mb-5 mt-4">
                        <a href="edit_profile.php"><button type="button" data-mdb-button-init data-mdb-ripple-init class="btn btn-warning btn-lg"><i class='bi bi-pencil me-2'></i>Edit</button></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>