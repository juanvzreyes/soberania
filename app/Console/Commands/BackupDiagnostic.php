<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class BackupDiagnostic extends Command
{
    protected $signature = 'backup:diagnostic';
    protected $description = 'Diagnosticar configuración del sistema de respaldo';

    public function handle()
    {
        $this->info('=== DIAGNÓSTICO DEL SISTEMA DE RESPALDO ===');
        $this->newLine();

        $this->info('1. Sistema Operativo:');
        $this->line('   OS: ' . PHP_OS);
        $this->line('   OS Family: ' . PHP_OS_FAMILY);
        $this->newLine();

        $this->info('2. MySQL/MariaDB:');
        exec('mysql --version 2>&1', $mysqlOutput, $mysqlReturn);
        if ($mysqlReturn === 0) {
            $this->line('   ✓ mysql disponible: ' . ($mysqlOutput[0] ?? 'Sí'));
        } else {
            $this->error('   ✗ mysql NO disponible');
            $this->warn('   Solución: Agrega C:\laragon\bin\mysql\mysql-x.x.x\bin al PATH');
        }
        $this->newLine();

        $this->info('3. mysqldump:');
        exec('mysqldump --version 2>&1', $dumpOutput, $dumpReturn);
        if ($dumpReturn === 0) {
            $this->line('   ✓ mysqldump disponible: ' . ($dumpOutput[0] ?? 'Sí'));
        } else {
            $this->error('   ✗ mysqldump NO disponible');
            $this->warn('   Solución: Agrega C:\laragon\bin\mysql\mysql-x.x.x\bin al PATH');
        }
        $this->newLine();

        $this->info('4. Directorio de Respaldos:');
        $backupPath = storage_path('app/backups');
        if (is_dir($backupPath)) {
            $this->line('   ✓ Directorio existe: ' . $backupPath);
            if (is_writable($backupPath)) {
                $this->line('   ✓ Directorio tiene permisos de escritura');
            } else {
                $this->error('   ✗ Directorio NO tiene permisos de escritura');
            }
        } else {
            $this->error('   ✗ Directorio NO existe: ' . $backupPath);
            $this->warn('   Intentando crear...');
            Storage::disk('local')->makeDirectory('backups');
            if (is_dir($backupPath)) {
                $this->info('   ✓ Directorio creado exitosamente');
            }
        }
        $this->newLine();

        $this->info('5. Respaldos Existentes:');
        $files = Storage::disk('local')->files('backups');
        $sqlFiles = array_filter($files, function($file) {
            return pathinfo($file, PATHINFO_EXTENSION) === 'sql';
        });
        
        if (count($sqlFiles) > 0) {
            foreach ($sqlFiles as $file) {
                $size = Storage::disk('local')->size($file);
                $this->line('   • ' . basename($file) . ' (' . $this->formatBytes($size) . ')');
            }
        } else {
            $this->line('   No hay respaldos disponibles');
        }
        $this->newLine();

        $this->info('6. Configuración de Base de Datos:');
        $this->line('   Host: ' . config('database.connections.mysql.host'));
        $this->line('   Port: ' . config('database.connections.mysql.port'));
        $this->line('   Database: ' . config('database.connections.mysql.database'));
        $this->line('   Username: ' . config('database.connections.mysql.username'));
        $this->newLine();

        $this->info('7. Prueba de Conexión:');
        try {
            DB::connection()->getPdo();
            $this->line('   ✓ Conexión exitosa a la base de datos');
        } catch (\Exception $e) {
            $this->error('   ✗ Error de conexión: ' . $e->getMessage());
        }
        $this->newLine();

        $this->info('8. Permisos de Spatie:');
        try {
            $permissions = \Spatie\Permission\Models\Permission::where('name', 'like', 'backups.%')->get();
            if ($permissions->count() > 0) {
                foreach ($permissions as $permission) {
                    $this->line('   ✓ ' . $permission->name);
                }
            } else {
                $this->warn('   ⚠ No hay permisos creados para backups');
                $this->warn('   Ejecuta: php artisan backup:setup-permissions');
            }
        } catch (\Exception $e) {
            $this->error('   ✗ Error al verificar permisos: ' . $e->getMessage());
        }
        $this->newLine();

        if ($mysqlReturn !== 0 || $dumpReturn !== 0) {
            $this->info('=== SUGERENCIAS ===');
            $this->warn('Para agregar MySQL al PATH en Laragon:');
            $this->line('1. Abre PowerShell como Administrador');
            $this->line('2. Ejecuta: dir C:\laragon\bin\mysql');
            $this->line('3. Encuentra tu versión (ej: mysql-8.0.30-winx64)');
            $this->line('4. Ejecuta este comando (ajusta la versión):');
            $this->line('   [Environment]::SetEnvironmentVariable("Path", $env:Path + ";C:\laragon\bin\mysql\mysql-8.0.30-winx64\bin", "Machine")');
            $this->line('5. Reinicia tu terminal y vuelve a ejecutar este diagnóstico');
            $this->newLine();
        }

        $this->info('=== FIN DEL DIAGNÓSTICO ===');
    }

    private function formatBytes($bytes, $precision = 2)
    {
        if ($bytes === 0) return '0 B';
        $units = ['B', 'KB', 'MB', 'GB'];
        $base = 1024;
        for ($i = 0; $bytes >= $base && $i < count($units) - 1; $i++) {
            $bytes /= $base;
        }
        return round($bytes, $precision) . ' ' . $units[$i];
    }
}