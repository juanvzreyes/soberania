<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class SetupBackupPermissions extends Command
{
    protected $signature = 'backup:setup-permissions';
    protected $description = 'Configurar permisos para el módulo de respaldos';

    public function handle()
    {
        $this->info('Configurando permisos para el módulo de respaldos...');
        $this->newLine();

        $permissions = [
            'backups.index' => 'Ver módulo de respaldos',
            'backups.export' => 'Crear respaldo de base de datos',
            'backups.restore' => 'Restaurar base de datos',
        ];

        $this->info('Creando permisos...');
        foreach ($permissions as $name => $description) {
            try {
                $permission = Permission::firstOrCreate(
                    ['name' => $name],
                    ['guard_name' => 'web']
                );
                
                if ($permission->wasRecentlyCreated) {
                    $this->line("  ✓ Creado: {$name} - {$description}");
                } else {
                    $this->line("  • Ya existe: {$name}");
                }
            } catch (\Exception $e) {
                $this->error("  ✗ Error al crear {$name}: " . $e->getMessage());
            }
        }
        $this->newLine();

        $this->info('Asignando permisos al rol Admin...');
        try {
            $admin = Role::where('name', 'Admin')->first();
            
            if ($admin) {
                $admin->givePermissionTo(array_keys($permissions));
                $this->line('  ✓ Permisos asignados al rol Admin');
            } else {
                $this->warn('  ⚠ No se encontró el rol "Admin"');
                $this->line('  Creando rol Admin...');
                $admin = Role::create(['name' => 'Admin', 'guard_name' => 'web']);
                $admin->givePermissionTo(array_keys($permissions));
                $this->line('  ✓ Rol Admin creado y permisos asignados');
            }
        } catch (\Exception $e) {
            $this->error('  ✗ Error al asignar permisos: ' . $e->getMessage());
        }
        $this->newLine();
        
        $this->info('=== RESUMEN ===');
        $totalPermissions = Permission::where('name', 'like', 'backups.%')->count();
        $this->line("Total de permisos creados: {$totalPermissions}");
        
        if (isset($admin)) {
            $adminPermissions = $admin->permissions()->where('name', 'like', 'backups.%')->count();
            $this->line("Permisos asignados a Admin: {$adminPermissions}");
        }
        
        $this->newLine();
        $this->info('✓ Configuración completada exitosamente');
        $this->newLine();
        $this->line('Ahora puedes ejecutar: php artisan backup:diagnostic');
    }
}