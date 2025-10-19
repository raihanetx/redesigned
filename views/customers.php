<!-- Customer Management View -->
<div id="view-customers" style="<?= $current_view === 'customers' ? '' : 'display:none;' ?>" class="p-6" x-data="customersManager()">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-bold text-gray-700">Manage Customers</h2>
    </div>

    <div class="mb-4">
        <input type="text" x-model.debounce.300ms="searchQuery" class="form-input" placeholder="Search by name or phone...">
    </div>

    <div class="bg-white border rounded-lg overflow-hidden">
        <div class="space-y-4 p-4">
            <template x-for="customer in filteredCustomers" :key="customer.phone">
                <div class="border rounded-lg p-4">
                    <div class="flex justify-between items-center">
                        <div>
                            <h3 class="font-bold text-gray-800" x-text="customer.name"></h3>
                            <p class="text-sm text-gray-500" x-text="customer.phone"></p>
                        </div>
                        <a :href="'https://wa.me/' + customer.phone" target="_blank" class="btn btn-success btn-sm"><i class="fab fa-whatsapp"></i> Message</a>
                    </div>
                    <div class="mt-4">
                        <h4 class="font-semibold mb-2 text-gray-500 uppercase text-xs tracking-wider">Purchased Products</h4>
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product Name</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Purchase Date</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Renewal Date</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <template x-for="product in customer.products" :key="product.name + product.purchase_date">
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap" x-text="product.name"></td>
                                        <td class="px-6 py-4 whitespace-nowrap" x-text="product.purchase_date"></td>
                                        <td class="px-6 py-4 whitespace-nowrap" x-text="product.renewal_date"></td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </template>
        </div>
    </div>
</div>

<script>
    function customersManager() {
        return {
            allCustomers: [],
            searchQuery: '',
            init() {
                this.loadCustomers();
            },
            loadCustomers() {
                fetch('api.php?action=get_customers')
                    .then(response => response.json())
                    .then(data => {
                        this.allCustomers = data;
                    });
            },
            get filteredCustomers() {
                if (this.searchQuery.trim() === '') {
                    return this.allCustomers;
                }
                const query = this.searchQuery.toLowerCase().trim();
                return this.allCustomers.filter(customer => {
                    const searchableText = `${customer.name} ${customer.phone}`.toLowerCase();
                    return searchableText.includes(query);
                });
            }
        }
    }
</script>
