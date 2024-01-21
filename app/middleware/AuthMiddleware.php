<?php

class AuthMiddleware extends Middleware
{
    public function handle()
    {
        $homeModel = Load::model('HomeModel');
        $data = $homeModel->all();

        if (Session::data('admin_login') == null) {
            $response = new Response();
            $response->redirect('trang-chu');
        }
    }
}