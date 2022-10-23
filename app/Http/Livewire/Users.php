<?php

namespace App\Http\Livewire;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class Users extends Component
{
    public function render(): View
    {
        return view('livewire.users', [
            'users' => User::query()->paginate()
        ]);
    }
}
