const defaultProducten =
[
    { productID: 1, productNaam: 'Kivik', categorie: 'Zetel', beschrijving: '3-zitsbank', Kleur: 'Grijs', prijs: 499, stock: 25 }, 
    { productID: 2, productNaam: 'Vimle', categorie: 'Zetel', beschrijving: '4-zitsbank', Kleur: 'Beige', prijs: 1099, stock: 300 },
    { productID: 3, productNaam: 'Friheten', categorie: 'Zetel', beschrijving: 'Hoekslaapbank', Kleur: 'Grijs', prijs: 629, stock: 300 },
    { productID: 4, productNaam: 'Linanäs', categorie: 'Zetel', beschrijving: '3-zitsbank', Kleur: 'Beige', prijs: 399, stock: 300 },
    { productID: 5, productNaam: 'Klippan', categorie: 'Zetel', beschrijving: '2-zitsbank', Kleur: 'Donkergrijs', prijs: 249, stock: 300 },
    { productID: 6, productNaam: 'Ektorp', categorie: 'Zetel', beschrijving: '3-zitsbank', Kleur: 'Beige', prijs: 499, stock: 300 },
    { productID: 7, productNaam: 'Jättebo', categorie: 'Zetel', beschrijving: 'Longue', Kleur: 'Grijs', prijs: 630, stock: 300 },
    { productID: 8, productNaam: 'Söderhamn', categorie: 'Zetel', beschrijving: 'Hoekelement', Kleur: 'Grijs', prijs: 330, stock: 300 },
    { productID: 9, productNaam: 'Skönabäck', categorie: 'Zetel', beschrijving: '2-zitsbank', Kleur: 'Donkergrijs', prijs: 299, stock: 300 },
    { productID: 10, productNaam: 'Östanö', categorie: 'Stoel', beschrijving: 'Eetkamerstoel', Kleur: 'Donkergrijs', prijs: 15, stock: 300 },
    { productID: 11, productNaam: 'Odger', categorie: 'Stoel', beschrijving: 'Eetkamerstoel', Kleur: 'Zwart', prijs: 50, stock: 300 },
    { productID: 12, productNaam: 'Krylbo', categorie: 'Stoel', beschrijving: 'Eetkamerstoel', Kleur: 'Beige', prijs: 59, stock: 300 },
    { productID: 13, productNaam: 'Tossberg', categorie: 'Stoel', beschrijving: 'Eetkamerstoel', Kleur: 'Donkergrijs', prijs: 119, stock: 300 },
    { productID: 14, productNaam: 'Karlpetter', categorie: 'Stoel', beschrijving: 'Eetkamerstoel', Kleur: 'Groen', prijs: 47, stock: 20 }, 
    { productID: 15, productNaam: 'Tobias', categorie: 'Stoel', beschrijving: 'Eetkamerstoel', Kleur: 'Transparant', prijs: 75, stock: 300 },
    { productID: 16, productNaam: 'Grönsta', categorie: 'Stoel', beschrijving: 'Eetkamerstoel', Kleur: 'Wit', prijs: 50, stock: 300 },
    { productID: 17, productNaam: 'Bergmund', categorie: 'Stoel', beschrijving: 'Eetkamerstoel', Kleur: 'Donkergrijs', prijs: 69, stock: 5 }, 
    { productID: 18, productNaam: 'Gävle', categorie: 'Stoel', beschrijving: 'Fauteuil', Kleur: 'Wit', prijs: 30, stock: 300 },
    { productID: 19, productNaam: 'Kyrre', categorie: 'Kruk', beschrijving: 'Hout', Kleur: 'Beige', prijs: 25, stock: 300 },
    { productID: 20, productNaam: 'Domsten', categorie: 'Kruk', beschrijving: 'Hout/Metaal', Kleur: 'Paars', prijs: 25, stock: 300 },
    { productID: 21, productNaam: 'Kullaberg', categorie: 'Kruk', beschrijving: 'Hout/Metaal', Kleur: 'Zwart', prijs: 40, stock: 300 },
    { productID: 22, productNaam: 'Marius', categorie: 'Kruk', beschrijving: 'Metaal', Kleur: 'Zwart', prijs: 5, stock: 300 },
    { productID: 23, productNaam: 'Hauga', categorie: 'Kruk', beschrijving: 'Hout', Kleur: 'Wit', prijs: 13, stock: 300 },
    { productID: 24, productNaam: 'Holmsjö', categorie: 'Kruk', beschrijving: 'Hout', Kleur: 'Zwart', prijs: 45, stock: 20 }, 
    { productID: 25, productNaam: 'Perjohan', categorie: 'Kruk', beschrijving: 'Hout', Kleur: 'Beige', prijs: 30, stock: 300 },
    { productID: 26, productNaam: 'Stackholmen', categorie: 'Kruk', beschrijving: 'Hout', Kleur: 'Zwart', prijs: 30, stock: 300 },
    { productID: 27, productNaam: 'Mörtfors', categorie: 'Kruk', beschrijving: 'Metaal', Kleur: 'Grijs', prijs: 40, stock: 300 }
];

