import {
    mdiChartScatterPlot,
    mdiViewList,
} from "@mdi/js";

export default [
    {
        label: "Reportes",
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