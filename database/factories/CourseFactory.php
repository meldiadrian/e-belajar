<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Course;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Course>
 */
class CourseFactory extends Factory
{
    protected $model = Course::class;

    public function definition(): array
    {
        $title = fake()->sentence(4);

        return [
            'category_id' => Category::factory(),
            'created_by' => User::factory()->admin(),
            'title' => $title,
            'slug' => Str::slug($title) . '-' . uniqid(),
            'description' => fake()->paragraph(),
            'level' => fake()->randomElement(['beginner', 'intermediate', 'advanced']),
            'status' => 'published',
            'duration' => fake()->numberBetween(30, 240),
            'certificate_enabled' => true,
            'published_at' => now(),
        ];
    }
}
