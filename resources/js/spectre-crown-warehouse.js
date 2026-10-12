console.log('js loaded');


document.addEventListener('DOMContentLoaded', function () {

    window.initAjaxTable({
        key: 'all',
        wrapper: 'all-spectre-crown-items-container',
        container: '#all-spectre-crown-items-table-container',
        url: '/spectre-crown-warehouse-menu/filter-all-spectre-crown-items'
    });

});

document.addEventListener('submit', function (e) {
    // Kiểm tra xem form nào đang được submit dựa vào ID
    const formData = new FormData(e.target);
    const url = e.target.getAttribute('action');

    if (e.target && e.target.id === 'create-spectre-crown-warehouse-item-form') {
        e.preventDefault();

        const form = e.target;

        Swal.fire({
            title: 'Bạn có chắc muốn nhập kho item này ?',
            icon: 'warning',
            showCancelButton: true,
            heightAuto: false
        })
        .then((result) => {
            if (!result.isConfirmed) {
                return;
            }

            // Confirm mới loading
            startButtonLoading(form);
            fetch('/import-asset', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                }
            })
            .then(response => response.json())
            .then(new_asset => {

                if (new_asset.success === true) {
                    Swal.fire({
                        title: 'Success!',
                        text: new_asset.message,
                        icon: 'success',
                        confirmButtonText: 'OK',
                        heightAuto: false
                    }).then((result) => {
                        document.querySelector('.ticket-form-overlay').classList.remove('active');
                        e.target.reset();
                        location.reload();
                    });
                    
                } else {
                    Swal.fire({
                        title:'Error',
                        text:new_asset.message,
                        icon:'error',
                        heightAuto: false
                    });
                    stopButtonLoading(form);
                }
                
            })
            .catch(error => console.error(error));
        });
        

    }

    if (e.target && e.target.id === 'asset-export') {
        e.preventDefault();

        const form = e.target;

        Swal.fire({
            title: 'Bạn có chắc muốn xuất kho item này ?',
            icon: 'warning',
            showCancelButton: true,
            heightAuto: false
        })
        .then((result) => {
            if (!result.isConfirmed) {
                return;
            }

            // Confirm mới loading
            startButtonLoading(form);
            fetch(url, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                }
            })
            .then(response => response.json())
            .then(new_asset => {

                if (new_asset.success === true) {
                    Swal.fire({
                        title: 'Success!',
                        text: new_asset.message,
                        icon: 'success',
                        confirmButtonText: 'OK',
                        heightAuto: false
                    }).then((result) => {
                        document.querySelector('.ticket-form-overlay').classList.remove('active');
                        e.target.reset();
                        location.reload();
                    });
                    
                } else {
                    Swal.fire({
                        title:'Error',
                        text:new_asset.message,
                        icon:'error',
                        heightAuto: false
                    });
                    stopButtonLoading(form);
                }
                
            })
            .catch(error => console.error(error));
        });
        

    }

    if (e.target && e.target.id === 'edit-asset-export') {
        e.preventDefault();

        const form = e.target;

        Swal.fire({
            title: 'Bạn có chắc muốn edit item này ?',
            icon: 'warning',
            showCancelButton: true,
            heightAuto: false
        })
        .then((result) => {
            if (!result.isConfirmed) {
                return;
            }

            // Confirm mới loading
            startButtonLoading(form);
            fetch(url, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                }
            })
            .then(response => response.json())
            .then(new_asset => {

                if (new_asset.success === true) {
                    Swal.fire({
                        title: 'Success!',
                        text: new_asset.message,
                        icon: 'success',
                        confirmButtonText: 'OK',
                        heightAuto: false
                    }).then((result) => {
                        document.querySelector('.ticket-form-overlay').classList.remove('active');
                        e.target.reset();
                        location.reload();
                    });
                    
                } else {
                    Swal.fire({
                        title:'Error',
                        text:new_asset.message,
                        icon:'error',
                        heightAuto: false
                    });
                    stopButtonLoading(form);
                }
                
            })
            .catch(error => console.error(error));
        });
        

    }

    if (e.target && e.target.id === 'edit-asset-details') {
        e.preventDefault();

        const form = e.target;

        Swal.fire({
            title: 'Bạn có chắc muốn edit asset này ?',
            icon: 'warning',
            showCancelButton: true,
            heightAuto: false
        })
        .then((result) => {
            if (!result.isConfirmed) {
                return;
            }

            // Confirm mới loading
            startButtonLoading(form);
            fetch(url, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                }
            })
            .then(response => response.json())
            .then(new_asset => {

                if (new_asset.success === true) {
                    Swal.fire({
                        title: 'Success!',
                        text: new_asset.message,
                        icon: 'success',
                        confirmButtonText: 'OK',
                        heightAuto: false
                    }).then((result) => {
                        document.querySelector('.ticket-form-overlay').classList.remove('active');
                        e.target.reset();
                        location.reload();
                    });
                    
                } else {
                    Swal.fire({
                        title:'Error',
                        text:new_asset.message,
                        icon:'error',
                        heightAuto: false
                    });
                    stopButtonLoading(form);
                }
                
            })
            .catch(error => console.error(error));
        });
        

    }
});


document.querySelectorAll('.btn-edit-asset-export').forEach(button => {

    button.addEventListener('click', function () {
        document.getElementById('edit-loan-unit-asset-tag').value =
            this.dataset.assetTag;

        Livewire.dispatch('set-user-owner', {
            userId: this.dataset.userId
        });



        document.getElementById('edit-ticket-receipt').value =
            this.dataset.ticketReceipt;

        document.getElementById('edit-part-request').value =
            this.dataset.partRequest;

        document.getElementById('edit-ct-loaned').value =
            this.dataset.ctLoaned;

    
        document.getElementById('edit-new-ct-return').value =
            this.dataset.newCtReturn;

        document.getElementById('edit-status').value =
            this.dataset.status;

        document.getElementById('edit-original').value =
            this.dataset.original;

        document.getElementById('edit-start-date').value =
            this.dataset.startDate;

        document.getElementById('edit-end-date').value =
            this.dataset.endDate;
        
        document.getElementById('edit-note').value =
            this.dataset.note;

        document.getElementById('edit-asset-export').action =
            '/edit-asset-export/' + this.dataset.id;

        
        // console.log(document.getElementById('edit-thermal-event-part-details').action);

    });

});
