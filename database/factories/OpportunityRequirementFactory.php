<?php

namespace Database\Factories;

use App\Models\Opportunity;
use App\Models\OpportunityRequirement;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OpportunityRequirement>
 */
class OpportunityRequirementFactory extends Factory
{
    protected $model = OpportunityRequirement::class;

    public function definition(): array
    {
        return [
            'opportunity_id' => Opportunity::factory(),
            'requirement_type' => fake()->randomElement(['space', 'power', 'water', 'license', 'experience']),
            'title' => fake()->sentence(3),
            'description' => fake()->sentence(8),
            'is_mandatory' => true,
        ];
    }
}
