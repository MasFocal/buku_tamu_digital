<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Perangkat</title>
</head>
<body>
    <?php 
        include "../session/session_login.php";
        $id = 0;

        function generateKey() {
            return hex2bin(hash('sha256', "5"));
        }

        // Fungsi untuk mendekripsi token
        function decryptData($encryptedData) {
            if (!$encryptedData) {
                return null;
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

                return $decrypted ?: null;
            } catch (Exception $e) {
                return "Kesalahan dalam dekripsi: " . $e->getMessage();
            }
        }

        // Ambil data token dari database dan langsung dekripsi
        $queryTokens = mysqli_query($konek_db, "SELECT token FROM perangkat");
        $tokens = [];

        while ($row = mysqli_fetch_assoc($queryTokens)) {
            if (isset($row['token'])) {
                $decryptedToken = decryptData($row['token']);
                if ($decryptedToken) {
                    $tokens[] = $decryptedToken;
                }
            }
        }

        // Ambil data perangkat dari database dan dekripsi token
        $querydata = mysqli_query($konek_db, "SELECT * FROM perangkat");
        $devices_db = [];

        while ($sqldata = mysqli_fetch_array($querydata)) {
            $decryptedToken = decryptData($sqldata['token']);

            $devices_db[$sqldata['no_hp']] = [
                'nama_perangkat' => $sqldata['nama_perangkat'],
                'no_hp' => $sqldata['no_hp'],
                'token' => $decryptedToken
            ];
        }

        $devices_api = [];

        foreach ($tokens as $token) {
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://api.fonnte.com/device',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_HTTPHEADER => array(
                    'Authorization: ' . $token
                ),
            ));
            $response = curl_exec($curl);
            curl_close($curl);

            // DEBUG: Cek apakah API memberikan respons
            //var_dump($response);

            $dataAPI = json_decode($response, true);

            // Mengambil data dari API
            if (is_array($dataAPI) && isset($dataAPI['device'], $dataAPI['name'])) {
                $devices_api[$dataAPI['device']] = [
                    'nama_perangkat' => $dataAPI['name'],
                    'no_hp' => $dataAPI['device'],
                    'status' => $dataAPI['device_status'] ?? '-',
                    'quota' => $dataAPI['quota'] ?? '-',
                    'package' => $dataAPI['package'] ?? '-',
                    'token' => $dataAPI['token'] ?? '-'
                ];
            }
        }

        // Gabungkan data API dan Database berdasarkan no_hp
        $merged_devices = $devices_api;

        // Data dari database (jika belum ada dalam API)
        foreach ($devices_db as $no_hp => $device) {
            if (!isset($merged_devices[$no_hp])) {
                $merged_devices[$no_hp] = [
                    'nama_perangkat' => $device['nama_perangkat'],
                    'no_hp' => $device['no_hp'],
                    'status' => '-',
                    'quota' => '-',
                    'package' => '-',
                    'token' => '-'
                ];
            }
        }

        // Menampilan QR Code
        $modalContent = "";
        $modalTokenUpdateData = "";
        $showModalToken = false;
        $showModalGagalToken = false;

        if(isset($_POST["connect"])) {
            $idk = $_POST["id_no_perangkat"];
            $token_perangkat = $devices_db[$idk]['token'] ?? null;
            $no_perangkat = $devices_db[$idk]['no_hp'] ?? null;
        
            if (!$token_perangkat) {
                $modalContent = "Error: Token tidak ditemukan";
            } else {
                $curl = curl_init();

                curl_setopt_array($curl, array(
                    CURLOPT_URL => 'https://api.fonnte.com/qr',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS => array(
                        'type' => 'qr',
                        'whatsapp' => $no_perangkat
                    ),
                    CURLOPT_HTTPHEADER => array(
                        'Authorization: ' . $token_perangkat
                    ),
                ));
            
                $response = curl_exec($curl);
                        
                curl_close($curl);
                $res = json_decode($response, true);
                if (isset($res['url']) && strlen($res['url']) > 0) {
                    $qr = $res['url'];
                    $modalContent = "
                        <p>1. Buka Aplikasi WhatsApp</p>
                        <p>2. Pilih menu Perangkat Tertaut</p>
                        <p>3. Tautkan Perangkat dan Scan Kode QR</p>
                        <img src='data:image/png;base64, $qr'>
                        <br><p>Mohon klik tutup jika sudah benar benar tersambung atau gagal</p>";
                    $modalTokenUpdateData = $token_perangkat;
                    $showModalToken = true;
                } else if (isset($res['code']) && strlen($res['code']) > 0) {
                    $modalContent = $res["code"];
                    $showModalGagalToken = true;
                } else {
                    $modalContent = $res["reason"];
                    $showModalGagalToken = true;
                }
            }
            
            $modalTokenUpdateData = $no_perangkat;
            $showModalToken = true;
        }

        // Update status perangkat
        if(isset($_POST['updateconnect'])){
            $idk = $_POST["id_updatetoken"];
            $status = $devices_api[$idk]['status'] ?? null;
        
            $query = "UPDATE perangkat SET status='$status' WHERE no_hp='$idk'";
            mysqli_query($konek_db, $query);
        
            header("Location: ".$_SERVER['PHP_SELF']);
            exit();
        }

        // Disconnect Perangkat
        $showSuccessDisconnectModal   = false;
        $showErrorDisconnectModal     = false;

        if(isset($_POST["disconnect"])) {
            $idk = $_POST["id_no_perangkat"];
            $token_perangkat = $devices_db[$idk]['token'] ?? null;
            $no_perangkat = $devices_db[$idk]['no_hp'] ?? null;
        
            if (!$token_perangkat) {
                echo "Error: Token tidak ditemukan";
            } else {
                $curl = curl_init();
                curl_setopt_array($curl, array(
                    CURLOPT_URL => 'https://api.fonnte.com/disconnect',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_HTTPHEADER => array(
                        'Authorization: ' . $token_perangkat
                    ),
                ));
        
                $response = curl_exec($curl);
                curl_close($curl);
            }

            if(mysqli_query($konek_db, "UPDATE perangkat SET status='disconnect' WHERE no_hp='$no_perangkat'")) {
                $showSuccessDisconnectModal = true;
            } else {
                $showErrorDisconnectModal = true;
            }
        }

        // Mengirimkan permintaan OTP ke API
        $modalTokenDelete = "";
        $modalNoHPDelete = "";
        $showModalDelete = false;

        if(isset($_POST["hapus"])) {
            $idk = $_POST["id_no_perangkat"];
            $token_perangkat = $devices_db[$idk]['token'] ?? null;
            $no_perangkat = $devices_db[$idk]['no_hp'] ?? null;

            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://api.fonnte.com/delete-device',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => array('otp' => ''),
                CURLOPT_HTTPHEADER => array(
                    'Authorization: ' . $token_perangkat
                ),
            ));

            $response = curl_exec($curl);

            curl_close($curl);

            $modalTokenDelete   = $token_perangkat;
            $modalNoHPDelete    = $no_perangkat;
            $showModalDelete    = true;
        }

        // Menerima data OTP, mengirimkan OTP ke API dan Hapus Data di Database
        $showSuccessDeleteModal   = false;
        $showErrorDeleteModal     = false;

        if(isset($_POST['submit_otp'])){
            $idk        = $_POST["id_token"];
            $no_hp      = $_POST["id_no_hp"];
            $otp        = $_POST['otp'];

            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://api.fonnte.com/delete-device',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => array(
                    'otp' => $otp
                ),
                CURLOPT_HTTPHEADER => array(
                    'Authorization: ' . $idk
                ),
            ));

            $response = curl_exec($curl);

            curl_close($curl);

            if(mysqli_query($konek_db, "DELETE FROM perangkat WHERE no_hp ='$no_hp'")) {
                $showSuccessDeleteModal = true;
            } else {
                $showErrorDeleteModal = true;
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
            <h1 class="mt-4 text-center">Data Perangkat</h1>
            <div class="mt-5 mb-4">
                <a href="tambah_perangkat.php">
                    <button class="btn btn-primary mb-3 me-3"><i class='bi bi-plus-lg me-2'></i>Tambah Perangkat</button>
                </a>
                <a href="atur_akun.php">
                    <button class="btn btn-warning mb-3 me-3"><i class='bi bi-key me-2'></i>Atur Token Akun</button>
                </a>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <tr class="text-center">
                        <th>No</th>
                        <th>Perangkat</th>
                        <th>Keterangan</th>
                        <th>Action</th>
                    </tr>
                    <?php
                        if (count($merged_devices) > 0) {
                            foreach ($merged_devices as $device) {
                                //$token = isset($devices_db[$device['no_hp']]['token']) ? htmlspecialchars($devices_db[$device['no_hp']]['token']) : "(Token tidak ditemukan)";
                                $id++;
                    ?>
                    <form action="" method="POST">
                        <input type="hidden" name="id_no_perangkat" value="<?= $device['no_hp']; ?>">
                        <tr>
                            <td><?= $id ?></td>
                            <td>
                                <b>Nama Perangkat : </b><?= $device['nama_perangkat'] ?>
                                <br>
                                <b>Nomor : </b><?= $device['no_hp'] ?>
                            </td>
                            <td>
                                <b>Paket : </b><?= $device['package'] ?>
                                <br>
                                <b>Status : </b><?= $device['status'] ?>
                                <br>
                                <b>Kuota : </b><?= $device['quota'] ?>
                            </td>
                            <td class='text-center'>
                                <div>
                                    <?php 
                                        if($device['status'] == "connect"){
                                            echo "<button name='disconnect' class='btn btn-warning me-2 mb-2 mt-2 pt-1 pb-1 pe-2 ps-2 border-dark'><i class='bi bi-unlock me-2'></i>Putuskan</button>";
                                        }else{
                                            echo "<button name='connect' class='btn btn-info me-2 mb-2 mt-2 pt-1 pb-1 pe-2 ps-2 border-dark' data-bs-toggle='modal' data-bs-target='#ModalToken'><i class='bi bi-link-45deg me-2'></i>Tautkan</button>";
                                        }
                                    ?>
                                    <button name='hapus' class='btn btn-danger mb-2 mt-2 pt-1 pb-1 pe-2 ps-2 border-dark' id="openModal"><i class='bi bi-trash text-light me-2'></i>Hapus</button>
                                </div>
                            </td>
                        </tr>
                        </form>
                    <?php
                            }
                        } else {
                            echo "<tr><td colspan='4' class='text-center'>Tidak ada perangkat ditemukan.</td></tr>";
                        }
                    ?>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Token -->
    <?php if ($showModalToken): ?>
        <div class="modal fade show d-block" tabindex="-1" role="dialog" style="background: rgba(0, 0, 0, 0.5);">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">QR Code</h5>
                    </div>
                    <div class="modal-body">
                        <p><?= $modalContent; ?></p>
                    </div>
                    <div class="modal-footer">
                        <form method="post">
                            <input type="hidden" name="id_updatetoken" value="<?= $modalTokenUpdateData; ?>">
                            <button type="submit" name="updateconnect" class="btn btn-secondary">Tutup</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            document.querySelectorAll(".connect-btn").forEach(function(button) {
                button.addEventListener("click", function(event) {
                    event.preventDefault();
                    var modalElement = document.getElementById("ModalToken");
                    var ModalToken = new bootstrap.Modal(modalElement, {
                        backdrop: 'static',
                        keyboard: false
                    });
                    ModalToken.show();
                });
            });
        });
    </script>

    <!-- Modal Token Gagal -->
    <?php if ($showModalGagalToken): ?>
        <div class="modal fade show d-block" tabindex="-1" role="dialog" style="background: rgba(0, 0, 0, 0.5);">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Notifikasi</h5>
                    </div>
                    <div class="modal-body">
                        <p><?= $modalContent; ?></p>
                    </div>
                    <div class="modal-footer">
                        <a href="" class="btn btn-secondary">Tutup</a>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
                button.addEventListener("click", function(event) {
                    event.preventDefault();
                    var modalElement = document.getElementById("modalGagalToken");
                    var modalGagalToken = new bootstrap.Modal(modalElement, {
                        backdrop: 'static',
                        keyboard: false
                    });
                    modalGagalToken.show();
            });
        });
    </script>

    <!-- Modal Hapus -->
    <?php if ($showModalDelete): ?>
        <!-- Modal -->
        <div class="modal fade show d-block" tabindex="-1" role="dialog" style="background: rgba(0, 0, 0, 0.5);">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Masukkan Kode OTP</h5>
                    </div>
                    <form method="post">
                        <div class="modal-body">
                            <input type="hidden" name="id_token" value="<?= $modalTokenDelete; ?>">
                            <input type="hidden" name="id_no_hp" value="<?= $modalNoHPDelete; ?>">
                            <label for="otp">Kode OTP:</label>
                            <input type="text" name="otp" id="otp" class="form-control mb-4" required>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" name="submit_otp" class="btn btn-success"><i class="bi bi-send me-2"></i>Kirim</button>
                            <a href="data_perangkat.php" class="btn btn-secondary"><i class="bi bi-arrow-return-left me-2"></i>Kembali</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php endif; ?>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var modalElement = document.getElementById("successModal");
            var successModal = new bootstrap.Modal(modalElement, {
                backdrop: 'static',
                keyboard: false
            });

            document.querySelectorAll(".connect-btn").forEach(function(button) {
                button.addEventListener("click", function(event) {
                    event.preventDefault();
                    successModal.show();
                });
            });
        });
    </script>

    <!-- Modal Berhasil Diputuskan -->
    <div class="modal fade" id="successDisconnectModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Notifikasi</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Data Berhasil Diputuskan
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <?php if ($showSuccessDisconnectModal): ?>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                var myModal = new bootstrap.Modal(document.getElementById('successDisconnectModal'));
                myModal.show();

                var modalElement = document.getElementById('successDisconnectModal');
                modalElement.addEventListener('hidden.bs.modal', function () {
                    window.location.href = 'data_perangkat.php';
                });
            });
        </script>
    <?php endif; ?>

    <!-- Modal Gagal Diputuskan -->
    <div class="modal fade" id="erorrDisconnectModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Notifikasi</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Data Gagal Diputuskan
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <?php if ($showErrorDisconnectModal): ?>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                var myModal = new bootstrap.Modal(document.getElementById('erorrDisconnectModal'));
                myModal.show();
            });
        </script>
    <?php endif; ?>

    <!-- Modal Berhasil Dihapus -->
    <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Notifikasi</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Data Berhasil Dihapus
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <?php if ($showSuccessDeleteModal): ?>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                var myModal = new bootstrap.Modal(document.getElementById('successModal'));
                myModal.show();

                var modalElement = document.getElementById('successModal');
                modalElement.addEventListener('hidden.bs.modal', function () {
                    window.location.href = 'data_perangkat.php';
                });
            });
        </script>
    <?php endif; ?>

    <!-- Modal Gagal Dihapus -->
    <div class="modal fade" id="erorrModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Notifikasi</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Data Gagal Dihapus
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <?php if ($showErrorDeleteModal): ?>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                var myModal = new bootstrap.Modal(document.getElementById('erorrModal'));
                myModal.show();
            });
        </script>
    <?php endif; ?>
</body>
</html>