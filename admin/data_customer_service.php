<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Customer Service</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <?php 
        include "../session/session_login.php";
        $showErrorSearchModal = false;
        $showSuccessModal = false;
        $showErrorModal = false;
        
        if (isset($_POST["edit"])) {
            $id = $_POST["username"];
            $encoded_id = base64_encode($id);
            header("location: edit_customer_service.php?id=" . urlencode($encoded_id));
        }
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['hapus'])) {
            $id = $_POST['username'];
            $result = mysqli_query($konek_db, "DELETE FROM customer_service WHERE username='$id'");

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
        <div class="container-fluid">
            <h1 class="mt-4 text-center">Data Customer Service</h1>
            <a href="tambah_customer_service.php">
                <button class="btn btn-primary mb-4 mt-4"><i class='bi bi-plus-lg me-2'></i>Tambah Customer Service</button>
            </a>

            <?php
                $query_total = mysqli_query($konek_db, "SELECT COUNT(*) FROM customer_service");
                $data_total = mysqli_fetch_row($query_total);
                $total_rows = $data_total[0]; 
            ?>

            <!-- Form Pencarian -->
            <form class="d-flex" role="search" method="GET">
                <input class="form-control me-2 w-50" type="search" placeholder="Search" aria-label="Search" name="search" <?= ($total_rows == 0) ? 'disabled' : ''; ?>>
                <button class="btn btn-success" type="submit" <?= ($total_rows == 0) ? 'disabled' : ''; ?>><i class='bi bi-search'></i></button>
            </form>

            <!-- Tabel Data Customer Service -->
            <table class="table table-bordered table-striped mt-4">
                <tr class="text-center">
                    <th>No</th>
                    <th>Username</th>
                    <th>Nama Customer Service</th>
                    <th>No Handphone</th>
                    <th>Action</th>
                </tr>
                <?php
                    $limit = 4;
                    $page_number = isset($_GET["page"]) ? $_GET["page"] : 1;
                    $initial_page = ($page_number - 1) * $limit;

                    $where = "";
                    if (isset($_GET['search']) && !empty($_GET['search'])) {
                        $search = mysqli_real_escape_string($konek_db, $_GET['search']);
                        $where = " WHERE nama LIKE '%$search%' OR username LIKE '%$search%'";
                    }

                    $querydata = mysqli_query($konek_db, "SELECT * FROM customer_service $where ORDER BY username ASC LIMIT $initial_page, $limit");
                    $id = $initial_page;
                    $hitung = mysqli_num_rows($querydata);

                    if ($hitung == 0) {
                        echo "<tr><td colspan='5' class='text-center'>Data belum ada</td></tr>";
                    } else {
                        while ($sqldata = mysqli_fetch_array($querydata)) {
                            $id++;
                            echo "
                            <form action='' method='POST'>
                                <input type='hidden' name='username' value='{$sqldata['username']}'>
                                <tr>
                                    <td class='text-center'>{$id}</td>
                                    <td>{$sqldata['username']}</td>
                                    <td>{$sqldata['nama']}</td>
                                    <td>{$sqldata['no_hp']}</td>
                                    <td class='text-center'>
                                        <button name='edit' class='btn btn-warning me-2'><i class='bi bi-pencil me-2'></i>Edit</button>
                                        <button name='hapus' class='btn btn-danger'><i class='bi bi-trash text-light me-2'></i>Hapus</button> 
                                    </td>
                                </tr>
                            </form>";
                        }
                    }
                ?>
            </table>

            <!-- Pagination -->
            <ul class="pagination justify-content-center mb-5">
                <?php 
                    $total_pages = ceil($total_rows / $limit);

                    if ($page_number >= 2) {
                        echo "<li class='page-item'><a class='page-link' href='data_customer_service.php?page=" . ($page_number - 1) . "'>Prev</a></li>";
                    }

                    for ($i = 1; $i <= $total_pages; $i++) {
                        echo "<li class='page-item'><a class='page-link " . ($i == $page_number ? "active" : "") . "' href='data_customer_service.php?page={$i}'>{$i}</a></li>";
                    }

                    if ($page_number < $total_pages) {
                        echo "<li class='page-item'><a class='page-link' href='data_customer_service.php?page=" . ($page_number + 1) . "'>Next</a></li>";
                    }
                ?>
            </ul>
        </div>
    </div>

    <!-- Modal Notifikasi -->
    <div class="modal fade" id="errorSearchModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Notifikasi</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">Data Tidak Ditemukan</div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <?php if ($showErrorSearchModal): ?>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var myModal = new bootstrap.Modal(document.getElementById('errorSearchModal'));
            myModal.show();
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
                <div class="modal-header">
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
