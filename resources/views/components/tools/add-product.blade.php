@extends("modals.layout")
@section("modal_title" , "Add Products To Waste")


@section("modal_content")
<form class="center-column">
    <input type="hidden" id="_token" value="{{ csrf_token() }}">
    <div class="center-column">
        <label class="select-label">Select a Category</label>
        <select onchange="loadCategories(event)" class="main-select main-select-large" placeholder="Select from the list">
            @foreach($categories as $key=>$category)
            <option value= "{{ $key }}">{{ $key }}</option>
            @endforeach
        </select>
    </div>

    <div id="productContainer" class="vert-scroll ">
    </div>
    <div id="error" class="error-text small-error-text"></div>
</form>

<script>
    function loadCategories(event) {
        var $search = event.target.value;
        var _token = document.getElementById("_token").value;
        let response;
        document.getElementById("error").innerText = "";

        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                response = JSON.parse(this.response);
                div = document.getElementById("productContainer");
                removeContent(div);

                for(var i =0; i < response.length; i++) {
                    buildResponse(response[i]);
                }

            }
            else if (this.status == 500) {
                document.getElementById("error").innerText = "An error has occured";
            }
        };

        xhttp.open("POST", "category", true);
        xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhttp.responseType = 'json';
        xhttp.send("category="+$search+"&_token="+_token);

    }

    function buildResponse(response) {
        div = document.getElementById("productContainer");
        createContent(div, response);
    }

    function createContent(div, product) {
        let label = document.createElement('label');
        let button = document.createElement('button');
        button.type = "button";
        button.classList = "ph-button ph-button-standard ph-button-small";
        button.innerText = "Add";
        button.addEventListener("click", function(e) {
            addProduct(product);
        });
        label.innerHTML = product["name"];

        div.appendChild(label);
        div.appendChild(button);
    }

    //https://stackoverflow.com/questions/3955229/remove-all-child-elements-of-a-dom-node-in-javascript
    function removeContent(node) {
        //createa range of elements of a given node and delete them all
        var range = document.createRange();
        range.selectNodeContents(node);
        range.deleteContents();
    }

    function addProduct(productData) {
        let tbody = document.getElementById("form").getElementsByTagName("tbody")[0];
        let tr, nameTd, codeTd, caseTd, priceTd, quantityTd, input, closeButton;

        tr = document.createElement("tr");
        nameTd = tdCreator(productData.name);
        codeTd = tdCreator(productData.code, "mob-hidden");
        caseTd = tdCreator(productData.units.description);
        priceTd = tdCreator(productData.units.price);
        quantityTd = document.createElement("td");
        quantityTd.classList = "waste-input-td";

        input = inputCreator(productData.id);
        closeButton = closeButtonCreator();
        tr.appendChild(nameTd);
        tr.appendChild(codeTd);
        tr.appendChild(caseTd);
        tr.appendChild(priceTd);
        tr.appendChild(quantityTd);
        quantityTd.appendChild(input);
        quantityTd.appendChild(closeButton);
        tbody.appendChild(tr);

    }

    function tdCreator(productInfo, classList = '') {
        td = document.createElement("td");
        td.classList = classList;
        td.innerText = productInfo;
        return td
    }

    function inputCreator(productid) {
        input = document.createElement("input");
        input.name = "product["+productid+"]";
        input.type = "number";
        input.classList = "table-input total-box";
        input.min = "0";
        input.step = "1";
        input.value = "0";

        return input;
    }

    function closeButtonCreator() {
        let button = document.createElement("button")
        button.classList = "ph-button ph-button-standard ph-button-rounded";
        button.type = "button";
        button.addEventListener("click", function(event) {
            closeDiv(event, "TR");
        })
        button.innerText = "X";

        return button;
    }

    //searchModal();
</script>
@endsection
