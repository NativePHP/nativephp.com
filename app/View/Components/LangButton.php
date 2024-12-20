<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class LangButton extends Component
{
    public $availableLangs;
    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        $this->availableLangs = [
            'en' => 'English',
            'es' => 'Español',
        ];
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.lang-button', ['availableLangs' => $this->availableLangs]);
    }
}
