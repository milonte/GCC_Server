<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BookController extends AbstractController
{
    /**
     * @Route("/books", name="book", methods={"GET"})
     */
    public function index()
    {
        $mysqli = new \mysqli("localhost", "root", "wcs", "books_api");
        if ($mysqli->connect_errno) {
            echo "Echec lors de la connexion à MySQL : (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
        }

        $res = $mysqli->query('SELECT * FROM book');

        $books = [];
        $res->data_seek(0);
        while ($row = $res->fetch_assoc()) {
            $books[] = $row;
        }

        return $this->json($books);
    }

    /**
     * @param Request $request
     * @Route("/books", name="book_create", methods={"POST"})
     */
    public function create(Request $request)
    {
        $data = $request->getContent();
        $bookData = json_decode($data);

        $mysqli = new \mysqli("localhost", "root", "wcs", "books_api");
        if ($mysqli->connect_errno) {
            echo "Echec lors de la connexion à MySQL : (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
        }

        $stmt = $mysqli->prepare("INSERT INTO book(name, author) VALUES (?, ?)");
        $name = $bookData->name;
        $author = $bookData->author;
        $stmt->bind_param("ss", $name, $author);

        $res = $stmt->execute();

        $response = new JsonResponse('', Response::HTTP_CREATED);
        if (false === $res) {
            $response = new JsonResponse('Enregistrement impossible !',Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $response;
    }
}
