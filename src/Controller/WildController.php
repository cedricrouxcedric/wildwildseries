<?php


namespace App\Controller;

use App\Entity\Category;
use App\Entity\Episode;
use App\Entity\Program;
use App\Entity\Season;
use App\Form\ProgramSearchType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class WildController
 * @package App\Controller
 ** @Route("/wild")
 */
class WildController extends AbstractController
{
    /**
     * Show all rows from Program's entity
     *
     * @Route("/", name="wild_index")
     * @return Response
     */
    public function index(): Response
    {
        $programs = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findAll();
        $form = $this->createForm(
                    ProgramSearchType::class,
                    null,
                    ['method' => Request::METHOD_GET]
        );
        if (!$programs) {
            throw $this->createNotFoundException(
                'No program found in program\'s table.'
            );
        }
        return $this->render('wild/index.html.twig',
            ['programs' => $programs,
                'form' => $form->createView(),
                ]
        );
    }

    /**
     * Getting a program with a formatted slug for title
     *
     * @param string $slug The slugger
     * @Route("/show/{slug<^[ a-zA-Z0-9]+$>}", defaults={"slug" = null}, name="show")
     * @return Response
     */
    public function show(?string $slug): Response
    {
        if (!$slug) {
            throw $this->createNotFoundException('Noslug has been sent to find a program in program\'s table.');
        }
        $slug = preg_replace(
            '/-/',
            ' ', ucwords(trim(strip_tags($slug)), "-")
        );
        $program = $this->getDoctrine()->getRepository(Program::class)->findOneBy(['title' => mb_strtolower($slug)]);
        if (!$program) {
            throw $this->createNotFoundException(
                'No program with ' . $slug . ' title, found in program\'s table.'
            );
        }
        $seasons = $this->getDoctrine()
            ->getRepository(Season::class)
            ->findBy([
                'program' => $program,
            ]);
        return $this->render('wild/show.html.twig', [
            'seasons' => $seasons,
            'program' => $program,
            'slug' => $slug,
        ]);
    }

    /**
     * @return Response
     * @Route("/categorys",name="wild_categorys")
     */
    public function allcategory(): Response
    {
        $categorys = $this->getDoctrine()->getRepository(Category::class)->findAll();
        return $this->render('wild/category.html.twig',[
            'categorys'=>$categorys,
        ]);
    }
    /**
     * @param string $categoryName The category
     * @route ("/category/{categoryName<^[ a-zA-Z0-9]+$>}", defaults={"category" = null},name="wild_category")
     * @return Response
     */
    public function showByCategory(?string $categoryName): Response
    {
        if (!$categoryName) {
            throw $this->createNotFoundException('no program found in this category.');
        }
        $categoryName = preg_replace(
            '/-/', ' ', ucwords(trim(strip_tags($categoryName)), "-")
        );
        $category = $this->getDoctrine()->getRepository(Category::class)->findOneBy(['name' => mb_strtolower($categoryName)]);
        if (!$category) {
            throw $this->createNotFoundException('No category with' . $categoryName . 'found in category\'s table');
        }
        $programs = $this->getDoctrine()->getRepository(Program::class)->findBy(['category' => $category]);
        if (!$programs) {
            throw $this->createNotFoundException('No programs found in program\'s table.');
        }

        return $this->render('wild/programs.html.twig', [
            'programs' => $programs,
            'category' => $category,
        ]);
    }

    /**
     * @param string|null $slug
     * @return Response
     * @Route("/program/{slug<^[ a-zA-Z0-9]+$>}", defaults={"slug" = null}, name="Program")
     */
    public function showByProgram(?string $slug): Response
    {
        if (!$slug) {
            throw $this->createNotFoundException('no program found .');
        }
        $slug = preg_replace(
            '/-/', ' ', ucwords(trim(strip_tags($slug)), "-")
        );
        $program = $this->getDoctrine()->getRepository(Program::class)->findOneBy(['title' => mb_strtolower($slug)]);

        $seasons = $this->getDoctrine()
            ->getRepository(Season::class)
            ->findBy([
                'program' => $program,
            ]);
        if (!$program) {
            throw $this->createNotFoundException('No category with' . $slug . 'found in category\'s table');
        }
        $programs = $this->getDoctrine()->getRepository(Program::class)->findBy(['title' => $slug]);
        if (!$programs) {
            throw $this->createNotFoundException('No programs found in program\'s table.');
        }
        return $this->render('wild/program.html.twig', [
            'program' => $program,
            'seasons' => $seasons,
        ]);
    }

    /**
     * @param int $saison
     * @return Response
     * @Route("/episode/{saison}", name="show_episodes")
     */
    public function showBySeason(int $saison): Response
    {
        if (!$saison) {
            throw $this
                ->createNotFoundException('No season has been find in season\'s table.');
        }
        $season = $this->getDoctrine()->getRepository(Season::class)->find($saison);
        $program = $season->getProgram();
        $episodes = $season->getEpisodes();
        if (!$season) {
            throw $this->createNotFoundException(
                'No season with '.$saison.' season, found in Season\'s table.'
            );
        }
        return $this->render('wild/episode.html.twig', [
            'program' => $program,
            'season' => $season,
            'episodes' => $episodes,
        ]);
    }
    /**
     * @param Episode $episode
     * @Route("/episode/{id}", name="episode")
     * @return Response
     */
    public function showEpisode (Episode $episode): Response
    {
        $season = $episode->getSeason();
        $program = $season->getProgram();
        return $this->render('wild/episode.html.twig',[
            'episode'    => $episode,
            'season'     => $season,
            'program'    => $program,
        ]);
    }

}
