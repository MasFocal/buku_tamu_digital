<?php
    include('koneksi.php');
    require 'vendor/autoload.php';

    $tahun      = $_GET['tahun'];

    use PhpOffice\PhpSpreadsheet\Spreadsheet;
    use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
    use PhpOffice\PhpSpreadsheet\Style\Alignment;
    use PhpOffice\PhpSpreadsheet\Style\Fill;
    use PhpOffice\PhpSpreadsheet\Style\Border;
    use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
    use PhpOffice\PhpSpreadsheet\Cell\DataType;

    // Inisialisasi Spreadsheet
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Ukuran Lebar Kolom
    $sheet->getColumnDimension('A')->setWidth(30, 'px'); 
    $sheet->getColumnDimension('B')->setWidth(200, 'px'); 
    $sheet->getColumnDimension('C')->setWidth(55, 'px'); 
    $sheet->getColumnDimension('D')->setWidth(200, 'px'); 
    $sheet->getColumnDimension('E')->setWidth(116, 'px'); 
    $sheet->getColumnDimension('F')->setWidth(240, 'px'); 
    $sheet->getColumnDimension('G')->setWidth(250, 'px'); 
    $sheet->getColumnDimension('H')->setWidth(130, 'px'); 
    $sheet->getColumnDimension('I')->setWidth(250, 'px');

    // Judul (A1:J3)
    $sheet->mergeCells('A1:I3');
    $sheet->setCellValue('A1', 'Data Buku Tamu Tahun ' .$tahun .'');
    $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle('A1')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
    $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(28);

    // Sub Judul (A4:J5)
    date_default_timezone_set('Asia/Jakarta');
    $sheet->mergeCells('A4:I5');
    $sheet->setCellValue('A4', 'Per ' . date('d F Y') . ' ' . date('H:i:s'));
    $sheet->getStyle('A4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle('A4')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
    $sheet->getStyle('A4')->getFont()->setBold(true)->setSize(14);

    // Header tabel (baris 6)
    $sheet->setCellValue('A6', 'NO');
    $sheet->setCellValue('B6', 'HARI, TANGGAL');
    $sheet->setCellValue('C6', 'JAM');
    $sheet->setCellValue('D6', 'NAMA LENGKAP');
    $sheet->setCellValue('E6', 'NO HANDPHONE');
    $sheet->setCellValue('F6', 'ASAL');
    $sheet->setCellValue('G6', 'PESAN');
    $sheet->setCellValue('H6', 'STATUS PERJANJIAN');
    $sheet->setCellValue('I6', 'PESAN JANJIAN');

    // Format header
    $sheet->getStyle('A6:I6')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle('A6:I6')->getFont()->setBold(true);
    $sheet->getStyle('A6:I6')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('F2F2F2');

    // Mulai isi data dari baris ke-7
    $i = 7;
    $no = 1;

    $queryDataPrint = mysqli_query($konek_db, "SELECT * FROM buku_tamu WHERE YEAR(tanggal_kedatangan) = '$tahun' ORDER BY message_id DESC
        ");

    while ($d = mysqli_fetch_array($queryDataPrint)) {
        $tanggal = new DateTime($d["tanggal_kedatangan"]);
        //$id++;

        $pesan = wordwrap($d["pesan"], 30, "\n", true);
        $pesan_perjanjian = wordwrap($d["pesan_perjanjian"], 30, "\n", true);

        //$sheet->setCellValueExplicit('G6', $pesan, DataType::TYPE_STRING);
        //$sheet->setCellValueExplicit('I6', $pesan, DataType::TYPE_STRING);

        $sheet->setCellValue('A'.$i, $no++);
        $sheet->setCellValue('B'.$i, $d['hari_kedatangan']. ", " . $tanggal->format('d F Y'));
        $sheet->setCellValue('C'.$i, $d['jam_kedatangan']);
        $sheet->setCellValue('D'.$i, $d['nama_lengkap']);
        $sheet->setCellValue('E'.$i, $d['no_hp']);
        $sheet->setCellValue('F'.$i, $d['asal']);
        $sheet->setCellValueExplicit('G'.$i, $pesan, DataType::TYPE_STRING);
        $sheet->setCellValue('H'.$i, $d['status_perjanjian']);
        $sheet->setCellValueExplicit('I'.$i, $pesan_perjanjian, DataType::TYPE_STRING);

        $sheet->getStyle("A$i:I$i")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->getStyle("A$i:I$i")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('G'.$i)->getAlignment()->setWrapText(true);
        $sheet->getStyle('I'.$i)->getAlignment()->setWrapText(true);

        $sheet->getRowDimension($i)->setRowHeight(-1);

        $i++;
    }

    // Border semua area dari A6 sampai baris terakhir data
    $lastRow = $i - 1;
    $sheet->getStyle("A6:I$lastRow")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

    // Output ke Excel
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="Data Buku Tamu Tahun ' .$tahun .'.xlsx"');
    header('Cache-Control: max-age=0');

    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    exit;
?>