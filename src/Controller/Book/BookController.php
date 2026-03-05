<?php

namespace App\Controller\Book;

use App\Application\Book\UpdateReadingStatus;
use App\Domain\Book\Entity\Note;
use App\Domain\Book\Entity\UserBook;
use App\Infrastructure\Persistence\NoteRepository;
use App\Infrastructure\Persistence\UserBookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
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
            'userBooks' => $userBookRepository->findByUserOrderedByStatus($this->getUser()),
        ]);
    }

    #[Route('/collection/{id}/show', name: 'book_show', methods: ['GET'])]
    public function show(UserBook $userBook): Response
    {
        if ($userBook->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        return $this->render('book/show.html.twig', [
            'userBook' => $userBook,
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

    #[Route('/collection/{id}/status', name: 'book_status', methods: ['POST'])]
    public function updateStatus(UserBook $userBook, UpdateReadingStatus $updateReadingStatus): JsonResponse
    {
        if ($userBook->getUser() !== $this->getUser()) {
            return new JsonResponse(null, Response::HTTP_FORBIDDEN);
        }

        $userBook = $updateReadingStatus->execute($userBook);
        $status = $userBook->getStatus();

        return new JsonResponse([
            'status' => $status->value,
            'label' => $status->label(),
            'colors' => $status->colors(),
        ]);
    }

    #[Route('/collection/{id}/note', name: 'book_note', methods: ['GET'])]
    public function note(UserBook $userBook, NoteRepository $noteRepository, EntityManagerInterface $em): Response
    {
        if ($userBook->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        $note = $noteRepository->findOneBy(['userBook' => $userBook]);
        if (!$note) {
            $note = new Note();
            $note->setUserBook($userBook);
            $em->persist($note);
            $em->flush();
        }

        return $this->render('book/note.html.twig', [
            'userBook' => $userBook,
            'note' => $note,
        ]);
    }

    #[Route('/collection/{id}/note', name: 'book_note_save', methods: ['POST'])]
    public function noteSave(UserBook $userBook, Request $request, NoteRepository $noteRepository, EntityManagerInterface $em): JsonResponse
    {
        if ($userBook->getUser() !== $this->getUser()) {
            return new JsonResponse(null, Response::HTTP_FORBIDDEN);
        }

        $data = json_decode($request->getContent(), true);
        $note = $noteRepository->findOneBy(['userBook' => $userBook]);
        if (!$note) {
            $note = new Note();
            $note->setUserBook($userBook);
            $em->persist($note);
        }

        $note->setContent($data['content'] ?? null);
        $note->setUpdatedAt(new \DateTime());
        $em->flush();

        return new JsonResponse(['success' => true]);
    }
}
