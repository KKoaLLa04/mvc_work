<?php
//HomeController Controller

class HomeController extends Controller
{
    public $data = [], $table;

    public function __construct()
    {
        //construct controller
    }

    public function index()
    {
        //index controller
        $this->render('welcome');
    }

    public function add()
    {
        // add controller
    }
}
