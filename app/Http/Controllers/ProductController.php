<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Helpers\ModelSearch;
use App\Http\Helpers\ModelValidator;
use App\Http\Helpers\ModelSearch\ModelSearchv4;
use App\Http\Controllers\Types\AdminAccessController;

class ProductController extends AdminAccessController
{
    public function home()
    {

        $title = "Products Home";

        $viewRoute = Route("product.view");

        $menuitems = [
            ["title" => "New Product", "anchor" => route("product.new"), "img" => "/images/icons/new-256.png", "action" => "Create"],
            ["title" => "Edit Product", "anchor" => $viewRoute, "img" => "/images/icons/edit-256.png", "action" => "Edit"],
            ["title" => "View Products", "anchor" => $viewRoute, "img" => "/images/icons/view-256.png", "action" => "View"]
        ];

        return view('menu', [
            "menuitems" => $menuitems,
            "title" => $title
        ]);
    }

    public function store(Request $request)
    {
        $categories = $this->buildCategories();

        $encoded = json_encode($categories);

        $modelValidator = new ModelValidator(Product::class, $request->id, old());
        $product = $modelValidator->validate();
        $case = $pack = null;
        $title = "New Product";

        //therefore edit is required
        if ($product != null && empty(old())) {
            $units = $product->units()->get()[0];
            $output = $units->splitUnit();
            $case = $output[0];
            $pack = $output[1];
        }

        if (!empty(old())) {
            $full = old();
            $case = $full['case'];
            $pack = $full['pack'];
        }

        return view(
            'product.new',
            [
                "title" => $title,
                "product" => $product,
                "categories" => $categories,
                "encoded" => $encoded,
                "case" => $case,
                "pack" => $pack
            ]
        );
    }

    public function save(Request $request)
    {
        //validate the form submission will need to find a way to prevent the unique rule from firing if the id is matched

        $id = $request->id;

        $this->validate($request, [
            'name' => ['required', \Illuminate\Validation\Rule::unique('products')->ignore($id)],
            'code' => ['required', \Illuminate\Validation\Rule::unique('products')->ignore($id), 'numeric'],
            'case.description' => ['required'],
            'case.price' => ['required'],
            'case.quantity' => ['required']
        ]);

        //create product
        $product = new Product;
        $unit = new Unit;
        $case = $request->case;
        $pack = $request->pack;

        //therefore is an edit
        if (isset($id)) {
            $product = Product::find($id);
            $unit = $product->units()->get()->first();
        }


        $product->fillItem($id, $request->name, $request->code, $request->category, $request->subcategory);
        //create can be used to save the product in one line. However save can be used when updating
        /*$product = Product::create([*/
        $product->save();

        //create units
        //todo update units creation to associate with precreated units rather than creating a new one everytime
        $unit->fillItem($case, $pack, $product->id);
        $unit->save();
        //currently wraps back around to the new page
        //return $this->store($request);

        //need to create a confimration page at some point
        return $this->confirm($product);
    }

    public function buildCategories()
    {
        //changes here need to be reflectd in js script
        $categories = [
            'Chilled' => ["Toppings", "Cheese", "Soft Beverages", "Salads", "Beer", "Wine/Spirits"],
            "Dry" => ["Food", "Sauces"],
            "Frozen" => ["Toppings", "Cheese", "Pasta", "Desserts", "Starters", "Dough", "Other"],
            "Other" => ["Other", "Goody Bags", "Paper", "Cleaning", "Ops Supplies", "Cutlery and Crockery"]
        ];
        return $categories;
    }

    //todo add pagination ability to full list of product. add list of tabs seperating units then all for switching with selects
    //enable display of all products with their details
    //need to add a check to test what happens when there are 0 products to display
    public function view(Request $request, $response = '')
    {
        // all products
        $product = new Product;
        $searchFields = $product->getSearchable();

        $title = "Display Products";
        $search = $request->search;

        $sort = $request->sort;
        if ($sort == null) {
            $sort = "name";
        }

        $input["products"] = $searchFields;
        $sortDirection = "desc";

        $modelSearch = new ModelSearchv4(Product::class, $input, $input);
        $products = $modelSearch->search($search, $sort, $sortDirection);

        return view("product.view", [
            "products" => $products,
            "title" => $title,
            "search" => $search,
            "sort" => $sort,
            "searchFields" => $searchFields,
            "response" => $response
        ]);
    }

    //destroying a product should remove its units as well, although i believe cascade delete is already set?
    //soft deletes should be enabled in final version
    public function destroy(Product $product, Request $request)
    {
        //remove product cascade delete removes all associated units as well.
        $message = "Product " . $product->name . " successfully deleted";
        $product->delete();

        //send back to previous page
        return $this->view($request, $message);
    }

    public function confirm(Product $product)
    {
        $title = "Product Confirmation";
        $heading = "Product Successfully Created";
        $text = "Product has been created successfully";
        $anchor = route('product.new');
        return view("general.confirmation", ["title" => $title, "heading" => $heading, "text" => $text, "anchor" => $anchor]);
    }
}
