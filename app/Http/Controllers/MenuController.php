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

//TODO create copy method to allow for a new menu to be created from a base menu, copying all the product mappings
class MenuController extends AdminAccessController
{
    public function home()
    {
        $item = "Menu";

        $title = $item . " Home";
        $newMenu = Route("menu.new");
        $viewRoute = Route("menu.view");

        $menuitems = [
            ["title" => "New " . $item, "anchor" => $newMenu, "img" => "/images/icons/new-256.png"],
            ["title" => "Edit " . $item, "anchor" => $viewRoute, "img" => "/images/icons/edit-256.png"],
            ["title" => "View " . $item . "s", "anchor" => $viewRoute, "img" => "/images/icons/view-256.png"],
            ["title" => "Copy " . $item, "anchor" => $viewRoute, "img" => "/images/icons/copy-256.png"],
            ["title" => $item . " Reports", "anchor" => "/test", "img" => "/images/icons/report-256.png"]
        ];

        return view('menu', [
            "menuitems" => $menuitems,
            "title" => $title
        ]);
    }

    public function store(Request $request)
    {

        $title = "New Menu";
        if (isset($request->id)) {
            $title = "Edit Menu";
        }

        $statuses = ["Active", "Inactive"];

        //$output here is actually an array of arrays, so weird

        $modelValidator = new ModelValidator(Menu::class, $request->id, old());
        $menu = $modelValidator->validate();

        if (isset($request->copy_id)) {
            $menuCopy = Menu::where('id', $request->copy_id)->get()->first();
        } else {
            $menuCopy = false;
        }

        return view('menus.new', [
            "title" => $title,
            "menu" => $menu,
            "statuses" => $statuses,
            "menuCopy" => $menuCopy
        ]);
    }

    public function save(Request $request)
    {
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

        if (isset($id)) {
            $menu = Menu::find($id);
        }

        $menu->fillArrayItem($request->menu);

        $menu->save();

        //TODO add send to menu listings to add products to menu


        //if copying a menu send to copy then confirm
        if (isset($request->copy_id)) {
            $copy = Menu::find($request->copy_id);
            return $this->copy($menu, $copy);
        } else {
            //otherwise send to assign products
            return $this->assign($menu, $request);
        }
    }

    public function copy(Menu $menu, Menu $copy)
    {
        $menuListings = [];
        foreach ($copy->products()->get() as $product) {
            $menuListings[] = $product->id;
        }

        $menu->products()->sync($menuListings);

        return $this->confirm($menu);
    }

    public function assign(Menu $menu, Request $request)
    {
        $productController = new ProductController();
        $categories = $productController->buildCategories();


        if ($menu == null) {
            return redirect(route("menu.new"));
        }

        if (isset($request->menu_id)) {
            $menu = Menu::find($request->menu_id);
        }

        $menu->products()->get();

        $menuListings = [];
        foreach ($menu->products()->get() as $list) {
            $menuListings[$list->id] = "set";
        }

        $title = "Assign Menu Items";
        //get all products by category then subcategory then name
        $products = Product::orderby('category')->orderby('subcategory')->orderby('name')->get();
        $organisedProducts = [];
        $defaultOpenTab = "Chilled";

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

    //display all menus with options to edit, delete and copy eventually
    public function view(Request $request, $response = "")
    {
        //TODO break this function down into a class
        $title = "View Menus";
        $menu = new Menu;
        $searchFields = $menu->searchable();

        $search = $request->search;

        $sort = $request->sort;
        if ($sort == null) {
            $sort = "id";
        }

        $input["menus"] = $searchFields;
        $sortDirection = "desc";

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

    public function confirm(Menu $menu)
    {
        $title = "Menu Confirmation";
        $heading = "Menu Successfully Created";
        $text = "Menu has been created successfully";
        $anchor = route('menu.new');
        return view("general.confirmation", ["title" => $title, "heading" => $heading, "text" => $text, "anchor" => $anchor]);
    }
}