const producten = window.producten || defaultProducten;
const productsPerPage = 8;
let currentPage = 1;
const container = document.getElementById('product-container');
const paginationContainer = document.getElementById('pagination-container');

if (container) 
{
    renderProducts(currentPage);
    renderPagination();
}

const uniqueValues = (key) => [...new Set(producten.map(product => product[key]))];
const populateFilters = () => 
    {
    const colorFilter = document.getElementById('filter-color');
    const categoryFilter = document.getElementById('filter-category');

    if (colorFilter && categoryFilter) 
    {
        uniqueValues('Kleur').forEach(color => 
        {
            const option = document.createElement('option');
            option.value = color;
            option.textContent = color;
            colorFilter.appendChild(option);
        });

        uniqueValues('categorie').forEach(category => 
        {
            const option = document.createElement('option');
            option.value = category;
            option.textContent = category;
            categoryFilter.appendChild(option);
        });
    }
};

populateFilters();
const nameFilter = document.getElementById('filter-name');
const priceFilter = document.getElementById('filter-price');
const stockFilter = document.getElementById('filter-stock');
const colorFilter = document.getElementById('filter-color');
const categoryFilter = document.getElementById('filter-category');
const priceLabel = document.getElementById('price-label');

let filters = 
{
    name: '',
    maxPrice: 1000,
    stock: 0,
    colors: [],
    categories: []
};

if (nameFilter) 
{
    nameFilter.addEventListener('input', () => 
    {
        filters.name = nameFilter.value.toLowerCase();
        applyFilters();
    });
}

if (priceFilter) 
{
    priceFilter.addEventListener('input', () => 
    {
        filters.maxPrice = parseInt(priceFilter.value, 10);
        if (priceLabel) {
            priceLabel.textContent = `Max: €${filters.maxPrice}`;
        }
        applyFilters();
    });
}

if (stockFilter) 
{
    stockFilter.addEventListener('input', () => 
    {
        filters.stock = parseInt(stockFilter.value, 10) || 0;
        applyFilters();
    });
}

if (colorFilter) 
{
    colorFilter.addEventListener('change', () => 
    {
        filters.colors = Array.from(colorFilter.selectedOptions).map(option => option.value);
        applyFilters();
    });
}

if (categoryFilter) 
{
    categoryFilter.addEventListener('change', () => 
    {
        filters.categories = Array.from(categoryFilter.selectedOptions).map(option => option.value);
        applyFilters();
    });
}

const urlParams = new URLSearchParams(window.location.search);
const productIDParam = urlParams.get('productID');
const detailsContainer = document.getElementById('product-details');

if (detailsContainer && productIDParam)
{
    const productID = parseInt(productIDParam);
    const product = producten.find(p => p.productID === productID);

    if (!product)
    {
        detailsContainer.innerHTML = `<h3>Product niet gevonden!</h3>`;
    } 
    else 
    {
        detailsContainer.innerHTML = `
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <img src="../Assets/Images/${product.productNaam.toLowerCase()}.png" alt="${product.productNaam}" class="img-fluid">
                </div>
                <div class="col-md-6">
                    <h2>${product.productNaam}</h2>
                    <p><strong>Categorie:</strong> ${product.categorie}</p>
                    <p><strong>Beschrijving:</strong> ${product.beschrijving}</p>
                    <p><strong>Kleur:</strong> ${product.Kleur}</p>
                    <p><strong>Prijs:</strong> €${product.prijs.toFixed(2)}</p>
                    <p><strong>Beschikbaarheid:</strong> ${product.stock} op voorraad</p>
                    <button class="btn btn-dark">Voeg toe aan winkelwagen</button>
                </div>
            </div>
        </div>
        `;
    }
}

