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

    public function all(string $searchQuery = '')
    {
        $books = Book::search($searchQuery)->paginate(20);
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
