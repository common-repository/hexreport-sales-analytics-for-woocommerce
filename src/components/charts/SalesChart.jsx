import {
	Chart as ChartJS,
	CategoryScale,
	LinearScale,
	PointElement,
	LineElement,
	Title,
	Tooltip,
	Filler,
	Legend,
} from 'chart.js';
import { Line } from 'react-chartjs-2';
import "./../../assets/scss/elements/chart.scss";

ChartJS.register(
	CategoryScale,
	LinearScale,
	PointElement,
	LineElement,
	Title,
	Tooltip,
	Filler,
	Legend
);

export const options = {
	responsive: true,
	plugins: {
		legend: {
			position: 'top',
		},
		title: {
			display: false,
			text: 'Chart.js Line Chart',
		},
	},
};

export default function SalesChart({ title, data }) {
	const chartData = {
		labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July'],
		datasets: [
			{
				fill: true,
				label: ' ',
				data: data, // Use the data prop here
				borderColor: 'rgb(53, 162, 235)',
				backgroundColor: 'rgba(53, 162, 235, 0.5)',
			},
		],
	};

	return (
		<div className="chartWrapper">
			<div className="topPart">
				<h4 className="title">{title}</h4>
			</div>
			<Line options={options} data={chartData} />
		</div>
	);
}
