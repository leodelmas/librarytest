<?php

namespace App\Controller\Book;

use App\Application\Book\AddBookToCollection;
use App\Domain\Book\Port\BookCatalogInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class BookSearchController extends AbstractController
{
    #[Route('/search', name: 'book_search')]
    public function index(): Response
    {
        return $this->render('book_search/index.html.twig');
    }

    #[Route('/search/results', name: 'book_search_results')]
    public function results(Request $request, BookCatalogInterface $catalog): Response
    {
        $query = trim($request->query->getString('q'));

        if ($query === '') {
            return $this->render('book_search/_results.html.twig', ['books' => []]);
        }

        $data = $catalog->search($query);

        return $this->render('book_search/_results.html.twig', [
            'books' => $data['docs'] ?? [],
        ]);
    }

    #[Route('/search/add', name: 'book_search_add', methods: ['POST'])]
    public function add(Request $request, AddBookToCollection $addBookToCollection): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (empty($data['key'])) {
            return new JsonResponse(['error' => 'Missing key'], 400);
        }

        try {
            $userBook = $addBookToCollection->execute(
                user: $this->getUser(),
                key: $data['key'],
                title: $data['title'] ?? 'Unknown',
                author: $data['author'] ?? null,
                firstPublishYear: isset($data['firstPublishYear']) ? (int) $data['firstPublishYear'] : null,
                sourceCoverUrl: $data['coverUrl'] ?? null,
            );
        } catch (\DomainException) {
            return new JsonResponse(['error' => 'Book already in collection'], 409);
        }

        return new JsonResponse(['id' => $userBook->getBook()->getId()], 201);
    }
}
