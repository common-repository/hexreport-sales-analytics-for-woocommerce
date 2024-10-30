import "../../assets/scss/sections/page-header.scss";

export default function PageHeader({pageTitle,children,extraClass}){
    return (
        <div className={`pageHeader ${extraClass}`}>
            <div className="leftWrap">
                <h1 className="pageTitle">{pageTitle}</h1>
            </div>

            {children != null ? (<div className="rightWrap">
                {children}
            </div>) : '' }
        </div>
    )
}
