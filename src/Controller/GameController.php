<?php

namespace App\Controller;

use App\Entity\Game;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GameController extends AbstractController
{
    /**
     * @Route("/games/possess/all", name="possesses_games_index", methods={"GET"})
     */
    public function getAllPossess(Request $request)
    {
        $em = $this->getDoctrine()->getmanager()->getRepository(Game::class);
        $games = $em->findBy(['user_id' => 1]);
        $result = [];
        foreach ($games as $game) {
            $result[] = ['game' => $game->getGame(),
                'user_id' => $game->getUserId()->getId()];
        }
        return new JsonResponse($result, Response::HTTP_CREATED);
    }

    /**
     * @Route("/games/possess", name="possesses_game", methods={"GET"})
     */
    public function getPossess(Request $request)
    {
        $data = $request->getContent();
        $gameData = json_decode($data);

        $em = $this->getDoctrine()->getmanager()->getRepository(Game::class);
        if (($em->findBy(['user_id' => $gameData->userId, 'game' => $gameData->game]))) {
            return new JsonResponse(true, Response::HTTP_CREATED);
        }
        return new JsonResponse(false, Response::HTTP_CREATED);
    }

    /**
     * @param Request $request
     * @Route("/games/possess/add", name="possesses_games_add", methods={"POST"})
     */
    public function create(Request $request)
    {
        $data = $request->getContent();
        $gameData = json_decode($data);

        $em = $this->getDoctrine()->getmanager()->getRepository(Game::class);
        if (!($em->findBy(['user_id' => $gameData->userId, 'game' => $gameData->game]))) {
            $game = new Game();
            $user = $this->getDoctrine()->getRepository(User::class)
                ->findOneBy(['id' => $gameData->userId]);
            $game->setGame($gameData->game);
            $game->setUserId($user);
            $em = $this->getDoctrine()->getManager();
            $em->persist($game);
            $em->flush();
            return new JsonResponse('Add Complete !', Response::HTTP_CREATED);
        }
        return new JsonResponse('Can t add !', Response::HTTP_CREATED);
    }

     /**
     * @param Request $request
     * @Route("/games/possess/remove", name="possesses_games_remove", methods={"POST"})
     */
    public function remove(Request $request)
    {
        $data = $request->getContent();
        $gameData = json_decode($data);
        $game = $this->getDoctrine()->getRepository(Game::class)
        ->findOneBy(['game' => $gameData->game, 'user_id' => $gameData->userId]);
        if(!$game) {
            return new JsonResponse('Game not exists !', Response::HTTP_CREATED);
        }
            
        $em = $this->getDoctrine()->getManager();
        $em->remove($game);
        $em->flush();
        return new JsonResponse('Delete Complete !', Response::HTTP_CREATED);
    }
}
