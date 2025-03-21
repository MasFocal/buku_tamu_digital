<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Website Buku Tamu</title>
    <link rel="stylesheet" href="bootstrap/css/bootstrap.css">
    <script src="bootstrap/js/bootstrap.bundle.min.js"></script>
</head>
<body>
    <?php
        include("koneksi.php");
        $showSuccessModal = false;
        $showErrorModal = false;

        $queryToken = mysqli_query ($konek_db, "SELECT * FROM perangkat WHERE status = 'connect' LIMIT 1");
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

        // Dekripsi token
        $token_akun = decryptData($encryptedData);
    
        //$token_akun = $decryptedText;

        $queryDataHP = mysqli_query($konek_db, "SELECT * FROM customer_service WHERE 1");
        $list_no_hp = [];
        while ($row = mysqli_fetch_array($queryDataHP)){
            $list_no_hp[] = $row['no_hp'];
        }

        $target = implode(", ", $list_no_hp);

        if(isset($_POST['kirim'])) {
            $tanggalwaktu           = new DateTime('now');
            $tanggalwaktu->setTimezone(new DateTimeZone('Asia/Jakarta'));
            $hari                   = $tanggalwaktu->format('l');
            $tanggal                = $tanggalwaktu->format('Y-m-d');
            $tanggalWa              = $tanggalwaktu->format('d F Y');
            $waktu                  = $tanggalwaktu->format('H:i:s');
            $nama                   = $_POST['nama'];
            $no_hp                  = $_POST['no_hp'];
            $asal                   = $_POST['asal'];
            $keterangan             = $_POST['keterangan'];
            $status                 = $_POST['status'];
            $keterangan_janjian     = $_POST['keterangan_janjian'];

            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://api.fonnte.com/send',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => array(
                    'target'  => $target, 
                    'message' => "*Hallo ada yang berkunjung nih* \n*Hari, Tanggal* : $hari, $tanggalWa \n*Jam* : $waktu \n*Nama* : $nama \n*No Handphone* : $no_hp \n*Asal* : $asal \n*Keterangan* : $keterangan \n*Status* : $status \n*Keterangan Janjian* : $keterangan_janjian",
                ),
                CURLOPT_HTTPHEADER => array(
                    'Authorization: ' . $token_akun
                ),
            ));

            $response = curl_exec($curl);
            curl_close($curl);

            $response_data = json_decode($response, true);

            $message_id = isset($response_data['id'][0]) ? $response_data['id'][0] : null;

            $query="INSERT INTO `buku_tamu`(
                `hari_kedatangan`,
                `tanggal_kedatangan`,
                `jam_kedatangan`,
                `nama_lengkap`,
                `no_hp`,
                `asal`,
                `pesan`,
                `status_perjanjian`,
                `pesan_perjanjian`, 
                `message_id`
            ) VALUE (
                '$hari',
                '$tanggal',
                '$waktu',
                '$nama',
                '$no_hp',
                '$asal',
                '$keterangan',
                '$status',
                '$keterangan_janjian', 
                '$message_id'
            )";

            if(mysqli_query($konek_db, $query)) {
                $showSuccessModal = true;
            } else {
                $showErrorModal = true;
            }
        }
        //echo $token_akun;
    ?>
    <section class="h-100 h-custom" style="background-color: #8fc4b7;">
        <div class="container py-5 h-100">
            <div class="row d-flex justify-content-center align-items-center h-100">
                <div class="col-lg-8 col-xl-6">
                    <div class="card rounded-3">
                        <img src="img/img3.png"
                            class="w-100" style="border-top-left-radius: .3rem; border-top-right-radius: .3rem;"
                            alt="Sample photo">
                        <div class="card-body p-4 p-md-5">
                            <h1 class=" pb-2 pb-md-0 px-md-2 text-center">Buku Tamu Digital</h1>
                            <h5 class="mb-4 pb-2 pb-md-0 mb-md-5 px-md-2 text-center">
                                <script type="text/javascript">
                                    document.addEventListener("DOMContentLoaded", function() {
                                        jam();
                                    });

                                    function jam() {
                                        var e = document.getElementById('jam');
                                        if (e) {
                                            var d = new Date(), h, m, s;
                                            h = d.getHours();
                                            m = set(d.getMinutes());
                                            s = set(d.getSeconds());

                                            e.innerHTML = h + ':' + m + ':' + s + ' WIB';
                                            setTimeout(jam, 1000);
                                        }
                                    }

                                    function set(e) {
                                        return e < 10 ? '0' + e : e;
                                    }
                                </script>
                                <?php
                                    date_default_timezone_set('Asia/Jakarta');
                                    echo date("l, j F Y, ");
                                    echo '<span id="jam" name="jam"></span>';
                                ?>
                            </h5>

                            <form class="px-md-2" method="POST" id="MyForm">
                                <div data-mdb-input-init class="form-outline mb-4">
                                    <h6 class="mb-2 pb-1">Nama Lengkap : </h6>
                                    <input type="text" id="form3Example1q" class="form-control" required name="nama" placeholder="Masukan Nama Lengkap" />
                                </div>
                                <div data-mdb-input-init class="form-outline mb-4">
                                    <h6 class="mb-2 pb-1">No Handphone : </h6>
                                    <input type="text" id="form3Example1q" class="form-control" required maxlength="13" oninput="this.value = this.value.replace(/[^0-9]/g, '');" name="no_hp" placeholder="Diawali angka 0"/>
                                </div>
                                <div data-mdb-input-init class="form-outline mb-4">
                                    <h6 class="mb-2 pb-1">Asal/Instansi : </h6>
                                    <input type="text" id="form3Example1q" class="form-control" required name="asal" placeholder="Masukan Asal/Instansi"/>
                                </div>
                                <div data-mdb-input-init class="form-outline mb-4">
                                    <h6 class="mb-2 pb-1">Pesan : </h6>
                                    <textarea type="text" id="form3Example1q" class="form-control" rows="5" required name="keterangan" placeholder="Masukan Keterangan Kunjungan"></textarea>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <h6 class="mb-2 pb-1">Sudah Buat Janji ? </h6>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="status" id="opsi1"
                                        value="sudah" required/>
                                        <label class="form-check-label" for="opsi1">Sudah</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="status" id="opsi2"
                                        value="belum" required/>
                                        <label class="form-check-label" for="opsi2">Belum</label>
                                    </div>
                                </div>
                                <div data-mdb-input-init class="form-outline mb-4">
                                    <h6 class="mb-2 pb-1">Keterangan Janjian (Abaikan jika belum buat janji) :</h6>
                                    <textarea type="text" id="form3Example1q" class="form-control" rows="5" name="keterangan_janjian" placeholder="Masukan Keterangan Janjian"></textarea>
                                </div>
                                <button type="submit" data-mdb-button-init data-mdb-ripple-init class="btn btn-success btn-lg mb-1" name="kirim">Kirim</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

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