function renderProducts(page, data = producten) 
{
    if (!container) return;

    container.innerHTML = '';
    const start = (page - 1) * productsPerPage;
    const end = start + productsPerPage;
    const productsToRender = data.slice(start, end);

    productsToRender.forEach(product => 
    {
        const card = document.createElement('div');
        card.className = 'col-md-3 mb-4';

        const cardDiv = document.createElement('div');
        cardDiv.className = 'card h-100';

        const img = document.createElement('img');
        img.className = 'card-img-top';
        img.alt = product.productNaam;
        img.src = `../Assets/Images/${product.productNaam.toLowerCase()}.png`;

        const cardBody = document.createElement('div');
        cardBody.className = 'card-body';

        const cardTitle = document.createElement('h5');
        cardTitle.className = 'card-title';
        cardTitle.textContent = product.productNaam;

        const cardText = document.createElement('p');
        cardText.className = 'card-text';
        cardText.innerHTML = `
            Categorie: ${product.categorie}<br>
            Kleur: ${product.Kleur}<br>
            Prijs: €${product.prijs.toFixed(2)}
        `;

        const buttonContainer = document.createElement('div');
        buttonContainer.className = 'd-flex justify-content-between';

        const addButton = document.createElement('button');
        addButton.className = 'btn btn-dark add-to-cart';
        addButton.textContent = '+';
        addButton.setAttribute('data-product-id', product.productID);

        const viewButton = document.createElement('button');
        viewButton.className = 'btn btn-dark';

        const viewLink = document.createElement('a');
        viewLink.href = `ProductDetail.php?productID=${product.productID}`;
        viewLink.style.color = 'white';
        const icon = document.createElement('i');
        icon.className = 'fa fa-eye';
        viewLink.appendChild(icon);
        viewLink.appendChild(document.createTextNode(' View Product'));

        viewButton.appendChild(viewLink);

        buttonContainer.appendChild(addButton);
        buttonContainer.appendChild(viewButton);

        cardBody.appendChild(cardTitle);
        cardBody.appendChild(cardText);
        cardBody.appendChild(buttonContainer);

        cardDiv.appendChild(img);
        cardDiv.appendChild(cardBody);

        card.appendChild(cardDiv);
        container.appendChild(card);
    });

    const addButtons = document.querySelectorAll('.btn-dark.add-to-cart');
    addButtons.forEach(button => 
    {
        button.addEventListener('click', () => 
        {
            const productID = parseInt(button.getAttribute('data-product-id'));
            addToCart(productID);
        });
    });
}

function renderPagination(data = producten)
{
    if (!paginationContainer) return;

    paginationContainer.innerHTML = '';
    const totalPages = Math.ceil(data.length / productsPerPage);

    for (let i = 1; i <= totalPages; i++)
    {
        const pageItem = document.createElement('li');
        pageItem.className = `page-item ${i === currentPage ? 'active' : ''}`;

        const pageLink = document.createElement('a');
        pageLink.className = 'page-link';
        pageLink.href = '#';
        pageLink.textContent = i;

        pageLink.addEventListener('click', (e) => 
        {
            e.preventDefault();
            currentPage = i;
            renderProducts(currentPage, data);
            renderPagination(data);
        });

        pageItem.appendChild(pageLink);
        paginationContainer.appendChild(pageItem);
    }
}

function applyFilters()
{
    const filteredProducts = producten.filter(product => 
    {
        const matchesName = product.productNaam.toLowerCase().includes(filters.name);
        const matchesPrice = product.prijs <= filters.maxPrice;
        const matchesStock = product.stock >= filters.stock;
        const matchesColor = filters.colors.length === 0 || filters.colors.includes(product.Kleur);
        const matchesCategory = filters.categories.length === 0 || filters.categories.includes(product.categorie);

        return matchesName && matchesPrice && matchesStock && matchesColor && matchesCategory;
    });

    currentPage = 1;
    renderProducts(currentPage, filteredProducts);
    renderPagination(filteredProducts);
}

function addToCart(productID)
{
    const cart = JSON.parse(localStorage.getItem('cart')) || [];
    const product = producten.find(p => p.productID === productID);

    if (product) 
    {
        const existingProductIndex = cart.findIndex(item => item.productID === productID);

        if (existingProductIndex > -1) 
        {
            cart[existingProductIndex].quantity += 1;
        } 
        else 
        {
            const productWithQuantity = { ...product, quantity: 1 };
            cart.push(productWithQuantity);
        }

        localStorage.setItem('cart', JSON.stringify(cart));
        alert(`${product.productNaam} has been added to the cart!`);
    }
}
