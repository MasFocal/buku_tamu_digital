<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Atur Akun</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.1.1/crypto-js.min.js"></script>
</head>
<body>
    <script>
        function generateKey() {
            return CryptoJS.enc.Hex.parse(CryptoJS.SHA256("5").toString()); // Hex parse agar cocok dengan PHP
        }

        function encryptBeforeSubmit(event) {
            event.preventDefault();
            var text = document.getElementById("text").value;
            if (!text) {
                alert("Teks harus diisi!");
                return false;
            }

            var key = generateKey();
            var iv = CryptoJS.lib.WordArray.random(16);
            
            var encrypted = CryptoJS.AES.encrypt(text, key, { 
                iv: iv, 
                mode: CryptoJS.mode.CBC, 
                padding: CryptoJS.pad.Pkcs7 
            });

            var cipherData = JSON.stringify({
                iv: CryptoJS.enc.Base64.stringify(iv),
                value: encrypted.toString()
            });

            var hash = CryptoJS.SHA256(cipherData).toString();
            var finalResult = btoa(cipherData) + "." + hash;

            document.getElementById("token").value = finalResult;
            document.getElementById("text").value = "";
            event.target.submit();
        }
    </script>
    <?php 
        include "../session/session_login.php";
        $showSuccessModal = false;
        $showErrorModal = false;

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $token = mysqli_real_escape_string($konek_db, $_POST['token']); 

            // Periksa apakah ada data dalam tabel
            $sql = mysqli_query($konek_db, "SELECT * FROM token_akun LIMIT 1");
            if (mysqli_num_rows($sql) > 0) {
                $query = "UPDATE token_akun SET token='$token' WHERE 1"; // Pastikan ada WHERE
            } else {
                $query = "INSERT INTO token_akun (token) VALUES ('$token')";
            }

            if (mysqli_query($konek_db, $query)) {
                //var_dump($token);
                $showSuccessModal = true;
            } else {
                $showErrorModal = true;
                //echo "Error: " . mysqli_error($konek_db); // Tampilkan error jika ada
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
            <h1 class="mt-4 text-center mb-4">Atur Token Akun</h1>
            <a href="data_perangkat.php" class="btn btn-primary btn-lg mt-4 mb-3"><i class="bi bi-arrow-return-left me-2"></i>Kembali</a>
            <form action="" method="POST" onsubmit="return encryptBeforeSubmit(event)">
                <div class="mb-5">
                    <label for="text" class="form-label">Masukkan Token Akun Fonnte:</label>
                    <input type="text" class="form-control w-50" name="text" id="text" required/>
                </div>
                <input type="hidden" id="token" name="token"/>
                <button type="submit" data-mdb-button-init data-mdb-ripple-init class="btn btn-success btn-lg" name="kirim"><i class="bi bi-send me-2"></i>Simpan</button>
            </form>
        </div>
    </div>

    <!-- Modal Berhasil -->
    <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Notifikasi</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Token Berhasil Diganti
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
                    Token Gagal Diganti
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