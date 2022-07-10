function getChildren(elem) {
    let parent = [];
    jQuery(elem).children("ul").children("li").each(function() {
        if(jQuery(this).data("id")) {
            parent.push(jQuery(this).data("id"));
        }
        if(jQuery(this).children("ul").children("li").length) {
            parent.push(getChildren(this));
        }
    });
    return parent;
}

jQuery(document).ready(function() {
    let menu = dragula(jQuery(".menu-drag ul").toArray(), {
        mirrorContainer: jQuery(".menu-drag")[0]
    });
    menu.on("drop", function(el, target, source, sibling) {
        console.log(getChildren(jQuery(".menu-drag")));
    });
});