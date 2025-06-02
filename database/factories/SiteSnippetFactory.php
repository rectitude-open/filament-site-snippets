<?php

declare(strict_types=1);

namespace RectitudeOpen\FilamentSiteSnippets\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class SiteSnippetFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'key' => $this->faker->unique()->word,
            'content' => $this->faker->text(),
            'type' => $this->faker->randomElement(['text', 'html', 'image_url']),
            'description' => $this->faker->optional()->sentence,
        ];
    }
}
