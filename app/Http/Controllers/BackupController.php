<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;
use Exception;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;

class BackupController extends Controller
{
    private string $source;
    private string $routeName;
    private string $backupDisk;
    private string $backupPath;

    public function __construct()
    {
        $this->source = 'Backups/';
        $this->routeName = 'backups.';
        $this->backupDisk = 'local';
        $this->backupPath = 'backups';

        $this->middleware("permission:{$this->routeName}index")->only(['index']);
        $this->middleware("permission:{$this->routeName}export")->only(['export']);
        $this->middleware("permission:{$this->routeName}restore")->only(['restore']);

        if (!Storage::disk($this->backupDisk)->exists($this->backupPath)) {
            Storage::disk($this->backupDisk)->makeDirectory($this->backupPath);
        }
    }

    public function index(): Response
    {
        return Inertia::render("{$this->source}Index", [
            'title' => 'Respaldo de Base de Datos',
            'routeName' => $this->routeName,
            'dbInfo' => $this->getDatabaseInfo(),
        ]);
    }

    public function export(): JsonResponse
    {
        try {
            Log::info('Iniciando proceso de exportación de base de datos', [
                'user_id' => Auth::id(),
                'timestamp' => Carbon::now()->toDateTimeString(),
                'os' => PHP_OS_FAMILY,
            ]);

            if (!$this->isMysqldumpAvailable()) {
                $errorMsg = 'mysqldump no está disponible. Por favor, asegúrate de que MySQL esté instalado y agregado al PATH del sistema.';
                Log::error($errorMsg, [
                    'user_id' => Auth::id(),
                    'os' => PHP_OS_FAMILY,
                ]);

                return response()->json([
                    'success' => false,
                    'message' => $errorMsg
                ], 500);
            }

            $timestamp = Carbon::now()->format('Y-m-d_H-i-s');
            $filename = "bd_respaldo_{$timestamp}.sql";
            $filepath = storage_path("app/{$this->backupPath}/{$filename}");

            $filepath = $this->normalizePath($filepath);

            $dbHost = config('database.connections.mysql.host');
            $dbPort = config('database.connections.mysql.port', 3306);
            $dbName = config('database.connections.mysql.database');
            $dbUser = config('database.connections.mysql.username');
            $dbPass = config('database.connections.mysql.password');

            if (empty($dbName) || empty($dbUser)) {
                $errorMsg = 'Configuración de base de datos incompleta.';
                Log::error($errorMsg, [
                    'user_id' => Auth::id(),
                    'db_name' => $dbName,
                    'db_user' => $dbUser,
                ]);

                return response()->json([
                    'success' => false,
                    'message' => $errorMsg
                ], 500);
            }

            Log::info('Configuración de base de datos validada', [
                'db_name' => $dbName,
                'db_host' => $dbHost,
                'db_port' => $dbPort,
            ]);

            if (PHP_OS_FAMILY === 'Windows') {
                if (empty($dbPass)) {
                    $command = sprintf(
                        'mysqldump --user=%s --host=%s --port=%s --single-transaction --routines --triggers --events --skip-comments %s > "%s" 2>&1',
                        $dbUser,
                        $dbHost,
                        $dbPort,
                        $dbName,
                        $filepath
                    );
                } else {
                    $command = sprintf(
                        'mysqldump --user=%s --password=%s --host=%s --port=%s --single-transaction --routines --triggers --events --skip-comments %s > "%s" 2>&1',
                        $dbUser,
                        $dbPass,
                        $dbHost,
                        $dbPort,
                        $dbName,
                        $filepath
                    );
                }
            } else {
                $command = sprintf(
                    'mysqldump --user=%s --password=%s --host=%s --port=%s --single-transaction --routines --triggers --events %s > %s 2>&1',
                    escapeshellarg($dbUser),
                    escapeshellarg($dbPass),
                    escapeshellarg($dbHost),
                    escapeshellarg($dbPort),
                    escapeshellarg($dbName),
                    escapeshellarg($filepath)
                );
            }

            Log::info('Ejecutando comando mysqldump', [
                'filename' => $filename,
                'filepath' => $filepath,
            ]);

            $output = [];
            $returnVar = 0;
            exec($command, $output, $returnVar);

            if ($returnVar !== 0) {
                $errorMsg = implode("\n", $output);
                Log::error("Error en mysqldump", [
                    'user_id' => Auth::id(),
                    'return_code' => $returnVar,
                    'output' => $errorMsg,
                    'filename' => $filename,
                ]);

                return response()->json([
                    'success' => false,
                    'message' => "Error al ejecutar mysqldump. Verifica que MySQL esté instalado correctamente y agregado al PATH.",
                    'details' => $errorMsg
                ], 500);
            }

            if (!file_exists($filepath)) {
                Log::error('El archivo de respaldo no se creó', [
                    'user_id' => Auth::id(),
                    'filepath' => $filepath,
                    'filename' => $filename,
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'El archivo de respaldo no se creó.'
                ], 500);
            }

            $fileSize = filesize($filepath);

            if ($fileSize === 0) {
                unlink($filepath);
                Log::error('El archivo de respaldo está vacío', [
                    'user_id' => Auth::id(),
                    'filepath' => $filepath,
                    'filename' => $filename,
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'El archivo de respaldo está vacío. Verifica la conexión a la base de datos.'
                ], 500);
            }

            Log::info("Respaldo de BD creado exitosamente", [
                'user_id' => Auth::id(),
                'filename' => $filename,
                'file_size' => $fileSize,
                'file_size_formatted' => $this->formatBytes($fileSize),
                'os' => PHP_OS_FAMILY,
                'duration' => microtime(true) - LARAVEL_START,
            ]);

            if (class_exists('\App\Models\Notification')) {
                try {
                    \App\Models\Notification::create([
                        'user_id' => Auth::id(),
                        'title' => 'Respaldo Exitoso',
                        'message' => "Se ha creado el respaldo: {$filename} ({$this->formatBytes($fileSize)})",
                        'type' => 'backup_success',
                        'data' => json_encode([
                            'filename' => $filename,
                            'file_size' => $fileSize,
                            'timestamp' => Carbon::now()->toDateTimeString(),
                        ]),
                    ]);
                } catch (Exception $e) {
                    Log::warning("No se pudo crear notificación", [
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString(),
                    ]);
                }
            }
            clearstatcache();

            return response()->json([
                'success' => true,
                'message' => "Respaldo creado exitosamente: {$filename}",
                'data' => [
                    'filename' => $filename,
                    'file_size' => $this->formatBytes($fileSize),
                    'timestamp' => Carbon::now()->format('d/m/Y H:i:s'),
                ]
            ]);
        } catch (Exception $e) {
            Log::error("Error general al crear respaldo de BD", [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al crear el respaldo: ' . $e->getMessage()
            ], 500);
        }
    }

