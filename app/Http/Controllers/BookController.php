<?php

namespace App\Http\Controllers;

use App\Http\Resources\BookResource;
use App\Models\Book;
use Illuminate\Http\Request;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Book::query();

        if ($request->filled('title')) {
            $title = $request->title;
            $query->where('title', $title);
        }

        if ($request->filled('author')) {
            $author = $request->author;
            $query->where('author', $author);
        }

        if ($request->filled('rented_by_first_name')) {
            $rentedByFirstName = $request->rented_by_first_name;
            $query->whereHas('rentedBy', function ($q) use ($rentedByFirstName) {
                $q->where('first_name', $rentedByFirstName);
            });
        }

        if ($request->filled('rented_by_last_name')) {
            $rentedByLastName = $request->rented_by_last_name;
            $query->whereHas('rentedBy', function ($q) use ($rentedByLastName) {
                $q->where('last_name', $rentedByLastName);
            });
        }
        $existingBooks = $query->paginate(20);
        return response()->json([
            'ok' => true,
            'data' => BookResource::collection($existingBooks),
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
        $existingBook = Book::find($bookId);
        if (!$existingBook) return response()->json(['ok' => false, 'message' => 'Book not found']);

        return response()->json(['ok' => true, 'data' => new BookResource($existingBook)]);
    }

}
