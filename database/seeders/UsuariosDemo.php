<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UsuariosDemo extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $usuarios = [
            [
                'email' => 'filtrosperu@gmail.com',
                'name' => 'Edu Aranguri',
                'password' => bcrypt('14e30s15b'), // Usamos bcrypt para encriptar la contraseña

            ],
            [
                'email' => 'jannet.rivero.pedemonte@gmail.com',
                'name' => 'jannet',
                'password' => bcrypt('14e30s15b'), // Usamos bcrypt para encriptar la contraseña

            ],
        ];

        foreach ($usuarios as $usuario) {
            // Encuentra o crea el usuario
            User::firstOrCreate(
                ['email' => $usuario['email']],
                ['name' => $usuario['name'], 'password' => $usuario['password']]
            );
        }
    }
}