    public function restore(Request $request): JsonResponse
    {
        try {
            Log::info('Iniciando proceso de restauración de base de datos', [
                'user_id' => Auth::id(),
                'timestamp' => Carbon::now()->toDateTimeString(),
            ]);

            $request->validate([
                'backup_file' => 'required|file|max:102400',
            ]);

            $file = $request->file('backup_file');

            Log::info('Archivo de respaldo recibido', [
                'user_id' => Auth::id(),
                'filename' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
            ]);

            if (strtolower($file->getClientOriginalExtension()) !== 'sql') {
                Log::warning('Intento de restauración con archivo inválido', [
                    'user_id' => Auth::id(),
                    'filename' => $file->getClientOriginalName(),
                    'extension' => $file->getClientOriginalExtension(),
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'El archivo debe tener extensión .sql'
                ], 422);
            }

            if (!$this->isMysqlAvailable()) {
                $errorMsg = 'mysql no está disponible en el sistema.';
                Log::error($errorMsg, [
                    'user_id' => Auth::id(),
                    'os' => PHP_OS_FAMILY,
                ]);

                return response()->json([
                    'success' => false,
                    'message' => $errorMsg
                ], 500);
            }

            Log::info('Creando respaldo automático antes de restaurar');
            $autoBackupFilename = $this->createAutoBackup();

            if (!$autoBackupFilename) {
                $errorMsg = 'No se pudo crear el respaldo automático de seguridad.';
                Log::error($errorMsg, [
                    'user_id' => Auth::id(),
                ]);

                return response()->json([
                    'success' => false,
                    'message' => $errorMsg
                ], 500);
            }

            Log::info("Respaldo automático creado", [
                'filename' => $autoBackupFilename,
                'user_id' => Auth::id(),
            ]);

            $filepath = $this->normalizePath($file->getRealPath());

            Log::info('Preparando restauración', [
                'filepath' => $filepath,
                'auto_backup' => $autoBackupFilename,
            ]);

            $dbHost = config('database.connections.mysql.host');
            $dbName = config('database.connections.mysql.database');
            $dbUser = config('database.connections.mysql.username');
            $dbPass = config('database.connections.mysql.password');
            $dbPort = config('database.connections.mysql.port', 3306);

            if (PHP_OS_FAMILY === 'Windows') {
                if (empty($dbPass)) {
                    $command = sprintf(
                        'mysql --user=%s --host=%s --port=%s %s < "%s" 2>&1',
                        $dbUser,
                        $dbHost,
                        $dbPort,
                        $dbName,
                        $filepath
                    );
                } else {
                    $command = sprintf(
                        'mysql --user=%s --password=%s --host=%s --port=%s %s < "%s" 2>&1',
                        $dbUser,
                        $dbPass,
                        $dbHost,
                        $dbPort,
                        $dbName,
                        $filepath
                    );
                }
            } else {
                $command = sprintf(
                    'mysql --user=%s --password=%s --host=%s --port=%s %s < %s 2>&1',
                    escapeshellarg($dbUser),
                    escapeshellarg($dbPass),
                    escapeshellarg($dbHost),
                    escapeshellarg($dbPort),
                    escapeshellarg($dbName),
                    escapeshellarg($filepath)
                );
            }

            Log::info('Ejecutando comando de restauración');
            $output = [];
            $returnVar = 0;
            exec($command, $output, $returnVar);

            if ($returnVar !== 0) {
                $errorMsg = implode("\n", $output);
                Log::error("Error al restaurar base de datos", [
                    'user_id' => Auth::id(),
                    'return_code' => $returnVar,
                    'output' => $errorMsg,
                    'auto_backup' => $autoBackupFilename,
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Error al restaurar la base de datos. Se ha creado un respaldo automático: ' . $autoBackupFilename,
                    'details' => $errorMsg
                ], 500);
            }

            Log::info("Base de datos restaurada exitosamente", [
                'user_id' => Auth::id(),
                'source_file' => $file->getClientOriginalName(),
                'auto_backup' => $autoBackupFilename,
                'duration' => microtime(true) - LARAVEL_START,
            ]);

            if (class_exists('\App\Models\Notification')) {
                try {
                    \App\Models\Notification::create([
                        'user_id' => Auth::id(),
                        'title' => 'Restauración Exitosa',
                        'message' => "Base de datos restaurada desde: {$file->getClientOriginalName()}",
                        'type' => 'backup_restore_success',
                        'data' => json_encode([
                            'source_file' => $file->getClientOriginalName(),
                            'auto_backup' => $autoBackupFilename,
                            'timestamp' => Carbon::now()->toDateTimeString(),
                        ]),
                    ]);
                } catch (Exception $e) {
                    Log::warning("No se pudo crear notificación", [
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Base de datos restaurada exitosamente.',
                'data' => [
                    'auto_backup' => $autoBackupFilename,
                    'timestamp' => Carbon::now()->format('d/m/Y H:i:s'),
                ]
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('Error de validación en restauración', [
                'user_id' => Auth::id(),
                'errors' => $e->errors(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error de validación: ' . implode(', ', $e->errors()['backup_file'] ?? ['Error desconocido'])
            ], 422);
        } catch (Exception $e) {
            Log::error("Error general al restaurar base de datos", [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al restaurar: ' . $e->getMessage()
            ], 500);
        }
    }

    private function getDatabaseInfo(): array
    {
        try {
            $dbName = config('database.connections.mysql.database');

            $result = DB::select("
                SELECT 
                    SUM(data_length + index_length) as size
                FROM information_schema.TABLES 
                WHERE table_schema = ?
            ", [$dbName]);

            $size = $result[0]->size ?? 0;

            $tables = DB::select("
                SELECT COUNT(*) as count
                FROM information_schema.TABLES 
                WHERE table_schema = ?
            ", [$dbName]);

            return [
                'name' => $dbName,
                'size' => $this->formatBytes($size),
                'size_bytes' => $size,
                'tables_count' => $tables[0]->count ?? 0,
            ];
        } catch (Exception $e) {
            Log::error("Error al obtener información de la BD", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'name' => config('database.connections.mysql.database'),
                'size' => 'N/A',
                'size_bytes' => 0,
                'tables_count' => 0,
            ];
        }
    }

    private function createAutoBackup(): ?string
    {
        try {
            $timestamp = Carbon::now()->format('Y-m-d_H-i-s');
            $filename = "bd_auto_backup_before_restore_{$timestamp}.sql";
            $filepath = storage_path("app/{$this->backupPath}/{$filename}");
            $filepath = $this->normalizePath($filepath);

            $dbHost = config('database.connections.mysql.host');
            $dbPort = config('database.connections.mysql.port', 3306);
            $dbName = config('database.connections.mysql.database');
            $dbUser = config('database.connections.mysql.username');
            $dbPass = config('database.connections.mysql.password');

            if (PHP_OS_FAMILY === 'Windows') {
                if (empty($dbPass)) {
                    $command = sprintf(
                        'mysqldump --user=%s --host=%s --port=%s --single-transaction --routines --triggers --events --skip-comments %s > "%s" 2>&1',
                        $dbUser,
                        $dbHost,
                        $dbPort,
                        $dbName,
                        $filepath
                    );
                } else {
                    $command = sprintf(
                        'mysqldump --user=%s --password=%s --host=%s --port=%s --single-transaction --routines --triggers --events --skip-comments %s > "%s" 2>&1',
                        $dbUser,
                        $dbPass,
                        $dbHost,
                        $dbPort,
                        $dbName,
                        $filepath
                    );
                }
            } else {
                $command = sprintf(
                    'mysqldump --user=%s --password=%s --host=%s --port=%s --single-transaction --routines --triggers --events %s > %s 2>&1',
                    escapeshellarg($dbUser),
                    escapeshellarg($dbPass),
                    escapeshellarg($dbHost),
                    escapeshellarg($dbPort),
                    escapeshellarg($dbName),
                    escapeshellarg($filepath)
                );
            }

            $output = [];
            $returnVar = 0;
            exec($command, $output, $returnVar);

            if ($returnVar !== 0 || !file_exists($filepath) || filesize($filepath) === 0) {
                Log::error('Error al crear respaldo automático', [
                    'return_code' => $returnVar,
                    'output' => implode("\n", $output),
                    'filepath' => $filepath,
                ]);
                return null;
            }

            return $filename;
        } catch (Exception $e) {
            Log::error('Excepción al crear respaldo automático', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return null;
        }
    }

    private function isMysqldumpAvailable(): bool
    {
        $output = [];
        $returnVar = 0;

        if (PHP_OS_FAMILY === 'Windows') {
            exec('mysqldump --version 2>nul', $output, $returnVar);
        } else {
            exec('mysqldump --version 2>&1', $output, $returnVar);
        }

        return $returnVar === 0;
    }

    private function isMysqlAvailable(): bool
    {
        $output = [];
        $returnVar = 0;

        if (PHP_OS_FAMILY === 'Windows') {
            exec('mysql --version 2>nul', $output, $returnVar);
        } else {
            exec('mysql --version 2>&1', $output, $returnVar);
        }

        return $returnVar === 0;
    }

    private function normalizePath(string $path): string
    {
        if (PHP_OS_FAMILY === 'Windows') {
            return str_replace('/', DIRECTORY_SEPARATOR, $path);
        }
        return $path;
    }

    private function formatBytes(int $bytes, int $precision = 2): string
    {
        if ($bytes === 0) {
            return '0 B';
        }

        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $base = 1024;

        for ($i = 0; $bytes >= $base && $i < count($units) - 1; $i++) {
            $bytes /= $base;
        }

        return round($bytes, $precision) . ' ' . $units[$i];
    }
}