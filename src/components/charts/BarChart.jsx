import React from 'react';
import {
	Chart as ChartJS,
	CategoryScale,
	LinearScale,
	BarElement,
	Title,
	Tooltip,
	Legend,
} from 'chart.js';
import { Bar } from 'react-chartjs-2';

ChartJS.register(
	CategoryScale,
	LinearScale,
	BarElement,
	Title,
	Tooltip,
	Legend
);

export const options = {
	responsive: true,
	plugins: {
		legend: {
			position: 'top',
		},
		title: {
			display: true,
			text: 'Comparison Chart',
		},
	},
};

const labels = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];

export default function BarChart({ title, label1, label2, data1, data2 }) {
	const data = {
		labels,
		datasets: [
			{
				label: label1,
				data: data1,
				backgroundColor: 'rgba(255, 99, 132, 0.5)',
			},
			{
				label: label2,
				data: data2,
				backgroundColor: 'rgba(53, 162, 235, 0.5)',
			},
		],
	};

	return (
		<div className="chartWrapper">
			<div className="topPart">
				<h4 className="title">{title}</h4>
			</div>
			<Bar options={options} data={data} />
		</div>
	);
}
