<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $books = Book::all();

        // ユーザーと書籍が存在しない場合は処理を終了
        if ($users->isEmpty() || $books->isEmpty()) {
            return;
        }

        $reviewsData = [
            ['book_index' => 0, 'user_index' => 0, 'rating' => 5, 'comment' => '猫の視点から描かれた人間模様が非常にユーモラスで面白いです。'],
            ['book_index' => 0, 'user_index' => 1, 'rating' => 4, 'comment' => '文体が独特で最初は読みづらかったですが、引き込まれました。'],
            ['book_index' => 0, 'user_index' => 2, 'rating' => 3, 'comment' => '古典の名作として一度は読む価値があります。'],
            // 書籍2 (3件)
            ['book_index' => 1, 'user_index' => 1, 'rating' => 5, 'comment' => '人間関係に悩んだら何度も読み返したい名著です！'],
            ['book_index' => 1, 'user_index' => 3, 'rating' => 5, 'comment' => '仕事でもプライベートでも使える知恵が詰まっています。'],
            ['book_index' => 1, 'user_index' => 4, 'rating' => 4, 'comment' => '相手の立場に立つ重要性を再認識させられました。'],
            // 書籍3 (3件)
            ['book_index' => 2, 'user_index' => 0, 'rating' => 5, 'comment' => 'エンジニア必読の一冊。コードの可読性が格段に上がります。'],
            ['book_index' => 2, 'user_index' => 2, 'rating' => 4, 'comment' => '具体的なビフォーアフターのコード例があってわかりやすい。'],
            ['book_index' => 2, 'user_index' => 4, 'rating' => 5, 'comment' => 'チーム開発をする全員に読んでほしい本です。'],
            // 書籍4 (3件)
            ['book_index' => 3, 'user_index' => 1, 'rating' => 5, 'comment' => '主主体性を持つことの大切さを学びました。人生のバイブルです。'],
            ['book_index' => 3, 'user_index' => 2, 'rating' => 4, 'comment' => 'ボリュームがありますが、得るものが大きいです。'],
            ['book_index' => 3, 'user_index' => 3, 'rating' => 4, 'comment' => 'インサイド・アウトの考え方が心に刺さりました。'],
            // 書籍5 (2件)
            ['book_index' => 4, 'user_index' => 0, 'rating' => 4, 'comment' => '坊っちゃんの真っ直ぐなキャラクターが好きです。爽快！'],
            ['book_index' => 4, 'user_index' => 3, 'rating' => 3, 'comment' => 'テンポが良く、さくっと読めました。'],
            // 書籍6 (3件)
            ['book_index' => 5, 'user_index' => 1, 'rating' => 5, 'comment' => '人類の歴史観がガラリと変わる衝撃的な一冊でした。'],
            ['book_index' => 5, 'user_index' => 2, 'rating' => 5, 'comment' => '虚構を信じる能力が人類を発展させたという視点が面白い。'],
            ['book_index' => 5, 'user_index' => 4, 'rating' => 4, 'comment' => '長編ですが読む手が止まりませんでした。'],
            // 書籍7 (3件)
            ['book_index' => 6, 'user_index' => 0, 'rating' => 4, 'comment' => 'プロフェッショナルとしてのコード設計思想が学べます。'],
            ['book_index' => 6, 'user_index' => 2, 'rating' => 3, 'comment' => '少し難易度は高めですが勉強になりました。'],
            ['book_index' => 6, 'user_index' => 3, 'rating' => 5, 'comment' => 'リファクタリングの意識が高まる良書です。'],
            // 書籍8 (3件)
            ['book_index' => 7, 'user_index' => 1, 'rating' => 5, 'comment' => '課題の分離という概念で対人関係のストレスが激減しました。'],
            ['book_index' => 7, 'user_index' => 3, 'rating' => 4, 'comment' => '対話形式なのでスラスラ頭に入ってきます。'],
            ['book_index' => 7, 'user_index' => 4, 'rating' => 5, 'comment' => '自分の人生を生きるための覚悟ができました。'],
            // 書籍9 (3件)
            ['book_index' => 8, 'user_index' => 0, 'rating' => 4, 'comment' => '漫才に対する熱量が伝わってきて胸が熱くなりました。'],
            ['book_index' => 8, 'user_index' => 2, 'rating' => 3, 'comment' => '独特の情景描写と空気感が素晴らしいです。'],
            ['book_index' => 8, 'user_index' => 4, 'rating' => 4, 'comment' => 'ラストシーンの余韻がすごく良かったです。'],
            // 書籍10 (3件)
            ['book_index' => 9, 'user_index' => 0, 'rating' => 5, 'comment' => 'いかに思い込みで世界を見ているかを痛感させられました。'],
            ['book_index' => 9, 'user_index' => 1, 'rating' => 5, 'comment' => 'グラフやデータで説明されていて説得力がすごいです。'],
            ['book_index' => 9, 'user_index' => 3, 'rating' => 4, 'comment' => 'ニュースの見方が変わる一冊。必読です。'],
            // 書籍11 (3件)
            ['book_index' => 10, 'user_index' => 2, 'rating' => 4, 'comment' => '物流のイノベーションがどれほど世界を変えたのかが分かる。'],
            ['book_index' => 10, 'user_index' => 3, 'rating' => 4, 'comment' => '地味なテーマに見えて、最高にドラマチックなビジネス書。'],
            ['book_index' => 10, 'user_index' => 4, 'rating' => 5, 'comment' => '標準化のパワーを痛感しました。面白い！'],
        ];

        foreach ($reviewsData as $data) {
            Review::create([
                'user_id' => $users[$data['user_index']]->id,
                'book_id' => $books[$data['book_index']]->id,
                'rating' => $data['rating'],
                'comment' => $data['comment'],
            ]);
        }
    }
}