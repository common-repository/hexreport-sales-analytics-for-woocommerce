import "../../assets/scss/elements/card-header.scss";

export default function CardHeader({title,align='left',padd,border= false}){
    const style = padd != null ? {padding:`${padd}px`} : {};
    return (
        <div className={`cardHeader ${ !border ? 'noborder' : ''}`} align={align} style={style}>
            <h4 className="title">{title}</h4>
        </div>
    )
}
