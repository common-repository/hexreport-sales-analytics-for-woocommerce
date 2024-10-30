import DashBox from "../Elements/DashBox.jsx";
import StickyTopBar from "./StickyTopBar.jsx";
import SalesChart from "../charts/SalesChart.jsx";
import OrderCard from "../OrderCard.jsx";
import Container from "../Layouts/Container.jsx";
import CardList from "../Cards/CardList.jsx";
import Card from "../Cards/Card.jsx";
// import {DoughnutChart} from "../charts/DoughnutChart.jsx";
import CardHeader from "../Cards/CardHeader.jsx";
import "../../assets/scss/sections/ContentArea.scss";
import {useSidebar} from "../../context/SidebarContext.jsx";

export default function ContentArea({pageTitle,children}){
    const { toggleSidebar, isSidebarActive } = useSidebar();
    return (
      <div className={`contentAreaWrapper ${!isSidebarActive ? 'full-width' : ''}`}>
          <StickyTopBar pageTitle={pageTitle}/>
          {children}
      </div>
    );
}
