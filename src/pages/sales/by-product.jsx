import PageLayout from "../../components/Layouts/PageLayout.jsx";
import PageHeader from "../../components/Sections/PageHeader.jsx";
import Card from "../../components/Cards/Card.jsx";
import Container from "../../components/Layouts/Container.jsx";
import DoughnutChart from "../../components/charts/DoughnutChart.jsx";
import CardList from "../../components/Cards/CardList.jsx";
import "../../assets/scss/sections/sales-by-product.scss";
import BarChart from "../../components/charts/BarChart.jsx";
import React, {useEffect, useState} from "react";
import axios from "axios";
import { __ } from '@wordpress/i18n';

export default function ByProduct(){
	const {nonce,ajaxUrl} = hexReportData;

	const [topSellingProductsNames, setTopSellingProductsNames] = useState([]);
	const [topSellingProductsCount, setTopSellingProductsCount] = useState([]);
	const [productSaleRatio, setProductSaleRatio] = useState([]);

	const [topFirstSellingProductMonthlyData, setTopFirstSellingProductMonthlyData] = useState([])
	const [topSecondSellingProductMonthlyData, setTopSecondSellingProductMonthlyData] = useState([])

	const [topFirstSellingProductName, setTopFirstSellingProductName] = useState([])
	const [topSecondSellingProductName, setTopSecondSellingProductName] = useState([])

	useEffect(() => {
		axios
			.get(ajaxUrl, {
				params: {
					nonce: nonce,
					action: 'show_first_top_selling_product_monthly_data',
				},
				headers: {
					'Content-Type': 'application/json',
				},
			})
			.then(({data}) => {
				if (data) {
					setTopFirstSellingProductMonthlyData(data.firstTopSellingProductMonthlyData)

					setTopSecondSellingProductMonthlyData(data.secondTopSellingProductMonthlyData)

					setTopFirstSellingProductName(data.firstTopSellingProductName)
					setTopSecondSellingProductName(data.secondTopSellingProductName)

					setTopSellingProductsNames(data.topSellingProductsNames)
					setTopSellingProductsCount(data.topSellingProductsCount)
					setProductSaleRatio(data.productSaleRatio)
				}
				// Handle the response data
			})
			.catch((error) => {
				console.error('Error:', error);
			});

	}, []);

	const noDataText = __("No Data Yet","hexreport");

	let cardListItems;

	if (topSellingProductsNames.length > 0) {
		cardListItems = topSellingProductsNames.map((name, index) => ({
			title: name,
			amount: "(" + parseFloat(productSaleRatio[index]).toFixed(2) + "%)",
		}));
	} else {
		cardListItems = [
			{ title: noDataText, amount: "(00.00%)" },
			{ title: noDataText, amount: "(00.00%)" },
			{ title: noDataText, amount: "(00.00%)" },
			{ title: noDataText, amount: "(00.00%)" },
		];
	}

	const noSaleText = __("No sales yet","hexreport");

	const doughnutProductData = {
		labels: topSellingProductsNames.length > 0 ? topSellingProductsNames : [noSaleText],
		datasets: [
			{
				label: __("No of times", "hexreport"),
				data: topSellingProductsCount.length > 0 ? topSellingProductsCount : [1],
				backgroundColor: [
					'rgba(255, 99, 132, 0.2)',
					'rgba(54, 162, 235, 0.2)',
					'rgba(255, 206, 86, 0.2)',
					'rgba(75, 192, 192, 0.2)',
					'rgba(153, 102, 255, 0.2)',
					'rgba(255, 159, 64, 0.2)',
				],
				borderColor: [
					'rgba(255, 99, 132, 1)',
					'rgba(54, 162, 235, 1)',
					'rgba(255, 206, 86, 1)',
					'rgba(75, 192, 192, 1)',
					'rgba(153, 102, 255, 1)',
					'rgba(255, 159, 64, 1)',
				],
				borderWidth: 1,
			},
		],
	};

	const label1 = topFirstSellingProductName;
	const label2 = topSecondSellingProductName;
	const data1 = topFirstSellingProductMonthlyData;
	const data2 = topSecondSellingProductMonthlyData;

    return (
            <>
				<Card padd="0">
					<PageHeader pageTitle={__("Sales By Product","hexreport")} extraClass="padding-20 padding-bottom-0"/>
					<div className="commonFilterWrap pb0">

					</div>
					<Container col="1">
						<div className="chartWithListWrapper">
							<div className="donChart">
								<DoughnutChart labels={doughnutProductData.labels} datasets={doughnutProductData.datasets}/>
							</div>
							<div className="orderListWrapper">
								<CardList lists={cardListItems}/>
							</div>
						</div>
					</Container>
				</Card>
				<Card extraClass="margin-top-20">
					<BarChart title={__("Sales By Product","hexreport")} label1={label1 ? label1 : noDataText} label2={label2 ? label2 : noDataText} data1={data1 ? data1 : []} data2={data2 ? data2 : []}/>
				</Card>
			</>
    );
}
