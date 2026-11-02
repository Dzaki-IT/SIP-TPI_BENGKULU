<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\VerificationHistory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<VerificationHistory>
 */
class VerificationHistoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'actor_id' => User::factory(),
            'action' => 'diajukan',
            'from_status' => 'draft',
            'to_status' => 'diajukan',
            'catatan' => null,
        ];
    }
}
