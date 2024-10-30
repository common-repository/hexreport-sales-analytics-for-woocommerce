
import PageLayout from "../../components/Layouts/PageLayout.jsx";
import PageHeader from "../../components/Sections/PageHeader.jsx";
import Card from "../../components/Cards/Card.jsx";

export default function Sales(){
    return (
        <PageLayout pageTitle="Sales">
           <Card>
               <PageHeader pageTitle="Sales By Channel"/>
           </Card>
        </PageLayout>
    );
}
