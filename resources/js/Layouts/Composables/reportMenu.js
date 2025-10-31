import {
    mdiChartScatterPlot,
    mdiViewList,
} from "@mdi/js";

export default [
    {
        label: "Reportes",
        permission: "menu.security",
        icon: mdiChartScatterPlot,
        menu: [
            {
                label: "Lista de productores",
                route: "producer.report.index",
                icon: mdiViewList,
            },
        ],
    },
]