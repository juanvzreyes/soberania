import { computed } from "vue";
import { usePage } from "@inertiajs/vue3";
import { mdiMonitorDashboard } from "@mdi/js";
import securityMenu from "./Menus/securityMenu";
import profileMenus from "./profileMenu";
import homeMenu from "./homeMenu";

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
        labelGroup: "Administración",
        items: [
            ...securityMenu,
        ],
    },    
    {
        labelGroup: "Público",
        items: [
            ...homeMenu,
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
