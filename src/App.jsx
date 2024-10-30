import MainLayoutContainer from "./components/Dashboard/MainLayoutContainer";
import {HashRouter, Routes, Route} from "react-router-dom";
import "./assets/scss/main.scss";
import "line-awesome/dist/line-awesome/css/line-awesome.min.css";
import Dashboard from "./components/Page/Dashboard";
import ByProduct from "./pages/sales/by-product";
import ByCategories from "./pages/sales/by-categories";
import PageLayout from "./components/Layouts/PageLayout";
export default function App() {
	return (
		<HashRouter>
			<div className="reportGenixMainArea">
				<PageLayout pageTitle="Dashboard">
					<Routes>
						<Route element={<Dashboard />} path="/"/>
						<Route element={<ByProduct />} path="/sales/by-product"/>
						<Route element={<ByCategories />} path="/sales/by-categories"/>
					</Routes>
				</PageLayout>
			</div>
		</HashRouter>
	);
}


