<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Book;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BooksApiTest extends TestCase
{
    use RefreshDatabase;

    function test_can_get_all_books()
    {
        $books = Book::factory(4)->create();

        $this->getJson(route('books.index'))->assertJsonFragment([
            'title'     => $books[0]->title,
            'author'    => $books[0]->author,
            'published' => $books[0]->published,
        ])->assertJsonFragment([
            'title'     => $books[1]->title,
            'author'    => $books[1]->author,
            'published' => $books[1]->published,
        ]);
    }

    function test_can_get_one_book()
    {
        $book = Book::factory()->create();

        $this->getJson(route('books.show', $book))->assertJsonFragment([
            'title'     => $book->title,
            'author'    => $book->author,
            'published' => $book->published,
        ]);

    }

    function test_can_create_books()
    {
        $this->postJson(route('books.store'), [])
            ->assertJsonValidationErrorFor('title');

        $this->postJson(route('books.store'), [])
            ->assertJsonValidationErrorFor('author');

        $this->postJson(route('books.store'), [])
            ->assertJsonValidationErrorFor('published');

        $this->postJson(route('books.store'), [
            'title' => 'My New Book',
            'author' => 'Erickson Suero',
            'published' => '1983-06-01',
        ])->assertJsonFragment([
            'title' => 'My New Book',
            'author' => 'Erickson Suero',
            'published' => '1983-06-01',
        ]);

        $this->assertDatabaseHas('books', [
            'title' => 'My New Book',
            'author' => 'Erickson Suero',
            'published' => '1983-06-01',
        ]);
    }

    function test_can_update_books()
    {
        $book = Book::factory()->create();

        $this->patchJson(route('books.update', $book), [])
            ->assertJsonValidationErrorFor('title');

        $this->patchJson(route('books.update', $book), [])
            ->assertJsonValidationErrorFor('author');

        $this->patchJson(route('books.update', $book), [])
            ->assertJsonValidationErrorFor('published');    

        $this->patchJson(route('books.update', $book), [
            'title'     => 'Edited Title',
            'author'    => 'Edited Author',
            'published' => '2000-01-01',
        ])->assertJsonFragment([
            'title'     => 'Edited Title',
            'author'    => 'Edited Author',
            'published' => '2000-01-01',
        ]);

        $this->assertDatabaseHas('books', [
            'title'     => 'Edited Title',
            'author'    => 'Edited Author',
            'published' => '2000-01-01',
        ]);
    }

    function test_can_delete_book()
    {
        $book = Book::factory()->create();

        $this->deleteJson(route('books.destroy', $book))
            ->assertNoContent();
        
        $this->assertDatabaseCount('books', 0);
    }
}
