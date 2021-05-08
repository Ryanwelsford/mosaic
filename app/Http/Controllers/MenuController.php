<?php

namespace App\Http\Controllers;

use stdClass;
use App\Models\Menu;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Helpers\ModelSearch;
use App\Http\Helpers\ModelValidator;
use App\Http\Controllers\ProductController;
use App\Http\Helpers\ModelSearch\ModelSearchv4;
use App\Http\Controllers\Types\AdminAccessController;

/***********************************
 *This class exists for the purpose of controlling menu inputs
 *Menus are supposed to act as product limitations for orders, this in turn prevents various issues for incorrect order placement
 *
 *************************************/
class MenuController extends AdminAccessController
{
    //display core menu home page
    public function home()
    {
        $item = "Menu";

        //setup menu items
        $title = $item . " Home";
        $newMenu = Route("menu.new");
        $viewRoute = Route("menu.view");

        $menuitems = [
            ["title" => "New " . $item, "anchor" => $newMenu, "img" => "/images/icons/new-256.png", "action" => "Create"],
            ["title" => "Edit " . $item, "anchor" => $viewRoute, "img" => "/images/icons/edit-256.png", "action" => "Edit"],
            ["title" => "View " . $item . "s", "anchor" => $viewRoute, "img" => "/images/icons/view-256.png", "action" => "View"],
            ["title" => "Copy " . $item, "anchor" => $viewRoute, "img" => "/images/icons/copy-256.png", "action" => "Copy"],
        ];

        return view('menu', [
            "menuitems" => $menuitems,
            "title" => $title
        ]);
    }

    //display menu creation page
    public function store(Request $request)
    {

        $title = "New Menu";
        if (isset($request->id)) {
            $title = "Edit Menu";
        }

        //setup statuses
        $statuses = ["Active", "Inactive"];

        //$output here is actually an array of arrays, so weird
        //pull menu information based on edit etc
        $modelValidator = new ModelValidator(Menu::class, $request->id, old());
        $menu = $modelValidator->validate();

        //pull old menu when form fails setup is bizzare but works
        if (!empty(old())) {
            $old = old();
            $menu = (object) $old['menu'];
        }

        //sets up copying feature using the same form as before
        if (isset($request->copy_id)) {
            //map passed id to new menu products
            $menuCopy = Menu::where('id', $request->copy_id)->get()->first();
        } else {
            $menuCopy = false;
        }
        //dd($menu);
        return view('menus.new', [
            "title" => $title,
            "menu" => $menu,
            "statuses" => $statuses,
            "menuCopy" => $menuCopy
        ]);
    }

    //save new menu
    public function save(Request $request)
    {
        //pull form data
        $id = $request->menu['id'];
        $menu = (object) $request->menu;

        //array field validation involves having to use dot notation
        //you can also pass custom messages through the the field useful when renaming input fields to allow for the above
        $this->validate(
            $request,
            [
                'menu.name' => ['required', \Illuminate\Validation\Rule::unique('menus', 'name')->ignore($id)]
            ],
            $messages = [
                'menu.name.required' => 'The name field is required',
                'menu.name.unique' => 'The menu name has been taken, please enter a new name'
            ]
        );

        $menu = new Menu;

        //find if iedit
        if (isset($id)) {
            $menu = Menu::find($id);
        }

        //fill and save data
        $menu->fillArrayItem($request->menu);

        $menu->save();

        //if copying a menu send to copy then confirm
        if (isset($request->copy_id)) {
            $copy = Menu::find($request->copy_id);
            return $this->copy($menu, $copy);
        } else {
            //otherwise send to assign products
            return $this->assign($menu, $request);
        }
    }

    //pull copied menu data and map to new menu, gives admins an avenue to recreate a menu easily
    public function copy(Menu $menu, Menu $copy)
    {
        $menuListings = [];
        foreach ($copy->products()->get() as $product) {
            $menuListings[] = $product->id;
        }

        $menu->products()->sync($menuListings);

        return $this->confirm($menu);
    }

    //pull up assign screen whihc allows selection of products to be placed inside a menu.
    public function assign(Menu $menu, Request $request)
    {
        //get category array
        $productController = new ProductController();
        $categories = $productController->buildCategories();

        //guard against malformed url
        if ($menu == null) {
            return redirect(route("menu.new"));
        }

        //pull mmenu data
        if (isset($request->menu_id)) {
            $menu = Menu::find($request->menu_id);
        }
        //pull related products is this here for no reason?
        $menu->products()->get();

        //remap products from id to set or not
        $menuListings = [];
        foreach ($menu->products()->get() as $list) {
            $menuListings[$list->id] = "set";
        }

        $title = "Assign Menu Items";
        //get all products by category then subcategory then name
        $products = Product::orderby('category')->orderby('subcategory')->orderby('name')->get();
        $organisedProducts = [];
        $defaultOpenTab = "Chilled";

        //remap products based on category and subcat for usage in view
        foreach ($products as $product) {
            $organisedProducts[$product->category][$product->subcategory][] = $product;
        }

        return view("menus.assign", [
            "menu" => $menu,
            "title" => $title,
            "products" => $products,
            "categories" => $categories,
            "organisedProducts" => $organisedProducts,
            "defaultOpenTab" => $defaultOpenTab,
            "menuListings" => $menuListings
        ]);
    }

    //for future reference migrations involving pivot tables require bigInterger and unsigned
    public function assignToMenu(Request $request)
    {
        //establish menu id and pull menu
        $id = $request->menu_id;
        $menu = Menu::find($id);

        //save menu to product listings
        $menu->products()->sync($request->menuListings);
        //return confirmation message
        return ($this->confirm($menu));
    }

    //will need to add menu when model created.
    public function destroy(Menu $menu, Request $request)
    {
        $response = "Successfully deleted menu #" . $menu->id . " " . $menu->name;
        $menu->delete();

        return $this->view($request, $response);
    }

    //display all menus with options to edit, delete add and copy
    public function view(Request $request, $response = "")
    {
        //TODO the overall function down into a single class as it generally always works the same way
        $title = "View Menus";
        $menu = new Menu;
        //get search values
        $searchFields = $menu->searchable();

        //setup vars
        $search = $request->search;

        $sort = $request->sort;
        if ($sort == null) {
            $sort = "id";
        }

        $input["menus"] = $searchFields;
        $sortDirection = "desc";

        //search with basic version, no need for restriction or join.
        $modelSearch = new ModelSearchv4(Menu::class, $input, $input);
        $menus = $modelSearch->search($search, $sort, $sortDirection);
        //dd($inventory);

        return view("menus.view", [
            'title' => $title,
            'menus' => $menus,
            "search" => $search,
            "sort" => $sort,
            "searchFields" => $searchFields,
            "response" => $response
        ]);
    }

    //confirm successful creation of menu
    public function confirm(Menu $menu)
    {
        $title = "Menu Confirmation";
        $heading = "Menu Successfully Created";
        $text = "Menu has been created successfully";
        $anchor = route('menu.new');

        return view("general.confirmation", ["title" => $title, "heading" => $heading, "text" => $text, "anchor" => $anchor]);
    }
}
