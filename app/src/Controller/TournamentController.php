<?php

namespace App\Controller;

use App\Entity\Team;
use App\Entity\Tournament;
use App\Utils\TournamentService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class TournamentController extends AbstractController
{
    public function __construct(private EntityManagerInterface $entityManager,
        private SluggerInterface $slugger,
        private TournamentService $tournamentService)
    {
    }

    #[Route('/tournaments', name: 'tournaments_index')]
    public function index()
    {
        $tournaments = $this->entityManager->getRepository(Tournament::class)->findAll();

        return $this->render('tournament/index.html.twig', compact('tournaments'));
    }

    #[Route('/tournaments/create', name: 'tournaments_create')]
    public function create()
    {
        $teams = $this->entityManager->getRepository(Team::class)->findAll();

        return $this->render('tournament/create.html.twig', compact('teams'));
    }

    #[Route('/tournaments/store', name: 'tournaments_store', methods: ['POST'])]
    public function store(Request $request, ValidatorInterface $validator)
    {
        $tournament = new Tournament();
        // не идеально, но как минимум инъекции здесь не грозят
        $tournament->setName(trim($request->get('name')));
        // можно проверить корректность и кинуть 400
        $teamIds = $request->get('tournament_teams');
        $teams = $this->entityManager->getRepository(Team::class)->findBy(['id' => $teamIds]);
        $tournament->setSlug($this->slugger->slug(mb_strtolower($tournament->getName()))->toString());
        $tournament->addTeams($teams);
        $errors = $validator->validate($tournament);
        if (count($errors) > 0) {
            foreach ($errors as $error) {
                $this->addFlash('error', $error->getMessage());
            }

            return $this->redirectToRoute('tournaments_create');
        }
        $this->tournamentService->generateSchedule($tournament);
        $this->entityManager->persist($tournament);
        $this->entityManager->flush();

        return $this->redirectToRoute('tournaments_index');
    }

    #[Route('/tournaments/{slug}/edit', name: 'tournaments_edit')]
    public function edit(Request $request, Tournament $tournament)
    {
        $teams = $tournament->getTeams();
        $teamIds = $tournament->getTeamsIdArray();
        $additionalTeams = $this->entityManager->getRepository(Team::class)->getTeamsWhereIdNotIn($teamIds);

        // изменить имя шаблона
        return $this->render('tournament/create.html.twig', compact('tournament', 'additionalTeams', 'teams'));
    }

    #[Route('/tournaments/{slug}/update', name: 'tournaments_update', methods: ['POST'])]
    public function update(Request $request, ValidatorInterface $validator, Tournament $tournament)
    {
        $tournament->setName(trim($request->get('name')));
        $teamIds = $request->get('tournament_teams');
        $teams = $this->entityManager->getRepository(Team::class)->findBy(['id' => $teamIds]);
        $tournament->setSlug($this->slugger->slug(mb_strtolower($tournament->getName()))->toString());
        $tournament->removeTeams($tournament->getTeams());
        $tournament->addTeams($teams);
        $errors = $validator->validate($tournament);
        if (count($errors) > 0) {
            foreach ($errors as $error) {
                $this->addFlash('error', $error->getMessage());
            }

            return $this->redirectToRoute('tournaments_update', compact('tournament'));
        }
        $this->tournamentService->regenerateSchedule($tournament);
        $this->entityManager->flush();

        return $this->redirectToRoute('tournaments_index');
    }

    #[Route('/tournaments/{slug}', name: 'tournaments_show')]
    public function show(Tournament $tournament)
    {
        $games = $tournament->getGamesIndexedById();

        return $this->render('tournament/show.html.twig', [
            'tournament' => $tournament,
            'games' => $games,
        ]);
    }

    #[Route('/tournaments/{slug}/delete', name: 'tournaments_delete', methods: ['POST'])]
    public function delete(Tournament $tournament)
    {
        $this->entityManager->remove($tournament);
        $this->entityManager->flush();
        $this->redirectToRoute('tournaments_index');
    }
}
