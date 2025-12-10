<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;
use App\Models\Setting;

class AdminLayout extends Component
{
	/**
	 * Get the view / contents that represents the component.
	 */
	public function render(): View
	{
		$companyName = Setting::get('company_name', 'Admin');
		
		return view('layouts.admin', [
			'companyName' => $companyName,
		]);
	}
}


