import Container from "../Layouts/Container.jsx";
import DashBox from "../Elements/DashBox.jsx";
import SalesChart from "../charts/SalesChart.jsx";
import Card from "../Cards/Card.jsx";
import DoughnutChart from "../charts/DoughnutChart.jsx";
import CardList from "../Cards/CardList.jsx";
import OrderCard from "../OrderCard.jsx";
import axios from "axios";
import React, {useEffect,useState} from 'react';
import { __ } from "@wordpress/i18n";

export default function Dashboard(){
	const [totalSalesAmount, setTotalSalesAmount] = useState( {
		totalSales: 0,
		totalCancelledAmount: 0,
		totalOrdersAmount: 0,
		totalRefundedAmount: 0,
		topSellingProductName: '',
		topSellingProductPrice: 0,
		topSellingCatName: '',
		topSellingCatPrice: 0,

		totalSalesOfYear: [],

		totalVisitorsCount: [],

		cancelledOrderRation : 0,
		refundedOrderRation : 0,
		failedOrderRation : 0,

		bankTransferRation : 0,
		checkPaymentRatio : 0,
		cashOnDeliveryRatio : 0,
		localPickupRatio : 0,
		flatRateRatio : 0,
		freeShippingRatio : 0,
	} );

	const {nonce,ajaxUrl} = hexReportData;

	useEffect(() => {
		axios
			.get(ajaxUrl, {
				params: {
					nonce: nonce,
					action: 'total_sales_amount',
				},
				headers: {
					'Content-Type': 'application/json',
				},
			})
			.then(({data}) => {
				if (data) {
					setTotalSalesAmount({
						totalSales : data.totalSales,
						totalCancelledAmount : data.totalCancelledAmount,
						totalOrdersAmount : data.totalOrdersAmount,
						totalRefundedAmount : data.totalRefundedAmount,
						topSellingProductName : data.topSellingProductName,
						topSellingProductPrice : data.topSellingProductPrice,
						topSellingCatName : data.topSellingCatName,
						topSellingCatPrice : data.topSellingCatPrice,

						totalSalesOfYear: data.totalSalesOfYear,

						totalVisitorsCount: data.totalVisitorsCount,

						cancelledOrderRation : data.cancelledOrderRation,
						refundedOrderRation : data.refundedOrderRation,
						failedOrderRation : data.failedOrderRation,

						bankTransferRation : data.bankTransferRation,
						checkPaymentRatio : data.checkPaymentRatio,
						cashOnDeliveryRatio : data.cashOnDeliveryRatio,
						localPickupRatio : data.localPickupRatio,
						flatRateRatio : data.flatRateRatio,
						freeShippingRatio : data.freeShippingRatio,
					})
				}
				// Handle the response data
			})
			.catch((error) => {
				console.error('Error:', error);
			});

	}, []);

	const {totalSales,totalCancelledAmount,totalOrdersAmount,totalRefundedAmount,topSellingProductName,topSellingProductPrice,topSellingCatName,topSellingCatPrice, totalSalesOfYear, totalVisitorsCount,cancelledOrderRation,refundedOrderRation,failedOrderRation,bankTransferRation,checkPaymentRatio,cashOnDeliveryRatio,localPickupRatio,flatRateRatio,freeShippingRatio  } = totalSalesAmount;

	const translatedText =
		{
			cancelled: __("Cancelled", "hexreport"),
			refunded: __("Refunded", "hexreport"),
			failed: __("Failed", "hexreport"),
			directBankTranser: __("Direct bank transfer", "hexreport"),
			checkPayments: __("Check payments", "hexreport"),
			cashOnDelivery: __("Cash on delivery", "hexreport"),
			completedOrders: __("Completed Orders", "hexreport"),
			totalOrders: __("Total Orders", "hexreport"),
			cancelledOrders: __("Cancelled Orders", "hexreport"),
			topSellingProduct: __("Top Selling Product:", "hexreport"),
			topSellingCategory: __("Top Selling Category:", "hexreport"),
			localPickup: __("Local Pickup", "hexreport"),
			flatRate: __("Flat Rate", "hexreport"),
			freeShipping: __("Free Shipping","hexreport"),
			percentOfRatio: __( '% of Ratio', 'hexreport' ),
			sales: __( 'Sales', 'hexreport' ),
			visitors: __( 'Visitors', 'hexreport' ),
		};

	const noDataFoundText = __("No data found","hexreport");

	const cardListItems = [
		{title: translatedText.cancelled,amount: cancelledOrderRation ? "("+Number(cancelledOrderRation).toFixed(2)+"%)" : "No data found"},
		{title: translatedText.refunded,amount: refundedOrderRation ? "("+Number(refundedOrderRation).toFixed(2)+"%)" : "No data found"},
		{title: translatedText.failed,amount: failedOrderRation ? "("+Number(failedOrderRation).toFixed(2)+"%)" : "No data found"},
		{title: translatedText.directBankTranser,amount: bankTransferRation ? "("+Number(bankTransferRation).toFixed(2)+"%)" : "No data found"},
		{title: translatedText.checkPayments,amount: checkPaymentRatio ? "("+Number(checkPaymentRatio).toFixed(2)+"%)" : "No data found"},
		{title: translatedText.cashOnDelivery,amount: cashOnDeliveryRatio ? "("+Number(cashOnDeliveryRatio).toFixed(2)+"%)" : "No data found"},
	];

	const doughnutDashboardData = {
		labels: [translatedText.localPickup, translatedText.flatRate, translatedText.freeShipping],
		datasets: [
			{
				label: translatedText.percentOfRatio,
				data: [localPickupRatio, flatRateRatio, freeShippingRatio],
				backgroundColor: ['rgba(255, 99, 132, 0.2)', 'rgba(54, 162, 235, 0.2)', 'rgba(255, 206, 86, 0.2)'],
				borderColor: ['rgba(255, 99, 132, 1)', 'rgba(54, 162, 235, 1)', 'rgba(255, 206, 86, 1)'],
				borderWidth: 1,
			},
		],
	};
	const noData = () => {
		if (0 === localPickupRatio && 0 === flatRateRatio && 0 === freeShippingRatio) {
			return <h2 className={"no-data"}>{noDataFoundText}</h2>;
		}
		return <DoughnutChart labels={doughnutDashboardData.labels} datasets={doughnutDashboardData.datasets}/>;
	}

	return (
		<>
			<Container col={3}>
				<DashBox title={translatedText.completedOrders} text={totalSales ? totalSales : 0} />
				<DashBox title={translatedText.totalOrders} text={totalOrdersAmount ? totalOrdersAmount : 0}  />
				<DashBox title={translatedText.cancelledOrders} text={totalCancelledAmount ? totalCancelledAmount : 0} />
				<DashBox title={`${translatedText.topSellingProduct} ${topSellingProductName}`} text={topSellingProductPrice ? topSellingProductPrice : 0} />

				<DashBox title={`${translatedText.topSellingCategory} ${topSellingCatName}`} text={topSellingCatPrice ? topSellingCatPrice : 0}  />
				<DashBox title={translatedText.refunded} text={totalRefundedAmount ? totalRefundedAmount : 0} />
			</Container>
			<Container col={2} extraClass={'margin-top-30'}>
				<SalesChart title={translatedText.sales} data={totalSalesOfYear ? totalSalesOfYear : []}/>
				<SalesChart title={translatedText.visitors} data={totalVisitorsCount ? totalVisitorsCount : []}/>
			</Container>
			<Container col={2} extraClass={'margin-top-30'}>
				<Card>
					<div className="chartWithListWrapper">
						<div className="donChart">
							{noData()}
						</div>
						<div className="orderListWrapper">
							<CardList lists={cardListItems}/>
						</div>
					</div>
				</Card>
				<OrderCard title="Orders"/>
			</Container>
		</>
	)
}
