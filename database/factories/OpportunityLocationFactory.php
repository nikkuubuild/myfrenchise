<?php

namespace Database\Factories;

use App\Models\Opportunity;
use App\Models\OpportunityLocation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OpportunityLocation>
 */
class OpportunityLocationFactory extends Factory
{
    protected $model = OpportunityLocation::class;

    public function definition(): array
    {
        $states = [
            ['code' => 'UP', 'name' => 'Uttar Pradesh', 'district' => 'Lucknow'],
            ['code' => 'MH', 'name' => 'Maharashtra', 'district' => 'Pune'],
            ['code' => 'DL', 'name' => 'Delhi NCR', 'district' => 'New Delhi'],
            ['code' => 'KA', 'name' => 'Karnataka', 'district' => 'Bengaluru'],
            ['code' => 'RJ', 'name' => 'Rajasthan', 'district' => 'Jaipur'],
        ];
        $selected = fake()->randomElement($states);

        return [
            'opportunity_id' => Opportunity::factory(),
            'state_code' => $selected['code'],
            'state_name' => $selected['name'],
            'district' => $selected['district'],
            'territory_tier' => 'Tier 2',
            'is_exclusive' => true,
            'status' => 'available',
        ];
    }
}
