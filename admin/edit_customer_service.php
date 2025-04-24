<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Data Customer Service</title>
</head>
<body>
    <?php 
        include "../session/session_login.php";
        $showErrorUsername  = false;
        $showSuccessModal   = false;
        $showErrorModal     = false;

        $id = isset($_GET['id']) ? $_GET['id'] : (isset($_POST['id']) ? $_POST['id'] : null);

        if (isset($_GET['id'])) {
            $decoded_id = base64_decode($_GET['id']); // Dekripsi ID
        }

        $query=mysqli_query($konek_db, "SELECT * FROM customer_service WHERE username ='$decoded_id'");
        $data = mysqli_fetch_array ($query);

        if(isset($_POST['kirim'])){
            $nama           = $_POST['nama'];
            $username       = $_POST['username'];
            $username_baru  = $_POST['username_baru'];
            $no_hp          = $_POST['no_hp'];

            if (!empty($_POST['password'])) {
                $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
            } else {
                $password = $data['password'];
            }

            $cekUsername = mysqli_query($konek_db, "SELECT * FROM `customer_service` WHERE `username` = '$username'");

            if(mysqli_num_rows($cekUsername) > 0){
                $showErrorUsername = true;
            } else {
                $query = "UPDATE customer_service SET
                    `nama`      = '$nama',
                    `username`  = '$username_baru',
                    `no_hp`     = '$no_hp',
                    `password`  = '$password' WHERE username ='$decoded_id'
                ";

                if(mysqli_query($konek_db, $query)) {
                    $showSuccessModal = true;
                } else {
                    $showErrorModal = true;
                }
            }
        }
    ?>

    <div class="d-flex" id="wrapper">
        <?php 
            include "sidebar.php";
            echo '<div id="page-content-wrapper">';
            include "navbar.php";
        ?>
        <!-- Page content-->
        <div class="container-fluid">
            <h1 class="mt-4 text-center">Edit Data Customer Service</h1>
            <a href="data_customer_service.php"><button type="button" data-mdb-button-init data-mdb-ripple-init class="btn btn-primary btn-lg mt-4 mb-3"><i class="bi bi-arrow-return-left me-2"></i>Kembali</button></a>
            <form action="" method="POST">
                <input type="hidden" name="username" value="<?= $sqldata['username']; ?>">
                <div class="col-xl-9 ms-2">
                    <div class="card-body">
                        <div class="row align-items-center pt-4 pb-3">
                            <div class="col-md-3">
                                <h6 class="mb-0">Nama :</h6>
                            </div>
                            <div class="col-md-9 pe-5">
                                <input type="text" class="form-control form-control-lg" name="nama" value="<?php echo $data['nama'] ?>" required/>
                            </div>
                        </div>
                        <div class="row align-items-center py-3">
                            <div class="col-md-3">
                                <h6 class="mb-0">Username :</h6>
                            </div>
                            <div class="col-md-9 pe-5">
                                <input type="text" class="form-control form-control-lg" name="username_baru" value="<?php echo $data['username'] ?>" required/>
                            </div>
                        </div>
                        <div class="row align-items-center py-3">
                            <div class="col-md-3">
                                <h6 class="mb-0">No Handphone :</h6>
                            </div>
                            <div class="col-md-9 pe-5">
                                <input type="text" class="form-control form-control-lg" name="no_hp" value="<?php echo $data['no_hp'] ?>" maxlength="13" oninput="this.value = this.value.replace(/[^0-9]/g, '');" required/>
                            </div>
                        </div>
                        <div class="row align-items-center py-3">
                            <div class="col-md-3">
                                <h6 class="mb-0">Password :</h6>
                            </div>
                            <div class="col-md-9 pe-5">
                                <input type="password" class="form-control form-control-lg" name="password" placeholder="Jika ingin ubah password silahkan isi, jika tidak tidak masalah"/>
                            </div>
                        </div>
                        <div class="mb-5 mt-4">
                            <button type="submit" data-mdb-button-init data-mdb-ripple-init class="btn btn-success btn-lg" name="kirim"><i class="bi bi-send me-2"></i>Simpan</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Gagal Username -->
    <div class="modal fade" id="erorrUsernameModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Notifikasi</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Username sudah ada, silahkan ganti username
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <?php if ($showErrorUsername): ?>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                var myModal = new bootstrap.Modal(document.getElementById('erorrUsernameModal'));
                myModal.show();
            });
        </script>
    <?php endif; ?>

    <!-- Modal Berhasil -->
    <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Notifikasi</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Data Berhasil Dikirimkan
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <?php if ($showSuccessModal): ?>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                var myModal = new bootstrap.Modal(document.getElementById('successModal'));
                myModal.show();

                var modalElement = document.getElementById('successModal');
                modalElement.addEventListener('hidden.bs.modal', function () {
                    window.location.href = 'data_customer_service.php'; // Redirect setelah modal ditutup
                });
            });
        </script>
    <?php endif; ?>

    <!-- Modal Gagal -->
    <div class="modal fade" id="erorrModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Notifikasi</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Data Gagal Dikirimkan
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <?php if ($showErrorModal): ?>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                var myModal = new bootstrap.Modal(document.getElementById('erorrModal'));
                myModal.show();
            });
        </script>
    <?php endif; ?>
</body>
</html>