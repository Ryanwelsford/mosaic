<!DOCTYPE html>
<html lang="en">
<head>

    <link rel="stylesheet" href="/styles/styles.css">
    <link rel="icon" href="/images/phr-logo.svg" sizes="16x16" type="image/png">
    <title>
        @hasSection("title")
            @yield("title")
        @else
            {{ "Pizza Hut Mosaic" }}
        @endif
    </title>

</head>

<body>

    <nav class="main-nav">
        <div class="logo-mobile-holder">
            <div class="logo-container"><image class="logo" src="/images/phr-logo.svg"></image></div>
            <nav class="mobile-nav">
                <button onclick="OpenModalNav(event)"href=""><image src="/images/menu-4-64.png"></image></button>
            </nav>

        </div>

        <?php
        $adminNav = false;
            if(auth()->user() !== null && auth()->user()->isAdmin()) {
                $adminNav = true;
            }

            ?>
        <ul>
        <!--In theory this is login check for admin then display admin tools-->
        @if ($adminNav)
            <li>
                <div>
                <a class=""href="{{ route('product.home') }}">Products</a>
                    <button onclick="openNavTab(event, 'products')" class="main-nav-button">
                        <image src="/images/side-arrow.png"></image>
                    </button>
                </div>


                <ul id="products" class="main-nav-tab side-bar-tab">
                    <li><a href="{{ route('product.new') }}">New Product</a></li>
                    <li><a href="{{ route('product.view') }}">Edit Product</a></li>
                    <li><a href="{{ route('product.view') }}">View Products</a></li>
                    <li><a href="">Product Reports</a></li>
                </ul>

            </li>

            <li>
                <div>
                   <a class=""href="{{ route('menu.home') }}">Menus</a>
                    <button onclick="openNavTab(event, 'menus')" class="main-nav-button">
                        <image src="/images/side-arrow.png"></image>
                    </button>
                </div>


                <ul id="menus" class="main-nav-tab side-bar-tab">
                    <li><a href="{{ route("menu.new") }}">New Menu</a></li>
                    <li><a href="{{ route("menu.view") }}">Edit Menu</a></li>
                    <li><a href="{{ route("menu.view") }}">View Menus</a></li>
                    <li><a href="{{ route("menu.view") }}">Copy Menu</a></li>
                    <li><a href="">Menu Reports</a></li>
                </ul>

            </li>

            <li>
                <div>
                   <a class=""href="{{ route('wastelist.home') }}">Waste Lists</a>
                    <button onclick="openNavTab(event, 'wastelists')" class="main-nav-button">
                        <image src="/images/side-arrow.png"></image>
                    </button>
                </div>


                <ul id="wastelists" class="main-nav-tab side-bar-tab">
                    <li><a href="{{ route('wastelist.new') }}">New Waste List</a></li>
                    <li><a href="{{ route('wastelist.view') }}">Edit Waste List</a></li>
                    <li><a href="{{ route('wastelist.view') }}">View Waste Lists</a></li>
                </ul>

            </li>

            <li>
                <div>
                   <a class=""href="{{ route('store.home') }}">Stores</a>
                    <button onclick="openNavTab(event, 'stores')" class="main-nav-button">
                        <image src="/images/side-arrow.png"></image>
                    </button>
                </div>


                <ul id="stores" class="main-nav-tab side-bar-tab">
                    <li><a href="{{ route("store.new") }}">New Store</a></li>
                    <li><a href="{{ route("store.view") }}">Edit Store</a></li>
                    <li><a href="{{ route("store.view") }}">View Stores</a></li>
                    <li><a href="">Store Reports</a></li>
                </ul>

            </li>
        @else
        <!--In theory this is login check for store then display store tools-->
            <li>
                <div>
                   <a class="active"href="{{ route('inventory.home') }}">Inventory</a>
                    <button onclick="openNavTab(event, 'inventory')" class="main-nav-button">
                        <image src="/images/side-arrow.png"></image>
                    </button>
                </div>


                <ul id="inventory" class="main-nav-tab side-bar-tab">
                    <li><a href="">New Count</a></li>
                    <li><a href="">Edit Count</a></li>
                    <li><a href="">View Count</a></li>
                    <li><a href="">Inventory Reports</a></li>
                </ul>

            </li>

            <li>
                <div>
                    <a href="{{ route('order.home') }}">Ordering</a>
                    <button onclick="openNavTab(event, 'ordering')" class="main-nav-button">
                        <image src="/images/side-arrow.png"></image>
                    </button>
                </div>


                <ul id="ordering" class="main-nav-tab side-bar-tab">
                    <li><a href="">New Order</a></li>
                    <li><a href="">Edit Order</a></li>
                    <li><a href="">View Orders</a></li>
                    <li><a href="">Order Reports</a></li>
                </ul>

            </li>

            <li>
                <div>
                    <a href="{{ route('receiving.home') }}">Receiving</a>
                    <button onclick="openNavTab(event, 'receiving')" class="main-nav-button">
                        <image src="/images/side-arrow.png"></image>
                    </button>
                </div>


                <ul id="receiving" class="main-nav-tab side-bar-tab">
                    <li><a href="">Test</a></li>
                    <li><a href="">Test</a></li>
                    <li><a href="">Test</a></li>
                </ul>
            </li>

            <li>
                <div>
                    <a href="{{ route("waste.home") }}">Waste</a>
                    <button onclick="openNavTab(event, 'Waste')" class="main-nav-button">
                            <image src="/images/side-arrow.png"></image>
                    </button>
                </div>


                <ul id="Waste" class="main-nav-tab side-bar-tab">
                    <li><a href="">Test</a></li>
                    <li><a href="">Test</a></li>
                    <li><a href="">Test</a></li>
                </ul>
            </li>

            <li>
                <div>
                    <a href="{{ route('forecasting.home') }}">Forecasting</a>
                    <button onclick="openNavTab(event, 'Forecasting')" class="main-nav-button">
                        <image src="/images/side-arrow.png"></image>
                    </button>
                </div>


                <ul id="Forecasting" class="main-nav-tab side-bar-tab">
                    <li><a href="{{ route("forecasting.date") }}">New Forecast</a></li>
                    <li><a href="">Edit Forecast</a></li>
                    <li><a href="">View Forecast</a></li>
                    <li><a href="">Update Actuals</a></li>
                    <li><a href="">Forecasting Reports</a></li>
                </ul>
            </li>

            <li>
                <div>
                    <a href="{{ route('soh.home') }}">Stock-on-hand</a>
                    <button onclick="openNavTab(event, 'SOH')" class="main-nav-button">
                        <image src="/images/side-arrow.png"></image>
                    </button>
                </div>


                <ul id="SOH" class="main-nav-tab side-bar-tab">
                    <li><a href="">New Soh Count</a></li>
                    <li><a href="">Edit Soh Count</a></li>
                    <li><a href="">View Soh</a></li>
                    <li><a href="">Soh Reports</a></li>
                </ul>
            </li>

            <li>
                <div>
                    <a href=" {{ route('dates.home') }}">Dates</a>
                    <button onclick="openNavTab(event, 'Dates')" class="main-nav-button">
                        <image src="/images/side-arrow.png"></image>
                    </button>
                </div>
                <ul id="Dates" class="main-nav-tab side-bar-tab">
                    <li><a href="">New Date</a></li>
                    <li><a href="">Edit Date</a></li>
                    <li><a href="">Search Dates</a></li>
                    <li><a href="">View Shelf Life Chart</a></li>
                </ul>
            </li>

            <li>
                <div>
                    <a href=" {{ route('product.home') }}">Products</a>
                    <button onclick="openNavTab(event, 'Products')" class="main-nav-button">
                    <image id="Products_arrow"  src="/images/side-arrow.png"></image>
                    </button>
                </div>


                <ul id="Products" class="main-nav-tab side-bar-tab">
                    <li><a href="{{ route('product.new') }}">New Product</a></li>
                    <li><a href="">Edit Product</a></li>
                    <li><a href="{{ route('product.view') }}">View Product</a></li>
                    <li><a href="">Product Reports</a></li>
                </ul>
            </li>

    @endif
        </ul>
    </nav>

    <main>
        <!--should move this around so tool bar is in its own section with maybe a yield tools?-->
        <div class="tool-bar">
            <div class="tools-section">Tool a tool b <img class="tool-icon" src = '/images/icons/search-3-48.png'></div>
            <div class="title">
                @hasSection ('title')
                    @yield('title')
                @endif
            </div>
            <div class ="user-section">
                @auth
                    {{ auth()->user()->getCorrectName() }}
                @endauth

                @guest
                    {{ "Not Logged" }}
                @endguest
                <button onclick="openNavTab(event, 'Users', 'rotated')" class="main-nav-button tool-button"><image class="" id="Products_arrow"  src="/images/side-arrow.png"></image></button>
                <ul id="Users" class="main-nav-tab user-tab">
                    @auth
                    <a href="{{ route('logout.index') }}"><li>Logout</li></a>
                    <a href=""><li>Settings</li></a>
                    @endauth

                    @guest
                    <a href="{{ route('login') }}"><li>Login</li></a>
                    @endguest
                </ul>
            </div>
        </div>

        @yield('content')

        <!--need to create admin modal as well-->
        <section class="modal" id="mobile-modal-nav">
            <div class="modal-internal">
                <div class="modal-title">Dashboard <button onclick="OpenModalNav(event)" class="close-X">X</button></div>
                <div class="modal-content">
                    <div class="grid-2-col modal-center">
                        @if(!$adminNav)
                        <a href="{{ route('inventory.home') }}"><button class="ph-button ph-button-standard">Inventory</button></a>
                        <a href="{{ route('order.home') }}"><button class="ph-button ph-button-standard">Ordering</button></a>
                        <a href="{{ route('receiving.home') }}"><button class="ph-button ph-button-standard">Receiving</button></a>
                        <a href="{{ route('waste.home') }}"><button class="ph-button ph-button-standard">Waste</button></a>
                        <a href="{{ route('forecasting.home') }}"><button class="ph-button ph-button-standard">Forecasting</button></a>
                        <a href="{{ route('soh.home') }}"><button class="ph-button ph-button-standard">SOH</button></a>
                        <a href="{{ route('dates.home') }}"><button class="ph-button ph-button-standard">Dates</button></a>

                        @else
                        <a href="{{ route('product.home') }}"><button class="ph-button ph-button-standard">Products</button></a>
                        <a href="{{ route('menu.home') }}"><button class="ph-button ph-button-standard">Menu</button></a>
                        <a href="{{ route('wastelist.home') }}"><button class="ph-button ph-button-standard">Waste List</button></a>
                        <a href="{{ route('store.home') }}"><button class="ph-button ph-button-standard">Store</button></a>

                        @endif
                        <a href="{{ "/test" }}"><button class="ph-button ph-button-standard">User</button></a>
                        <div class="center-button-container"><button class="ph-button ph-button-important" onclick="OpenModalNav(event)">Close</button></div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <div class = "footer">
        <p>Pizza Hut Restaurants 2021 &copy;</p>
    </div>


