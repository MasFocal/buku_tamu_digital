<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link rel="stylesheet" href="bootstrap/css/bootstrap.css">
    <link rel="stylesheet" href="bootstrap-icons/font/bootstrap-icons.css">
    <script src="bootstrap/js/bootstrap.bundle.min.js"></script>
</head>
<body>
    <?php 
        session_start();
        include 'koneksi.php';
        $showModal = false;

        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['username']) && isset($_POST['password'])) {
            $username = $_POST['username'];
            $password = $_POST['password'];

            $role = '';
            $isLoggedIn = false;

            if (!$isLoggedIn) {
                $query_admin = mysqli_query($konek_db, "SELECT * FROM admin WHERE username = '$username'");

                if ($row = mysqli_fetch_assoc($query_admin)) {
                    if (password_verify($password, $row['password'])) {
                        $role = 'admin';
                        $isLoggedIn = true;
                    }
                }
            }

            if (!$isLoggedIn) {
                $query_cs = mysqli_query($konek_db, "SELECT * FROM customer_service WHERE username = '$username'");

                if ($row = mysqli_fetch_assoc($query_cs)) {
                    if (password_verify($password, $row['password'])) {
                        $role = 'customer_service';
                        $isLoggedIn = true;
                    }
                }
            }

            if ($isLoggedIn) {
                $_SESSION['username'] = $username;
                $_SESSION['role'] = $role;

                if ($role == 'admin') {
                    header("Location: admin/index.php");
                } else {
                    header("Location: customer_service/index.php");
                }
                exit();
            } else {
                $showModal = isset($_POST['username']);

            }
        }
    ?>
    <section class="vh-100 d-flex align-items-center justify-content-center" style="background-color: #8fc4b7;">
        <div class="container">
            <div class="row d-flex justify-content-center">
                <div class="col-md-8 col-lg-6">
                    <div class="card" style="border-radius: 1rem; max-width: 500px; margin: auto;">
                        <div class="card-body p-4 p-lg-5 text-black">
                            <form method="POST">
                                <div class="d-flex align-items-center mb-3 pb-1">
                                    <i class="bi bi-book h1 me-3" style="color: #ff6219;"></i>
                                    <span class="h1 fw-bold">Buku Tamu</span>
                                </div>
                                <h5 class="fw-normal mb-3 pb-3" style="letter-spacing: 1px;">Sign into your account</h5>
                                <div data-mdb-input-init class="form-outline mb-4">
                                    <label class="form-label" for="form2Example17" >Username</label>
                                    <input type="text" id="form2Example17" class="form-control form-control-lg" name="username" placeholder="Masukan Username"/>
                                </div>
                                <div data-mdb-input-init class="form-outline mb-4">
                                    <label class="form-label" for="form2Example27" >Password</label>
                                    <input type="password" id="form2Example27" class="form-control form-control-lg" name="password" placeholder="Masukan Password"/>
                                </div>
                                <div class="pt-1">
                                    <button data-mdb-button-init data-mdb-ripple-init class="btn btn-dark btn-lg btn-block" type="submit">Login</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Modal Salah Username or Password -->
    <div class="modal fade" id="loginErrorModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Notifikasi</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Maaf Username atau Password Salah
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <?php if ($showModal): ?>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                var myModal = new bootstrap.Modal(document.getElementById('loginErrorModal'));
                myModal.show();
            });
        </script>
    <?php endif; ?>
</body>
</html>