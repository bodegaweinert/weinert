
document.addEventListener("DOMContentLoaded", function(event) {
    var combinatoriaHelpDeskMenuElement = document.getElementById("menu-combinatoria-helpdesk-portal");
    if (typeof(combinatoriaHelpDeskMenuElement) != 'undefined' && combinatoriaHelpDeskMenuElement != null)
    {
        combinatoriaHelpDeskMenuElement.childNodes[0].href = "http://support.dablox.com/";
        combinatoriaHelpDeskMenuElement.childNodes[0].target = "_blank";
    }
});