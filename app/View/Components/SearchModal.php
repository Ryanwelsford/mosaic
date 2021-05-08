<?php

namespace App\View\Components;

use Illuminate\View\Component;

//setup entire model for searching based on nav model
class SearchModal extends Component
{
    /**
     * Create a new component instance.
     *
     *
     */
    public $model;
    public $action;
    public $search;
    public $fields;

    public function __construct($model,$action, $search, $fields)
    {
        $this->model = $model;
        $this->action = $action;
        $this->search = $search;
        $this->fields = $fields;
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
