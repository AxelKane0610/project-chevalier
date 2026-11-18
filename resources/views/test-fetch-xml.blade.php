<!DOCTYPE html>
<html>
    <head>

        <title>CENTRA</title>
        <meta charset="utf-8">
        <link rel="icon" type="image/png" href="{{ asset('imgs/logo.png') }}">
        @vite(['resources/js/app.js', 'resources/js/hps-warehouse.js',  'resources/css/icons/themify-icons.css', ])
    </head>

    <body>
        <button type="button" class="js-input-required-btn" data-target="test-fetch-form"><i class="ti-plus"></i> Nhập kho</button>
    </body>

    <x-common-ticket-form title="Test fetch" id="test-fetch-form" action1="/import-asset">
        <label>Case ID</label>
        <input type="text"
            name="hpi_caseid"
            class="form-control"
            >

        <x-slot:footer>
            <button class="ticket-form-body-input" type="submit">Pull</button> 
        </x-slot:footer>
    </x-common-ticket-form>
</html>