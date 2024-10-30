import CardHeader from "./CardHeader.jsx";
import CardList from "./CardList.jsx";
export default function Card({children,padd,extraClass}){
    const  style = padd !== null ? {padding: `${padd}px`} : {};
    return (
        <div className={`orderCard ${extraClass ?? ""}`} style={style}>
            {children}
        </div>
    )
}
