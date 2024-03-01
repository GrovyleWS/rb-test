<?php

namespace App\Controller;

use App\Entity\Team;
use App\Utils\TournamentService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class TeamController extends AbstractController
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    #[Route('/teams', name: 'teams_index')]
    public function index()
    {
        $teams = $this->entityManager->getRepository(Team::class)->findAll();

        return $this->render('team/index.html.twig', compact('teams'));
    }

    #[Route('/teams/create', name: 'teams_create')]
    public function create()
    {
        return $this->render('team/create.html.twig');
    }

    #[Route('/teams/delete/{id}', name: 'teams_delete', methods: ['POST'])]
    public function delete(Team $team, TournamentService $tournamentService)
    {
        $tournaments = $team->getTournaments();
        $this->entityManager->remove($team);
        foreach ($tournaments as $tournament) {
            $tournamentService->regenerateSchedule($tournament);
        }
        $this->entityManager->flush();

        return $this->redirectToRoute('teams_index');
    }

    #[Route('/teams/store', name: 'teams_store', methods: ['POST'])]
    public function store(Request $request, ValidatorInterface $validator)
    {
        $team = new Team();
        $name = $request->get('name');
        $team->setName(trim($name));
        $errors = $validator->validate($team);
        if (count($errors) > 0) {
            foreach ($errors as $error) {
                $this->addFlash('error', $error->getMessage());
            }

            return $this->redirectToRoute('teams_create');
        }
        $this->entityManager->persist($team);
        $this->entityManager->flush();

        return $this->redirectToRoute('teams_index');
    }
}
