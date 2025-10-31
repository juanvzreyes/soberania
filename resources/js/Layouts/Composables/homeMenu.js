import {
    mdiHome,
    mdiHomeOutline,
    mdiMapSearchOutline,
} from "@mdi/js";

export default [
    {
        label: "Público",
        icon: mdiHome,
        menu: [
            {
                label: "Inicio",
                route: "welcome",
                icon: mdiHomeOutline,
            },
            {
                label: "Mapa de productores",
                route: "producer.map.index",
                icon: mdiMapSearchOutline,
            },
            // {
            //     label: "Servicios",
            //     route: "services",
            //     icon: mdiBriefcaseOutline,
            // },
            // {
            //     label: "Contacto",
            //     route: "contact",
            //     icon: mdiPhone,
            // },

        ],
    },
];
