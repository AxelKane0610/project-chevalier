<div class="dialog-box">
    <div class="dialog-box-content">
        <div class="dialog-box-header">
            <h2>{{ $title }}</h2>
            <i class="ti-close js-close-dialog"></i>
            
        </div>

        <div class="dialog-box-body">
            {{ $slot }} <!-- Nội dung của dialog sẽ được truyền vào đây -->
        </div>
    </div>
</div>  

<script>
    const dialog = document.querySelector('.dialog-box')
    const closeBtn = document.querySelector('.js-close-dialog')

    function showDialog() {
        if(dialog) dialog.classList.add('active')
    }

    function hideDialog() {
        if(dialog) dialog.classList.remove('active')
    }

    if (closeBtn) {
        closeBtn.addEventListener('click', hideDialog)
    }
</script>