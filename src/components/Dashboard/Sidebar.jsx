import "./../../assets/scss/sections/sidebar.scss";
import  logoIcon  from "../../img/logoIcon.png";
import {NavLink} from "react-router-dom";
import {useSidebar} from "../../context/SidebarContext.jsx";
import { __ } from "@wordpress/i18n";
import {ShoppingBasket, LayoutGrid, Gauge} from "lucide-react";
export default function Sidebar(){

    const { toggleSidebar, isSidebarActive } = useSidebar();

    return (
        <div className={`sidebarWrapper ${isSidebarActive ? 'active' : ''}`}>
            <div className="logoWrap">
                <a href="#" className="logoBox">
                    <img  className="logoIcon" src={logoIcon} alt="report genix logo icon"/>
                    <span className="logoText">HexReport</span>
                </a>
            </div>
            <ul>
                <li>
					<NavLink to={'/'} exact activeClassName="active"><Gauge size={20}/><span className="menuText">{__("Dashboard","hexreport")} </span></NavLink>
                </li>
				<li>
					<NavLink to={"/sales/by-product"} exact activeClassName="active"><ShoppingBasket size={20}/><span className="menuText">{__( "Sales by Products", "hexreport" )}</span></NavLink>
				</li>
				<li>
					<NavLink to={"/sales/by-categories"} exact activeClassName="active"><LayoutGrid size={20}/><span className="menuText">{__("Sales by Categories","hexreport")}</span></NavLink>
				</li>
            </ul>
        </div>
    )
}
