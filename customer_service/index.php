<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Customer Service</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

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
            <h1 class="mt-4 text-center">Dashboard Customer Service</h1>
            <div class="grey-bg container-fluid">
                <section id="minimal-statistics" class="mt-4">
                    <div class="row">
                        <div class="col-12 mt-3 mb-1">
                            <h4 class="text-uppercase fw-bold">Hallo, <?php echo $data['nama'] ?> Selamat Datang di Buku Tamu</h4>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 mt-3 mb-1">
                            <h4 class="text-uppercase">Chart Tamu Harian</h4>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xl-12 col-sm-6 col-12"> 
                            <div class="card">
                                <div class="card-content">
                                    <div class="card-body">
                                        <div class="media d-flex">
                                            <canvas id="tamuChart" class="mb-4 vh-100"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php
                            $query = "SELECT DATE(tanggal_kedatangan) AS tanggal, COUNT(*) AS jumlah 
                            FROM buku_tamu 
                            GROUP BY DATE(tanggal_kedatangan) 
                            ORDER BY tanggal ASC";

                            $result = mysqli_query($konek_db, $query);

                            $tanggal = [];
                            $jumlah = [];

                            while ($row = mysqli_fetch_assoc($result)) {
                                $tanggal[] = date("d M", strtotime($row['tanggal'])); // Format tanggal misalnya "01 Jan"
                                $jumlah[] = $row['jumlah'];
                            }

                            // Konversi data ke format JSON
                            $tanggal_json = json_encode($tanggal);
                            $jumlah_json = json_encode($jumlah);
                        ?>
                        <script>
                            document.addEventListener('DOMContentLoaded', () => {
                                const tanggal = <?php echo $tanggal_json; ?>;  // ✅ Data tanggal dari PHP
                                const jumlah = <?php echo $jumlah_json; ?>;    // ✅ Data jumlah kunjungan

                                const ctx = document.getElementById('tamuChart').getContext('2d');
                                new Chart(ctx, {
                                    type: 'line', // ✅ Ganti dengan 'line' agar sesuai contoh
                                    data: {
                                        labels: tanggal,
                                        datasets: [{
                                            label: 'Jumlah Tamu Harian',
                                            data: jumlah,
                                            backgroundColor: 'rgba(75, 192, 192, 0.2)', // Warna lebih soft
                                            borderColor: 'rgb(75, 192, 192)', // Warna garis
                                            borderWidth: 2,
                                            pointRadius: 5, // ✅ Tambahkan titik data
                                            pointBackgroundColor: 'rgb(75, 192, 192)',
                                            tension: 0.2 // ✅ Buat garis agak melengkung seperti di gambar
                                        }]
                                    },
                                    options: {
                                        responsive: true,
                                        maintainAspectRatio: false,
                                        scales: {
                                            y: {
                                                beginAtZero: true,
                                                ticks: {
                                                    stepSize: 1 // ✅ Supaya jumlah pengunjung tampil dengan benar
                                                }
                                            }
                                        }
                                    }
                                });
                            });
                        </script>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12 mt-3 mb-1">
                            <h4 class="text-uppercase ">Tamu Kunjungan</h4>
                        </div>
                    </div>
                    <div class="row mb-4 mt-2">
                        <div class="col-md-6">
                            <div class="card p-3 d-flex flex-row align-items-center gap-3">
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
                            <div class="card p-3 d-flex flex-row align-items-center gap-3">
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
                </section>
            </div>
        </div>
    </div>
</body>
</html>