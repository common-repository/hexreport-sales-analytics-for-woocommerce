import "./../../assets/scss/sections/topbar.scss";
import {useState,useEffect,useRef} from "react";
import menuIcon from "../../img/Menu.png"
import {useSidebar} from "../../context/SidebarContext.jsx";

export default function StickyTopBar(){
    const [openDropdown,setOpenDropdown] = useState(false);
    const dropdownRef = useRef(null);
    useEffect(() => {
        const handleDocumentClick = (event) => {
            if (dropdownRef.current && !dropdownRef.current.contains(event.target)) {
                setOpenDropdown(false);
            }
        };

        // Only add the event listener when the dropdown is open
        if (openDropdown) {
            document.addEventListener('mousedown', handleDocumentClick);
        }

        // Remove event listener on cleanup
        return () => {
            document.removeEventListener('mousedown', handleDocumentClick);
        };
    }, [openDropdown]);

    const { toggleSidebar, isSidebarActive } = useSidebar();

    return (
      <div className="topbarWrapper">
        <div className="left-wrap">
             <span className="menuIcon" onClick={toggleSidebar}>
                <img src={menuIcon} alt="report genix logo icon"/>
            </span>
        </div>
          <div className="right-wrap">
          </div>
      </div>
    );
}
