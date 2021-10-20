<?php

namespace App\Controller;

use App\Entity\Game;
use App\Form\GameType;
use App\Repository\GameRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GameController extends AbstractController
{
    /**
     * @Route("/", name="app_home")
     */
    public function index(GameRepository $pinRepository): Response
    {
        $game = $pinRepository->findBy([], ['CreatedAt' => 'DESC']);

        return $this->render('game/index.html.twig', compact('game'));
    }

    /**
     * @Route("/game/create", name="app_game_create")
     */

    public function create(Request $request, EntityManagerInterface $em, UserRepository $userRepo): Response

    {
        $game = new Game;

        $form = $this->createForm(GameType::class, $game);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $janeDoe = $userRepo->findOneBy(['email' => 'janedoe@example.com']);
            $game->setUser($janeDoe);
            $em->persist($game);
            $em->flush();

            $this->addFlash('success', 'article crée avec succès');

            return $this->redirectToRoute('app_home');
        }

        return $this->render('game/create.html.twig', [

            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/game/{id<[0-9]+>}", name="app_game_show")
     */
    public function show(Game $game): Response
    {
        return $this->render('game/show.html.twig', compact('game'));
    }


    /**
     * @Route("/game/{id<[0-9]+>}/edit", name="app_game_edit")
     */
    public function edit(Request $request, EntityManagerInterface $em, Game $game): Response

    {
        $form = $this->createForm(GameType::class, $game, [
            'method' => 'PUT'
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Modifications enregistrés');

            return $this->redirectToRoute('app_home');
        }



        return $this->render('game/edit.html.twig', [
            'game' => $game,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/game/{id<[0-9]+>}/delete", name="app_game_delete")
     */
    public function delete(EntityManagerInterface $em, Game $game): Response

    {
        $em->remove($game);
        $em->flush();
        $this->addFlash('info', 'L\'article a bien été supprimé !');

        return $this->redirectToRoute('app_home');
    }
}
