<?php


namespace App\Controller;

use App\Entity\Actor;
use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\Episode;
use App\Entity\Program;
use App\Entity\Season;
use App\Form\CommentType;
use App\Form\ProgramSearchType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
/**
 * @Route("/wild", name="wild_")
 */
class WildController extends AbstractController
{
    /**
     * @Route("/", name="index")
     * @param EntityManagerInterface $em
     * @param Requeste $request
     * @return Response
     */
    public function index(Request $request, EntityManagerInterface $em): Response
    {
        $programs = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findAll();

        if (!$programs) {
            throw $this->createNotFoundException(
                'No program found in program\'s table.'
            );
        }
        $form = $this->createForm(
            ProgramSearchType::class,
            null,
            ['method' => Request::METHOD_GET]
        );
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $programs = $em->getRepository(Program::class)->findBy(['title' => $data['searchField']]);
        }
        return $this->render('wild/index.html.twig',
            ['programs' => $programs,
                'form' => $form->createView(),
            ]
        );
    }


    /**
     *
     * @Route("/show/{slug}", name="show")
     * @param Program $program
     * @return Response
     */
    public function show(Program $program): Response
    {
        $seasons = $program->getSeasons();
        return $this->render('wild/show.html.twig', [
            'program' => $program,
            'seasons' => $seasons,
        ]);
    }

    /**
     * @param string|null $categoryName
     * @return Response
     * @Route("/category/{categoryName<^[a-z0-9-]+$>}", defaults={"categoryName" = null}, name="show_category")
     */
    public function showByCategory(string $categoryName = ''): Response
    {
        if (!$categoryName) {
            throw $this
                ->createNotFoundException('No category has been sent to find a program in program\'s table.');
        }
        $categoryName = preg_replace(
            '/-/',
            ' ', ucwords(trim(strip_tags($categoryName)), "-")
        );
        $category = $this->getDoctrine()
            ->getRepository(Category::class)
            ->findOneBy(['name' => mb_strtolower($categoryName)]);
        $programs = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findBy(['category' => $category]);
        if (!$category) {
            throw $this->createNotFoundException(
                'No program with ' . $categoryName . ' title, found in program\'s table.'
            );
        }
        return $this->render('wild/category.html.twig', [
            'programs' => $programs,
            'categoryName' => $categoryName,
        ]);
    }

    /**
     * Getting a program with a formatted slug for title
     *
     * @param string $slug The slugger
     * @Route("/showByProgram/{slug}", defaults={"slug" = null}, name="show_program")
     * @return Response
     */
    public function showByProgram(string $slug): Response
    {
        if (!$slug) {
            throw $this
                ->createNotFoundException('No slug has been sent to find a program in program\'s table.');
        }
        $slug = preg_replace(
            '/-/',
            ' ', ucwords(trim(strip_tags($slug)), "-")
        );
        $program = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findOneBy(['title' => mb_strtolower($slug)]);
        $seasons = $program->getSeasons();
        if (!$program) {
            throw $this->createNotFoundException(
                'No program with ' . $slug . ' title, found in program\'s table.'
            );
        }

        $actors = $program->getActors();

        return $this->render('wild/program.html.twig', [
            'program' => $program,
            'slug' => $slug,
            'seasons' => $seasons,
            'actors' => $actors,
        ]);
    }

    /**
     * @param int $id
     * @return Response
     * @Route("/program/season/{id}", defaults={"id" = null}, name="program_season")
     */
    public function showBySeason(int $id): Response
    {
        $season = $this->getDoctrine()
            ->getRepository(Season::class)
            ->findOneBy(['id' => $id]);
        $program = $season->getProgram();
        $episodes = $season->getEpisodes();
        return $this->render('wild/season.html.twig', [
            'season' => $season,
            'program' => $program,
            'episodes' => $episodes,
        ]);
    }

    /**
     * @param Episode $episode
     * @return Response
     * @Route("/episode/{slug}", name="show_episode")
     */
    public function showEpisode(Episode $episode, Request $request, EntityManagerInterface $entityManager): Response
    {
        $season = $episode->getSeason();
        $program = $season->getProgram();
        $comments = $episode->getComments();

        $comment = new Comment();

        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $author = $this->getUser();
            $comment->setEpisode($episode);
            $comment->setAuthor($author);
            $entityManager->persist($comment);
            $entityManager->flush();
            return $this->redirectToRoute('wild_show_episode',['slug' => $episode->getSlug()]);
        }
        return $this->render('wild/episode.html.twig', [
            'episode' => $episode,
            'season' => $season,
            'program' => $program,
            'comments' => $comments,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/actors", name="show_actors")
     */
    public function showAllActors()
    {
        $actors = $this->getDoctrine()
            ->getRepository(Actor::class)
            ->findAll();
        return $this->render('wild/actors.html.twig', [
            'actors' => $actors,
        ]);

    }

    /**
     * @param Actor $actor
     * @return Response
     * @Route("/actor/{name}", name="show_actor")
     */
    public function showActor(Actor $actor): Response
    {
        $programs = $actor->getProgram();
        return $this->render('wild/actor.html.twig', [
            'actor' => $actor,
            'programs' => $programs,
        ]);
    }
    /**
     * Getting a program with a formatted slug for title
     *
     * @param string $slug The slugger
     * @Route("/showActorsByProgram/{slug}", defaults={"slug" = null}, name="show_program_actors")
     * @return Response
     */
    public function showActorsByProgram(string $slug): Response
    {
        if (!$slug) {
            throw $this
                ->createNotFoundException('No slug has been sent to find a program in program\'s table.');
        }
        $slug = preg_replace(
            '/-/',
            ' ', ucwords(trim(strip_tags($slug)), "-")
        );
        $program = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findOneBy(['title' => mb_strtolower($slug)]);
        $seasons = $program->getSeasons();
        if (!$program) {
            throw $this->createNotFoundException(
                'No program with ' . $slug . ' title, found in program\'s table.'
            );
        }

        $actors = $program->getActors();

        return $this->render('wild/actors_program.html.twig', [
            'program' => $program,
            'slug' => $slug,
            'seasons' => $seasons,
            'actors' => $actors,
        ]);
    }

    /**
     * @Route("/deleteComment/{id}", name="delete", methods={"DELETE", "GET"})
     * @param EntityManagerInterface $entityManager
     * @param int $id
     * @return Response
     */
    public function delete(EntityManagerInterface $entityManager, int $id):Response
    {
        $comment = $this->getDoctrine()->getRepository(Comment::class)->find($id);
        $episode = $comment->getEpisode();
        $entityManager->remove($comment);
        $entityManager->flush();

        return $this->redirectToRoute('wild_show_episode',['slug' => $episode->getSlug()]);
    }
    /**
     * @Route("/my-profil", name="my_profil")
     * @return Response
     */
    public function profilUser(): Response
    {
        return $this->render('security/profil.html.twig');
    }
}
