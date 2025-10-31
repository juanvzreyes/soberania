import { computed } from "vue";
import { usePage } from "@inertiajs/vue3";
import { mdiMonitorDashboard, mdiStoreSearch, 
        mdiPackageVariantPlus, 
        mdiPackageVariantMinus, 
        mdiClipboardListOutline,
        mdiCreditCardOutline,
        mdiTruckDelivery,
        mdiHistory,
        mdiDatabaseCog,
         } from "@mdi/js";
import securityMenu from "./Menus/securityMenu";
import profileMenus from "./profileMenu";
import homeMenu from "./homeMenu";
import catalogMenu from "./catalogMenu";
import reportMenu from "./reportMenu";

export const baseMenu = [
    {
        labelGroup: "Inicio",
        items: [
            {
                label: "Dashboard",
                route: "dashboard",
                icon: mdiMonitorDashboard,
            },
        ],
    },
    {
        labelGroup: "Mi Perfil",
        items: [
            ...profileMenus,
        ],
        
    },
        {
        labelGroup: "Catálogo",
        items: [
            ...catalogMenu,
        ],
    },
        {
        labelGroup: "Inventario",
        permission: "inventoryEntry.index", 
        items: [
            {
                label: "Entradas de Inventario",
                route: "inventoryEntry.index",
                icon: mdiPackageVariantPlus, 
                permission: "inventoryEntry.index", 
            },
                        {
                label: "Salidas de Inventario",
                route: "inventoryExit.index",
                icon: mdiPackageVariantMinus, 
                permission: "inventoryExit.index", 
            },
        ],
    },
    {
        labelGroup: "Gestión de Pedidos",
        permission: "orders.index",
        items: [
            {
                label: "Pedidos",
                route: "orders.index",
                icon: mdiClipboardListOutline, 
                permission: "orders.index",
            },
            {
                label: "Pagos",
                route: "payments.index",
                icon: mdiCreditCardOutline, 
                permission: "payments.index", 
            },
            {
                label: "Entregas",
                route: "deliveries.index",
                icon: mdiTruckDelivery,
                permission: "deliveries.index",
            },
        ],
    }, 
    {
        labelGroup: "Administración",
        items: [
            ...securityMenu,
            {
                label: "Respaldo de BD",
                route: "backups.index",
                icon: mdiDatabaseCog,
                permission: "backups.index",
            },
        ],
        
    },    
    {
        labelGroup: "Reportes",
        items: [
            ...reportMenu,
        ],
    },   
    {
        labelGroup: "Público",
        items: [
            ...homeMenu,
            {
                label: "Catálogo de Productos", 
                route: "catalog.index",        
                icon: mdiStoreSearch,          
                permission: "menu.catalog",    
            },
            {
                label: "Mi Historial de Compras",
                route: "purchase-history.index",
                icon: mdiHistory,
                permission: "purchase-history.index", 
            },
        ],
    },
];

export const useAside = () => {
    const { props } = usePage();
    
    const permissions = computed(() => 
        props.auth.can ? Object.keys(props.auth.can) : []
    );

    const hasPermission = (permission) => {
        return permissions.value.includes(permission);
    };

    const asideMenu = computed(() => {
        const filterMenu = (menuItems) => {
            return menuItems.filter(item => {
                if (!item.permission) {
                    return true;
                }
                return hasPermission(item.permission);
            }).map(item => {
                if (item.menu) {
                    const filteredSubMenu = filterMenu(item.menu);
                    if (filteredSubMenu.length > 0) {
                        return { ...item, menu: filteredSubMenu };
                    }
                    return null;
                }
                return item;
            }).filter(Boolean);
        };

        return baseMenu.map(section => ({
            ...section,
            items: filterMenu(section.items),
        })).filter(section => section.items.length > 0);
    });

    return {
        asideMenu,
    };
};
