console.log("JS LOADED");
const test = document.querySelector('.test-js').addEventListener('click', alertTest);

function alertTest() {
    alert('test');
}

const completeTicketBtn = document.querySelector('#complete-sw-ticket');
    completeTicketBtn.addEventListener('click', function() 
    {
        
        fetch(`/change-ticket-status/{{ $ticket->id }}/status`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ status: 4 }) // Gửi status mới là "Complete"
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Ticket marked as complete!');
                location.reload(); // Tải lại trang để cập nhật trạng thái
            } else {
                alert('Failed to update ticket status.');
            }
        })
        
    });