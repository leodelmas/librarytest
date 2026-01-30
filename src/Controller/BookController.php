<?php

namespace App\Controller;

use App\Entity\UserBook;
use App\Repository\UserBookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class BookController extends AbstractController
{
    #[Route('/collection', name: 'book_collection')]
    public function index(UserBookRepository $userBookRepository): Response
    {
        return $this->render('book/index.html.twig', [
            'userBooks' => $userBookRepository->findBy(['user' => $this->getUser()]),
        ]);
    }

    #[Route('/collection/{id}', name: 'book_delete', methods: ['DELETE'])]
    public function delete(UserBook $userBook, EntityManagerInterface $em): JsonResponse
    {
        if ($userBook->getUser() !== $this->getUser()) {
            return new JsonResponse(null, Response::HTTP_FORBIDDEN);
        }

        $em->remove($userBook);
        $em->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
