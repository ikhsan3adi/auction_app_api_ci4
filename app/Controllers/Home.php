<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index()
    {
        // return view('welcome_message');
        return "<h1>Welcome to Online Auction API</h1><p>By: <a href='https://github.com/ikhsan3adi'>@ikhsan3adi</a></p>";
    }
}
