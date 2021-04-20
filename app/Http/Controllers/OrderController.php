<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Helpers\ModelValidator;


class OrderController extends UserAccessController
{
    //$user is available as a protected variable of UserAccessController

    public function home()
    {

        $title = "Order Home";
        $menuitems = [
            ["title" => "New Order", "anchor" => route("order.new"), "img" => "/images/icons/new-256.png"],
            ["title" => "Edit Saved Order", "anchor" => "/test", "img" => "/images/icons/edit-256.png"],
            ["title" => "View Orders", "anchor" => "/test", "img" => "/images/icons/view-256.png"],
            ["title" => "Order Reports", "anchor" => "/test", "img" => "/images/icons/report-256.png"]
        ];

        return view('menu', [
            "menuitems" => $menuitems,
            "title" => $title,

        ]);
    }

    public function store(Request $request)
    {
        $title = "Create An Order";

        $order = false;
        //why does user have many stores...
        $store = $this->user->stores()->get()->first();
        $menus = Menu::orderby("updated_at")->get();

        return view("orders.new", [
            "title" => $title,
            "order" => $order,
            "user" => $this->user,
            "store" => $store,
            "menus" => $menus
        ]);
    }

    public function pick(Request $request)
    {
        $title = "Select Products";
        $display = $request->display_mode;
        $view = "orders.pick";

        $menu = Menu::where("id", $request->menu_id)->get()->first();
        $products = $menu->products()->orderby('category')->orderby('subcategory')->orderby('name')->with("units")->get();
        //  dd($products[0]);
        if ($display == true) {
            $view = "orders.full";
        }

        $organisedProducts = [];
        $defaultOpenTab = "Chilled";

        foreach ($products as $product) {
            $organisedProducts[$product->category][$product->subcategory][] = $product;
        }

        return view($view, ["title" => $title, "menu" => $menu, "products" => $products, "organisedProducts" => $organisedProducts]);
    }

    public function save(Request $request)
    {
        $status = "book";

        if (isset($request->save)) {
            $status = "save";
        }

    }
}
