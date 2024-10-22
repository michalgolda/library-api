<?php

namespace App\Repositories;

use App\Interfaces\BookRepositoryInterface;
use App\Models\Book;
use Nette\NotImplementedException;

class DatabaseBookRepository implements BookRepositoryInterface
{
    public function getById(int $id)
    {
        return Book::find($id);
    }

    public function all(array $filters = [])
    {
        $query = Book::query();

        $titleFilterValue = $filters['title'] ?? NULL;
        if ($titleFilterValue) {
            $query->where('title', $titleFilterValue);
        }

        $authorFilterValue = $filters['author'] ?? NULL;
        if ($authorFilterValue) {
            $query->where('author', $authorFilterValue);
        }

        $rentedByFirstNameFilterValue = $filters['rented_by_first_name'] ?? NULL;
        if ($rentedByFirstNameFilterValue) {
            $query->whereHas('rentedBy', function ($q) use ($rentedByFirstNameFilterValue) {
                $q->where('first_name', $rentedByFirstNameFilterValue);
            });
        }

        $rentedByLastNameFilterValue = $filters['rented_by_last_name'] ?? NULL;
        if ($rentedByLastNameFilterValue) {
            $query->whereHas('rentedBy', function ($q) use ($rentedByLastNameFilterValue) {
                $q->where('last_name', $rentedByLastNameFilterValue);
            });
        }

        $books = $query->paginate(20);
        return $books;
    }

    public function create(array $data)
    {
        return Book::create($data);
    }

    public function update(int $id, array $data)
    {
        $book = Book::find($id);
        $book->rented_by = $data['rented_by'];
        $book->save();
        $book->refresh();
    }

    public function delete(int $id)
    {
        throw new NotImplementedException();
    }
}
