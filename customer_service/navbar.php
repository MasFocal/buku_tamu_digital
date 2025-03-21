<!-- Top navigation-->
 <nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom">
    <div class="container-fluid">
        <button class="btn btn-secondary" id="sidebarToggle"><i class="bi bi-list"></i></button>
        <h6 class="text-center me-3">
            <script type="text/javascript">
                document.addEventListener("DOMContentLoaded", function() {
                    jam();
                });

                function jam() {
                    var e = document.getElementById('jam');
                    if (e) {
                        var d = new Date(), h, m, s;
                        h = d.getHours();
                        m = set(d.getMinutes());
                        s = set(d.getSeconds());

                        e.innerHTML = h + ':' + m + ':' + s;
                        setTimeout(jam, 1000);
                    }
                }

                function set(e) {
                    return e < 10 ? '0' + e : e;
                }
            </script>
            <?php
                date_default_timezone_set('Asia/Jakarta');
                echo date("l, j F Y");
                echo '<br><span id="jam" name="jam"></span>';
            ?>
        </h6>
    </div>
</nav>