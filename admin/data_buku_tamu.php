<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buku Tamu</title>
</head>
<body>
    <?php 
        include "../session/session_login.php";
        $showErrorSearchModal   = false;
        $showSuccessModal       = false;
        $showErrorModal         = false;

        $queryToken = mysqli_query ($konek_db, "SELECT * FROM perangkat WHERE status = 'connect' LIMIT 1");
        $dataToken = mysqli_fetch_array ($queryToken);

        $encryptedData = isset($dataToken['token']) ? $dataToken['token'] : null;

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

        $token_akun = decryptData($encryptedData);
    
        //$token_akun = $dataToken['token'];

        if(isset($_POST["detail"])) {
            $id = $_POST["id_tamu"];
            $encoded_id = base64_encode($id);
            header("location: detail_data_tamu.php?id=". urlencode($encoded_id));
        }
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['hapus']) == 'yes') {
            $id = $_POST['id_tamu'];

            $curl = curl_init();
            curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.fonnte.com/delete-message',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array('id' => $id),
            CURLOPT_HTTPHEADER => array(
                'Authorization: ' . $token_akun
            ),
            ));

            $response = curl_exec($curl);

            curl_close($curl);
            //echo $response;

            $result = mysqli_query($konek_db, "DELETE FROM buku_tamu WHERE message_id='$id'");
    
            if ($result) {
                $showSuccessModal = true;
            } else {
                $showErrorModal = true;
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
                <h1 class="mt-4 mb-4 text-center">Data Buku Tamu</h1>
                <div class="mt-5 mb-4">
                    <a href="../print.php" target="_blank">
                        <button class="btn btn-danger mb-3 me-3"><i class='bi bi-printer me-2'></i>Download Data (.pdf)</button>
                    </a>
                    <a href="../excel.php">
                        <button class="btn btn-success mb-3 me-3"><i class='bi bi-printer me-2'></i>Download Data (.xlsx)</button>
                    </a>
                </div>

                <?php
                    $query_total = mysqli_query($konek_db, "SELECT COUNT(*) FROM buku_tamu");
                    $data_total = mysqli_fetch_row($query_total);
                    $total_rows = $data_total[0]; 
                ?>

                <!-- Form Pencarian -->
                <form class="d-flex" role="search" method="GET">
                    <input class="form-control me-2 w-50" type="search" placeholder="Search" aria-label="Search" name="search" <?= ($total_rows == 0) ? 'disabled' : ''; ?>>
                    <button class="btn btn-success" type="submit" <?= ($total_rows == 0) ? 'disabled' : ''; ?>><i class='bi bi-search'></i></button>
                </form>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped mt-4">
                        <tr class="text-center">
                            <th id="">No</th>
                            <th id="">Hari, Tanggal</th>
                            <th id="">Jam Kedatangan</th>
                            <th id="">Nama Tamu</th>
                            <th id="">Action</th>
                        </tr>
                        <?php
                            $limit = 5;

                            if (isset($_GET["page"])) {    
                                $page_number  = $_GET["page"];    
                            }else{
                                $page_number=1;
                            }
                            $initial_page = ($page_number-1) * $limit;

                            $where = '';
                            if(isset($_GET['search'])) {
                                $search = $_GET['search'];
                                $where = " WHERE tanggal_kedatangan LIKE '%$search%' OR hari_kedatangan LIKE '%$search%' OR jam_kedatangan LIKE '%$search%' OR nama_lengkap LIKE '%$search%'";
                            }
                            $querydata = mysqli_query($konek_db, "SELECT * FROM buku_tamu $where ORDER BY message_id DESC LIMIT $initial_page, $limit");
                            $id = $initial_page+0;
                            $hitung = mysqli_num_rows($querydata);
                            if ($hitung == 0) {
                                echo "<tr><td colspan='5' class='text-center'>Data belum ada</td></tr>";
                            } else {
                            while($sqldata = mysqli_fetch_array($querydata)){
                        ?>
                        <form action="" method="POST">
                        <input type="hidden" name="id_tamu" value="<?= $sqldata['message_id']; ?>">
                        <tr>
                            <?php
                                $tanggal = new DateTime($sqldata["tanggal_kedatangan"]);
                                $id++;
                                echo "
                                    <td>".$id."</td>
                                    <td>".$sqldata["hari_kedatangan"].", ".$tanggal->format('d F Y')."</td>
                                    <td>".$sqldata["jam_kedatangan"]."</td>
                                    <td>".$sqldata["nama_lengkap"]."</td>
                                    <td class='text-center'>
                                        <div>
                                            <button name='detail' class='btn btn-info me-2 mb-2 mt-2 pt-1 pb-1 pe-2 ps-2 border-dark'><i class='bi bi-info-lg me-2'></i>Lihat</button>
                                            <button name='hapus' class='btn btn-danger me-2 mb-2 mt-2 pt-1 pb-1 pe-2 ps-2 border-dark'><i class='bi bi-trash text-light me-2'></i>Hapus</button> 
                                        </div>
                                    </td>
                                ";
                            ?>
                        </tr>
                        </form>
                        <?php 
                            }
                        }
                        ?>
                    </table>
                </div>
                <ul class="pagination justify-content-center mb-5">
                <?php 
                    $query2 = mysqli_query($konek_db, "SELECT COUNT(*) FROM buku_tamu");
                    $data2  = mysqli_fetch_row($query2);
                    
                    $total_rows = $data2[0];              
                    echo "</br>";
                    $total_pages = ceil($total_rows / $limit);     
                    $pageURL = "";

                    if($page_number>=2){
                        echo "<li class='page-item'><a class='page-link' href='data_buku_tamu.php?page=".($page_number-1)."'>  Prev </a></li>";
                    }

                    for($i=1; $i<=$total_pages; $i++){
                        if ($i == $page_number) {   
                            $pageURL .= "<li class='page-item'><a class = 'page-link active' href='data_buku_tamu.php?page=".$i."'>".$i." </a></li>";   
                        }else{
                            $pageURL .= "<li class='page-item'><a class='page-link' href='data_buku_tamu.php?page=".$i."'>".$i." </a></li>";
                        }   
                    }

                    echo $pageURL;    
                    if($page_number<$total_pages){   
                        echo "<li class='page-item'><a class='page-link' href='data_buku_tamu.php?page=".($page_number+1)."'>  Next </a></li>";
                    } 
                ?>
                </ul>
        </div>
    </div>

    <!-- Modal Gagal Pencarian Data -->
    <div class="modal fade" id="erorrSearchModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Notifikasi</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Data Tidak Ditemukan
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <?php if ($showErrorSearchModal): ?>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var myModal = new bootstrap.Modal(document.getElementById('erorrSearchModal'));
            myModal.show();

            document.getElementById('erorrSearchModal').addEventListener('hidden.bs.modal', function () {
                window.location.href = "data_buku_tamu.php";
            });
        });
    </script>
    <?php endif; ?>

    <!-- Modal Berhasil Hapus -->
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
    <?php if ($showSuccessModal): ?>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                var myModal = new bootstrap.Modal(document.getElementById('successModal'));
                myModal.show();
            });
        </script>
    <?php endif; ?>

    <!-- Modal Gagal Hapus -->
    <div class="modal fade" id="erorrModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header" style="background-color: red;">
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