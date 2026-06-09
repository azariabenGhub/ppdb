<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Global override for window.alert
    const originalAlert = window.alert;
    window.alert = function (message) {
        if (!message) return;
        
        let icon = 'info';
        let title = 'Informasi';
        const msgLower = message.toLowerCase();
        
        if (msgLower.includes('berhasil') || msgLower.includes('sukses') || msgLower.includes('tersimpan') || msgLower.includes('disimpan') || msgLower.includes('diperbarui') || msgLower.includes('dihapus') || msgLower.includes('dikirim') || msgLower.includes('update') || msgLower.includes('ditambahkan')) {
            icon = 'success';
            title = 'Berhasil';
        } else if (msgLower.includes('gagal') || msgLower.includes('error') || msgLower.includes('salah') || msgLower.includes('terjadi kesalahan') || msgLower.includes('tidak tersedia') || msgLower.includes('tidak memiliki akses')) {
            icon = 'error';
            title = 'Gagal';
        } else if (msgLower.includes('wajib') || msgLower.includes('harap') || msgLower.includes('pilih') || msgLower.includes('perhatian') || msgLower.includes('yakin')) {
            icon = 'warning';
            title = 'Perhatian';
        }

        Swal.fire({
            title: title,
            text: message,
            icon: icon,
            customClass: {
                popup: 'custom-swal-popup',
                title: 'custom-swal-title',
                htmlContainer: 'custom-swal-html',
                confirmButton: 'custom-swal-confirm'
            },
            buttonsStyling: false
        });
    };

    // Helper for confirm actions
    window.confirmAction = function(message, confirmCallback) {
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: message,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Lanjutkan!',
            cancelButtonText: 'Batal',
            customClass: {
                popup: 'custom-swal-popup',
                title: 'custom-swal-title',
                htmlContainer: 'custom-swal-html',
                confirmButton: 'custom-swal-confirm',
                cancelButton: 'custom-swal-cancel'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                confirmCallback();
            }
        });
    };
</script>
