import {
    mdiAccount,
    mdiDatabaseCog,
    mdiSecurity,
} from "@mdi/js";

export default [
    {
        label: "Seguridad",
        permission: "menu.security",
        icon: mdiSecurity,
        menu: [
            // {
            //     label: "Modulos",
            //     route: "users.index",
            //     icon: mdiViewModule,
            //     permission: "modules.index",
            // },
            // {
            //     label: "Permisos",
            //     route: "users.index",
            //     icon: mdiLockCheck,
            //     permission: "permissions.index",
            // },
            // {
            //     label: "Roles",
            //     route: "users.index",
            //     icon: mdiAccountMultiple,
            //     permission: "roles.index",
            // },
            {
                label: "Usuarios",
                route: "users.index",
                icon: mdiAccount,
                permission: "users.index",
            },
            {
                label: "Respaldo de BD",
                route: "backups.index",
                icon: mdiDatabaseCog,
                permission: "backups.index",
            },
        ],
    },
]