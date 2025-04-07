<template>
    <div style="height: 300px">
        <Doughnut :data="chartData" :options="chartOptions" />
    </div>
</template>

<script>
import { Doughnut } from "vue-chartjs";
import { Chart as ChartJS, Title, Tooltip, Legend, ArcElement } from "chart.js";

ChartJS.register(Title, Tooltip, Legend, ArcElement);

export default {
    name: "DashboardChart",
    components: {
        Doughnut,
    },
    props: {
        chartData: {
            type: Object,
            required: true,
        },
        chartOptions: {
            type: Object,
            required: false,
            default: () => ({}),
        },
    },
};
</script>
