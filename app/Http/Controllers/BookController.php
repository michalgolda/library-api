<?php

namespace App\Http\Controllers;

use App\Http\Resources\BookResource;
use App\Interfaces\BookRepositoryInterface;
use App\Interfaces\CustomerRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class BookController extends Controller
{
    private $bookRepository;
    private $customerRepository;

    public function __construct(BookRepositoryInterface $bookRepository, CustomerRepositoryInterface $customerRepository)
    {
        $this->bookRepository = $bookRepository;
        $this->customerRepository = $customerRepository;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $searchQuery = $request->get('query') ?? '';

        $existingBooks = $this->bookRepository->all($searchQuery);
        $serializedBooks = BookResource::collection($existingBooks);

        return response()->json([
            'data' => $serializedBooks,
            'meta' => [
                'current_page' => $existingBooks->currentPage(),
                'last_page' => $existingBooks->lastPage(),
                'per_page' => $existingBooks->perPage(),
                'total' => $existingBooks->total()
            ],
            'links' => [
                'first' => $existingBooks->url(1),
                'last' => $existingBooks->url($existingBooks->lastPage()),
                'prev' => $existingBooks->previousPageUrl(),
                'next' => $existingBooks->nextPageUrl(),
            ]
        ]);
    }


    /**
     * Display the specified resource.
     */
    public function show($bookId)
    {
        $existingBook = $this->bookRepository->getById($bookId);
        if (!$existingBook) return response()->json(['message' => 'Book not found']);

        $serializedBook = new BookResource($existingBook);

        return response()->json(['data' => $serializedBook]);
    }

    public function rent(Request $request, $bookId)
    {
        $existingBook = $this->bookRepository->getById($bookId);
        if (!$existingBook) return response()->json(['message' => 'Book not found'], 404);
        if ($existingBook->rentedBy()->exists()) return response()->json(['message' => 'Book already rented'], 400);

        $customerId = $request->get('customer_id');
        if (!$customerId) return response()->json(['message' => '"customer_id" query is required'], 400);

        $existingCustomer = $this->customerRepository->getById($customerId);
        if (!$existingCustomer) return response()->json(['message' => 'Customer not found'], 404);

        $this->bookRepository->update($existingBook->id, ['rented_by' => $customerId]);

        return response()->json(['message' => 'Book successfully rented']);
    }

    public function return(Request $request, $bookId)
    {
        $customerId = $request->get('customer_id');
        if (!$customerId) return response()->json(['message' => '"customer_id" query is required'], 400);

        $existingCustomer = $this->customerRepository->getById($customerId);
        if (!$existingCustomer) return response()->json(['message' => 'Customer not found'], 404);

        $existingBook = $this->bookRepository->getById($bookId);
        if (!$existingBook) return response()->json(['message' => 'Book not found'], 404);
        if (!$existingBook->rentedBy()->exists()) {
            return response()->json(['message' => 'Book is not rented'], 400);
        }

        if ($existingBook->rented_by != $existingCustomer->id) {
            return response()->json(['message' => 'You can not return a book if you do not rented it'], 400);
        }

        $this->bookRepository->update($existingBook->id, ['rented_by' => NULL]);

        return response()->json(['message' => 'Book successfully returned']);
    }
}
