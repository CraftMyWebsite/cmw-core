var elements = document.getElementsByClassName('list');

for (var i = 0; i < elements.length; i++) {
    console.log(i);
    new Sortable(elements[i], {
        group: 'shared',
        invertSwap: true
    });
}