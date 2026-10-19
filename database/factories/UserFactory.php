<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('Password123!'),
            'role' => 'customer',
            'phone' => '9876543210',
            'state' => 'MH',
            'city' => 'Mumbai',
            'is_active' => true,
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Customer role state.
     */
    public function customer(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'customer',
        ]);
    }

    /**
     * Case manager role state.
     */
    public function caseManager(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'case_manager',
        ]);
    }

    /**
     * Verification officer role state.
     */
    public function verificationOfficer(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'verification_officer',
        ]);
    }

    /**
     * Administrator role state.
     */
    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'admin',
        ]);
    }

    /**
     * Deactivated user state.
     */
    public function deactivated(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
