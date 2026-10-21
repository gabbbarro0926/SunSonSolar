<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index()
    {
        
        return view('home');
    }
    public function login()
    {
    return view('login');
    }
    public function register()
    {
    return view('register');
    }
    public function services()
    {
    return view('services');
    }
    public function about()
    {
    return view('about');
    }
    public function contact()
    {
    return view('contact');
    }
    public function dashboard()
    {
    return view('dashboard');
    }
}