<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Company;
use App\Models\Opportunity;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Opportunity>
 */
class OpportunityFactory extends Factory
{
    protected $model = Opportunity::class;

    public function definition(): array
    {
        $title = fake()->words(3, true).' Franchise';
        $min = fake()->numberBetween(5, 15) * 100000;
        $max = $min + (fake()->numberBetween(3, 8) * 100000);

        return [
            'company_id' => Company::factory(),
            'category_id' => Category::factory(),
            'title' => ucwords($title),
            'slug' => Str::slug($title).'-'.fake()->unique()->randomNumber(3),
            'summary' => fake()->paragraph(),
            'description' => fake()->paragraphs(3, true),
            'business_model' => 'FOFO',
            'min_investment' => $min,
            'max_investment' => $max,
            'franchise_fee' => 250000,
            'royalty_percentage' => 6.00,
            'min_space_sqft' => 150,
            'max_space_sqft' => 300,
            'min_term_years' => 5,
            'publication_status' => 'published',
            'is_featured' => false,
            'published_at' => now()->subDays(5),
            'last_verified_at' => now()->subDays(2),
            'verification_valid_until' => now()->addDays(180),
            'disclaimer' => 'Approval authority remains solely with the brand.',
        ];
    }

    /**
     * Indicate that the opportunity is published and verified.
     */
    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'publication_status' => 'published',
            'last_verified_at' => now()->subDays(2),
            'verification_valid_until' => now()->addDays(180),
        ]);
    }

    /**
     * Indicate that the opportunity is draft or unpublished.
     */
    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'publication_status' => 'draft',
            'last_verified_at' => null,
            'published_at' => null,
        ]);
    }
}
