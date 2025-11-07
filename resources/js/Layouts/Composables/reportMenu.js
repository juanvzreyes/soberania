import {
    mdiAccountGroup,
    mdiChartBoxMultiple,
    mdiHomeGroup,
    mdiListBox,
    mdiNaturePeople,
} from "@mdi/js";

export default [
    {
        label: "Reportes",
        icon: mdiChartBoxMultiple,
        menu: [
            {
                label: "Productores",
                route: "producer.report.index",
                icon: mdiListBox,
            },
            {
                label: "Productores",
                route: "producer.reports.index",
                icon: mdiAccountGroup,
                permission: "producer.reports.index",
            },
            {
                label: "Cooperativas",
                route: "cooperative.reports.index",
                icon: mdiHomeGroup,
                permission: "cooperative.reports.index",
            },
            {
                label: "Consumidores",
                route: "consumer.reports.index",
                icon: mdiNaturePeople,
                permission: "consumer.reports.index",
            },
        ],
    },
]