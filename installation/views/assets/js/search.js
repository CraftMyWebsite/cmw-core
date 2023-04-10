const searchFunction = () => {

    let input = document.getElementById("searchInput");
    let filter = input.value.toUpperCase();
    let ul = document.getElementById("mySearch");
    let li = ul.getElementsByTagName("li");
    let txtValue;

    for (let i = 0; i < li.length; i++) {
        let span = li[i].getElementsByTagName("span")[0];
        txtValue = span.textContent || span.innerText;
        if (txtValue.toUpperCase().indexOf(filter) > -1) {
            li[i].style.display = "";
        } else {
            li[i].style.display = "none";
        }
    }
}