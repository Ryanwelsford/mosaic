<?php

namespace App\View\Components;

use Illuminate\View\Component;

class SearchForm extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public $model;
    public $action;
    public $search;

    public function __construct($model, $action, $search)
    {
        $this->model = $model;
        $this->action = $action;
        $this->search = $search;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|string
     */
    public function render()
    {
        return view('components.search-form');
    }
}
