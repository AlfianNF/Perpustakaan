<template>
    <div style="height: 300px">
        <Doughnut :data="chartData" :options="chartOptions" />
    </div>
</template>

<script>
import { Doughnut } from "vue-chartjs";
import ChartDataLabels from 'chartjs-plugin-datalabels';
import { Chart as ChartJS, Title, Tooltip, Legend, ArcElement } from "chart.js";

const centerTextPlugin = {
  id: 'centerText',
  beforeDraw(chart) {
    const { width } = chart;
    const { height } = chart;
    const ctx = chart.ctx;

    const data = chart.data.datasets[0].data;
    const total = data.reduce((sum, val) => sum + val, 0);

    ctx.save();
    ctx.font = 'bold 16px sans-serif';
    ctx.fillStyle = '#333';
    ctx.textAlign = 'center';
    ctx.textBaseline = 'middle';
    ctx.fillText(`Total: ${total}`, width / 2, height / 2);
    ctx.restore();
  }
};

ChartJS.register(Title, Tooltip, Legend, ArcElement,ChartDataLabels,centerTextPlugin);

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
