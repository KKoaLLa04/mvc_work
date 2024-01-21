<?php

class ParamsMiddleware extends Middleware
{
    public function handle()
    {
        if (!empty($_SERVER['QUERY_STRING'])) {
            $response = new Response();
            $response->redirect(Route::getFullUrl());
        }
    }
}
