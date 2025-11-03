import {
    mdiChartLine,
    mdiChartScatterPlot,
    mdiFormatListNumbered,
    mdiChartLine,
} from "@mdi/js";

export default [
    {
        label: "Reportes",
        icon: mdiChartScatterPlot,
        menu: [
            {
                label: "Lista de productores",
                route: "producer.report.index",
                icon: mdiFormatListNumbered,
            },
            {
                label: "Reporte de productores",
                route: "producer.reports.index",
                icon: mdiChartLine,
                permission: "producer.reports.index",
            },
        ],
    },
]