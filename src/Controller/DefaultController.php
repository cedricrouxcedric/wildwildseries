<?php


namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DefaultController extends AbstractController
{
    /**
     * @return Response
     * @route ("/", name="app_index")
     */
    public function index() :Response
    {
        return $this->render('home.html.twig',[
            'message' => 'bienvenue ! ',
        ]);

    }
}
