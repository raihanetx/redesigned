<div v-show="currentView === 'orderHistory'" class="bg-white p-4 md:p-8 rounded-lg shadow-md max-w-4xl mx-auto my-4 md:my-8" style="display: none;">
    <h2 class="text-2xl font-bold text-gray-800 mb-6 border-b pb-4">My Order History</h2>
    <div class="space-y-4">
        <div class="flex items-center space-x-4">
            <input type="text" v-model="orderIdInput" placeholder="Enter Order ID" class="form-input flex-grow">
            <button @click="fetchOrderById" class="btn btn-primary"><i class="fas fa-search"></i> Find Order</button>
        </div>
        <div v-if="orderHistory.length === 0 && !isLoading" class="text-center text-gray-500 py-10">
            <i class="fas fa-receipt text-4xl mb-3"></i>
            <p>You have no past orders, or you haven't searched for one yet.</p>
        </div>
        <div v-if="isLoading" class="text-center text-gray-500 py-10">
            <i class="fas fa-spinner fa-spin text-4xl"></i>
        </div>
        <div class="space-y-6">
            <div v-for="order in orderHistory" :key="order.order_id" class="border rounded-lg shadow-sm">
                <div class="p-4 bg-gray-50 border-b flex justify-between items-center flex-wrap gap-4">
                    <div>
                        <h3 class="font-bold text-gray-800">Order #<span v-text="order.order_id"></span></h3>
                        <p class="text-sm text-gray-500" v-text="new Date(order.order_date).toLocaleString()"></p>
                    </div>
                    <div>
                        <span class="font-bold py-1 px-3 rounded-full text-sm" v-text="order.status" :class="{ 'bg-green-100 text-green-800': order.status === 'Confirmed', 'bg-red-100 text-red-800': order.status === 'Cancelled', 'bg-yellow-100 text-yellow-800': order.status === 'Pending' }"></span>
                    </div>
                </div>
                <div class="p-4 text-sm">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <h4 class="font-semibold mb-2 text-gray-500 uppercase text-xs tracking-wider">Items Ordered</h4>
                            <div class="space-y-1">
                                <div v-for="item in order.items" :key="item.id + item.pricing.duration">
                                    <span v-text="item.quantity"></span>x <span v-text="item.name"></span> (<span v-text="item.pricing.duration"></span>)
                                </div>
                            </div>
                        </div>
                        <div>
                            <h4 class="font-semibold mb-2 text-gray-500 uppercase text-xs tracking-wider">Summary</h4>
                            <p><strong>Subtotal:</strong> <span v-text="'৳' + order.totals.subtotal.toFixed(2)"></span></p>
                            <p v-if="order.totals.discount > 0" class="text-green-600"><strong>Discount:</strong> <span v-text="'-৳' + order.totals.discount.toFixed(2)"></span></p>
                            <p class="font-bold text-base mt-1"><strong>Total:</strong> <span v-text="'৳' + order.totals.total.toFixed(2)"></span></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
