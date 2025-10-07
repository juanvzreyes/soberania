import {
    mdiMonitorDashboard,
} from "@mdi/js";
import { computed } from "vue";
import securityMenu from "./Menus/securityMenu";

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
        labelGroup: "Administración",
        items: [
            ...securityMenu,
        ],
    },
];

export const useAside = () => {
    const asideMenu = computed(() => {

        return baseMenu.map((section) => {
            if (section.labelGroup === "Inicio") {
                return {
                    ...section,
                    items: [
                        ...section.items,
                        // profileConfig, // Agregar configuración dinámica
                    ].filter(Boolean), // Filtrar nulls
                };
            }
            return section;
        });
    });

    return {
        asideMenu,
    };
};
