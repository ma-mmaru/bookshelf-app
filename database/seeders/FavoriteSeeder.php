<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\User;
use Illuminate\Database\Seeder;

class FavoriteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $books = Book::all();

        foreach ($users as $index => $user) {
            $favoriteCount = [3, 4, 5, 3, 4][$index % 5];
            $favoriteBooks = $books->random($favoriteCount);

            $user->favoriteBooks()->syncWithoutDetaching($favoriteBooks->pluck('id'));
        }
    }
}