<?php

namespace App\View\Components\Container\Galaxyhub;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Single extends Component
{
    public string $class;
    public string $style;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(string $class = '', string $style = '')
    {
        $this->class = $class;
        $this->style = $style;
    }

    public function render():View
    {
        return view('components.container.galaxyhub.single');
    }
}
