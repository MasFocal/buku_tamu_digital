<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Perangkat</title>
</head>
<body>
<?php 
    include "../session/session_login.php";
    $showErrorUsername  = false;
    $showSuccessModal   = false;
    $showErrorModal     = false;

    $queryToken = mysqli_query ($konek_db, "SELECT * FROM token_akun WHERE 1");
    $dataToken = mysqli_fetch_array ($queryToken);
    $encryptedData = isset($dataToken['token']) ? $dataToken['token'] : null;

    function generateKey() {
        return hex2bin(hash('sha256', "5"));
    }

    function decryptData($encryptedData) {
        if (!$encryptedData) {
            return "Belum ada token";
        }

        try {
            list($base64Cipher, $hash) = explode(".", $encryptedData);

            $cipherText = base64_decode($base64Cipher);
            $aesData = json_decode($cipherText, true);

            if (!$aesData || !isset($aesData['iv']) || !isset($aesData['value'])) {
                throw new Exception("Format data tidak valid");
            }

            $iv = base64_decode($aesData['iv']);
            $cipherValue = base64_decode($aesData['value']);

            $decrypted = openssl_decrypt($cipherValue, 'AES-256-CBC', generateKey(), OPENSSL_RAW_DATA, $iv);

            return $decrypted ?: "Gagal mendekripsi data";
        } catch (Exception $e) {
            return "Kesalahan dalam dekripsi: " . $e->getMessage();
        }
    }

    $token_akun = decryptData($encryptedData);

    if(isset($_POST['kirim'])){
        $nama   = mysqli_real_escape_string($konek_db, $_POST['nama']);
        $no_hp  = mysqli_real_escape_string($konek_db, $_POST['no_hp']);

        // Inisialisasi cURL untuk mengambil token dari API
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.fonnte.com/add-device',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array(
                'name' => $nama,
                'device' => $no_hp,
                'autoread' => 'false',
                'personal' => 'false',
                'group' => 'false'
            ),
            CURLOPT_HTTPHEADER => array(
                'Authorization: ' . $token_akun
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);

        $response_data = json_decode($response, true);

        function encryptBeforeSubmit($text) {
            if (!$text) {
                return "Teks harus diisi!";
            }

            $key = generateKey();
            $iv = random_bytes(16);

            $cipherText = openssl_encrypt($text, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);

            $cipherData = json_encode([
                'iv' => base64_encode($iv),
                'value' => base64_encode($cipherText)
            ]);

            $hash = hash('sha256', $cipherData);

            $finalResult = base64_encode($cipherData) . "." . $hash;

            return $finalResult;
        }

        if ($response_data === null) {
            die("Gagal mendapatkan respons dari API: " . json_last_error_msg());
        }

        if (isset($response_data['token'])) {
            $encryptedToken = encryptBeforeSubmit($response_data['token']);
        } else {
            die("Gagal mendapatkan token dari API.");
        }

        $cekUsername = mysqli_query($konek_db, "SELECT * FROM `perangkat` WHERE `no_hp` = '$no_hp'");

        if(mysqli_num_rows($cekUsername) > 0){
            $showErrorUsername = true;
        } else {
            $query = "INSERT INTO `perangkat` (
                `nama_perangkat`,
                `no_hp`,
                `token`,
                `status`
            ) VALUES (
                '$nama',
                '$no_hp',
                '$encryptedToken',
                'disconnect'
            )";

            if(mysqli_query($konek_db, $query)) {
                $showSuccessModal = true;
            } else {
                $showErrorModal = true;
            }
        }
    }

    //echo nl2br($dataToken['token'] . "\n" . $token_akun);
?>

    <div class="d-flex" id="wrapper">
        <?php 
            include "sidebar.php";
            echo '<div id="page-content-wrapper">';
            include "navbar.php";
        ?>
        <!-- Page content-->
        <div class="container-fluid">
            <h1 class="mt-4 text-center">Tambah Perangkat</h1>
            <a href="data_perangkat.php"><button type="button" data-mdb-button-init data-mdb-ripple-init class="btn btn-primary btn-lg mt-4 mb-3"><i class="bi bi-arrow-return-left me-2"></i>Kembali</button></a>
            <form action="" method="POST">
                <div class="col-xl-9 ms-2">
                    <div class="card-body">
                        <div class="row align-items-center pt-4 pb-3">
                            <div class="col-md-3">
                                <h6 class="mb-0">Nama Perangkat :</h6>
                            </div>
                            <div class="col-md-9 pe-5">
                                <input type="text" class="form-control form-control-lg" name="nama" placeholder="Masukan Nama" required/>
                            </div>
                        </div>
                        <div class="row align-items-center py-3">
                            <div class="col-md-3">
                                <h6 class="mb-0">No Handphone :</h6>
                            </div>
                            <div class="col-md-9 pe-5">
                                <input type="text" class="form-control form-control-lg" name="no_hp" placeholder="Masukan No Handphone" required/>
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
    
    <!-- Modal Gagal No HP -->
    <div class="modal fade" id="erorrUsernameModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Notifikasi</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    No HP Sudah Ada
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
                    window.location.href = 'data_perangkat.php'; // Redirect setelah modal ditutup
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