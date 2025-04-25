<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu Cetak Data Buku Tamu</title>
</head>
<body>
    <?php 
        include "../session/session_login.php";
    ?>

    <div class="d-flex" id="wrapper">
        <?php 
            include "sidebar.php";
            echo '<div id="page-content-wrapper">';
            include "navbar.php";
        ?>
        <!-- Page content-->
        <div class="container-fluid">
            <h1 class="mt-4 mb-4 text-center">Menu Cetak Data Buku Tamu</h1>
            <a href="data_buku_tamu.php">
                <button type="button" data-mdb-button-init data-mdb-ripple-init class="btn btn-primary btn-lg mt-4">
                    <i class="bi bi-arrow-return-left me-2"></i>Kembali
                </button>
            </a>

            <div class="mt-4">
                <span><strong>* Silahkan Pilih Salah Satu Sesuai Kebutuhan <br> dan Type File yang Di inginkan</strong></span>
            </div>

            <div class="card mt-3 me-3 col-sm-3">
                <div class="card-body">
                <h5>Cetak Data Keseluruhan</h5>
                <a href="../pdf_keseluruhan.php" target="_blank">
                    <button class="btn btn-danger p-2 mt-2 me-5">
                        <i class="bi bi-filetype-pdf me-2"></i>Typefile .pdf
                    </button>
                </a>
                <a href="../excel_keseluruhan.php">
                    <button class="btn btn-success p-2 mt-2 me-5">
                        <i class="bi bi-filetype-xlsx me-2"></i>Typefile .xlsx
                    </button>
                </a>
                </div>
            </div>
            <div class="card mt-4 me-3 col-sm-3">
                <div class="card-body">
                    <h5>Cetak Data Berdasarkan Bulan</h5>
                    <form action="" method="GET" target="_blank">
                        <div class="row g-3 align-items-center mb-3">
                            <label for="bulan" class="col-form-label">Pilih Bulan dan Tahun:</label>
                            <div class="col-auto">
                                <select id="bulan" name="bulan" class="form-select">
                                    <option value="" disabled>-- Pilih Bulan --</option>
                                    <option value="01">Januari</option>
                                    <option value="02">Februari</option>
                                    <option value="03">Maret</option>
                                    <option value="04">April</option>
                                    <option value="05">Mei</option>
                                    <option value="06">Juni</option>
                                    <option value="07">Juli</option>
                                    <option value="08">Agustus</option>
                                    <option value="09">September</option>
                                    <option value="10">Oktober</option>
                                    <option value="11">November</option>
                                    <option value="12">Desember</option>
                                </select>
                            </div>
                            <div class="col-auto">
                                <select id="tahun" name="tahun" class="form-select" required>
                                    <option value="" disabled>-- Pilih Tahun --</option>
                                    <?php
                                        $tahun_sekarang = date("Y");
                                        for ($i = $tahun_sekarang; $i >= 2010; $i--) {
                                            echo "<option value='$i'>$i</option>";
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <button type="submit" formaction="../pdf_bulan.php" class="btn btn-danger p-2 mt-2 me-5">
                            <i class="bi bi-filetype-pdf me-2"></i>Typefile .pdf
                        </button>

                        <button type="submit" formaction="../excel_bulan.php" class="btn btn-success p-2 mt-2 me-5">
                            <i class="bi bi-filetype-xlsx me-2"></i>Typefile .xlsx
                        </button>
                    </form>
                </div>
            </div>
            <div class="card mt-3 mb-5 me-3 col-sm-3">
                <div class="card-body">
                    <h5>Cetak Data Berdasarkan Tahun</h5>
                    <form action="" method="GET" target="_blank">
                        <div class="row g-3 align-items-center mb-3">
                            <div class="col-auto">
                                <label for="bulan" class="col-form-label">Pilih Tahun:</label>
                            </div>
                            <div class="col-auto">
                                <select id="tahun" name="tahun" class="form-select" required>
                                    <option value="" disabled>-- Pilih Tahun --</option>
                                    <?php
                                        $tahun_sekarang = date("Y");
                                        for ($i = $tahun_sekarang; $i >= 2010; $i--) {
                                            echo "<option value='$i'>$i</option>";
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <button type="submit" formaction="../pdf_tahun.php" class="btn btn-danger p-2 mt-2 me-5">
                            <i class="bi bi-filetype-pdf me-2"></i>Typefile .pdf
                        </button>

                        <button type="submit" formaction="../excel_tahun.php" class="btn btn-success p-2 mt-2 me-5">
                            <i class="bi bi-filetype-xlsx me-2"></i>Typefile .xlsx
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>