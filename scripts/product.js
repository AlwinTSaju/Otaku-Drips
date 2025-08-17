import productData from './product-data.js';
document.addEventListener('DOMContentLoaded', () => {

  const urlParams = new URLSearchParams(window.location.search);
  const productId = urlParams.get('id');
  const sizeParam = urlParams.get('size');

  const product = productData[productId];
  if (!product) {
    document.querySelector('.product-main').innerHTML = '<h2>Product not found.</h2>';
    return;
  }

  document.getElementById('product-title').textContent = product.title;
  document.getElementById('product-category').textContent = product.category;
  document.getElementById('product-price').textContent = `₹${product.price}`;
  document.getElementById('product-desc').textContent = product.description;

  const originalPriceElement = document.getElementById("original-price");
  if (product.originalPrice && product.originalPrice > product.price) {
    originalPriceElement.textContent = `₹${product.originalPrice.toFixed(2)}`;
    originalPriceElement.style.display = "inline";
  } else {
    originalPriceElement.style.display = "none";
  }

  const mainImage = document.getElementById('main-product-image');
  const thumbnailContainer = document.querySelector('.thumbnail-container');

  mainImage.src = product.images[0];
  thumbnailContainer.innerHTML = '';

  product.images.forEach((src, index) => {
    const thumb = document.createElement('img');
    thumb.src = src;
    thumb.className = 'thumbnail';
    if (index === 0) thumb.classList.add('active');

    thumb.addEventListener('click', () => {
      document.querySelectorAll('.thumbnail').forEach(t => t.classList.remove('active'));
      thumb.classList.add('active');
      mainImage.src = src;
    });

    thumbnailContainer.appendChild(thumb);
  });

  // Discount Calculator
  function calculateDiscount(price, originalPrice) {
    if (!originalPrice || originalPrice <= price) return null;

    const discount = ((originalPrice - price) / originalPrice) * 100;
    return Math.round(discount);
}

    const discount = calculateDiscount(product.price, product.originalPrice);
    const discountBadgeElement = document.getElementById("discount-percent");

    if (discount !== null) {
      discountBadgeElement.textContent = `${discount}% OFF`;
     discountBadgeElement.style.display = "inline-block";
    } else {
    discountBadgeElement.style.display = "none";
  }

  // Manual Size Selection Handler
  document.querySelectorAll('.size-radio').forEach(input => {
    input.addEventListener('change', () => {
      document.querySelectorAll('.size-options label').forEach(label => {
        label.classList.remove('selected');
      });
      const label = document.querySelector(`label[for="${input.id}"]`);
      if (label) label.classList.add('selected');
    });
  });

  // Preselect size from URL
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