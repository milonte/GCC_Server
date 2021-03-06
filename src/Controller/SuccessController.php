<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Success;

class SuccessController extends AbstractController
{
    /**
     * @Route("/success/all", name="success_index", methods={"GET"})
     */
    public function getAllSuccesses(Request $request)
    {
        $em = $this->getDoctrine()->getmanager()->getRepository(Success::class);
        $successes = $em->findAll();
        $result = [];
        foreach ($successes as $success) {
            $result[] = [
                'id' => $success->getId(),
                'name' => $success->getName(),
                'title' => $success->getTitle(),
                'description' => $success->getDescription(),
                'image_url' => $success->getImageUrl(),
            ];
        }
        return new JsonResponse($result, Response::HTTP_CREATED);
        
    }

    /**
     * @Route("/success/user", name="success_user", methods={"POST"})
     */
    public function getUserSuccesses(Request $request)
    {
        $data = $request->getContent();
        $userData = json_decode($data);
        $em = $this->getDoctrine()->getmanager();

        $user = $em->getRepository(User::class)
        ->findOneBy(['id' => $userData->userId]);

        $result = [];
        foreach ($user->getSuccesses() as $success) {
            $result[] = $success->getName();
        }
        return new JsonResponse($result, Response::HTTP_CREATED);
        
    }

    /**
     * @param Request $request
     * @Route("/success/add", name="success_add", methods={"POST"})
     */
    public function addSuccess(Request $request)
    {
        $data = $request->getContent();
        $successData = json_decode($data);

        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)
        ->findOneBy(['id' => $successData->userId]);
        $success = $em->getRepository(Success::class)
        ->findOneBy(['id' => $successData->successId]);

        $user->addSuccess($success);
        $em->persist($user);
        $em->flush();
        return new JsonResponse('Add Complete !', Response::HTTP_CREATED);
    }

     /**
     * @param Request $request
     * @Route("/success/remove", name="success_remove", methods={"POST"})
     */
    public function removePossess(Request $request)
    {
        $data = $request->getContent();
        $successData = json_decode($data);
        $success = $this->getDoctrine()->getRepository(success::class)
        ->findOneBy(['success' => $successData->success, 'user_id' => $successData->userId, 'possessed' => true]);
        if(!$success) {
            return new JsonResponse('success not exists !', Response::HTTP_CREATED);
        }
            
        $em = $this->getDoctrine()->getManager();
        $em->remove($success);
        $em->flush();
        return new JsonResponse('Delete Complete !', Response::HTTP_CREATED);
    }

}
