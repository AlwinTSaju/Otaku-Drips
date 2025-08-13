import productData from './product-data.js';
function setupProductPopups() {
  const selectBtns = document.querySelectorAll('.select-btn');
  const sizePopup = document.querySelector('.size-popup');
  const addToCartBtn = document.querySelector('.add-to-cart-popup');
  const closePopup = document.querySelector('.close-popup');

  let selectedProductId = '';
  let selectedCategory = '';

  selectBtns.forEach(btn => {
    btn.addEventListener('click', function () {
      const productCard = this.closest('.product') || this.closest('.product-card');
      selectedCategory = productCard.dataset.category;
      selectedProductId = productCard.dataset.productId;

      // Reset size selection
      document.querySelectorAll('.size-radio').forEach(input => input.checked = false);

      // Show popup
      sizePopup.style.display = 'flex';
    });
  });

  // Close popup when clicking on × or outside
  closePopup.addEventListener('click', () => sizePopup.style.display = 'none');
  sizePopup.addEventListener('click', (e) => {
    if (e.target === sizePopup) sizePopup.style.display = 'none';
  });

  // Redirect to product page with selected size
  addToCartBtn.addEventListener('click', () => {
    const selectedSize = document.querySelector('.size-radio:checked');
    if (!selectedSize) {
      alert('Please select a size.');
      return;
    }

    if (selectedProductId) {
      window.location.href = `product.html?id=${selectedProductId}&size=${selectedSize.value}`;
    }
  });
}

document.addEventListener('DOMContentLoaded', setupProductPopups);
export default setupProductPopups;