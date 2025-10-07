function setupProductPopups() {
  const sizePopup = document.querySelector('.size-popup');
  const addToCartBtn = document.querySelector('.add-to-cart-popup');
  const closePopup = document.querySelector('.close-popup');

  let selectedProductId = '';

  // Event delegation for "Select Options" button
  document.addEventListener('click', async (e) => {
    if (e.target.classList.contains('select-btn')) {
      const productCard = e.target.closest('.product-card') || e.target.closest('.product');
      if (!productCard) return;

      selectedProductId = productCard.dataset.productId;

      // Confirm product exists in DB before allowing popup
      try {
        const res = await fetch(`get-product.php?id=${selectedProductId}`);
        const data = await res.json();

        if (data.error) {
          alert("Product not found.");
          return;
        }

        // Reset size selection
        document.querySelectorAll('.size-radio').forEach(input => input.checked = false);

        // Show popup
        if (sizePopup) sizePopup.style.display = 'flex';
      } catch (err) {
        console.error("Error fetching product:", err);
        alert("Could not load product info.");
      }
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
      // Now redirect with numeric ID
      window.location.href = `product.php?id=${selectedProductId}&size=${selectedSize.value}`;
    }
  });
}

document.addEventListener('DOMContentLoaded', setupProductPopups);
export default setupProductPopups;
