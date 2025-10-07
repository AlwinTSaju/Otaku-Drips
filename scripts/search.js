document.addEventListener('DOMContentLoaded', async () => {
  const params = new URLSearchParams(window.location.search);
  const query = params.get('query')?.toLowerCase().trim();
  const container = document.getElementById('search-results');

  if (!query) {
    container.innerHTML = '<p>No search term provided.</p>';
    return;
  }

  console.log("Search query:", query);

  let results = [];
  try {
    // Fetch results from DB
    const res = await fetch(`get-product.php?query=${encodeURIComponent(query)}`);
    results = await res.json();
    console.log("Fetched search results:", results);
  } catch (error) {
    console.error("Error fetching products:", error);
    container.innerHTML = '<p>Error loading products.</p>';
    return;
  }

  if (!results || results.length === 0) {
    container.innerHTML = '<p>No products found for your search.</p>';
    return;
  }

  container.innerHTML = '';

  for (const product of results) {
    const card = document.createElement('div');
    card.className = 'product-card';
    card.dataset.productId = product.product_id; // numeric ID
    card.dataset.category = product.category;

    const discount =
      product.original_price && product.original_price > product.price
        ? Math.round(((product.original_price - product.price) / product.original_price) * 100)
        : null;

    card.innerHTML = `
      <img src="${product.image}" alt="${product.name}">
      <div class="product-info">
        <span class="series-tag">${product.category}</span>
        <h3>${product.name}</h3>
        <div class="price">
          ${
            product.original_price && product.original_price > product.price
              ? `<span class="original-price">₹${product.original_price}</span>`
              : ''
          }
          <span class="current-price">₹${product.price}</span>
          ${discount !== null ? `<span class="discount">(${discount}% off)</span>` : ''}
        </div>
        <div class="product-actions">
          <button class="select-btn">Select</button>
        </div>
      </div>
    `;

    container.appendChild(card);
  }

  // Click handler to go to product.php with NUMERIC ID
  document.querySelectorAll('.select-btn').forEach(button => {
    button.addEventListener('click', () => {
      const productCard = button.closest('.product-card');
      const productId = productCard?.dataset.productId;
      if (productId) {
        window.location.href = `product.php?id=${productId}`; // numeric ID
      } else {
        console.error('Product ID missing on product card.');
      }
    });
  });
});
