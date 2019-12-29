<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Entity\Category;
use App\Form\CategoryType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;



class CategoryController extends AbstractController
{

    /**
     * @Route("/category", name="category_index", methods={"GET"})
     */
    public function index(CategoryRepository $categoryRepository): Response
    {
        return $this->render('category/index.html.twig', [
            'categorys' => $categoryRepository->findAll(),
        ]);
    }
    /**
     * @Route("/category/new", name="category_new")
     * @param Request $request
     * @return Response
     */
    public function new(Request $request): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class,
            $category,
            ['method' => Request::METHOD_POST]
        );
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($category);
            $em->flush();
            return $this->redirectToRoute('category_index');
        }
        return $this->render('category/add.html.twig', [
            'form'          => $form->createView(),
        ]);
    }
}
