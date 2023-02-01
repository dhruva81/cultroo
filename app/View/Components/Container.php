<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Container extends Component
{
    public function render()
    {
        $containerWidth = 'max-w-7xl mx-auto px-4 sm:px-6 md:px-8';

        return view('components.container', compact('containerWidth'));
    }
}
