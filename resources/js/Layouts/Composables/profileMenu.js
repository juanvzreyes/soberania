import {
    mdiAccountTie,
    mdiAccountGroup,
    mdiCart,
    mdiAccountDetails,
    mdiStore,
    mdiPackageVariant
} from "@mdi/js";

const producerProfileMenu = {
    label: "Mi Perfil Productor",
    icon: mdiAccountTie,
    permission: "profile.producer.index",
    menu: [ 
        {
            label: "Información General",
            route: "profile.producer.show",
            icon: mdiAccountDetails,
            permission: "profile.producer.index",
        },
        // {
        //     label: "Mis Productos",
        //     route: "profile.producer.products",
        //     icon: mdiPackageVariant,
        //     permission: "profile.producer.products.index",
        // },
    ],
};

const cooperativeProfileMenu = {
    label: "Mi Perfil Cooperativa",
    icon: mdiAccountGroup,
    permission: "profile.cooperative.index",
    menu: [
        {
            label: "Detalles de la Cooperativa",
            route: "profile.cooperative.show",
            icon: mdiStore,
            permission: "profile.cooperative.index",
        },
        // {
        //     label: "Miembros",
        //     route: "profile.cooperative.members",
        //     icon: mdiAccountGroup,
        //     permission: "profile.cooperative.members.index",
        // },
    ],
};

// const consumerProfileMenu = {
//     label: "Mi Perfil Consumidor",
//     icon: mdiCart,
//     permission: "profile.consumer.index", 
//     menu: [
//         {
//             label: "Mis Datos",
//             route: "profile.consumer.show",
//             icon: mdiAccountDetails,
//             permission: "profile.consumer.index",
//         },
//         {
//             label: "Historial de Compras",
//             route: "profile.consumer.orders",
//             icon: mdiCart,
//             permission: "profile.consumer.orders.index",
//         },
//     ],
// };

export default [
    producerProfileMenu,
    cooperativeProfileMenu,
    // consumerProfileMenu,
];