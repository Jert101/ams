<?php

namespace App\View\Components;

use Illuminate\View\Component;

class FacialRecognitionModal extends Component
{
    /**
     * Whether to show the modal by default.
     *
     * @var bool
     */
    public $show;

    /**
     * Create a new component instance.
     *
     * @param  bool  $show
     * @return void
     */
    public function __construct($show = false)
    {
        $this->show = $show;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.facial-recognition-modal');
    }
} 