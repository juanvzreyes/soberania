import {
    mdiShape,
    mdiPackageVariant,
} from "@mdi/js";

const catalogMenu = [
    {
        label: "Categorías de producto",
        route: "categories.index",
        icon: mdiShape,
        permission: "categories.index", 
    },
    {
        label: "Productos",
        route: "products.index",
        icon: mdiPackageVariant,
        permission: "products.index", 
    },
];

export default catalogMenu;