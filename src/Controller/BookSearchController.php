<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\UserBook;
use App\Repository\BookRepository;
use App\Repository\UserBookRepository;
use App\Service\OpenLibraryClient;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[IsGranted('ROLE_USER')]
class BookSearchController extends AbstractController
{
    #[Route('/search', name: 'book_search')]
    public function index(): Response
    {
        return $this->render('book_search/index.html.twig');
    }

    #[Route('/search/results', name: 'book_search_results')]
    public function results(Request $request, OpenLibraryClient $openLibrary): Response
    {
        $query = trim($request->query->getString('q'));

        if ($query === '') {
            return $this->render('book_search/_results.html.twig', ['books' => []]);
        }

        $data = $openLibrary->search($query);

        return $this->render('book_search/_results.html.twig', [
            'books' => $data['docs'] ?? [],
        ]);
    }

    #[Route('/search/add', name: 'book_search_add', methods: ['POST'])]
    public function add(Request $request, BookRepository $bookRepository, UserBookRepository $userBookRepository, EntityManagerInterface $em, HttpClientInterface $httpClient): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (empty($data['key'])) {
            return new JsonResponse(['error' => 'Missing key'], 400);
        }

        $user = $this->getUser();
        $book = $bookRepository->findOneBy(['openLibraryKey' => $data['key']]);

        if ($book && $userBookRepository->findOneBy(['user' => $user, 'book' => $book])) {
            return new JsonResponse(['error' => 'Book already in collection'], 409);
        }

        if (!$book) {
            $coverUrl = null;
            if (!empty($data['coverUrl'])) {
                try {
                    $response = $httpClient->request('GET', $data['coverUrl']);
                    if ($response->getStatusCode() === 200) {
                        $coversDir = $this->getParameter('kernel.project_dir') . '/public/uploads/covers';
                        $filename = str_replace('/', '-', trim($data['key'], '/')) . '.jpg';
                        file_put_contents($coversDir . '/' . $filename, $response->getContent());
                        $coverUrl = '/uploads/covers/' . $filename;
                    }
                } catch (\Throwable) {
                }
            }

            $book = new Book();
            $book->setTitle($data['title'] ?? 'Unknown');
            $book->setAuthor($data['author'] ?? null);
            $book->setCoverUrl($coverUrl);
            $book->setFirstPublishYear(isset($data['firstPublishYear']) ? (int) $data['firstPublishYear'] : null);
            $book->setOpenLibraryKey($data['key']);

            $em->persist($book);
        }

        $userBook = new UserBook();
        $userBook->setUser($user);
        $userBook->setBook($book);
        $em->persist($userBook);
        $em->flush();

        return new JsonResponse(['id' => $book->getId()], 201);
    }
}
