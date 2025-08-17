import productData from './product-data.js';

function setupProductPopups() {
  const sizePopup = document.querySelector('.size-popup');
  const addToCartBtn = document.querySelector('.add-to-cart-popup');
  const closePopup = document.querySelector('.close-popup');

  let selectedProductId = '';
  let selectedCategory = '';

  // Event delegation — works for dynamically loaded products
  document.addEventListener('click', (e) => {
    if (e.target.classList.contains('select-btn')) {
      const productCard = e.target.closest('.product-card') || e.target.closest('.product');
      if (!productCard) return;

      selectedCategory = productCard.dataset.category;
      selectedProductId = productCard.dataset.productId;

      // Reset size selection
      document.querySelectorAll('.size-radio').forEach(input => input.checked = false);

      // Show popup
      if (sizePopup) sizePopup.style.display = 'flex';
    }
  });

  // Close popup
  closePopup?.addEventListener('click', () => sizePopup.style.display = 'none');
  sizePopup?.addEventListener('click', (e) => {
    if (e.target === sizePopup) sizePopup.style.display = 'none';
  });

  // Redirect to product page with selected size
  addToCartBtn?.addEventListener('click', () => {
    const selectedSize = document.querySelector('.size-radio:checked');
    if (!selectedSize) {
      alert('Please select a size.');
      return;
    }

    if (selectedProductId) {
      window.location.href = `product.php?id=${selectedProductId}&size=${selectedSize.value}`;
    }
  });
}

document.addEventListener('DOMContentLoaded', setupProductPopups);
export default setupProductPopups;
