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

//actual addition function
function totalUp(event) {

    let current, tr, totalBox, runningTotal, newTotal;

    //added if statement to prevent issue of clicking out of input without entering number
    if(!isNaN(parseFloat(event.target.value))) {
        current = parseFloat(event.target.value);
        tr = event.target.parentNode;

        //always find the parent tr of input even after further divs created
        while(tr.tagName != "TR" ) {
            tr = tr.parentNode
        }

        totalBox = tr.getElementsByClassName("total-box")[0];
        runningTotal = parseFloat(totalBox.value);

        //data attributes are set using data-name, but accessed in js using dataset.name
        newTotal = runningTotal + (current*parseFloat(event.target.dataset.count)) ;

        //dont allow negative totals
        if(newTotal <= 0) {
            newTotal = 0;
        }

        totalBox.value = newTotal;
        event.target.value = '';
    }

}

//add event listeners
function setupTotals() {
    let input = document.getElementsByClassName("count-box");


    for(i=0; i < input.length; i++) {
    //when passing variables to an event function you have to call it anonomouysly then call the named function with parameter
    input[i].addEventListener('focusout', function () {
        totalUp(event);
        });
    }
}

function updateText(element) {
    parent = element.parentNode.parentNode
    div = parent.getElementsByTagName("div")[0];
    updateDivText(div);
}

//update text on select boxes
function updateDivText(div) {
    if(div.innerText == "Select") {
        div.innerText = "Remove";
        return
    }

    if(div.innerText == "Remove") {
        div.innerText = "Select";
        return
    }

}

var defaultOptions = {
    'backgroundColor': '#d3d3d3',
    "titleTextStyle": {
        "fontSize": 18
      },
    'legend': {'position': 'bottom'},
    'width': "100%",
    'height': 310
  }


//functions that run on page load
function setup() {
    setupTotals();
    setupNav();
}

//wait for page load then setup js functions
document.addEventListener('DOMContentLoaded', (event) => {
    setup();
});
