export default function DashBox({title, text, currency='$'}){
    return (
      <div className="dashBox">
            <h6 className="title">{title}</h6>
            <p className="text"><span className="base-color">{currency}</span>{text}</p>
      </div>
    );
}
