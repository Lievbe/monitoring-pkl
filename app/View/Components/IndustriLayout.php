<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class IndustriLayout extends Component
{
    public function render(): View
    {
        return view('layouts.management', ['portal' => 'industri']);
    }
}
