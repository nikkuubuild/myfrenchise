<?php

namespace Database\Factories;

use App\Models\Opportunity;
use App\Models\OpportunityFinancialBreakdown;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OpportunityFinancialBreakdown>
 */
class OpportunityFinancialBreakdownFactory extends Factory
{
    protected $model = OpportunityFinancialBreakdown::class;

    public function definition(): array
    {
        return [
            'opportunity_id' => Opportunity::factory(),
            'item_name' => fake()->randomElement([
                'Franchise Fee',
                'Kitchen Equipment',
                'Interior Architecture & Fit-out',
                'Initial Inventory Stock',
                'Refundable Security Deposit'
            ]),
            'amount' => fake()->numberBetween(1, 5) * 100000,
            'is_estimate' => fake()->boolean(30),
            'is_refundable' => false,
            'source_evidence' => 'Brand Agreement schedule dated '.now()->subMonths(2)->toDateString(),
            'sort_order' => 1,
        ];
    }
}
