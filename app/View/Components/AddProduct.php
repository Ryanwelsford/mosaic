<?php

namespace App\View\Components;

use Illuminate\View\Component;

//add product component class used within waste entry
class AddProduct extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($categories)
    {
        $this->categories = $categories;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|string
     */
    public function render()
    {
        return view('components.tools.add-product');
    }
}
