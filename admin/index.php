<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.css' rel='stylesheet' />
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.js'></script>
    <script src='https://cdn.jsdelivr.net/npm/@fullcalendar/interaction@5.10.1/main.min.js'></script>
    <script src='https://cdn.jsdelivr.net/npm/@fullcalendar/daygrid@5.10.1/main.min.js'></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

</head>
<body>
    <?php 
        include "../session/session_login.php";
        $sql = mysqli_query ($konek_db, "SELECT * FROM admin WHERE username='".$_SESSION['username']."'");
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
            <h1 class="mt-4 text-center">Dashboard Admin</h1>
            <div class="grey-bg container-fluid">
                <section id="minimal-statistics" class="mt-4">
                    <div class="row">
                        <div class="col-12 mt-3 mb-5">
                            <span class="text-uppercase h4">Hallo, <strong><?php echo $data['nama'] ?></strong> Selamat Datang di Buku Tamu</span>
                        </div>
                    </div>

                    <!-- Kalender Historis Tamu -->
                    <div class="row">
                        <div class="col-12 mt-3 mb-1">
                            <h4 class="text-uppercase">Kalender Data Tamu</h4>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xl-12 col-sm-6 col-12"> 
                            <div class="card">
                                <div class="card-content">
                                    <div class="card-body">
                                        <div id='calendar' class="mt-3 vh-100"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Modal untuk menampilkan detail tamu -->
                        <div class="modal fade" id="eventModal" tabindex="-1" aria-labelledby="eventModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="eventModalLabel">Detail Tamu</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p><strong>Nama :</strong> <span id="modalNama"></span></p>
                                        <p><strong>Hari, Tanggal :</strong> <span id="modalHariTanggal"></span></p>
                                        <p><strong>Jam :</strong> <span id="modalJam"></span></p>
                                        <p><strong>No HP :</strong> <span id="modalNoHP"></span></p>
                                        <p><strong>Asal :</strong> <span id="modalAsal"></span></p>
                                        <p><strong>Pesan :</strong> <span id="modalPesan"></span></p>
                                        <p><strong>Status :</strong> <span id="modalStatus"></span></p>
                                        <p><strong>Pesan Janjian :</strong> <span id="modalPesanPerjanjian"></span></p>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <?php
                            $sql = "SELECT * FROM buku_tamu";
                            $result = $konek_db->query($sql);

                            $events = array();
                            while ($row = $result->fetch_assoc()) {
                                $encoded_id         = base64_encode($row['message_id']);
                                $tanggal            = new DateTime($row["tanggal_kedatangan"]);
                                $pesan              = str_replace("<br>", "\n", wordwrap($row["pesan"], 45, "<br>", true));
                                $pesan_perjanjian   = str_replace("<br>", "\n", wordwrap($row["pesan_perjanjian"], 45, "<br>", true));
                                $events[] = [
                                    'title'             => $row['nama_lengkap'],
                                    'start'             => $row['tanggal_kedatangan'],
                                    'id'                => $encoded_id,
                                    'nama'              => $row['nama_lengkap'],
                                    'hari_tanggal'      => $row['hari_kedatangan'] . "," . $tanggal->format('d F Y'),
                                    'jam'               => $row['jam_kedatangan'],
                                    'no_hp'             => $row['no_hp'],
                                    'asal'              => $row['asal'],
                                    'pesan'             => $pesan,
                                    'status'            => $row['status_perjanjian'],
                                    'pesan_perjanjian'  => $pesan_perjanjian
                                ];
                            }
                        ?>

                        <script>
                            document.addEventListener('DOMContentLoaded', function () {
                                var calendarEl = document.getElementById('calendar');
                                var calendar = new FullCalendar.Calendar(calendarEl, {
                                    initialView: 'dayGridMonth',
                                    events: <?php echo json_encode($events); ?>,
                                    eventClick: function(info) {
                                        var eventNama               = info.event.extendedProps.nama;
                                        var eventHariTanggal        = info.event.extendedProps.hari_tanggal;
                                        var eventJam                = info.event.extendedProps.jam;
                                        var eventNoHP               = info.event.extendedProps.no_hp;
                                        var eventAsal               = info.event.extendedProps.asal;
                                        var eventPesan              = info.event.extendedProps.pesan;
                                        var eventStatus             = info.event.extendedProps.status;
                                        var eventPesanPerjanjian    = info.event.extendedProps.pesan_perjanjian;

                                        document.getElementById('modalNama').textContent            = eventNama;
                                        document.getElementById('modalHariTanggal').textContent     = eventHariTanggal;
                                        document.getElementById('modalJam').textContent             = eventJam;
                                        document.getElementById('modalNoHP').textContent            = eventNoHP;
                                        document.getElementById('modalAsal').textContent            = eventAsal;
                                        document.getElementById('modalPesan').textContent           = eventPesan;
                                        document.getElementById('modalStatus').textContent          = eventStatus;
                                        document.getElementById('modalPesanPerjanjian').textContent = eventPesanPerjanjian;

                                        var eventModal = new bootstrap.Modal(document.getElementById('eventModal'));
                                        eventModal.show();
                                    },
                                    eventDidMount: function(info) {
                                        info.el.style.cursor = 'pointer';
                                    }
                                });
                                calendar.render();
                            });
                        </script>
                    </div>

                    <!-- Chart Historis Tamu 30 Hari Terakhir -->
                    <div class="row mt-4">
                        <div class="col-12 mt-3 mb-1">
                            <h4 class="text-uppercase">Line Chart Tamu 20 Hari Terakhir</h4>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xl-12 col-sm-6 col-12"> 
                            <div class="card">
                                <div class="card-content">
                                    <div class="card-body">
                                        <div class="media d-flex">
                                            <canvas id="tamuHarianChart" class="mb-4 vh-50"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <?php
                            $tanggal_sekarang = new DateTime();
                            $tanggal_sekarang->modify('+1 day');
                            $tanggal_sebulan_lalu = clone $tanggal_sekarang;
                            $tanggal_sebulan_lalu->modify('-20 days');

                            $tanggal_harian = [];
                            $data_tamu = [];

                            while ($tanggal_sebulan_lalu < $tanggal_sekarang) {
                                $format_tgl = $tanggal_sebulan_lalu->format("Y-m-d");
                                $tanggal_harian[$format_tgl] = 0;
                                $tanggal_sebulan_lalu->modify('+1 day');
                            }

                            // Ambil data tamu dari database
                            $query = "SELECT DATE(tanggal_kedatangan) AS tanggal, COUNT(*) AS jumlah 
                                    FROM buku_tamu 
                                    WHERE tanggal_kedatangan >= DATE_SUB(CURDATE(), INTERVAL 20 DAY) 
                                    GROUP BY tanggal 
                                    ORDER BY tanggal ASC";

                            $result = mysqli_query($konek_db, $query);

                            // Masukkan data dari database ke array
                            while ($row = mysqli_fetch_assoc($result)) {
                                $tanggal_harian[$row['tanggal']] = $row['jumlah'];
                            }

                            // Ubah format untuk JSON
                            $tanggal_label = [];
                            $jumlah_tamu = [];

                            foreach ($tanggal_harian as $tgl => $jml) {
                                $tanggal_label[] = date("d M", strtotime($tgl)); // Format label (01 Mar, 02 Mar, dst.)
                                $jumlah_tamu[] = $jml;
                            }

                            // Konversi data ke JSON
                            $tanggal_json = json_encode($tanggal_label);
                            $jumlah_json = json_encode($jumlah_tamu);
                        ?>

                        <script>
                            document.addEventListener('DOMContentLoaded', () => {
                                const tanggal = <?php echo $tanggal_json; ?>;  // ✅ Semua tanggal 30 hari terakhir
                                const jumlah = <?php echo $jumlah_json; ?>;  // ✅ Jumlah tamu (termasuk yang 0)

                                const ctx = document.getElementById('tamuHarianChart').getContext('2d');
                                new Chart(ctx, {
                                    type: 'line', // ✅ Tetap menggunakan Line Chart
                                    data: {
                                        labels: tanggal, // ✅ Label tetap 30 hari terakhir
                                        datasets: [{
                                            label: 'Jumlah Tamu Harian',
                                            data: jumlah,
                                            backgroundColor: 'rgba(255, 99, 132, 0.2)', // Warna merah soft
                                            borderColor: 'rgb(255, 99, 132)', // Warna garis merah
                                            borderWidth: 2,
                                            pointRadius: 4, // Titik data lebih kecil
                                            pointBackgroundColor: 'rgb(255, 99, 132)',
                                            tension: 0.3 // Buat garis agak melengkung
                                        }]
                                    },
                                    options: {
                                        responsive: true,
                                        maintainAspectRatio: false,
                                        scales: {
                                            y: {
                                                beginAtZero: true,
                                                ticks: {
                                                    stepSize: 1
                                                }
                                            }
                                        }
                                    }
                                });
                            });
                        </script>
                    </div>

                    <!-- Log Data Kunjungan Tamu -->
                    <div class="row mt-4">
                        <div class="col-12 mt-3 mb-1">
                            <h4 class="text-uppercase ">Log Data Tamu</h4>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xl-12 col-sm-6 col-12"> 
                            <div class="card">
                                <div class="card-content">
                                    <div class="card-body">
                                        <div class="table-responsive">
                                        <table class="table table-bordered table-striped">
                                            <thead>
                                                <tr class="text-center">
                                                    <th>No</th>
                                                    <th>Tanggal & Jam</th>
                                                    <th>Nama Tamu</th>
                                                    <th>Pesan</th>
                                                    <th>Status</th>
                                                    <th>Pesan Janjian</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php 
                                                    $id = 0;
                                                    $data = mysqli_query($konek_db, "SELECT * FROM buku_tamu ORDER BY message_id DESC LIMIT 10");
                                                    while($sqldata = mysqli_fetch_array($data)){
                                                        $pesan = wordwrap($sqldata["pesan"], 20, "<br>", true);
                                                        $pesan_perjanjian = wordwrap($sqldata["pesan_perjanjian"], 20, "<br>", true);
                                                        $tanggal = new DateTime($sqldata["tanggal_kedatangan"]);
                                                        $id++;
                                                        echo "
                                                            <tr>
                                                                <td>".$id."</td>
                                                                <td>".$tanggal->format('j F Y').", ".$sqldata["jam_kedatangan"]."</td>
                                                                <td>".$sqldata["nama_lengkap"]."</td>
                                                                <td>".$pesan."</td>
                                                                <td>".$sqldata["status_perjanjian"]."</td>
                                                                <td>".$pesan_perjanjian."</td>
                                                            </tr>
                                                        ";
                                                    }
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12 mt-3 mb-1">
                            <h4 class="text-uppercase ">Tamu Kunjungan</h4>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card p-3 d-flex flex-row align-items-center gap-3 mt-2">
                                <i class="bi bi-people text-primary display-6"></i>
                                <div class="ms-auto text-end">
                                    <h3 class="fw-bold">
                                        <?php 
                                            $queryTotalPengunjung = mysqli_fetch_assoc(mysqli_query($konek_db, "SELECT COUNT(*) AS total FROM buku_tamu"));
                                            $total_pengunjung = $queryTotalPengunjung['total'];

                                            echo "$total_pengunjung Tamu ";
                                        ?>
                                    </h3>
                                    <span>Total Tamu Keseluruhan</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card p-3 d-flex flex-row align-items-center gap-3 mt-2">
                                <i class="bi bi-people text-primary display-6"></i>
                                <div class="ms-auto text-end">
                                    <h3 class="fw-bold">
                                        <?php 
                                            $queryTotalPengunjung = mysqli_fetch_assoc(mysqli_query($konek_db, "SELECT COUNT(*) AS total_hari_ini FROM buku_tamu WHERE tanggal_kedatangan = CURDATE()"));
                                            $total_pengunjung = $queryTotalPengunjung['total_hari_ini'];

                                            echo "$total_pengunjung Tamu ";
                                        ?>
                                    </h3>
                                    <span>Total Tamu Hari Ini</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12 mt-3 mb-1">
                            <h4 class="text-uppercase ">Entitas Akun</h4>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card p-3 d-flex flex-row align-items-center gap-3 mt-2">
                                <i class="bi bi-person text-danger display-6"></i>
                                <div class="ms-auto text-end">
                                    <h3 class="fw-bold">
                                        <?php 
                                            $queryTotalPengunjung = mysqli_fetch_assoc(mysqli_query($konek_db, "SELECT COUNT(*) AS total FROM admin"));
                                            $total_pengunjung = $queryTotalPengunjung['total'];

                                            echo "$total_pengunjung Admin ";
                                        ?>
                                    </h3>
                                    <span>Total Admin</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card p-3 d-flex flex-row align-items-center gap-3 mt-2">
                                <i class="bi bi-person text-danger display-6"></i>
                                <div class="ms-auto text-end">
                                    <h3 class="fw-bold">
                                        <?php 
                                            $queryTotalPengunjung = mysqli_fetch_assoc(mysqli_query($konek_db, "SELECT COUNT(*) AS total FROM customer_service"));
                                            $total_pengunjung = $queryTotalPengunjung['total'];

                                            echo "$total_pengunjung Customer Service ";
                                        ?>
                                    </h3>
                                    <span>Total Customer Service</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12 mt-3 mb-1">
                            <h4 class="text-uppercase ">Perangkat</h4>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card p-3 d-flex flex-row align-items-center gap-3 mt-2">
                                <i class="bi bi-phone text-warning display-6"></i>
                                <div class="ms-auto text-end">
                                    <h3 class="fw-bold">
                                        <?php 
                                            $queryTotalPengunjung = mysqli_fetch_assoc(mysqli_query($konek_db, "SELECT COUNT(*) AS total FROM perangkat"));
                                            $total_pengunjung = $queryTotalPengunjung['total'];

                                            echo "$total_pengunjung Perangkat ";
                                        ?>
                                    </h3>
                                    <span>Total Perangkat</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card p-3 d-flex flex-row align-items-center gap-3 mt-2">
                                <i class="bi bi-phone text-warning display-6"></i>
                                <div class="ms-auto text-end">
                                    <h3 class="fw-bold">
                                        <?php 
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

                                            $curl = curl_init();

                                            curl_setopt_array($curl, array(
                                                CURLOPT_URL => 'https://api.fonnte.com/get-devices',
                                                CURLOPT_RETURNTRANSFER => true,
                                                CURLOPT_ENCODING => '',
                                                CURLOPT_MAXREDIRS => 10,
                                                CURLOPT_TIMEOUT => 0,
                                                CURLOPT_FOLLOWLOCATION => true,
                                                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                                                CURLOPT_CUSTOMREQUEST => 'POST',
                                                CURLOPT_HTTPHEADER => array(
                                                    'Authorization: ' . $token_akun
                                                ),
                                            ));

                                            $response = curl_exec($curl);
                                            curl_close($curl);

                                            // Decode JSON menjadi array PHP
                                            $data = json_decode($response, true);

                                            // Ambil jumlah perangkat yang terhubung
                                            $connectedDevices = isset($data['connected']) ? $data['connected'] : 0;

                                            echo "$connectedDevices Perangkat Terkoneksi";
                                        ?>
                                    </h3>
                                    <span>Total Perangkat Terkoneksi</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
</body>
</html>