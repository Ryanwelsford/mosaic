<?php

namespace App\View\Components;

use Illuminate\View\Component;

class FindModal extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */

    public $model;
    public function __construct($model)
    {
        $this->model = $model;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|string
     */
    public function render()
    {
        return view('components.find-modal');
    }
}
