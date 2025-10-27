<?php

namespace Database\Seeders;

use App\Models\Empresa;
use App\Models\Sucursal;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SucursalSeeder extends Seeder
{
    public function run()
    {
        $empresas = Empresa::all();

        foreach ($empresas as $empresa) {
            $sucursal = Sucursal::create([
                'empresa_id' => $empresa->id,
                'nombre' => 'Sucursal ' . $empresa->razon_social,
                'direccion' => $empresa->direccion,
                'telefono' => $empresa->telefono,
            ]);

            // Crear Super Administrador para la sucursal
            $superAdmin = User::create([
                'name' => 'Super Admin Sucursal ' . $sucursal->nombre,
                'email' => 'superadmin.sucursal' . $sucursal->id . '@' . strtolower(str_replace(' ', '', $empresa->razon_social)) . '.com',
                'password' => Hash::make('password'),
                'empresa_id' => $empresa->id,
                'sucursal_id' => $sucursal->id,
                'status' => true,
            ]);
            $superAdmin->assignRole('Super Administrador');

            // Crear Administrador para la sucursal
            $admin = User::create([
                'name' => 'Admin Sucursal ' . $sucursal->nombre,
                'email' => 'admin.sucursal' . $sucursal->id . '@' . strtolower(str_replace(' ', '', $empresa->razon_social)) . '.com',
                'password' => Hash::make('password'),
                'empresa_id' => $empresa->id,
                'sucursal_id' => $sucursal->id,
                'status' => true,
            ]);
            $admin->assignRole('Administrador');

            // Crear Recepcionista para la sucursal
            $recepcionista = User::create([
                'name' => 'Recepcionista Sucursal ' . $sucursal->nombre,
                'email' => 'recepcionista.sucursal' . $sucursal->id . '@' . strtolower(str_replace(' ', '', $empresa->razon_social)) . '.com',
                'password' => Hash::make('password'),
                'empresa_id' => $empresa->id,
                'sucursal_id' => $sucursal->id,
                'status' => true,
            ]);
            $recepcionista->assignRole('Recepcionista');
        }
    }
}
