<?php
// src/Controller/HomeController.php
namespace Project\Controller;

use Symfony\Component\HttpFoundation\Response;

class HomeController
{
    public function show()
    {
        return $this->render("home.html.twig");
    }
}