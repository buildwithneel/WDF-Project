<?php

namespace Database\Seeders;

use App\Models\Bus;
use App\Models\Route;
use App\Models\Schedule;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * DatabaseSeeder
 *
 * Seeds all demo data in the correct order (respects FK dependencies).
 * Run with: php artisan db:seed  OR  php artisan migrate:fresh --seed
 */
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── 1. Users ──────────────────────────────────────────────────
        // Admin user — can access /admin routes
        User::create([
            'name'     => 'Admin User',
            'email'    => 'admin@busgo.com',
            'password' => Hash::make('password123'), // bcrypt hashed by Eloquent
            'role'     => 'admin',
        ]);

        // Secondary alias for admin
        User::create([
            'name'     => 'Admin Backup',
            'email'    => 'admin@bus.test',
            'password' => Hash::make('password123'),
            'role'     => 'admin',
        ]);

        // Regular passenger user
        User::create([
            'name'     => 'Test Passenger',
            'email'    => 'user@busgo.com',
            'password' => Hash::make('password123'),
            'role'     => 'user',
        ]);

        User::create([
            'name'     => 'Test Passenger (Local)',
            'email'    => 'user@bus.test',
            'password' => Hash::make('password123'),
            'role'     => 'user',
        ]);

        // ── 2. Buses ──────────────────────────────────────────────────
        $bus1 = Bus::create(['name' => 'Royal Express (AC)',   'type' => 'AC',      'total_seats' => 20]);
        $bus2 = Bus::create(['name' => 'Night Rider (Sleeper)', 'type' => 'Sleeper', 'total_seats' => 20]);
        $bus3 = Bus::create(['name' => 'City Cruiser',          'type' => 'Non-AC',  'total_seats' => 20]);

        // ── 3. Routes ─────────────────────────────────────────────────
        $r1 = Route::create(['source' => 'Mumbai',    'destination' => 'Pune',      'fare' => 450.00]);
        $r2 = Route::create(['source' => 'Delhi',     'destination' => 'Jaipur',    'fare' => 650.00]);
        $r3 = Route::create(['source' => 'Ahmedabad', 'destination' => 'Mumbai',    'fare' => 850.00]);
        $r4 = Route::create(['source' => 'Bangalore', 'destination' => 'Hyderabad', 'fare' => 950.00]);

        // ── 4. Schedules (Today and upcoming days) ─────────────────────
        // Schedule::create() automatically generates seats via the model observer/booted hook!
        foreach ([0, 1, 2] as $dayOffset) {
            $date = now()->addDays($dayOffset)->toDateString();
            Schedule::create(['bus_id' => $bus1->id, 'route_id' => $r1->id, 'travel_date' => $date, 'departure_time' => '08:00']);
            Schedule::create(['bus_id' => $bus2->id, 'route_id' => $r1->id, 'travel_date' => $date, 'departure_time' => '21:30']);
            Schedule::create(['bus_id' => $bus1->id, 'route_id' => $r2->id, 'travel_date' => $date, 'departure_time' => '07:30']);
            Schedule::create(['bus_id' => $bus2->id, 'route_id' => $r2->id, 'travel_date' => $date, 'departure_time' => '22:00']);
            Schedule::create(['bus_id' => $bus3->id, 'route_id' => $r3->id, 'travel_date' => $date, 'departure_time' => '09:00']);
            Schedule::create(['bus_id' => $bus1->id, 'route_id' => $r4->id, 'travel_date' => $date, 'departure_time' => '06:30']);
        }

        $this->command->info('✅ Demo data seeded successfully!');
        $this->command->info('   Admin: admin@busgo.com / password123');
        $this->command->info('   User:  user@busgo.com  / password123');
    }
}
