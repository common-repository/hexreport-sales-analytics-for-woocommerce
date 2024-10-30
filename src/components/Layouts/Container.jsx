import "../../assets/scss/common/grid.scss";
export default function Container({children,col=1,extraClass}){
    const column = `col${col}`;
    return (
        <div className={`gridContainer ${column} ${extraClass ?? ''}`}>
            {children}
        </div>
    )
}
