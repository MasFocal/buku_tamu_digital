<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Buku Tamu Bulan</title>
    <link rel="stylesheet" href="print.css">
</head>
<body>
    <?php
        include "koneksi.php";
        ob_start();

        $id=0;

        $bulanAngka = $_GET['bulan'];
        $tahun      = $_GET['tahun'];

        $namaBulan = [
            "01" => "Januari",
            "02" => "Februari",
            "03" => "Maret",
            "04" => "April",
            "05" => "Mei",
            "06" => "Juni",
            "07" => "Juli",
            "08" => "Agustus",
            "09" => "September",
            "10" => "Oktober",
            "11" => "November",
            "12" => "Desember"
        ];

        if (!array_key_exists($bulanAngka, $namaBulan)) {
            die("Bulan tidak valid!");
        }

        $bulan = $_GET['bulan'];
        if (!preg_match('/^(0[1-9]|1[0-2])$/', $bulan)) {
            die("Bulan tidak valid!");
        }

        $queryDataPrint = mysqli_query($konek_db, "SELECT * FROM buku_tamu WHERE MONTH(tanggal_kedatangan) = '$bulan' AND YEAR(tanggal_kedatangan) = '$tahun' ORDER BY message_id DESC");
    ?>
    <style>
        h1 {
            text-align: center;
        }

        #table {
            width: 100%;
            border-collapse: collapse;
            font-size: 16px;
        }

        #th-td {
            border: 1px solid black;
            padding: 8px;
            text-align: center;
            background-color: #bdbdbd;
        }

        #tb-td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
        }

        tbody tr:nth-child(odd) {
            background-color: #f2f2f2;
        }

        tbody tr:nth-child(even) {
            background-color: #e0e0e0;
        }
    </style>

    <h1>Data Buku Tamu Bulan <?php echo $namaBulan[$bulanAngka] . " $tahun "; ?>
        <br>
        <?php
            date_default_timezone_set('Asia/Jakarta');
            echo "Per " . date("j F Y, H:i:s");
        ?>
    </h1>
    <h4>*Diurutkan dari tanggal yang terbaru</h4>
    <table id="table">
        <thead>
            <tr>
                <th id="th-td">No</th>
                <th id="th-td">Hari, Tanggal</th>
                <th id="th-td">Jam</th>
                <th id="th-td">Nama Lengkap</th>
                <th id="th-td">No Handphone</th>
                <th id="th-td">Asal</th>
                <th id="th-td">Pesan</th>
                <th id="th-td">Status Perjanjian</th>
                <th id="th-td">Pesan Perjanjian</th>
            </tr>
        </thead>
        <!-- Data untuk file pdf -->
        <tbody>
            <?php 
                while($sqlPrint = mysqli_fetch_assoc($queryDataPrint)) {
                    $tanggal = new DateTime($sqlPrint["tanggal_kedatangan"]);
                    $id++;

                    $pesan = wordwrap($sqlPrint["pesan"], 30, "<br>", true);
                    $pesan_perjanjian = wordwrap($sqlPrint["pesan_perjanjian"], 30, "<br>", true);

                    echo "
                        <tr>
                            <td id='tb-td'>".$id."</td>
                            <td id='tb-td'>".$sqlPrint["hari_kedatangan"].", ".$tanggal->format('d F Y')."</td>
                            <td id='tb-td'>".$sqlPrint["jam_kedatangan"]."</td>
                            <td id='tb-td'>".$sqlPrint["nama_lengkap"]."</td>
                            <td id='tb-td'>".$sqlPrint["no_hp"]."</td>
                            <td id='tb-td'>".$sqlPrint["asal"]."</td>
                            <td id='tb-td'>".$pesan."</td>
                            <td id='tb-td'>".$sqlPrint["status_perjanjian"]."</td>
                            <td id='tb-td'>".$pesan_perjanjian."</td>
                        </tr>
                    ";
                }
            ?>
        </tbody>
    </table>

    <?php 
        require 'vendor/autoload.php';

        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8', 
            'format' => 'A4-L',
            'margin_top' => 25, 
            'margin_bottom' => 25, 
            'margin_left' => 10, 
            'margin_right' => 10
        ]);

        $html = ob_get_contents();
        ob_end_clean();

        $html = mb_convert_encoding($html, 'UTF-8', 'UTF-8');

        $mpdf->WriteHTML($html);
        $mpdf->Output("Data Buku Tamu Bulan $namaBulan[$bulanAngka] $tahun.pdf", "I");
    ?>
</body>
</html>
