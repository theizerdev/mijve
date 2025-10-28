<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\Support\Facades\Route;

class Breadcrumbs extends Component
{
    public $breadcrumbs;

    public function __construct()
    {
        $this->breadcrumbs = $this->generateBreadcrumbs();
    }

    private function generateBreadcrumbs()
    {
        $route = Route::current();
        if (!$route) {
            return [];
        }

        $breadcrumbs = [];
        $breadcrumbs[] = [
            'title' => 'Inicio',
            'url' => url('/'),
            'active' => false
        ];

        // Extraer el nombre de la ruta
        $routeName = $route->getName();
        
        if ($routeName) {
            // Separar el nombre de la ruta en partes
            $parts = explode('.', $routeName);
            
            $url = '';
            $title = '';
            
            foreach ($parts as $index => $part) {
                if ($index === 0) {
                    // Primera parte - generalmente 'admin'
                    $url = url('/');
                    $title = $this->formatTitle($part);
                } else {
                    // Partes subsiguientes
                    $title = $this->formatTitle($part);
                    
                    // Determinar la URL basada en la acción
                    if ($part === 'index') {
                        // No cambiar la URL para la acción index
                    } elseif ($part === 'create') {
                        $url .= '/create';
                    } elseif ($part === 'edit') {
                        // Para edit, necesitamos el ID, así que dejamos la URL como está
                        // En una implementación más completa, obtendríamos el ID real
                    } else {
                        $url .= '/' . $this->slugify($title);
                    }
                    
                    $breadcrumbs[] = [
                        'title' => $title,
                        'url' => $url,
                        'active' => $index === count($parts) - 1
                    ];
                }
            }
        }

        // Marcar el último breadcrumb como activo
        if (!empty($breadcrumbs)) {
            $breadcrumbs[count($breadcrumbs) - 1]['active'] = true;
        }

        return $breadcrumbs;
    }

    private function formatTitle($title)
    {
        // Convertir guiones y guiones bajos a espacios
        $title = str_replace(['-', '_'], ' ', $title);
        
        // Capitalizar palabras
        $title = ucwords($title);
        
        // Casos especiales
        $specialCases = [
            'Admin' => 'Administración',
            'Empresas' => 'Empresas',
            'Sucursales' => 'Sucursales',
            'Users' => 'Usuarios',
            'Students' => 'Estudiantes',
            'Create' => 'Crear',
            'Edit' => 'Editar',
            'Index' => 'Listado',
            'Dashboard' => 'Panel de Control',
            'Roles' => 'Roles',
            'Permissions' => 'Permisos',
            'School Periods' => 'Periodos Escolares',
            'Niveles Educativos' => 'Niveles Educativos',
            'Turnos' => 'Turnos',
            'Active Sessions' => 'Sesiones Activas',
            'Monitoreo' => 'Monitoreo',
            'Servidor' => 'Servidor',
            'Base Datos' => 'Base de Datos',
            'Accesos' => 'Accesos'
        ];
        
        return $specialCases[$title] ?? $title;
    }

    private function slugify($text)
    {
        // Reemplazar espacios y caracteres especiales con guiones
        $text = preg_replace('/[^A-Za-z0-9-]+/', '-', $text);
        return strtolower(trim($text, '-'));
    }

    public function render()
    {
        return view('components.breadcrumbs');
    }
}