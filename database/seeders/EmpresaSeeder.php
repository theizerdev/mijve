<?php

namespace Database\Seeders;

use App\Models\Empresa;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class EmpresaSeeder extends Seeder
{
    public function run()
    {
        $empresas = [
            [
                'razon_social' => 'Tech Solutions SA',
                'direccion' => 'Av. Principal 123',
                'documento' => '1234567890',
            ],
            [
                'razon_social' => 'Comercializadora XYZ',
                'direccion' => 'Calle Comercial 456',
                'documento' => '0987654321',
            ],
            [
                'razon_social' => 'Servicios Integrales LTDA',
                'direccion' => 'Boulevard Industrial 789',
                'documento' => '5432167890',
            ],
            [
                'razon_social' => 'Distribuidora ABC',
                'direccion' => 'Carrera 10 #20-30',
                'documento' => '6789054321',
            ],
            [
                'razon_social' => 'Consultoría Profesional',
                'direccion' => 'Av. Consultores 567',
                'documento' => '1357924680',
            ],
        ];

        foreach ($empresas as $empresaData) {
            $empresa = Empresa::create($empresaData);

            // Crear Super Administrador
            $superAdmin = User::create([
                'name' => 'Super Admin ' . $empresa->razon_social,
                'email' => 'superadmin@' . strtolower(str_replace(' ', '', $empresa->razon_social)) . '.com',
                'password' => Hash::make('password'),
                'empresa_id' => $empresa->id,
                'status' => true,
            ]);
            $superAdmin->assignRole('Super Administrador');

            // Crear Administrador
            $admin = User::create([
                'name' => 'Admin ' . $empresa->razon_social,
                'email' => 'admin@' . strtolower(str_replace(' ', '', $empresa->razon_social)) . '.com',
                'password' => Hash::make('password'),
                'empresa_id' => $empresa->id,
                'status' => true,
            ]);
            $admin->assignRole('Administrador');

            // Crear Recepcionista
            $recepcionista = User::create([
                'name' => 'Recepcionista ' . $empresa->razon_social,
                'email' => 'recepcionista@' . strtolower(str_replace(' ', '', $empresa->razon_social)) . '.com',
                'password' => Hash::make('password'),
                'empresa_id' => $empresa->id,
                'status' => true,
            ]);
            $recepcionista->assignRole('Recepcionista');
        }
    }
}
