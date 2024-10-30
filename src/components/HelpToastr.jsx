import "../assets/scss/elements/help-toastr.scss";
export default function HelpToastr({text,anchorText,link}){
    return (
        <div className="helpWanted">
           <div className="textWrapper">
               {text} <a target="_blank" href={link}>{anchorText}</a>
           </div>
        </div>
    )
}
