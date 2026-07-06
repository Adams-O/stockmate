document.addEventListener('DOMContentLoaded', () => {
  const tbody = document.getElementById('sale-items');
  const template = document.getElementById('row-template');
  const addRowButton = document.getElementById('add-row');
  const totalEl = document.getElementById('sale-total');
  const form = document.getElementById('sale-form');

  const money = (value) => `GHS ${value.toFixed(2)}`;

  function updateRow(row) {
    const select = row.querySelector('.product-select');
    const quantityInput = row.querySelector('.quantity-input');
    const selected = select.options[select.selectedIndex];
    const price = Number(selected?.dataset.price || 0);
    const stock = Number(selected?.dataset.stock || 0);
    let quantity = Number(quantityInput.value || 0);

    if (stock > 0) {
      quantityInput.max = String(stock);
    } else {
      quantityInput.removeAttribute('max');
    }
    if (quantity > stock && stock > 0) {
      quantity = stock;
      quantityInput.value = String(stock);
    }

    row.querySelector('.unit-price').textContent = money(price);
    row.querySelector('.line-total').textContent = money(price * quantity);
    updateTotal();
  }

  function updateTotal() {
    let total = 0;
    tbody.querySelectorAll('tr').forEach((row) => {
      const select = row.querySelector('.product-select');
      const selected = select.options[select.selectedIndex];
      const price = Number(selected?.dataset.price || 0);
      const quantity = Number(row.querySelector('.quantity-input').value || 0);
      total += price * quantity;
    });
    totalEl.textContent = money(total);
  }

  function addRow() {
    const row = template.content.firstElementChild.cloneNode(true);
    tbody.appendChild(row);
    row.querySelector('.product-select').addEventListener('change', () => updateRow(row));
    row.querySelector('.quantity-input').addEventListener('input', () => updateRow(row));
    row.querySelector('.remove-row').addEventListener('click', () => {
      row.remove();
      updateTotal();
    });
    updateRow(row);
  }

  addRowButton.addEventListener('click', addRow);
  form.addEventListener('submit', (event) => {
    if (!tbody.querySelector('tr')) {
      event.preventDefault();
      alert('Add at least one item.');
      return;
    }

    for (const row of tbody.querySelectorAll('tr')) {
      const select = row.querySelector('.product-select');
      const selected = select.options[select.selectedIndex];
      const stock = Number(selected?.dataset.stock || 0);
      const quantity = Number(row.querySelector('.quantity-input').value || 0);

      if (!select.value || quantity < 1 || quantity > stock) {
        event.preventDefault();
        alert('Check product selection and quantity before completing the sale.');
        return;
      }
    }
  });

  addRow();
});

