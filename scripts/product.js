document.addEventListener('DOMContentLoaded', async () => {
  const urlParams = new URLSearchParams(window.location.search);
  const productId = urlParams.get('id'); // numeric product_id
  const sizeParam = urlParams.get('size'); // optional size preselect

  if (!productId) {
    document.querySelector('.product').innerHTML = '';
    return;
  }

  // Fetch product details from PHP (database)
  let product;
try {
  const res = await fetch(`get-product.php?id=${productId}`);
  product = await res.json();
  console.log("Fetched product:", product); // DEBUG
} catch (error) {
  console.error("Error fetching product:", error);
  document.querySelector('.product').innerHTML = '<h2>Error loading product.</h2>';
  return;
}

if (!product || product.error) {
  console.warn("Product not found or invalid:", product); 
  document.querySelector('.product').innerHTML = ''; 
  return;
}


  // Fill product details
  document.getElementById('product-title').textContent = product.name;
  document.getElementById('product-category').textContent = product.category;
  document.getElementById('product-price').textContent = `₹${product.price}`;
  document.getElementById('product-desc').textContent = product.description;

  // Original price (strikethrough)
  const originalPriceElement = document.getElementById("original-price");
  if (product.original_price && product.original_price > product.price) {
    originalPriceElement.textContent = `₹${product.original_price}`;
    originalPriceElement.style.display = "inline";
  } else {
    originalPriceElement.style.display = "none";
  }

  // Product image (main + thumbnail)
  const mainImage = document.getElementById('main-product-image');
  const thumbnailContainer = document.querySelector('.thumbnail-container');

  mainImage.src = product.image;
  thumbnailContainer.innerHTML = '';

  const thumb = document.createElement('img');
  thumb.src = product.image;
  thumb.className = 'thumbnail active';
  thumb.addEventListener('click', () => {
    document.querySelectorAll('.thumbnail').forEach(t => t.classList.remove('active'));
    thumb.classList.add('active');
    mainImage.src = product.image;
  });
  thumbnailContainer.appendChild(thumb);

  // Discount badge
  function calculateDiscount(price, originalPrice) {
    if (!originalPrice || originalPrice <= price) return null;
    return Math.round(((originalPrice - price) / originalPrice) * 100);
  }

  const discount = calculateDiscount(product.price, product.original_price);
  const discountBadgeElement = document.getElementById("discount-percent");

  if (discount !== null) {
    discountBadgeElement.textContent = `${discount}% OFF`;
    discountBadgeElement.style.display = "inline-block";
  } else {
    discountBadgeElement.style.display = "none";
  }

  // Size preselect from URL (if provided)
  if (sizeParam) {
    const normalizedSize = sizeParam.trim().toLowerCase();
    const sizeInput = document.getElementById(`size-${normalizedSize}`);
    if (sizeInput) {
      sizeInput.checked = true;
      document.querySelectorAll('.size-options label').forEach(label => {
        label.classList.remove('selected');
      });
      const label = document.querySelector(`label[for="${sizeInput.id}"]`);
      if (label) label.classList.add('selected');
    }
  }
});
