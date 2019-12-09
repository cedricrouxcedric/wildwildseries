<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


/**
 * @Route("/category", name="category_")
 */
class CategoryController extends AbstractController
{

    /**
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return Response
     * @Route("/",name="index")
     */
    public function index(Request $request, EntityManagerInterface $em): Response

    {
        $categorys = $this->getDoctrine()->getRepository(Category::class)->findAll();
        $category = new Category();
        $form = $this->createForm(CategoryType::class,$category);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $em->persist($category);
            $em->flush();
            return $this->redirectToRoute('category_index');
        }
        return $this->render('wild/category.html.twig', [
            'categorys'  => $categorys,
            'form'       => $form->createView(),
        ]);
    }

}
