<?php 
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