
# Buku Tamu Digital

Website Buku Tamu Digital digunakan agar memudahkan dalam melakukan mencatatan dan pengarsipan data tamu yang berkunjung. Buku Tamu Digital ini berintegrasi dengan API WhatsApp Gateways Fonnte

## Fitur

1. Entitas
- Admin
- Customer Service
- User/Guest

2. Admin
- Mengelola Perangkat
- Mengelola data customer Service
- Mengelola buku tamu
- Mengunduh buku tamu ke pdf

3. Customer Service
- Mengelola buku tamu
- Mengunduh buku tamu ke pdf
- Menerima informasi tamu melalui WhatsApp

4. User/Guest
- Mengisi buku tamu

5. API WhatsApp
- Menambahkan dan Menghapus Perangkat
- Mengirimkan data buku tamu ke WhatsApp setiap mengisi data
- Menghapus data buku tamu

## Instalansi

- Download atau Clone Source Code
- Download dan Install NPM dan Composer
- Download dan Install Bootstrap v5.3
``` 
$ npm i bootstrap@5.3.3
```
- Download dan Install Bootstrap Icons
``` 
$ npm i bootstrap-icons
```
- Download dan Install mPDF
``` 
$ composer require mpdf/mpdf
```
- Registrasi dan Login Fonnte
```
https://fonnte.com/
```

- Upload ```buku_tamu.sql```

- Setting koneksi di ```koneksi.php```

- Login admin
```
username : admin
password : admin
```
- Pada menu Data Perangkat, pilih Atur Token Akun kemudikan isikan Token Akun Fonnte yang didapatkan melalui website Fonnte pada menu Setting > Akun Token.

- Setelah itu Tambah Perangkat dengan memasukan nama dan nomor telepon yang digunakan untuk mengirimkan WhatsApp.

- Kemudian tautkan perangkat dengan klik tombol "Tautkan" dan Scan QR Code WhatsApp.

- Isi nomor handphone WhatsApp penerima pada bagian Customer Service (Nomor yang digunakan tidak boleh sama dengan Nomor Pengiriman)

- Kembali pada halaman buku_tamu/index.php dan isi data. Cek apakah sudah masuk WhatsApp atau belum

## Documentation

[Documentation Fonnte](https://docs.fonnte.com/) \
[Documentation mPDF](https://mpdf.github.io/) \
[Documentation Bootstrap 5.3](https://getbootstrap.com/docs/5.3/getting-started/introduction/)

## License

[MIT](https://choosealicense.com/licenses/mit/)

