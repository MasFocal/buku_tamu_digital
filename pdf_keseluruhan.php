<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Buku Tamu</title>
    <link rel="stylesheet" href="print.css">
</head>
<body>
    <?php
        include "koneksi.php";
        ob_start();

        $id=0;

        $queryDataPrint = mysqli_query($konek_db, "SELECT * FROM buku_tamu ORDER BY message_id DESC");
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

    <h1>Data Buku Tamu 
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
        $mpdf->Output("Data Buku Tamu Keseluruhan.pdf", "I");
    ?>
</body>
</html>
