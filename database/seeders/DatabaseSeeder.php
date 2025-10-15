<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Usuário administrador padrão
        User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );

        // Status padrão para o Kanban
        $statuses = [
            ['name' => 'Fila Corte', 'color' => '#7c3aed', 'position' => 1],
            ['name' => 'Cortado', 'color' => '#0ea5e9', 'position' => 2],
            ['name' => 'Costura', 'color' => '#22c55e', 'position' => 3],
            ['name' => 'Personalização', 'color' => '#f59e0b', 'position' => 4],
            ['name' => 'Pronto', 'color' => '#10b981', 'position' => 5],
        ];

        foreach ($statuses as $status) {
            DB::table('statuses')->updateOrInsert(
                ['name' => $status['name']],
                ['color' => $status['color'], 'position' => $status['position']]
            );
        }

        // Parâmetros iniciais de preços
        $settings = [
            ['key' => 'price.serigrafia.a4', 'value' => '59.40', 'type' => 'decimal'],
            ['key' => 'price.dtf.a4', 'value' => '59.40', 'type' => 'decimal'],
            ['key' => 'delivery.fee.default', 'value' => '0', 'type' => 'decimal'],
        ];

        foreach ($settings as $setting) {
            DB::table('settings')->updateOrInsert(
                ['key' => $setting['key']],
                ['value' => $setting['value'], 'type' => $setting['type']]
            );
        }

        // Opções de produtos
        $this->call([
            ProductOptionSeeder::class,
            SublimationSeeder::class,
            SerigraphySeeder::class,
            SizeSurchargeSeeder::class,
        ]);
    }
}
