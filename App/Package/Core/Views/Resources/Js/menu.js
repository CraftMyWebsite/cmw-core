
/*
    Get choice input select
 */

const choice = document.getElementsByClassName('super-choice')
const packageParent = document.getElementsByClassName('addPackage')
const customParent = document.getElementsByClassName('addCustom')

for (let i = 0; i < choice.length; i++) {
    choice[i].addEventListener("change", () => {
        for (let o = 0; o < packageParent.length; o++) {
            packageParent[o].classList.toggle('d-none');
        }
        for (let p = 0; p < customParent.length; p++) {
            customParent[p].classList.toggle('d-none');
        }
    })
}


/*
    Display Allowed Groups list input
 */

const toggleBtn = document.getElementsByClassName('allowedGroups')
const allowedGroupsParent = document.getElementsByClassName('listAllowedGroups')

for (let u = 0; u < toggleBtn.length; u++) {
    toggleBtn[u].addEventListener("change", () => {
        for (let y = 0; y < allowedGroupsParent.length; y++) {
            allowedGroupsParent[y].classList.toggle('d-none')
        }
    })
}









/*
    Drag menus
 */



const nestedQuery = '.nested-sortable';
const identifier = 'sortableId';
const root = document.getElementById('menus');

updateSortables();

function updateSortables() {
    let nestedSortables = [].slice.call(document.querySelectorAll('.nested-sortable'));

    // Loop through each nested sortable element
    for (let i = 0; i < nestedSortables.length; i++) {
        new Sortable(nestedSortables[i], {
            handle: '.handle',
            group: 'nested',
            animation: 150,
            fallbackOnBody: true,
            swapThreshold: 0.65,
            onEnd: function (evt) {
                updateStatus(evt)
            },
        });
    }
}



function serialize(sortable) {
    let serialized = [];
    let children = [].slice.call(sortable.children);
    for (let i in children) {
        let nested = children[i].querySelector(nestedQuery);
        serialized.push({
            id: children[i].dataset[identifier],
            children: nested ? serialize(nested) : []
        });
    }
    return serialized
}


function updateStatus(evt) {
    console.log(evt.item.children[1].value)
}


// Add dropdown menu
function addDropdown() {
    let container = document.createElement("div");
    container.className = "list-group-item nested-1";

    let dropdown = document.createElement("div");
    dropdown.className = "list-group nested-sortable";
    dropdown.innerText = "Nouveau dropdown";

    root.append(container);
    container.append(dropdown);
    dropdown.append(addMenu())

    updateSortables();
}

// Add menu
function addMenu() {
    let menus = document.createElement("div");
    menus.className = "list-group-item nested-1";
    menus.innerText = "Nouveau menu";

    root.append(menus);

    return menus;
}