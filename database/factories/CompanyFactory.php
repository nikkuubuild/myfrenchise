<?php

namespace Database\Factories;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Company>
 */
class CompanyFactory extends Factory
{
    protected $model = Company::class;

    public function definition(): array
    {
        $brand = fake()->company();

        return [
            'legal_name' => $brand.' Private Limited',
            'brand_name' => $brand,
            'slug' => Str::slug($brand).'-'.fake()->unique()->randomNumber(3),
            'cin' => 'U'.fake()->numerify('#####').'DL'.fake()->numerify('####').'PTC'.fake()->numerify('######'),
            'gstin' => '07'.fake()->bothify('?????####?').'1Z'.fake()->randomLetter(),
            'pan' => fake()->bothify('?????####?'),
            'registered_address' => fake()->address(),
            'operational_state' => 'Delhi',
            'official_website' => 'https://example.com',
            'official_email' => 'contact@example.com',
            'official_phone' => '+91'.fake()->numerify('##########'),
            'incorporation_year' => fake()->numberBetween(2005, 2022),
            'status' => 'verified',
        ];
    }
}
