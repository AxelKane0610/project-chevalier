<!DOCTYPE html>
<html>
    <head>
        <title>Project Chevalier</title>
        <meta charset="utf-8">
        @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/css/icons/themify-icons.css'])
        
    </head>

    <body>

        <div id="software-tickets-menu">

            <x-common-header title="Invoice Exceptional">
                <li>
                    <form action="/main-menu">
                        <button type="submit"><i class="ti-home"></i>Home</button>
                    </form>
                </li>
                <li>
                    <form action="" class="js-input-required-btn" >
                        @csrf
                        <button type="button"><i class="ti-search"></i>Search</button>
                    </form>
                    
                </li>
                
            </x-common-header>

        </div>
    </body>

</html>