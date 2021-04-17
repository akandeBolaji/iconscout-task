let selectedIcon;

const editClicked = (icon) => {
    selectedIcon = icon;
    let name = document.getElementById("editName");
    name.value = icon.name;
    let style = document.getElementById("editStyle");
    style.value = icon.style;
    let price = document.getElementById("editPrice");
    price.value = icon.price;
    let img_url = document.getElementById("editImage");
    img_url.value = icon.img_url;
    let categories = document.getElementById("editCategories");
    categories.value = icon.categories.map(c => c.value).join();
    let tags = document.getElementById("editTags");
    tags.value = icon.tags.map(t => t.value).join();
    let colors = document.getElementById("editColors");
    colors.value = icon.colors.map(c => `#${c.hex_value}`).join();
}

const deleteClicked = (icon) => {
    selectedIcon = icon;
    let text = document.getElementById("deleteText");
    text.innerText = `Are you sure you want to delete icon ${icon.name} with id - ${icon.id}?`
}

const detailsClicked = (icon) => {
    let id = document.getElementById("detailsId");
    id.innerText = `Id - ${icon.id}`
    let name = document.getElementById("detailsName");
    name.innerText = `Name - ${icon.name}`
    let style = document.getElementById("detailsStyle");
    style.innerText = `Style - ${icon.style}`;
    let price = document.getElementById("detailsPrice");
    price.innerText = `Price - ${icon.price}`;
    let img_url = document.getElementById("detailsImage");
    img_url.innerText = `Image URL - ${icon.img_url}`;
    let categories = document.getElementById("detailsCategories");
    categories.innerText = `Categories - ${icon.categories.map(c => c.value).join()}`;
    let tags = document.getElementById("detailsTags");
    tags.innerText = `Tags - ${icon.tags.map(t => t.value).join()}`;
    let colors = document.getElementById("detailsColors");
    colors.innerText = `Colors - ${icon.colors.map(c => `#${c.hex_value}`).join()}`;
}

const submitEdit = (event) => {
    event.preventDefault();
    document.getElementById("editButton").disabled = true;
    let name = document.getElementById("editName").value
    let style = document.getElementById("editStyle").value
    let price = document.getElementById("editPrice").value
    let img_url = document.getElementById("editImage").value
    let categories = document.getElementById("editCategories").value
    let tags = document.getElementById("editTags").value
    let colors = document.getElementById("editColors").value
    if (!name && !style && !price && !img_url && !categories && !tags && !colors ) {
        return;
    }
    let data = {
        name:   name
                ? name
                : selectedIcon.name,
        style:   style
                ? style
                : selectedIcon.style,
        price:   price
                ? price
                : selectedIcon.price,
        img_url:  img_url
                ? img_url
                : selectedIcon.img_url,
        categories: categories
                ? categories.split(",")
                : selectedIcon.categories.map(c => c.value).join(),
        tags: tags
                ? tags.split(",")
                : selectedIcon.tags.map(c => c.value).join(),
        colors: colors
                ? colors.split(",")
                : selectedIcon.colors.map(c => c.value).join()

    }
    fetch(`icons/${selectedIcon.id}`, {
        method: 'PUT', // or 'PUT'
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        body: JSON.stringify(data),
        })
        .then(response => response.json())
        .then(data => {
            console.log('Success:', data);
            location.reload();
        })
        .catch((error) => {
            document.getElementById("editButton").disabled = false;
            console.error('Error:', error);
            //TODO implement error
        });
}

const submitNew = (event) => {
    event.preventDefault();
    document.getElementById("createButton").disabled = true;
    let name = document.getElementById("newName").value
    let style = document.getElementById("newStyle").value
    let price = document.getElementById("newPrice").value
    let img_url = document.getElementById("newImage").value
    let categories = document.getElementById("newCategories").value
    let tags = document.getElementById("newTags").value
    let colors = document.getElementById("newColors").value
    if (!name || !style || !price || !img_url || !categories || !tags || !colors ) {
        return;
    }
    let data = {
        name: name,
        style: style,
        price: price,
        img_url: img_url,
        categories: categories.split(","),
        tags: tags.split(","),
        colors: colors.split(",")

    }
    fetch('icons', {
        method: 'POST', // or 'PUT'
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        body: JSON.stringify(data),
        })
        .then(response => response.json())
        .then(data => {
            console.log('Success:', data);
            location.reload();
        })
        .catch((error) => {
            console.error('Error:', error);
            document.getElementById("createButton").disabled = false;
            //TODO implement error
        });
}


const submitDelete = () => {
    document.getElementById("deleteButton").disabled = true;
    fetch(`icons/${selectedIcon.id}`, {
        method: 'DELETE', // or 'PUT'
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        })
        .then(response => response.json())
        .then(data => {
            console.log('Success:', data);
            location.reload();
        })
        .catch((error) => {
            document.getElementById("deleteButton").disabled = false;
            console.error('Error:', error);
            //TODO implement error
        });
}
