export default [
    {
        routeName: "welcome",
        label: "Inicio",
    },
    {
        label: "Productores",
        children: [
            {
                routeName: "producer.map.index",
                label: "Mapa de Productores",
                description: "Explora la ubicación de los productores."
            }
        ]
    },
];