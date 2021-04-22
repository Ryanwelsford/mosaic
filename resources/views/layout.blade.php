<!DOCTYPE html>
<html lang="en">
<head>

    <link rel="stylesheet" href="/styles/styles.css">
    <link rel="icon" href="/images/phr-logo.svg" sizes="16x16" type="image/png">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.3/css/all.css" integrity="sha384-SZXxX4whJ79/gErwcOYf+zWLeJdY/qpuqC4cAa9rOGUstPomtqpuNWT9wdPEn2fk" crossorigin="anonymous">
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
                    <button onclick="openNavTab(event, 'product')" class="main-nav-button">
                        <image src="/images/side-arrow.png"></image>
                    </button>
                </div>


                <ul id="product" class="main-nav-tab side-bar-tab">
                    <li><a href="{{ route('product.new') }}">New Product</a></li>
                    <li><a href="{{ route('product.view') }}">Edit Product</a></li>
                    <li><a href="{{ route('product.view') }}">View Products</a></li>
                    <li><a href="">Product Reports</a></li>
                </ul>

            </li>

            <li>
                <div>
                   <a class=""href="{{ route('menu.home') }}">Menus</a>
                    <button onclick="openNavTab(event, 'menu')" class="main-nav-button">
                        <image src="/images/side-arrow.png"></image>
                    </button>
                </div>


                <ul id="menu" class="main-nav-tab side-bar-tab">
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
                    <button onclick="openNavTab(event, 'wastelist')" class="main-nav-button">
                        <image src="/images/side-arrow.png"></image>
                    </button>
                </div>


                <ul id="wastelist" class="main-nav-tab side-bar-tab">
                    <li><a href="{{ route('wastelist.new') }}">New Waste List</a></li>
                    <li><a href="{{ route('wastelist.view') }}">Edit Waste List</a></li>
                    <li><a href="{{ route('wastelist.view') }}">View Waste Lists</a></li>
                </ul>

            </li>

            <li>
                <div>
                   <a class=""href="{{ route('store.home') }}">Stores</a>
                    <button onclick="openNavTab(event, 'store')" class="main-nav-button">
                        <image src="/images/side-arrow.png"></image>
                    </button>
                </div>


                <ul id="store" class="main-nav-tab side-bar-tab">
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
                   <a href="{{ route('inventory.home') }}">Inventory</a>
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
                    <button onclick="openNavTab(event, 'order')" class="main-nav-button">
                        <image src="/images/side-arrow.png"></image>
                    </button>
                </div>


                <ul id="order" class="main-nav-tab side-bar-tab">
                    <li><a href="{{ route('order.new') }}">New Order</a></li>
                    <li><a href="{{ route('order.view', ["search" => "Saved"]) }}">Edit Order</a></li>
                    <li><a href="{{ route("order.view") }}">View Orders</a></li>
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
                    <button onclick="openNavTab(event, 'waste')" class="main-nav-button">
                            <image src="/images/side-arrow.png"></image>
                    </button>
                </div>


                <ul id="waste" class="main-nav-tab side-bar-tab">
                    <li><a href="">Test</a></li>
                    <li><a href="">Test</a></li>
                    <li><a href="">Test</a></li>
                </ul>
            </li>

            <li>
                <div>
                    <a href="{{ route('forecasting.home') }}">Forecasting</a>
                    <button onclick="openNavTab(event, 'forecasting')" class="main-nav-button">
                        <image src="/images/side-arrow.png"></image>
                    </button>
                </div>


                <ul id="forecasting" class="main-nav-tab side-bar-tab">
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
                    <button onclick="openNavTab(event, 'StockOnHand')" class="main-nav-button">
                        <image src="/images/side-arrow.png"></image>
                    </button>
                </div>


                <ul id="StockOnHand" class="main-nav-tab side-bar-tab">
                    <li><a href="">New Soh Count</a></li>
                    <li><a href="">Edit Soh Count</a></li>
                    <li><a href="">View Soh</a></li>
                    <li><a href="">Soh Reports</a></li>
                </ul>
            </li>

            <li>
                <div>
                    <a href=" {{ route('dates.home') }}">Dates</a>
                    <button onclick="openNavTab(event, 'dates')" class="main-nav-button">
                        <image src="/images/side-arrow.png"></image>
                    </button>
                </div>
                <ul id="dates" class="main-nav-tab side-bar-tab">
                    <li><a href="">New Date</a></li>
                    <li><a href="">Edit Date</a></li>
                    <li><a href="">Search Dates</a></li>
                    <li><a href="">View Shelf Life Chart</a></li>
                </ul>
            </li>

            <li>
                <div>
                    <a href=" {{ route('product.home') }}">Products</a>
                    <button onclick="openNavTab(event, 'product')" class="main-nav-button">
                    <image id="Products_arrow"  src="/images/side-arrow.png"></image>
                    </button>
                </div>


                <ul id="product" class="main-nav-tab side-bar-tab">
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
            @hasSection ('tools')
                <div class="tools-section"> @yield('tools') </div>
            @else
            <div class="tools-section">
                <a href="{{ route("order.home") }}" class="bar-tool-button">Home <i class="fas fa-home"></i></a>
            </div>
            @endif
            <div class="title">
                @hasSection ('title')
                    @yield('title')
                @endif
            </div>
            <div class ="user-section">
                @auth
                    <a href="#" class="bar-tool-button">{{ auth()->user()->getCorrectName() }} <i class="fas fa-user-cog"></i></a>
                    <a class="bar-tool-button" href="{{ route('logout.index') }}">Logout <i class="fas fa-sign-in-alt"></i></a>
                @endauth

                @guest
                    <a class="bar-tool-button" href="{{ route('login') }}">Login <i class="fas fa-sign-in-alt"></i></a>
                @endguest

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
                        <a href="{{ route('inventory.home') }}"><button id="alt-inventory" class="ph-button ph-button-standard modal-nav-button">Inventory</button></a>
                        <a href="{{ route('order.home') }}"><button id="alt-order" class="ph-button ph-button-standard modal-nav-button">Ordering</button></a>
                        <a href="{{ route('receiving.home') }}"><button id="alt-receiving" class="ph-button ph-button-standard modal-nav-button">Receiving</button></a>
                        <a href="{{ route('waste.home') }}"><button id="alt-waste" class="ph-button ph-button-standard modal-nav-button">Waste</button></a>
                        <a href="{{ route('forecasting.home') }}"><button id="alt-forecasting" class="ph-button ph-button-standard modal-nav-button">Forecasting</button></a>
                        <a href="{{ route('soh.home') }}"><button id="alt-soh" class="ph-button ph-button-standard modal-nav-button">SOH</button></a>
                        <a href="{{ route('dates.home') }}"><button id="alt-dates" class="ph-button ph-button-standard modal-nav-button">Dates</button></a>

                        @else
                        <a href="{{ route('product.home') }}"><button id="alt-product" class="ph-button ph-button-standard modal-nav-button">Products</button></a>
                        <a href="{{ route('menu.home') }}"><button id="alt-menu" class="ph-button ph-button-standard modal-nav-button">Menu</button></a>
                        <a href="{{ route('wastelist.home') }}"><button id="alt-wastelist" class="ph-button ph-button-standard modal-nav-button">Waste List</button></a>
                        <a href="{{ route('store.home') }}"><button id="alt-store" class="ph-button ph-button-standard modal-nav-button">Store</button></a>

                        @endif

                        @auth
                        <a href="{{ route('logout.index') }}"><button id="" class="ph-button ph-button-standard ph-button-important ph-button-alt modal-nav-button">Logout <i class="fas fa-sign-in-alt"></i></button></a>
                        @endauth

                        @guest
                        <a href="{{ route('login') }}"><button id="" class="ph-button ph-button-standard ph-button-important ph-button-alt modal-nav-button">Login <i class="fas fa-sign-in-alt"></i></button></a>
                        @endguest

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
    var selectList = Array(5);
    //these should be alphabetical
    //update this list if required
    selectList['Chilled'] = ["Toppings", "Cheese", "Soft Beverages", "Salads", "Beer", "Wine/Spirits"]
    selectList['Dry'] = ["Food", "Sauces"]
    selectList['Frozen'] =  ["Toppings", "Cheese", "Pasta", "Desserts", "Starters", "Dough", "Other"]
    selectList["Other"] = ["Other", "Goody Bags", "Paper", "Cleaning", "Ops Supplies", "Cutlery and Crockery"]

    function updateSelect() {

    //test = Object.keys(categories)
    let main, updated, current;

    main = document.getElementById("mainSelect");
    updated = document.getElementById("updatedSelect");

    current = main.options[main.selectedIndex].value;

    if(current in selectList) {
        //remove all current options.
        while(updated.options.length > 0) {
        updated.remove(0);
        }

    //create new options based on list
        for(let o = 0; o <selectList[current].length; o++) {
            newOption = document.createElement("option");
            newOption.value = selectList[current][o];
            newOption.text = selectList[current][o];

            updated.appendChild(newOption);
        }
    }


}

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

    function setupNav() {
        let comps, url = window.location.pathname;
        className = "active";
        comps = url.split("/");
        //first split is always empty as starts with /
        if(comps[1] != "") {
            //second section is controller in use
            controller = comps[1];

            //waste list fix
            //could change this to have a comps[2] check instead of just for waste?
            if(controller == "waste" && comps[2] == "list") {
                controller = controller+comps[2];
            }

            ul = document.getElementById(controller);


            //in case not found
            if(ul == null) {
                return;
            }

            parentLi = ul.parentNode;
            anchor = parentLi.getElementsByTagName("a");

            if(anchor.length > 0) {
                anchor = anchor[0];
                anchor.classList.add(className);
            }

            mobileNav = document.getElementById("alt-"+controller);
            console.log(mobileNav);
            if(mobileNav != null) {
                mobileNav.classList.add(className);
            }
        }
    }

    function centerOn(id, highlight = true) {
        element = document.getElementById(id);
        element.focus();
        let test = element.classList.contains("highlight");
        if(highlight && !test) {
            element.classList.add("highlight");
        }
    }
    function openOrCloseModal(id) {
        let modal = document.getElementById(id);
        if(modal.style.display == "flex") {
            modal.style.display = "none";
        }
        //otherwise open modal
        else {
            //changed to display flex in order to center modal in page
            modal.style.display = "flex";
        }
    }

    function searchModal() {
        //if modal is already open
        openOrCloseModal("search-modal");
    }

    //find a text input in a table
    function findInTable(input, searchable) {
        let searchbar = document.getElementById(input);
        let table = document.getElementById(searchable);

        let value = searchbar.value;
        let found = false;
        let tds = table.getElementsByTagName("td");
        let foundTd = [];
        let field = document.getElementById("response");

        //get all tds within table
        for (i = 0; i < tds.length; i++) {
            current = tds[i];


            //check for matches, assign to array if found
            //old value check
            //value.toUpperCase() === current.innerText.toUpperCase()

            //with includes
            if(current.innerText.toUpperCase().includes(value.toUpperCase())) {
                foundTd.push(current);
                found = true;
                field.innerText = " ";
            }
        }

        //for found values
        if(found) {
            //add highlight effect and click event to remove highlight
            for (i = 0; i < foundTd.length; i++) {
                foundTd[i].parentNode.classList.add("find-highlight");

                //changing onclick rather than adding event listener allows for event onclick removal.
                foundTd[i].parentNode.addEventListener("click", function(event)  {
                    event.target.parentNode.classList.remove('find-highlight');
                });
            }

            //close modal and scroll to first highlighted td
            searchModal();
            foundTd[0].scrollIntoView({behavior: 'smooth'});
        }
        //otherwise show response
        else {
            field.innerText = "0 Results found";
        }

    }

    //no idea why adding click event doesnt work here, onclick cant be removed, remove click event is not working well either
    function highlightChange(event) {
        event.target.parentNode.classList.remove('find-highlight');
    }
    setupNav();
</script>
