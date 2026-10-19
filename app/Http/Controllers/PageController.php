<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class PageController extends Controller
{
    public function howItWorks(): View
    {
        return view('pages.how-it-works');
    }

    public function verificationPolicy(): View
    {
        return view('pages.verification-policy');
    }

    public function grievance(): View
    {
        return view('pages.grievance');
    }

    public function privacy(): View
    {
        return view('pages.privacy');
    }

    public function terms(): View
    {
        return view('pages.terms');
    }
}
