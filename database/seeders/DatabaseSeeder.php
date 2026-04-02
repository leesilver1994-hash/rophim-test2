<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::query()->firstOrCreate(
            ['email' => 'admin@lasotuvi.uk'],
            [
                'name' => 'Admin',
                'password' => Hash::make('ChangeMeNow123!'),
                'is_admin' => true,
            ]
        );

        Article::query()->firstOrCreate(
            ['slug' => 'welcome-lasotuvi'],
            [
                'title' => 'Welcome to Lasotuvi',
                'excerpt' => 'Initial article seed for deployment smoke tests.',
                'content' => 'This content is a starter article. Replace in admin panel.',
                'is_published' => true,
                'published_at' => now(),
            ]
        );
    }
}
