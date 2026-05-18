import Swal from "sweetalert2";

console.log("JS LOADED");



document.addEventListener('submit', function (e) {
    // Kiểm tra xem form nào đang được submit dựa vào ID
    
        

        const formData = new FormData(e.target);
        const url = e.target.getAttribute('action');

    if (e.target && e.target.id === 'close-ticket-form') 
    {
        e.preventDefault();
        fetch(url, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
            }
        })
        
        
                .then(response => response.json()) 
                .then(ticket_complete_response => { 
                    console.log(ticket_complete_response);
                    Swal.fire({
                        title: 'Success!',
                        text: ticket_complete_response.message,
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        document.querySelector('.ticket-form-overlay').classList.remove('active');
                        e.target.reset();
                        location.reload();
                    });
                    
                    
                    
                })
                .catch(error => {
                    Swal.fire({
                        title: 'Error!',
                        text: error,
                        icon: 'error',
                        confirmButtonText: 'OK'
                    })
                    console.error(error)
                });

    }       

    if (e.target && e.target.id === 'send-approval-form') 
    {
        e.preventDefault();
        fetch(url, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
            }
        })
            .then(response => response.json()) 
            .then(ticket_approval_response => { 
                console.log(ticket_approval_response);
                Swal.fire({
                    title: 'Success!',
                    text: ticket_approval_response.message,
                    icon: 'success',
                    confirmButtonText: 'OK'
                }).then((result) => {
                    document.querySelector('.ticket-form-overlay').classList.remove('active');
                    e.target.reset();
                    location.reload();
                });
                
                
                
            })
            .catch(error => {
                Swal.fire({
                    title: 'Error!',
                    text: error,
                    icon: 'error',
                    confirmButtonText: 'OK'
                })
                console.error(error)
            });

    }

    if (e.target && e.target.id === 'approve-ticket-form')
    {
        e.preventDefault();
        Swal.fire({
            title: 'Are you sure?',
            text: "You are about to approve this ticket.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, approve it!'
        }).then((result) => {
            if (result.isConfirmed) {
                // Proceed with the approval logic
                fetch(url, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                    }
                })
                .then(response => response.json())
                .then(ticket_approval_response => {
                    console.log(ticket_approval_response);
                    Swal.fire({
                        title: 'Success!',
                        text: ticket_approval_response.message,
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        document.querySelector('.ticket-form-overlay').classList.remove('active');
                        e.target.reset();
                        location.reload();
                    });
                })
                .catch(error => {
                    Swal.fire({
                        title: 'Error!',
                        text: error,
                        icon: 'error',
                        confirmButtonText: 'OK'
                    })
                    console.error(error)
                });

            }
        });

    
    }
});