<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <h2 class="font-semibold text-xl text-slate-900 leading-tight">
                {{ __('POS / Barcode Orders') }}
            </h2>
            <a href="{{ route('orders.list') }}" class="text-sm text-slate-600 hover:text-slate-900">View orders history</a>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto grid gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2 space-y-4">
            <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm space-y-4">
                <div>
                    <h3 class="text-base font-semibold text-slate-900">Scan products</h3>
                    <p class="text-sm text-slate-500">Type a barcode and press Enter to simulate a scan.</p>
                </div>

                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label for="warehouse_id" class="block text-sm font-medium text-slate-700">Warehouse</label>
                        <select id="warehouse_id" class="mt-1 w-full rounded-md border-slate-300">
                            @foreach ($warehouses as $warehouse)
                                <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="barcode_input" class="block text-sm font-medium text-slate-700">Barcode / SKU</label>
                        <input id="barcode_input" type="text" placeholder="Scan barcode" class="mt-1 w-full rounded-md border-slate-300" />
                    </div>
                </div>

                <div>
                    <label for="name_search" class="block text-sm font-medium text-slate-700">Search by name</label>
                    <input id="name_search" type="text" placeholder="Start typing a product name" class="mt-1 w-full rounded-md border-slate-300" />
                    <div id="name_results" class="mt-2 hidden rounded-md border border-slate-200 bg-white shadow-sm"></div>
                </div>

                <div id="scan_status" class="hidden rounded-md border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700"></div>

                <div id="product_info" class="hidden rounded-lg border border-slate-200 bg-slate-50 p-4">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-sm text-slate-500">Product</p>
                            <h4 id="product_name" class="text-lg font-semibold text-slate-900"></h4>
                            <p id="product_meta" class="text-sm text-slate-600"></p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-slate-500">Price</p>
                            <p id="product_price" class="text-lg font-semibold text-slate-900"></p>
                            <p id="product_stock" class="text-sm text-slate-600"></p>
                        </div>
                    </div>
                    <p id="product_description" class="mt-3 text-sm text-slate-600"></p>
                </div>
            </div>

            <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                <h3 class="text-base font-semibold text-slate-900 mb-4">Scanned items</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-slate-50 text-slate-600">
                            <tr>
                                <th class="px-3 py-2 text-left">Product</th>
                                <th class="px-3 py-2 text-left">Qty</th>
                                <th class="px-3 py-2 text-left">Unit price</th>
                                <th class="px-3 py-2 text-left">Total</th>
                                <th class="px-3 py-2 text-left">Action</th>
                            </tr>
                        </thead>
                        <tbody id="cart_body" class="divide-y divide-slate-100">
                            <tr>
                                <td colspan="5" class="px-3 py-6 text-center text-slate-500">Scan a barcode to add items.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="space-y-4">
            <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm space-y-4">
                <h3 class="text-base font-semibold text-slate-900">Order details</h3>

                <div>
                    <label for="channel" class="block text-sm font-medium text-slate-700">Order type</label>
                    <select id="channel" class="mt-1 w-full rounded-md border-slate-300">
                        <option value="online">Online order</option>
                        <option value="physical">Physical (in-store)</option>
                    </select>
                </div>

                <div>
                    <label for="client_id" class="block text-sm font-medium text-slate-700">Client (optional)</label>
                    <select id="client_id" class="mt-1 w-full rounded-md border-slate-300">
                        <option value="">None</option>
                        @foreach ($clients as $client)
                            <option value="{{ $client->id }}">{{ $client->company_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="notes" class="block text-sm font-medium text-slate-700">Notes</label>
                    <textarea id="notes" rows="3" class="mt-1 w-full rounded-md border-slate-300"></textarea>
                </div>
            </div>

            <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm space-y-4">
                <h3 class="text-base font-semibold text-slate-900">Payment</h3>

                <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                    <input id="issue_invoice" type="checkbox" value="1" />
                    Issue invoice for this order
                </label>

                <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                    <input id="paid" type="checkbox" value="1" />
                    Order paid and confirmed
                </label>

                <div>
                    <label for="payment_method" class="block text-sm font-medium text-slate-700">Payment method</label>
                    <select id="payment_method" class="mt-1 w-full rounded-md border-slate-300">
                        <option value="">Select method</option>
                        <option value="cash">Cash</option>
                        <option value="card">Card</option>
                        <option value="transfer">Bank transfer</option>
                        <option value="mobile_money">Mobile money</option>
                        <option value="other">Other</option>
                    </select>
                </div>

                <div>
                    <label for="account_id" class="block text-sm font-medium text-slate-700">Account</label>
                    <select id="account_id" class="mt-1 w-full rounded-md border-slate-300">
                        <option value="">Select account</option>
                        @foreach ($accounts as $account)
                            <option value="{{ $account->id }}">{{ $account->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm space-y-4">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-slate-600">Items total</span>
                    <span id="order_total" class="text-lg font-semibold text-slate-900">$0.00</span>
                </div>

                <button id="submit_order" type="button" class="w-full inline-flex items-center justify-center rounded-md bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-700">
                    Confirm order
                </button>

                <div id="order_status" class="hidden rounded-md border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700"></div>
            </div>
        </div>
    </div>

    <script>
        (() => {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const barcodeInput = document.getElementById('barcode_input');
            const warehouseSelect = document.getElementById('warehouse_id');
            const scanStatus = document.getElementById('scan_status');
            const productInfo = document.getElementById('product_info');
            const productName = document.getElementById('product_name');
            const productMeta = document.getElementById('product_meta');
            const productPrice = document.getElementById('product_price');
            const productStock = document.getElementById('product_stock');
            const productDescription = document.getElementById('product_description');
            const cartBody = document.getElementById('cart_body');
            const orderTotal = document.getElementById('order_total');
            const submitOrder = document.getElementById('submit_order');
            const orderStatus = document.getElementById('order_status');
            const paidCheckbox = document.getElementById('paid');
            const accountSelect = document.getElementById('account_id');
            const paymentMethodInput = document.getElementById('payment_method');
            const nameSearch = document.getElementById('name_search');
            const nameResults = document.getElementById('name_results');

            const state = {
                items: [],
            };

            const setStatus = (element, message, type = 'info') => {
                element.textContent = message;
                element.classList.remove('hidden');
                element.classList.remove('border-emerald-200', 'bg-emerald-50', 'text-emerald-700', 'border-red-200', 'bg-red-50', 'text-red-700');
                if (type === 'success') {
                    element.classList.add('border-emerald-200', 'bg-emerald-50', 'text-emerald-700');
                } else if (type === 'error') {
                    element.classList.add('border-red-200', 'bg-red-50', 'text-red-700');
                } else {
                    element.classList.add('border-slate-200', 'bg-slate-50', 'text-slate-700');
                }
            };

            const updateTotals = () => {
                const total = state.items.reduce((sum, item) => sum + (item.quantity * item.unit_price), 0);
                orderTotal.textContent = `$${total.toFixed(2)}`;
            };

            const renderCart = () => {
                if (state.items.length === 0) {
                    cartBody.innerHTML = '<tr><td colspan="5" class="px-3 py-6 text-center text-slate-500">Scan a barcode to add items.</td></tr>';
                    updateTotals();
                    return;
                }

                cartBody.innerHTML = state.items.map((item, index) => `
                    <tr>
                        <td class="px-3 py-2">
                            <div class="font-medium text-slate-900">${item.name}</div>
                            <div class="text-xs text-slate-500">${item.sku || '-'} ${item.barcode ? '| ' + item.barcode : ''}</div>
                        </td>
                        <td class="px-3 py-2">
                            <input data-index="${index}" type="number" min="0.0001" step="0.0001" value="${item.quantity}" class="w-20 rounded-md border-slate-300 text-sm" />
                        </td>
                        <td class="px-3 py-2">$${item.unit_price.toFixed(2)}</td>
                        <td class="px-3 py-2">$${(item.quantity * item.unit_price).toFixed(2)}</td>
                        <td class="px-3 py-2">
                            <button data-remove="${index}" class="text-sm text-red-600 hover:text-red-700">Remove</button>
                        </td>
                    </tr>
                `).join('');

                cartBody.querySelectorAll('input[data-index]').forEach((input) => {
                    input.addEventListener('change', (event) => {
                        const idx = Number(event.target.dataset.index);
                        const qty = Number(event.target.value);
                        if (Number.isFinite(qty) && qty > 0) {
                            state.items[idx].quantity = qty;
                            renderCart();
                        }
                    });
                });

                cartBody.querySelectorAll('button[data-remove]').forEach((button) => {
                    button.addEventListener('click', (event) => {
                        const idx = Number(event.target.dataset.remove);
                        state.items.splice(idx, 1);
                        renderCart();
                    });
                });

                updateTotals();
            };

            const showProductInfo = (product) => {
                productInfo.classList.remove('hidden');
                productName.textContent = product.name;
                productMeta.textContent = `SKU: ${product.sku || '-'} | Barcode: ${product.barcode || '-'}`;
                productPrice.textContent = `$${product.price.toFixed(2)}`;
                productStock.textContent = `Available: ${product.available.toFixed(2)}`;
                productDescription.textContent = product.description || '';
            };

            const addProductToCart = (product) => {
                showProductInfo(product);

                const existing = state.items.find((item) => item.product_id === product.id);
                if (existing) {
                    existing.quantity += 1;
                } else {
                    state.items.push({
                        product_id: product.id,
                        name: product.name,
                        sku: product.sku,
                        barcode: product.barcode,
                        unit_price: product.price,
                        quantity: 1,
                    });
                }

                renderCart();
            };

            const lookupBarcode = async () => {
                const barcode = barcodeInput.value.trim();
                if (!barcode) {
                    return;
                }

                setStatus(scanStatus, 'Searching product...');

                try {
                    const response = await fetch(`{{ route('pos.lookup') }}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({
                            barcode,
                            warehouse_id: warehouseSelect.value,
                        }),
                    });

                    if (!response.ok) {
                        const payload = await response.json();
                        const message = payload.message || (payload.errors ? Object.values(payload.errors)[0][0] : 'Product not found.');
                        throw new Error(message);
                    }

                    const product = await response.json();
                    addProductToCart(product);
                    setStatus(scanStatus, `${product.name} added to cart.`, 'success');
                    barcodeInput.value = '';
                    barcodeInput.focus();
                } catch (error) {
                    setStatus(scanStatus, error.message, 'error');
                }
            };

            barcodeInput.addEventListener('keydown', (event) => {
                if (event.key === 'Enter') {
                    event.preventDefault();
                    lookupBarcode();
                }
            });

            let searchTimeout = null;
            nameSearch.addEventListener('input', () => {
                clearTimeout(searchTimeout);
                const query = nameSearch.value.trim();

                if (query.length < 2) {
                    nameResults.classList.add('hidden');
                    nameResults.innerHTML = '';
                    return;
                }

                searchTimeout = setTimeout(async () => {
                    try {
                        const response = await fetch(`{{ route('pos.search') }}?q=${encodeURIComponent(query)}&warehouse_id=${warehouseSelect.value}`, {
                            headers: { 'Accept': 'application/json' },
                        });
                        const payload = await response.json();
                        const results = payload.results || [];

                        if (!results.length) {
                            nameResults.innerHTML = '<div class="px-3 py-2 text-sm text-slate-500">No products found.</div>';
                            nameResults.classList.remove('hidden');
                            return;
                        }

                        nameResults.innerHTML = results.map((product, idx) => `
                            <button type="button" data-index="${idx}" class="w-full text-left px-3 py-2 hover:bg-slate-50 text-sm">
                                <div class="font-medium text-slate-900">${product.name}</div>
                                <div class="text-xs text-slate-500">SKU: ${product.sku || '-'} | Available: ${product.available.toFixed(2)}</div>
                            </button>
                        `).join('');

                        nameResults.classList.remove('hidden');

                        Array.from(nameResults.querySelectorAll('button[data-index]')).forEach((button) => {
                            button.addEventListener('click', () => {
                                const product = results[Number(button.dataset.index)];
                                addProductToCart(product);
                                setStatus(scanStatus, `${product.name} added to cart.`, 'success');
                                nameResults.classList.add('hidden');
                                nameResults.innerHTML = '';
                                nameSearch.value = '';
                                nameSearch.focus();
                            });
                        });
                    } catch (error) {
                        nameResults.classList.add('hidden');
                        nameResults.innerHTML = '';
                    }
                }, 250);
            });

            document.addEventListener('click', (event) => {
                if (!nameResults.contains(event.target) && event.target !== nameSearch) {
                    nameResults.classList.add('hidden');
                }
            });

            submitOrder.addEventListener('click', async () => {
                if (state.items.length === 0) {
                    setStatus(orderStatus, 'Add at least one product before confirming.', 'error');
                    return;
                }

                const payload = {
                    client_id: document.getElementById('client_id').value || null,
                    warehouse_id: warehouseSelect.value,
                    channel: document.getElementById('channel').value,
                    issue_invoice: document.getElementById('issue_invoice').checked,
                    paid: paidCheckbox.checked,
                    payment_method: paymentMethodInput.value || null,
                    account_id: accountSelect.value || null,
                    notes: document.getElementById('notes').value || null,
                    items: state.items.map((item) => ({
                        product_id: item.product_id,
                        quantity: item.quantity,
                    })),
                };

                setStatus(orderStatus, 'Saving order...');

                try {
                    const response = await fetch(`{{ route('pos.checkout') }}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify(payload),
                    });

                    if (!response.ok) {
                        const payload = await response.json();
                        const message = payload.message || (payload.errors ? Object.values(payload.errors)[0][0] : 'Unable to save order.');
                        throw new Error(message);
                    }

                    const result = await response.json();
                    setStatus(orderStatus, result.message || 'Order saved.', 'success');
                    state.items = [];
                    renderCart();
                    productInfo.classList.add('hidden');
                    paymentMethodInput.value = '';
                    paidCheckbox.checked = false;
                    if (result.redirect) {
                        window.location.href = result.redirect;
                    }
                } catch (error) {
                    setStatus(orderStatus, error.message, 'error');
                }
            });

            paidCheckbox.addEventListener('change', () => {
                const disabled = !paidCheckbox.checked;
                accountSelect.disabled = disabled;
                paymentMethodInput.disabled = disabled;
            });

            paidCheckbox.dispatchEvent(new Event('change'));
        })();
    </script>
</x-app-layout>
