// DoughnutChart.js
import React from 'react';
import { Chart as ChartJS, ArcElement, Tooltip, Legend } from 'chart.js';
import { Doughnut } from 'react-chartjs-2';

ChartJS.register(ArcElement, Tooltip, Legend);

function DoughnutChart({ labels, datasets }) {
	const data = {
		labels: labels,
		datasets: datasets,
	};

	return <Doughnut data={data} />;
}

export default DoughnutChart;
