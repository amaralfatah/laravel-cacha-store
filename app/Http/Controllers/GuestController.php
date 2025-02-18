<?php

namespace App\Http\Controllers;


class GuestController extends Controller
{
    public function index()
    {
        return view('welcome');
    }

    public function shop()
    {
        return view('guest.shop');
    }

    public function productDetails()
    {
        return view('guest.product-details');
    }

    public function contactUs()
    {
        return view('guest.contact-us');
    }
}
