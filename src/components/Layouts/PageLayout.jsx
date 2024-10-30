import Sidebar from "../Dashboard/Sidebar.jsx";
import ContentArea from "../Dashboard/ContentArea.jsx";
import HelpToastr from "../HelpToastr.jsx";
import {SidebarProvider} from "../../context/SidebarContext.jsx";
import {__} from "@wordpress/i18n";

export default function PageLayout({children,pageTitle,helpToastr}){
	const needHelpText = __("Need any help? ","hexreport");
	const contactUsText = __("Contact us.","hexreport");
    return (
        <SidebarProvider>
            <div className="LayoutContainer">
                <Sidebar />
                <ContentArea pageTitle={pageTitle}>
                    {children}
                    <HelpToastr text={needHelpText} anchorText={contactUsText} link="https://wphex.com"/>
                </ContentArea>
            </div>
        </SidebarProvider>
    )
}
