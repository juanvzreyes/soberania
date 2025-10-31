<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Permission::create(['name' => 'menu.security', 'guard_name' => 'web', 'description' => 'Visibilidad menú', 'module_key' => 'menu']);

        Permission::create(['name' => 'modules.index', 'guard_name' => 'web', 'description' => 'Leer Registros', 'module_key' => 'seg']);
        Permission::create(['name' => 'modules.store', 'guard_name' => 'web', 'description' => 'Crear Registros', 'module_key' => 'seg']);
        Permission::create(['name' => 'modules.update', 'guard_name' => 'web', 'description' => 'Actualizar Registros', 'module_key' => 'seg']);
        Permission::create(['name' => 'modules.delete', 'guard_name' => 'web', 'description' => 'Eliminar Registros', 'module_key' => 'seg']);
        
        Permission::create(['name' => 'permissions.index', 'guard_name' => 'web', 'description' => 'Leer Registros', 'module_key' => 'seg']);
        Permission::create(['name' => 'permissions.store', 'guard_name' => 'web', 'description' => 'Crear Registros', 'module_key' => 'seg']);
        Permission::create(['name' => 'permissions.update', 'guard_name' => 'web', 'description' => 'Actualizar Registros', 'module_key' => 'seg']);
        Permission::create(['name' => 'permissions.delete', 'guard_name' => 'web', 'description' => 'Eliminar Registros', 'module_key' => 'seg']);

        Permission::create(['name' => 'roles.index', 'guard_name' => 'web', 'description' => 'Leer Registros', 'module_key' => 'seg']);
        Permission::create(['name' => 'roles.store', 'guard_name' => 'web', 'description' => 'Crear Registros', 'module_key' => 'seg']);
        Permission::create(['name' => 'roles.update', 'guard_name' => 'web', 'description' => 'Actualizar Registros', 'module_key' => 'seg']);
        Permission::create(['name' => 'roles.delete', 'guard_name' => 'web', 'description' => 'Eliminar Registros', 'module_key' => 'seg']);

        Permission::create(['name' => 'users.index', 'guard_name' => 'web', 'description' => 'Leer Registros', 'module_key' => 'seg']);
        Permission::create(['name' => 'users.store', 'guard_name' => 'web', 'description' => 'Crear Registros', 'module_key' => 'seg']);
        Permission::create(['name' => 'users.update', 'guard_name' => 'web', 'description' => 'Actualizar Registros', 'module_key' => 'seg']);
        Permission::create(['name' => 'users.delete', 'guard_name' => 'web', 'description' => 'Eliminar Registros', 'module_key' => 'seg']);

        Permission::create(['name' => 'categories.index', 'guard_name' => 'web', 'description' => 'Ver Categorías', 'module_key' => 'categories']);
        Permission::create(['name' => 'categories.store', 'guard_name' => 'web', 'description' => 'Crear Categorías', 'module_key' => 'categories']);
        Permission::create(['name' => 'categories.update', 'guard_name' => 'web', 'description' => 'Editar Categorías', 'module_key' => 'categories']);
        Permission::create(['name' => 'categories.delete', 'guard_name' => 'web', 'description' => 'Eliminar Categorías', 'module_key' => 'categories']);

        Permission::create(['name' => 'products.index', 'guard_name' => 'web', 'description' => 'Ver Productos', 'module_key' => 'products']);
        Permission::create(['name' => 'products.store', 'guard_name' => 'web', 'description' => 'Crear Productos', 'module_key' => 'products']);
        Permission::create(['name' => 'products.update', 'guard_name' => 'web', 'description' => 'Editar Productos', 'module_key' => 'products']);
        Permission::create(['name' => 'products.delete', 'guard_name' => 'web', 'description' => 'Eliminar Productos', 'module_key' => 'products']);

        Permission::create(['name' => 'profile.producer.index', 'guard_name' => 'web', 'description' => 'Leer Registros', 'module_key' => 'producer']);
        Permission::create(['name' => 'profile.producer.update', 'guard_name' => 'web', 'description' => 'Actualizar Registros', 'module_key' => 'producer']);

        Permission::create(['name' => 'profile.cooperative.index', 'guard_name' => 'web', 'description' => 'Leer Registros', 'module_key' => 'cooperative']);
        Permission::create(['name' => 'profile.cooperative.update', 'guard_name' => 'web', 'description' => 'Actualizar Registros', 'module_key' => 'cooperative']);

        Permission::create(['name' => 'profile.consumer.index', 'guard_name' => 'web', 'description' => 'Leer Registros', 'module_key' => 'consumer']);
        Permission::create(['name' => 'profile.consumer.update', 'guard_name' => 'web', 'description' => 'Actualizar Registros', 'module_key' => 'consumer']);
        
        Permission::create(['name' => 'inventoryEntry.index', 'guard_name' => 'web', 'description' => 'Ver Historial de Entradas', 'module_key' => 'inventory']);
        Permission::create(['name' => 'inventoryEntry.create', 'guard_name' => 'web', 'description' => 'Formulario de Entrada', 'module_key' => 'inventory']);
        Permission::create(['name' => 'inventoryEntry.store', 'guard_name' => 'web', 'description' => 'Registrar Entradas', 'module_key' => 'inventory']);

        Permission::create(['name' => 'inventoryExit.index', 'guard_name' => 'web', 'description' => 'Ver Historial de Salidas', 'module_key' => 'inventory']);
        Permission::create(['name' => 'inventoryExit.create', 'guard_name' => 'web', 'description' => 'Formulario de Salida', 'module_key' => 'inventory']);
        Permission::create(['name' => 'inventoryExit.store', 'guard_name' => 'web', 'description' => 'Registrar Salidas', 'module_key' => 'inventory']);
        
        Permission::create(['name' => 'menu.catalog', 'guard_name' => 'web', 'description' => 'Visibilidad menú Catálogo', 'module_key' => 'menu']);
        
        Permission::create(['name' => 'cart.index', 'guard_name' => 'web', 'description' => 'Ver Carrito', 'module_key' => 'cart']);
        Permission::create(['name' => 'cart.store', 'guard_name' => 'web', 'description' => 'Añadir al Carrito', 'module_key' => 'cart']);
        Permission::create(['name' => 'cart.update', 'guard_name' => 'web', 'description' => 'Actualizar cantidad', 'module_key' => 'cart']);
        Permission::create(['name' => 'cart.destroy', 'guard_name' => 'web', 'description' => 'Eliminar del Carrito', 'module_key' => 'cart']);
        Permission::create(['name' => 'cart.clear', 'guard_name' => 'web', 'description' => 'Vaciar Carrito', 'module_key' => 'cart']);
        
        Permission::create(['name' => 'checkout.index', 'guard_name' => 'web', 'description' => 'Ver formulario de Checkout', 'module_key' => 'checkout']);
        Permission::create(['name' => 'checkout.store', 'guard_name' => 'web', 'description' => 'Completar Pedido', 'module_key' => 'checkout']);
        Permission::create(['name' => 'checkout.confirmation', 'guard_name' => 'web', 'description' => 'Ver Confirmación de Pedido', 'module_key' => 'checkout']);
    
        Permission::create(['name' => 'orders.index', 'guard_name' => 'web', 'description' => 'Ver lista de Pedidos', 'module_key' => 'orders']);
        Permission::create(['name' => 'orders.show', 'guard_name' => 'web', 'description' => 'Ver detalle de Pedido', 'module_key' => 'orders']);
        Permission::create(['name' => 'orders.updateStatus', 'guard_name' => 'web', 'description' => 'Actualizar estado de Pedido', 'module_key' => 'orders']);
        Permission::create(['name' => 'orders.cancel', 'guard_name' => 'web', 'description' => 'Cancelar Pedido', 'module_key' => 'orders']);
        
        Permission::create(['name' => 'payments.index', 'guard_name' => 'web', 'description' => 'Ver lista de Pagos', 'module_key' => 'payments']);
        Permission::create(['name' => 'payments.show', 'guard_name' => 'web', 'description' => 'Ver detalle de Pago', 'module_key' => 'payments']);
        Permission::create(['name' => 'payments.updateStatus', 'guard_name' => 'web', 'description' => 'Actualizar estado de Pago', 'module_key' => 'payments']);
        
        Permission::create(['name' => 'deliveries.index', 'guard_name' => 'web', 'description' => 'Ver Entregas', 'module_key' => 'deliveries']);
        Permission::create(['name' => 'deliveries.show', 'guard_name' => 'web', 'description' => 'Ver Detalle de Entrega', 'module_key' => 'deliveries']);
        Permission::create(['name' => 'deliveries.updateStatus', 'guard_name' => 'web', 'description' => 'Actualizar Estado de Entrega', 'module_key' => 'deliveries']);
    
        Permission::create(['name' => 'purchase-history.index', 'guard_name' => 'web', 'description' => 'Ver lista de Compras', 'module_key' => 'purchase-history']);
        Permission::create(['name' => 'purchase-history.show', 'guard_name' => 'web', 'description' => 'Ver detalle de Compra', 'module_key' => 'purchase-history']);
        Permission::create(['name' => 'purchase-history.reorder', 'guard_name' => 'web', 'description' => 'Reordenar una Compra', 'module_key' => 'purchase-history']);
    
        Permission::create(['name' => 'backups.index', 'guard_name' => 'web', 'description' => 'Ver Respaldos', 'module_key' => 'backups']);
        Permission::create(['name' => 'backups.export', 'guard_name' => 'web', 'description' => 'Exportar Base de Datos', 'module_key' => 'backups']);
        Permission::create(['name' => 'backups.restore', 'guard_name' => 'web', 'description' => 'Restaurar Base de Datos', 'module_key' => 'backups']);
        Permission::create(['name' => 'backups.download', 'guard_name' => 'web', 'description' => 'Descargar Respaldo', 'module_key' => 'backups']);
        Permission::create(['name' => 'backups.delete', 'guard_name' => 'web', 'description' => 'Eliminar Respaldo', 'module_key' => 'backups']);

        Permission::create(['name' => 'producer.reports.index', 'guard_name' => 'web', 'description' => 'Ver página de reportes', 'module_key' => 'reports-producer']);
        Permission::create(['name' => 'producer.reports.sales.export', 'guard_name' => 'web', 'description' => 'Generar reporte de ventas (PDF)', 'module_key' => 'reports-producer']);
        Permission::create(['name' => 'producer.reports.inventory.export', 'guard_name' => 'web', 'description' => 'Generar reporte de inventario (PDF)', 'module_key' => 'reports-producer']);
        Permission::create(['name' => 'producer.reports.customers.export', 'guard_name' => 'web', 'description' => 'Generar reporte de clientes (PDF)', 'module_key' => 'reports-producer']);
    }
}
