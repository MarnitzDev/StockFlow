<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class OrganizationController extends Controller
{
    public function profile()
    {
        return Inertia::render('App/Organization/Profile');
    }

    public function branding()
    {
        return Inertia::render('App/Organization/Branding');
    }

    public function locations()
    {
        return Inertia::render('App/Organization/Locations');
    }

    public function currency()
    {
        return Inertia::render('App/Organization/Currency');
    }
}
