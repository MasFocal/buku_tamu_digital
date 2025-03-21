<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../bootstrap/css/bootstrap.css">
    <script src="../bootstrap/js/bootstrap.bundle.min.js"></script>
</head>
<body>
    <?php 
        include "../session/session_login.php";
        $id = isset($_GET['id']) ? $_GET['id'] : (isset($_POST['id']) ? $_POST['id'] : null);

        if (isset($_GET['id'])) {
            $decoded_id = base64_decode($_GET['id']); // Dekripsi ID
        }

        $query=mysqli_query($konek_db, "SELECT * FROM buku_tamu WHERE message_id ='$decoded_id'");
        $data = mysqli_fetch_array ($query);
        $tanggal = new DateTime($data["tanggal_kedatangan"]);
    ?>

    <div class="d-flex" id="wrapper">
        <?php 
            include "sidebar.php";
            echo '<div id="page-content-wrapper">';
            include "navbar.php";
        ?>
        <!-- Page content-->
        <div class="container-fluid">
            <h1 class="mt-4 text-center">Detail Data Buku Tamu</h1>
            <div class="col-xl-9 ms-2">
                <div class="card-body">
                    <div class="row align-items-center pt-4 pb-3">
                        <div class="col-md-3">
                            <h6 class="mb-0">Nama :</h6>
                        </div>
                        <div class="col-md-9 pe-5">
                            <h6 class="fw-normal"><?php echo $data['nama_lengkap'] ?></h6>
                        </div>
                    </div>
                    <div class="row align-items-center py-3">
                        <div class="col-md-3">
                            <h6 class="mb-0">Hari, Tanggal :</h6>
                        </div>
                        <div class="col-md-9 pe-5">
                            <h6 class="fw-normal"><?php echo $data['hari_kedatangan']. ", ".$tanggal->format('d F Y')?></h6>
                        </div>
                    </div>
                    <div class="row align-items-center py-3">
                        <div class="col-md-3">
                            <h6 class="mb-0">Jam :</h6>
                        </div>
                        <div class="col-md-9 pe-5">
                            <h6 class="fw-normal"><?php echo $data['jam_kedatangan']. " WIB" ?></h6>
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
                    <div class="row align-items-center py-3">
                        <div class="col-md-3">
                            <h6 class="mb-0">Asal/Instasi :</h6>
                        </div>
                        <div class="col-md-9 pe-5">
                            <h6 class="fw-normal"><?php echo $data['asal'] ?></h6>
                        </div>
                    </div>
                    <div class="row align-items-center py-3">
                        <div class="col-md-3">
                            <h6 class="mb-0">Pesan :</h6>
                        </div>
                        <div class="col-md-9 pe-5">
                            <h6 class="fw-normal"><?php echo $data['pesan'] ?></h6>
                        </div>
                    </div>
                    <div class="row align-items-center py-3">
                        <div class="col-md-3">
                            <h6 class="mb-0">Status Janjian :</h6>
                        </div>
                        <div class="col-md-9 pe-5">
                            <h6 class="fw-normal"><?php echo $data['status_perjanjian'] ?></h6>
                        </div>
                    </div>
                    <div class="row align-items-center py-3">
                        <div class="col-md-3">
                            <h6 class="mb-0">Pesan Janjian :</h6>
                        </div>
                        <div class="col-md-9 pe-5">
                            <h6 class="fw-normal"><?php echo $data['pesan_perjanjian'] ?></h6>
                        </div>
                    </div>
                    <div class="mb-5 mt-4">
                        <a href="data_buku_tamu.php"><button type="submit" data-mdb-button-init data-mdb-ripple-init class="btn btn-primary btn-lg"><i class="bi bi-arrow-return-left me-2"></i>Kembali</button></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>