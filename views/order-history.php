<div v-show="currentView === 'orderHistory'" class="" style="display: none;">
    <h2 class="text-2xl font-bold text-gray-800 mb-6 border-b pb-4">My Order History</h2>
    <div class="space-y-4">
        <div v-if="orderHistory.length === 0 && !isLoading" class="text-center text-gray-500 py-10">
            <i class="fas fa-receipt text-4xl mb-3"></i>
            <p>You have no past orders, or you haven't searched for one yet.</p>
        </div>
        <div v-if="isLoading" class="text-center text-gray-500 py-10">
            <i class="fas fa-spinner fa-spin text-4xl"></i>
        </div>
        <div class="space-y-8">
            <div v-for="order in orderHistory" :key="order.order_id" class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="p-6">
                    <div class="grid grid-cols-2 gap-8">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800">Order #<span v-text="order.order_id"></span></h3>
                            <p class="text-sm text-gray-500 mt-1" v-text="new Date(order.order_date).toLocaleString()"></p>
                            <span class="mt-3 inline-flex items-center px-3 py-1 rounded-full text-sm font-medium" :class="{ 'bg-green-100 text-green-800': order.status === 'Confirmed', 'bg-red-100 text-red-800': order.status === 'Cancelled', 'bg-yellow-100 text-yellow-800': order.status === 'Pending' }" v-text="order.status"></span>
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-gray-500">Subtotal</p>
                            <p class="text-md font-medium text-gray-700" v-text="'৳' + order.totals.subtotal.toFixed(2)"></p>
                            <p v-if="order.totals.discount > 0" class="text-sm text-green-600">Discount: <span v-text="'-৳' + order.totals.discount.toFixed(2)"></span></p>
                            <p class="text-xl font-bold text-gray-900 mt-2" v-text="'৳' + order.totals.total.toFixed(2)"></p>
                        </div>
                    </div>
                </div>
                <div class="border-t border-gray-200 px-6 py-4">
                    <h4 class="text-sm font-semibold text-gray-600 mb-3">Items Ordered</h4>
                    <div class="space-y-2">
                        <div v-for="item in order.items" :key="item.id + item.pricing.duration" class="flex justify-between text-sm">
                            <span class="text-gray-700"><span v-text="item.quantity"></span>x <span v-text="item.name"></span> (<span v-text="item.pricing.duration"></span>)</span>
                            <span class="font-medium text-gray-800" v-text="'৳' + (item.pricing.price * item.quantity).toFixed(2)"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
