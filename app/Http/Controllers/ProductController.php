<?php

namespace App\Http\Controllers;

use App\Http\Helpers\ModelValidator;
use App\Models\Unit;
use App\Models\Product;
use Exception;
use Illuminate\Http\Request;
use stdClass;

class ProductController extends Controller
{
    public function home()
    {

        $title = "Products Home";

        $viewRoute = Route("product.new");

        $menuitems = [
            ["title" => "New Product", "anchor" => route("product.new"), "img" => "/images/icons/new-256.png"],
            ["title" => "Edit Product", "anchor" => $viewRoute, "img" => "/images/icons/edit-256.png"],
            ["title" => "View Product", "anchor" => $viewRoute, "img" => "/images/icons/view-256.png"],
            ["title" => "Product Reports", "anchor" => "/test", "img" => "/images/icons/report-256.png"]
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

        $title = "New Product";

        return view(
            'product.new',
            [
                "title" => $title,
                "product" => $product,
                "categories" => $categories,
                "encoded" => $encoded
            ]
        );
    }

    public function save(Request $request)
    {
        //validate the form submission will need to find a way to prevent the unique rule from firing if the id is matched

        $id = $request->id;

        $this->validate($request, [
            'name' => ['required', \Illuminate\Validation\Rule::unique('products')->ignore($id)],
            'code' => 'required|unique:products|numeric'
        ]);

        $unittype = $request->unit['type'];
        $unitdesc = $request->unit['description'];
        $unitprice = $request->unit['price'];

        //create product
        $product = new Product;

        if (isset($id)) {
            $product = Product::find($id);
        }

        $product->fillItem($id, $request->name, $request->code, $request->category, $request->subcategory);
        //create can be used to save the product in one line. However save can be used when updating
        /*$product = Product::create([*/
        $product->save();

        //create units
        //todo update units creation to associate with precreated units rather than creating a new one everytime

        for ($i = 0; $i < count($unittype); $i++) {
            Unit::create([
                'unittype' => $unittype[$i],
                'description' => $unitdesc[$i],
                'price' => $unitprice[$i],
                'product_id' => $product->id
            ]);
        }

        //currently wraps back around to the new page
        //return $this->store($request);

        //need to create a confimration page at some point
        return $this->confirm($product);
    }

    public function buildCategories()
    {
        $categories = [
            'Chilled' => ["Toppings", "Cheese", "Soft Beverages", "Salads"],
            "Drinks" => [],
            "Dry" => [],
            "Frozen" => [],
            "Other" => []
        ];
        return $categories;
    }

    //todo add pagination ability to full list of product. add list of tabs seperating units then all for switching with selects
    //enable display of all products with their details
    //need to add a check to test what happens when there are 0 products to display
    public function view()
    {
        // all products
        $products = Product::orderBy('name')->get();
        $title = "Display Products";
        $categories = $this->buildCategories();

        return view("product.view", [
            "products" => $products,
            "title" => $title,
            "categories" => $categories
        ]);
    }

    //destroying a product should remove its units as well, although i believe cascade delete is already set?
    //soft deletes should be enabled in final version
    public function destroy(Product $product)
    {
        //remove product cascade delete removes all associated units as well.
        $message = "Product " . $product->name . " successfully deleted";
        $product->delete();

        //send back to previous page
        return back()->with("confirmation", $message);
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
