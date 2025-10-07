import {
    mdiAccount,
    mdiSecurity,
} from "@mdi/js";

export default [
    {
        label: "Seguridad",
        permission: "menu.security",
        icon: mdiSecurity,
        menu: [
            {
                label: "Usuarios",
                route: "users.index",
                icon: mdiAccount,
                permission: "users.index",
            },
        ],
    },
]