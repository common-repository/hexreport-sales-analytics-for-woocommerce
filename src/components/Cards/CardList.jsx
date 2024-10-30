import "../../assets/scss/elements/card-list.scss";
export default function CardList({lists,padd}){
    const style = padd != null ? {padding:`${padd}px`} : {};
    return (
        <div className="cardList" style={style}>
            <ol>
                {lists.map((item,index)=>(
                    <li className="line-container" key={index}>
                        <div className="title">{item.title}</div>
                        <div className="line"></div>
                        <div className="amount">{item.amount}</div>
                    </li>
                ))}

            </ol>
        </div>
    )
}
