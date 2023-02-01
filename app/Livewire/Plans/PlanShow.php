<?php

namespace App\Livewire\Plans;

use App\Models\Plan;
use Livewire\Component;

class PlanShow extends Component
{
    public Plan $plan;

    public function render()
    {
        return view('app.plans.plan-show');
    }
}