</body>
</html>
<script>

    //https://www.w3schools.com/howto/howto_js_tabs.asp
    function openNavTab(evt, tabId, classN = "rotated") {
        // Declare all variables
        let i, tabcontent, tablinks, alreadyOpen;

        alreadyOpen = evt.currentTarget.classList.contains(classN);
        classN = " "+classN;
        // Get all elements with class="tabcontent" and hide them
        tabs = document.getElementsByClassName("main-nav-tab");
        for (i = 0; i < tabs.length; i++) {
            tabs[i].style.display = "none";
        }

        // Get all elements with class="tablinks" and remove the class "active"
        tabbuttons = document.getElementsByClassName("main-nav-button");
        for (i = 0; i < tabbuttons.length; i++) {
            tabbuttons[i].className = tabbuttons[i].className.replace(classN, "");
        }

        // Show the current tab, and add an "active" class to the button that opened the tab
        if(alreadyOpen) {
            document.getElementById(tabId).style.display = "none";
        }
        else {
            document.getElementById(tabId).style.display = "block";
            evt.currentTarget.className += " rotated";
        }




    }

    function OpenModalNav(evt) {

        //open mobile nav specifcally
        let modal = document.getElementById('mobile-modal-nav');

        //if modal is already open
        if(modal.style.display == "flex") {
            modal.style.display = "none";
        }
        //otherwise open modal
        else {
            //changed to display flex in order to center modal in page
            modal.style.display = "flex";
        }

    }

    //add click event to entire window
    window.onclick = function(event) {
    let modal = document.getElementsByClassName('modal');
    //idea is to have this work for all modals rather than just the mobile nav ?
    //loop through the modals on page, if a click is detected on open modals background, i.e. not he modal content close modal
    for (i = 0; i< modal.length; i++) {
        if(event.target == modal[i]) {
            modal[i].style.display = "none";
        }
    }

}
    //on button click scroll to top of page.
    function scrollUpTop() {

            //i.e. desktop modes
            if(window.innerWidth > 1000) {
                //scrollTop is for safari other is for ie chrome etc
                document.body.scrollTop = 0;
                document.documentElement.scrollTop = 0;
            }
            else {
                //when smaller screens therefore in mobile format scroll to top of page (logo)
                let logo = document.querySelector(".logo");
                logo.scrollIntoView({behavior: 'smooth'});
            }
        }


    function closeDiv(event, type="DIV") {

        let parentElement, clickedElement = event.target;
        parentElement = clickedElement.parentNode;

        while(parentElement.tagName !== type) {
            parentElement = parentElement.parentNode;
        }

        parentElement.remove();
    }
</script>
