    <!-- Modal Token -->
    <?php if ($showModalToken): ?>
        <div class="modal fade show d-block" tabindex="-1" role="dialog" style="background: rgba(0, 0, 0, 0.5);">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">QR Code</h5>
                    </div>
                    <div class="modal-body">
                        <p><?= $modalContent; ?></p>
                    </div>
                    <div class="modal-footer">
                        <form method="post">
                            <input type="hidden" name="id_updatetoken" value="<?= $modalTokenUpdateData; ?>">
                            <button type="submit" name="updateconnect" class="btn btn-secondary">Tutup</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            document.querySelectorAll(".connect-btn").forEach(function(button) {
                button.addEventListener("click", function(event) {
                    event.preventDefault();
                    var modalElement = document.getElementById("ModalToken");
                    var ModalToken = new bootstrap.Modal(modalElement, {
                        backdrop: 'static',
                        keyboard: false
                    });
                    ModalToken.show();
                });
            });
        });
    </script>

    <!-- Modal Token Gagal -->
    <?php if ($showModalGagalToken): ?>
        <div class="modal fade show d-block" tabindex="-1" role="dialog" style="background: rgba(0, 0, 0, 0.5);">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Notifikasi</h5>
                    </div>
                    <div class="modal-body">
                        <p><?= $modalContent; ?></p>
                    </div>
                    <div class="modal-footer">
                        <a href="" class="btn btn-secondary">Tutup</a>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
                button.addEventListener("click", function(event) {
                    event.preventDefault();
                    var modalElement = document.getElementById("modalGagalToken");
                    var modalGagalToken = new bootstrap.Modal(modalElement, {
                        backdrop: 'static',
                        keyboard: false
                    });
                    modalGagalToken.show();
            });
        });
    </script>

    <!-- Modal Berhasil Diputuskan -->
    <div class="modal fade" id="successDisconnectModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Notifikasi</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Data Berhasil Diputuskan
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <?php if ($showSuccessDisconnectModal): ?>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                var myModal = new bootstrap.Modal(document.getElementById('successDisconnectModal'));
                myModal.show();

                var modalElement = document.getElementById('successDisconnectModal');
                modalElement.addEventListener('hidden.bs.modal', function () {
                    window.location.href = 'data_perangkat.php';
                });
            });
        </script>
    <?php endif; ?>

    <!-- Modal Gagal Diputuskan -->
    <div class="modal fade" id="erorrDisconnectModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Notifikasi</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Data Gagal Diputuskan
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <?php if ($showErrorDisconnectModal): ?>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                var myModal = new bootstrap.Modal(document.getElementById('erorrDisconnectModal'));
                myModal.show();
            });
        </script>
    <?php endif; ?>