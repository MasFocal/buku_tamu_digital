    <!-- Modal Hapus -->
    <?php if ($showModalDelete): ?>
        <div class="modal fade show d-block" tabindex="-1" role="dialog" style="background: rgba(0, 0, 0, 0.5);">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Masukkan Kode OTP</h5>
                    </div>
                    <form method="post">
                        <div class="modal-body">
                            <input type="hidden" name="id_token" value="<?= $modalTokenDelete; ?>">
                            <input type="hidden" name="id_no_hp" value="<?= $modalNoHPDelete; ?>">
                            <label for="otp">Kode OTP:</label>
                            <input type="text" name="otp" id="otp" class="form-control mb-4" required>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" name="submit_otp" class="btn btn-success"><i class="bi bi-send me-2"></i>Kirim</button>
                            <a href="data_perangkat.php" class="btn btn-secondary"><i class="bi bi-arrow-return-left me-2"></i>Kembali</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php endif; ?>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var modalElement = document.getElementById("successModal");
            var successModal = new bootstrap.Modal(modalElement, {
                backdrop: 'static',
                keyboard: false
            });

            document.querySelectorAll(".connect-btn").forEach(function(button) {
                button.addEventListener("click", function(event) {
                    event.preventDefault();
                    successModal.show();
                });
            });
        });
    </script>

    <!-- Modal Berhasil Dihapus -->
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
    <?php if ($showSuccessDeleteModal): ?>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                var myModal = new bootstrap.Modal(document.getElementById('successModal'));
                myModal.show();

                var modalElement = document.getElementById('successModal');
                modalElement.addEventListener('hidden.bs.modal', function () {
                    window.location.href = 'data_perangkat.php';
                });
            });
        </script>
    <?php endif; ?>

    <!-- Modal Gagal Dihapus -->
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
    <?php if ($showErrorDeleteModal): ?>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                var myModal = new bootstrap.Modal(document.getElementById('erorrModal'));
                myModal.show();
            });
        </script>
    <?php endif; ?>