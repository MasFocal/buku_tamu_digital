<link rel="stylesheet" href="../css_dashboard/styles.css">
<link rel="stylesheet" href="../bootstrap/css/bootstrap.css">
<link rel="stylesheet" href="../bootstrap-icons/font/bootstrap-icons.css">
<script src="../bootstrap/js/bootstrap.bundle.min.js"></script>
<link href="https://fonts.googleapis.com/css?family=Montserrat&display=swap" rel="stylesheet">

<script>
    window.addEventListener('DOMContentLoaded', event => {

        const sidebarToggle = document.body.querySelector('#sidebarToggle');
        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', event => {
                event.preventDefault();
                document.body.classList.toggle('sb-sidenav-toggled');
                localStorage.setItem('sb|sidebar-toggle', document.body.classList.contains('sb-sidenav-toggled'));
            });
        }

    });
</script>

<div class="border-end bg-white" id="sidebar-wrapper">
    <div class="sidebar-heading border-bottom bg-light">
        <a href="index.php" class="link-dark link-offset-2 link-underline link-underline-opacity-0 fw-bold"><i class="bi bi-book me-2 text-warning"></i>Buku Tamu</a>
    </div>
    <div class="list-group list-group-flush">
        <a class="list-group-item list-group-item-action list-group-item-light p-3" href="index.php"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a>
        <a class="list-group-item list-group-item-action list-group-item-light p-3" href="profile.php"><i class="bi bi-person me-2"></i>Profile</a>
        <a class="list-group-item list-group-item-action list-group-item-light p-3" href="data_customer_service.php"><i class="bi bi-people me-2"></i>Data Customer Service</a>
        <a class="list-group-item list-group-item-action list-group-item-light p-3" href="data_buku_tamu.php"><i class="bi bi-book me-2"></i>Data Buku Tamu</a>
        <a class="list-group-item list-group-item-action list-group-item-light p-3" href="data_perangkat.php"><i class="bi bi-phone me-2"></i>Data Perangkat</a>
        <div class="text-center mt-5">
            <a class="" href="../session/session_logout.php"><button class="btn btn-danger pt-2 pb-2 pe-3 ps-3 border-dark border-2">LOGOUT</button></a>
        </div>
    </div>
</div>