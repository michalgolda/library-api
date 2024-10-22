<?php

namespace App\Http\Controllers;

use App\Http\Resources\BookResource;
use App\Models\Book;
use App\Models\Customer;
use Illuminate\Http\Request;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Book::query();

        $activeFilters = false;

        if ($request->filled('title')) {
            $title = $request->title;
            $query->where('title', $title);
            $activeFilters = true;
        }

        if ($request->filled('author')) {
            $author = $request->author;
            $query->where('author', $author);
            $activeFilters = true;
        }

        if ($request->filled('rented_by_first_name')) {
            $rentedByFirstName = $request->rented_by_first_name;
            $query->whereHas('rentedBy', function ($q) use ($rentedByFirstName) {
                $q->where('first_name', $rentedByFirstName);
            });
            $activeFilters = true;
        }

        if ($request->filled('rented_by_last_name')) {
            $rentedByLastName = $request->rented_by_last_name;
            $query->whereHas('rentedBy', function ($q) use ($rentedByLastName) {
                $q->where('last_name', $rentedByLastName);
            });
            $activeFilters = true;
        }
        $existingBooks = $query->paginate(20);

        $response = [
            'ok' => true,
            'data' => BookResource::collection($existingBooks)
        ];

        if (!$activeFilters) {
            $response['meta'] = [
                'current_page' => $existingBooks->currentPage(),
                'last_page' => $existingBooks->lastPage(),
                'per_page' => $existingBooks->perPage(),
                'total' => $existingBooks->total()
            ];
            $response['links'] = [
                'first' => $existingBooks->url(1),
                'last' => $existingBooks->url($existingBooks->lastPage()),
                'prev' => $existingBooks->previousPageUrl(),
                'next' => $existingBooks->nextPageUrl(),
            ];
        }

        return response()->json($response);
    }


    /**
     * Display the specified resource.
     */
    public function show($bookId)
    {
        $existingBook = Book::find($bookId);
        if (!$existingBook) return response()->json(['ok' => false, 'message' => 'Book not found']);

        return response()->json(['ok' => true, 'data' => new BookResource($existingBook)]);
    }

    public function rent(Request $request, $bookId)
    {
        $existingBook = Book::find($bookId);
        if (!$existingBook) return response()->json(['ok' => true, 'message' => 'Book not found'], 404);
        if ($existingBook->rentedBy()->exists()) return response()->json(['ok' => true, 'message' => 'Book already rented'], 400);

        $customerId = $request->get('customer_id');
        if (!$customerId) return response()->json(['ok' => true, 'message' => 'customer_id query is required'], 400);

        $existingCustomer = Customer::find($customerId);
        if (!$existingCustomer) return response()->json(['ok' => true, 'message' => 'Customer not found'], 404);

        $existingBook->rented_by = $customerId;
        $existingBook->save();
        $existingBook->refresh();


        return response()->json(['ok' => true, 'message' => 'Book successfully rented']);
    }

    public function return(Request $request, $bookId)
    {
        $customerId = $request->get('customer_id');
        if (!$customerId) return response()->json(['ok' => true, 'message' => 'customer_id query is required'], 400);

        $existingCustomer = Customer::find($customerId);
        if (!$existingCustomer) return response()->json(['ok' => true, 'message' => 'Customer not found'], 404);

        $existingBook = Book::find($bookId);
        if (!$existingBook) return response()->json(['ok' => true, 'message' => 'Book not found'], 404);
        if (!$existingBook->rentedBy()->exists()) {
            return response()->json(['ok' => true, 'message' => 'Book is not rented'], 400);
        }

        if ($existingBook->rented_by != $existingCustomer->id) {
            return response()->json(['ok' => false, 'message' => 'You can not return a book if you do not rented it'], 400);
        }

        $existingBook->rented_by = NULL;
        $existingBook->save();
        $existingBook->refresh();


        return response()->json(['ok' => true, 'message' => 'Book successfully returned']);
    }
}
