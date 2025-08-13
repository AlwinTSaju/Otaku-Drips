import productData from './product-data.js';
import setupProductPopups from './product-popup.js';

document.addEventListener('DOMContentLoaded', () => {
  const params = new URLSearchParams(window.location.search);
  const query = params.get('query')?.toLowerCase().trim();
  const container = document.getElementById('search-results');

  if (!query) {
    container.innerHTML = '<p>No search term provided.</p>';
    return;
  }

  console.log("Search query:", query);

  const results = Object.entries(productData).filter(([id, product]) => {
    const searchText = `${product.title} ${product.category} ${product.description}`.toLowerCase();
    return searchText.includes(query);
  });

  console.log("Matched results:", results);

  if (results.length === 0) {
    container.innerHTML = '<p>No products found for your search.</p>';
    return;
  }

  container.innerHTML = '';

  for (const [id, product] of results) {
    const card = document.createElement('div');
    card.className = 'product-card';
    card.dataset.productId = id;
    card.dataset.category = product.category;

    card.innerHTML = `
      <img src="${product.images[0]}" alt="${product.title}">
      <div class="product-info">
        <span class="series-tag">${product.category}</span>
        <h3>${product.title}</h3>
        <div class="price">
          ${product.originalPrice && product.originalPrice > product.price
            ? `<span class="original-price">₹${product.originalPrice}</span>`
            : ''}
          <span class="current-price">₹${product.price}</span>
        </div>
        <div class="product-actions"><button class="select-btn">Select</button>
        </div>
      </div>
    `;

    container.appendChild(card);
    console.log("Rendered product card:", card);
  }

  const selectButtons = document.querySelectorAll('.select-btn');

  // Add click event to each button
  selectButtons.forEach(button => {
    button.addEventListener('click', () => {
      const productCard = button.closest('.product-card');
      const productId = productCard?.dataset.productId;
      if (productId) {
        window.location.href = `product.html?id=${productId}`;
      } else {
        console.error('Product ID missing on product card.');
      }
    });
  });

  setupProductPopups?.();
});

