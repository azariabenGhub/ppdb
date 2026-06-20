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

    // Global loader state
    let activeWriteRequests = 0;
    
    function showLoadingSpinner() {
        Swal.fire({
            title: 'Mohon Tunggu',
            text: 'Sedang memproses...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
    }

    function hideLoadingSpinner() {
        if (Swal.isVisible() && Swal.isLoading()) {
            Swal.close();
        }
    }

    // Intercept fetch requests (POST, PUT, PATCH, DELETE)
    const originalFetch = window.fetch;
    window.fetch = async function(...args) {
        let url = args[0];
        let options = args[1] || {};
        let method = (options.method || 'GET').toUpperCase();
        
        let isWriteRequest = method !== 'GET';
        
        if (isWriteRequest) {
            activeWriteRequests++;
            if (activeWriteRequests === 1) {
                showLoadingSpinner();
            }
        }
        
        try {
            const response = await originalFetch(...args);
            if (isWriteRequest) {
                activeWriteRequests--;
                if (activeWriteRequests <= 0) {
                    activeWriteRequests = 0;
                    hideLoadingSpinner();
                }
            }
            return response;
        } catch (error) {
            if (isWriteRequest) {
                activeWriteRequests--;
                if (activeWriteRequests <= 0) {
                    activeWriteRequests = 0;
                    hideLoadingSpinner();
                }
            }
            throw error;
        }
    };

    // Global listener for traditional form submits (non-AJAX)
    document.addEventListener('submit', function(event) {
        setTimeout(() => {
            if (!event.defaultPrevented) {
                showLoadingSpinner();
            }
        }, 50);
    });
</script>
