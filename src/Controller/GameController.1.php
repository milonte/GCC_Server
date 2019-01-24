<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GameController extends AbstractController
{
    /**
     * @Route("/games/possess", name="possesses_games_index", methods={"GET"})
     */
    public function getPossess()
    {
        $mysqli = new \mysqli("localhost", "milonte", "25121997", "games_colelction_api");
        if ($mysqli->connect_errno) {
            echo "Echec lors de la connexion à MySQL : (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
        }

        $res = $mysqli->query(
            'SELECT game, user_id
            FROM possessed_game AS games
            JOIN user ON games.user_id = user.id
            WHERE user.mail = "tata@tata.fr"');

        $games = [];
        $res->data_seek(0);
        while ($row = $res->fetch_assoc()) {
            $games[] = $row;
        }

        return $this->json($games);
    }

    /**
     * @param Request $request
     * @Route("/games/possess/add", name="possesses_games_add", methods={"POST"})
     */
    public function create(Request $request)
    {
        $data = $request->getContent();
        $gameData = json_decode($data);

        $mysqli = new \mysqli("localhost", "milonte", "25121997", "games_colelction_api");
        if ($mysqli->connect_errno) {
            echo "Echec lors de la connexion à MySQL : (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
        }

        $stmt = $mysqli->prepare('SELECT COUNT(*) AS nb FROM possessed_game WHERE game = ? AND user_id = ?');
        $game = $gameData->game;
        $user_id = $gameData->userId;
        $stmt->bind_param("ii", $game, $user_id); 
        $res = $stmt->execute();
        return  new JsonResponse($stmt, Response::HTTP_CREATED);
        
        $stmt->close();

        
        $stmt = $mysqli->prepare(
            "INSERT INTO possessed_game (game, user_id)
            VALUES (?, ?)"
        );
        $game = $gameData->game;
        $user_id = $gameData->userId;
        $stmt->bind_param("ii", $game, $user_id); 

        $res = $stmt->execute();
        $response = new JsonResponse('', Response::HTTP_CREATED);
        if (false === $res) {
            $response = new JsonResponse('Enregistrement impossible !', Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $response;
    }
}
