<?php
namespace app\index\controller;
use think\Controller;

class Login extends Controller
{

    public function index()
    {
       return view('login');
    }
}