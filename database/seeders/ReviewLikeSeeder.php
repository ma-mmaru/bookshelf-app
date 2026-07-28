<?php

namespace Database\Seeders;

use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReviewLikeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $reviews = Review::all();

        foreach ($reviews as $review) {
            $eligibleUsers = $users->where('id', '!=', $review->user_id);

            $likeCount = rand(0, min(3, $eligibleUsers->count()));
            if ($likeCount > 0) {
                $likers = $eligibleUsers->random($likeCount);
                $review->likedByUsers()->syncWithoutDetaching($likers->pluck('id'));
            }
        }
    }
}