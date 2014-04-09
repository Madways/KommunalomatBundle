<?php

namespace Madways\KommunalomatBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class StaticPageController extends Controller
{
    public function indexAction($page)
    {
        switch($page) {
            case "welcome":
                return $this->render('::welcome.html.twig');
                break;
            case "info":
                return $this->render('::info.html.twig');
                break;
        }
    }
}
