import {
    mdiHome,
    mdiInformationOutline,
    mdiBriefcaseOutline,
    mdiPhone,
    mdiHomeOutline,
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
            // {
            //     label: "Nosotros",
            //     route: "about",
            //     icon: mdiInformationOutline,
            // },
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
