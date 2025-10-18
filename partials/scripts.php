<script>
    const { createApp, nextTick } = Vue;

    createApp({
        data() {
            return {
                basePath: '<?= BASE_PATH ?>',
                allProducts: <?= json_encode($all_products_flat) ?>,
                allCoupons: <?= json_encode($all_coupons_data) ?>,
                paymentMethods: <?= json_encode($payment_methods) ?>,
                usdRate: <?= $usd_to_bdt_rate ?>,
                currentView: '<?= $initial_view ?>',
                previousView: 'home',
                isSideMenuOpen: false,
                fabOpen: false,
                selectedProduct: null,
                cart: [],
                orderHistory: [],
                isSearchingOrders: false,
                newNotificationCount: 0,
                productsTitle: 'All Products',
                productFilter: { filterType: null, filterValue: null },
                searchQuery: '',
                selectedDurationIndex: 0,
                activeTab: 'description',
                isDescriptionExpanded: false,
                checkoutItems: [],
                checkoutForm: { name: '', phone: '', email: ''},
                paymentForm: { trx_id: ''},
                selectedPayment: null,
                couponCode: '',
                appliedCoupon: null,
                couponMessage: '',
                copySuccess: false,
                reviewModalOpen: false,
                newReview: { name: '', rating: 0, comment: '' },
                hoverRating: 0,
                modal: { visible: false, title: '', message: '', type: 'info', onOk: null },
                currency: 'BDT',
                openOrder: null,
                heroSlides: {
                    slides: [],
                    activeSlide: 0,
                    hasImages: false,
                    interval: null,
                    sliderInterval: <?= $hero_slider_interval ?>,
                },
                resizeObserver: null,
                sitePages: <?= json_encode($site_config['site_pages'] ?? ['about_us' => '', 'privacy_policy' => '', 'terms_and_conditions' => '', 'refund_policy' => '']) ?>
            }
        },
        computed: {
            cartCount() { return this.cart.reduce((total, item) => total + item.quantity, 0); },
            cartTotal() { return this.cart.reduce((total, item) => { const product = this.getProductById(item.productId); if (product) { return total + (product.pricing[item.durationIndex].price * item.quantity); } return total; }, 0); },
            isCartCheckoutable() { if (this.cart.length === 0) return false; return this.cart.some(cartItem => { const product = this.getProductById(cartItem.productId); return product && !product.stock_out; }); },
            filteredProducts() { if (this.searchQuery.trim() !== '') { this.productsTitle = `Search Results for "${this.searchQuery.trim()}"`; const query = this.searchQuery.trim().toLowerCase(); return this.allProducts.filter(p => p.name.toLowerCase().includes(query) || p.description.toLowerCase().includes(query)); } if (this.productFilter.filterType === 'category') { this.productsTitle = `All ${this.productFilter.filterValue}`; return this.allProducts.filter(p => p.category === this.productFilter.filterValue); } this.productsTitle = 'All Products'; return this.allProducts; },
            relatedProducts() { if (!this.selectedProduct) return []; const limit = window.innerWidth < 768 ? 2 : 3; return this.allProducts.filter(p => p.category === this.selectedProduct.category && p.id !== this.selectedProduct.id).slice(0, limit); },
            selectedPrice() { if (!this.selectedProduct) return 0; return this.selectedProduct.pricing[this.selectedDurationIndex].price; },
            selectedPriceFormatted() { return this.formatPrice(this.selectedPrice); },
            formattedLongDescription() { if (!this.selectedProduct || !this.selectedProduct.long_description) return ''; return this.selectedProduct.long_description.replace(/\*\*(.*?)\*\*/gs, '<strong>$1</strong>'); },
            checkoutTotals() {
                const subtotal = this.checkoutItems.reduce((total, item) => { const product = this.getProductById(item.productId); return product ? total + (product.pricing[item.durationIndex].price * item.quantity) : total; }, 0);
                let discount = 0;
                if (this.appliedCoupon) {
                    let eligibleSubtotal = 0; const coupon = this.appliedCoupon;
                    if (!coupon.scope || coupon.scope === 'all_products') { eligibleSubtotal = subtotal; } 
                    else if (coupon.scope === 'category') { this.checkoutItems.forEach(item => { const product = this.getProductById(item.productId); if (product && product.category === coupon.scope_value) { eligibleSubtotal += product.pricing[item.durationIndex].price * item.quantity; } }); }
                    else if (coupon.scope === 'single_product') { this.checkoutItems.forEach(item => { if (item.productId == coupon.scope_value) { eligibleSubtotal += this.getProductById(item.productId)?.pricing[item.durationIndex].price * item.quantity; } }); }
                    discount = eligibleSubtotal * (coupon.discount_percentage / 100);
                }
                return { subtotal, discount, total: Math.max(0, subtotal - discount) };
            },
            formattedAboutUs() { return this.formatPageContent(this.sitePages.about_us); },
            formattedPrivacyPolicy() { return this.formatPageContent(this.sitePages.privacy_policy); },
            formattedTerms() { return this.formatPageContent(this.sitePages.terms_and_conditions); },
            formattedRefund() { return this.formatPageContent(this.sitePages.refund_policy); },
            paymentDisplayLabel() {
                if (!this.selectedPayment) return '';
                if (this.selectedPayment.hasOwnProperty('number')) {
                    return `${this.selectedPayment.name} Number`;
                }
                if (this.selectedPayment.hasOwnProperty('pay_id')) {
                    return `${this.selectedPayment.name} ID`;
                }
                if (this.selectedPayment.hasOwnProperty('account_number')) {
                    return `${this.selectedPayment.name} Account Number`;
                }
                return this.selectedPayment.name;
            },
            standardizedInstructions() {
                if (!this.selectedPayment) return [];
                const totalAmount = this.formatPrice(this.checkoutTotals.total);
                let instructions = [];

                let step2Action = `use the <strong>Send Money</strong> option.`;
                if (this.selectedPayment.hasOwnProperty('pay_id')) {
                    step2Action = `use the <strong>Pay</strong> option.`;
                } else if (this.selectedPayment.hasOwnProperty('account_number')) {
                    step2Action = `use the <strong>Bank/Fund Transfer</strong> option.`;
                }

                instructions.push(`Copy the ${this.paymentDisplayLabel}.`);
                instructions.push(`In your <strong>${this.selectedPayment.name}</strong> app, ${step2Action}`);
                instructions.push(`Enter <strong>${totalAmount}</strong> as the amount and complete the payment.`);
                instructions.push(`After the payment is successful, copy the <strong>Transaction ID</strong>.`);
                instructions.push(`Paste the <strong>Transaction ID</strong> into the box below to complete your order.`);

                return instructions;
            }
        },
        watch: {
            searchQuery(newValue) { if (newValue.trim() && this.currentView !== 'products') { this.setView('products'); } },
            selectedProduct(newProduct) { if (newProduct && this.currentView === 'productDetail') { nextTick(() => { this.setupResizeObserver(); }); } else { this.disconnectResizeObserver(); } },
            selectedPayment() { this.copySuccess = false; },
            currentView(newView) {
                if (newView === 'home') {
                    nextTick(() => {
                        this.setCategoryScrollerWidth();
                    });
                }
            }
        },
        methods: {
            formatPageContent(text) {
                if (!text) return '';
                let safeText = text.replace(/</g, '&lt;').replace(/>/g, '&gt;');
                let formattedText = safeText.replace(/\*\*(.*?)\*\*/gs, '<strong>$1</strong>');
                return formattedText;
            },
            formatPrice(bdtPrice) { if (this.currency === 'USD') { if (!bdtPrice || !this.usdRate) return '$0.00'; const usdPrice = bdtPrice / this.usdRate; return '$' + usdPrice.toFixed(2); } return '৳' + Number(bdtPrice).toFixed(2); },
            toggleCurrency() { this.currency = (this.currency === 'BDT') ? 'USD' : 'BDT'; localStorage.setItem('submonthCurrency', this.currency); },
            showModal(title, message, type = 'info', onOkCallback = null) { this.modal = { visible: true, title, message, type, onOk: onOkCallback }; },
            closeModal() { if (typeof this.modal.onOk === 'function') { this.modal.onOk(); } this.modal.visible = false; this.modal.onOk = null; },
            scrollCategories(direction) { const scroller = this.$refs.categoryScroller; if (!scroller) return; const icons = Array.from(scroller.querySelectorAll('.category-icon')); if (icons.length === 0) return; const containerRect = scroller.getBoundingClientRect(); let firstVisibleIndex = icons.findIndex(icon => { const iconRect = icon.getBoundingClientRect(); return iconRect.right > containerRect.left + 1; }); if (firstVisibleIndex === -1) firstVisibleIndex = 0; let targetIndex; if (direction > 0) { targetIndex = Math.min(firstVisibleIndex + 1, icons.length - 1); } else { targetIndex = Math.max(firstVisibleIndex - 1, 0); } icons[targetIndex].scrollIntoView({ behavior: 'smooth', inline: 'start', block: 'nearest' }); },
            setCategoryScrollerWidth() { const wrapper = this.$refs.categoryScrollerWrapper; if (!wrapper) return; if (window.innerWidth < 768) { wrapper.style.maxWidth = ''; return; } const scroller = this.$refs.categoryScroller; const firstIcon = scroller.querySelector('.category-icon'); const container = scroller.querySelector('.category-scroll-container'); if (firstIcon && container) { const gap = parseFloat(window.getComputedStyle(container).gap); const iconWidth = firstIcon.offsetWidth; const totalWidth = (iconWidth * 6) + (gap * 5); wrapper.style.maxWidth = totalWidth + 'px'; } },
            adjustImageSize() { const imageContainer = this.$refs.imageContainer; const infoContainer = this.$refs.infoContainer; if (window.innerWidth < 768 || !imageContainer || !infoContainer) { if(imageContainer) { imageContainer.style.height = ''; imageContainer.style.width = ''; } return; } const infoHeight = infoContainer.offsetHeight; const parentContainer = imageContainer.parentNode; const gap = parseInt(window.getComputedStyle(parentContainer).gap, 10) || 32; const maxImageWidth = (parentContainer.clientWidth - infoContainer.offsetWidth - gap); const finalSize = Math.min(infoHeight, maxImageWidth); if (finalSize > 0) { imageContainer.style.height = `${finalSize}px`; imageContainer.style.width = `${finalSize}px`; } },
            setupResizeObserver() { this.disconnectResizeObserver(); if (this.$refs.infoContainer) { this.resizeObserver = new ResizeObserver(() => { this.adjustImageSize(); }); this.resizeObserver.observe(this.$refs.infoContainer); this.adjustImageSize(); } },
            disconnectResizeObserver() { if (this.resizeObserver) { this.resizeObserver.disconnect(); this.resizeObserver = null; } },
            setView(viewName, params = {}) {
                let newUrlPath = '';
                const viewMap = { 'orderHistory': 'order-history', 'aboutUs': 'about-us', 'privacyPolicy': 'privacy-policy', 'termsAndConditions': 'terms-and-conditions', 'refundPolicy': 'refund-policy' };
                if (viewName === 'home') { newUrlPath = '/'; } 
                else if (viewName === 'productDetail' && params.productId) { const product = this.getProductById(params.productId); if (product) newUrlPath = `/${product.category_slug}/${product.slug}`; } 
                else if (viewName === 'products' && params.filterType === 'category') { const category = this.allProducts.find(p => p.category === params.filterValue); if (category) newUrlPath = `/products/category/${category.category_slug}`; } 
                else if (Object.keys(viewMap).concat(['products', 'cart', 'checkout']).includes(viewName)) { newUrlPath = `/${viewMap[viewName] || viewName}`; }
                const newUrl = (this.basePath + (newUrlPath === '/' ? '' : newUrlPath)) || '/';
                if (window.location.pathname !== newUrl || window.location.search) { history.pushState(params, '', newUrl); }
                this.changeView(viewName, params);
            },
            changeView(viewName, params = {}, isInitialLoad = false) {
                if (!isInitialLoad) { this.previousView = this.currentView; }
                this.currentView = viewName;
                if (viewName === 'productDetail') { this.selectedProduct = this.getProductById(params.productId); if (this.selectedProduct) { this.resetProductDetailState(); } } 
                else { this.selectedProduct = null; }
                if (viewName === 'orderHistory') { this.clearNotifications(); }
                if (viewName === 'products') { if (params.filterType === 'category') { this.searchQuery = ''; this.productFilter = { filterType: 'category', filterValue: params.filterValue }; } else if (!this.searchQuery.trim()) { this.productFilter = { filterType: null, filterValue: null }; } }
                if (viewName === 'checkout') { this.checkoutItems = params.items || this.checkoutItems; }
                if (!isInitialLoad) { window.scrollTo({ top: 0, behavior: 'smooth' }); }
            },
            handleRouting() { const path = window.location.pathname.replace(this.basePath, '').substring(1); if (path.endsWith('.php')) return; const pathParts = path.split('/'); const productSlugMap = <?php echo json_encode($product_slug_map); ?>; const categorySlugMap = <?php echo json_encode($category_slug_map); ?>; const staticPages = <?php echo json_encode($static_pages); ?>; const viewMap = { 'order-history': 'orderHistory', 'about-us': 'aboutUs', 'privacy-policy': 'privacyPolicy', 'terms-and-conditions': 'termsAndConditions', 'refund-policy': 'refundPolicy' }; if (productSlugMap[path]) { this.changeView('productDetail', { productId: productSlugMap[path] }); } else if (pathParts[0] === 'products' && pathParts.length > 2 && pathParts[1] === 'category' && categorySlugMap[pathParts[2]]) { this.changeView('products', { filterType: 'category', filterValue: categorySlugMap[pathParts[2]] }); } else if (staticPages.includes(pathParts[0]) && pathParts.length === 1) { this.changeView(viewMap[pathParts[0]] || pathParts[0]); } else if (path === '') { this.changeView('home'); } },
            getProductById(id) { return this.allProducts.find(p => p.id == id); },
            performSearch() { if (this.searchQuery.trim()) { this.setView('products'); } },
            resetProductDetailState() { this.activeTab = 'description'; this.isDescriptionExpanded = false; this.selectedDurationIndex = 0; },
            resetCheckoutState() { this.appliedCoupon = null; this.couponCode = ''; this.couponMessage = ''; this.selectedPayment = null; this.paymentForm.trx_id = ''; this.copySuccess = false; },
            selectPayment(method, name) { this.selectedPayment = { ...method, name: name }; },
            addToCart(productId, quantity = 1) { const existingItemIndex = this.cart.findIndex(item => item.productId === productId && item.durationIndex === this.selectedDurationIndex); if (existingItemIndex > -1) { this.cart[existingItemIndex].quantity += quantity; } else { this.cart.push({ productId, quantity, durationIndex: this.selectedDurationIndex }); } const product = this.getProductById(productId); if (product) { this.showModal('Added to Cart', `'${product.name}' has been added to your cart.`, 'success'); } this.saveCart(); },
            buyNowAndCheckout(productId) { this.checkoutItems = [{ productId, quantity: 1, durationIndex: this.selectedDurationIndex }]; this.resetCheckoutState(); this.setView('checkout'); },
            proceedToCheckout() { const inStockItems = this.cart.filter(item => !this.getProductById(item.productId)?.stock_out); if (inStockItems.length === 0) { this.showModal("Cart Error", "All items in your cart are out of stock.", "error"); return; } const outOfStockCount = this.cart.length - inStockItems.length; if (outOfStockCount > 0) { this.showModal("Stock Alert", `${outOfStockCount} item(s) are out of stock and were excluded from this order.`, "info"); } this.checkoutItems = inStockItems; this.resetCheckoutState(); this.setView('checkout'); },
            removeFromCart(productId) { this.cart = this.cart.filter(item => item.productId !== productId); this.saveCart(); },
            updateCartQuantity(productId, change) { const item = this.cart.find(item => item.productId === productId); if (item) { item.quantity += change; if (item.quantity <= 0) this.removeFromCart(productId); else this.saveCart(); } },
            saveCart() { localStorage.setItem('submonthCart', JSON.stringify(this.cart)); },
            applyCoupon() {
                this.couponMessage = ''; this.appliedCoupon = null;
                if (!this.couponCode.trim()) { this.couponMessage = 'Please enter a coupon code.'; return; }
                const codeToApply = this.couponCode.toUpperCase(); const foundCoupon = this.allCoupons.find(c => c.code === codeToApply);
                if (foundCoupon && foundCoupon.is_active) {
                    let isApplicable = false;
                    if (!foundCoupon.scope || coupon.scope === 'all_products') { isApplicable = this.checkoutItems.length > 0; }
                    else if (foundCoupon.scope === 'category') { isApplicable = this.checkoutItems.some(item => this.getProductById(item.productId)?.category === foundCoupon.scope_value); }
                    else if (foundCoupon.scope === 'single_product') { isApplicable = this.checkoutItems.some(item => item.productId == foundCoupon.scope_value); }
                    if (isApplicable) { this.appliedCoupon = foundCoupon; this.couponMessage = `Coupon "${foundCoupon.code}" applied successfully!`; this.showModal('Success', this.couponMessage, 'success'); } 
                    else { this.couponMessage = `Coupon is not valid for the items in your cart.`; this.showModal('Invalid Coupon', this.couponMessage, 'error'); }
                } else { this.couponMessage = 'The coupon code is invalid or has expired.'; this.showModal('Invalid Coupon', this.couponMessage, 'error'); }
            },
            async placeOrder() {
                const phoneRegex = /^01[3-9]\d{8}$/;
                if (!phoneRegex.test(this.checkoutForm.phone)) {
                    this.showModal("Invalid Phone Number", "Please enter a valid 11-digit Bangladeshi mobile number (e.g., 01712345678).", "error");
                    return;
                }

                if (!this.selectedPayment) {
                    this.showModal("Error", "Please select a payment method.", "error");
                    return;
                }

                const trxIdValidators = {
                    'bKash': /^(?=.{10}$)(?=.*[A-Z])(?=.*\d)[A-Z0-9]+$/,
                    'Upay': /^(?=.{10}$)(?=.*[A-Z])(?=.*\d)[A-Z0-9]+$/,
                    'Nagad': /^(?=.*\d)(?=.*[A-Z])[A-Z0-9]{8}$/,
                    'Rocket': /^\d{10}$/,
                    'Binance Pay': /^[A-Za-z0-9]{17}$/
                };

                const validatorRegex = trxIdValidators[this.selectedPayment.name];

                if (validatorRegex && !validatorRegex.test(this.paymentForm.trx_id)) {
                    this.showModal("Invalid Transaction ID", "Please enter a valid Transaction ID.", "error");
                    return;
                }

                if (this.checkoutItems.length === 0) { this.showModal("Error", "Your checkout is empty!", "error"); return; }
                if (!this.checkoutForm.name || !this.checkoutForm.email) { this.showModal("Error", "Please fill in all billing information.", "error"); return; }
                
                const orderPayload = { action: 'place_order', order: { customerInfo: this.checkoutForm, paymentInfo: { method: this.selectedPayment.name, trx_id: this.paymentForm.trx_id }, items: this.checkoutItems.map(item => ({ id: item.productId, name: this.getProductById(item.productId).name, quantity: item.quantity, pricing: this.getProductById(item.productId).pricing[item.durationIndex] })), coupon: this.appliedCoupon || {} } };
                try {
                    const response = await fetch(`${this.basePath}/api.php`, { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(orderPayload) });
                    const result = await response.json();
                    if (result.success) {
                        this.checkoutItems.forEach(orderedItem => this.cart = this.cart.filter(cartItem => cartItem.productId !== orderedItem.productId));
                        this.saveCart();
                        const savedOrderIds = JSON.parse(localStorage.getItem('submonthOrderIds') || '[]');
                        savedOrderIds.push(result.order_id); localStorage.setItem('submonthOrderIds', JSON.stringify(savedOrderIds));
                        this.checkoutItems = []; this.checkoutForm = { name: '', phone: '', email: '' }; this.resetCheckoutState();
                        
                        this.showModal(
                            "Order Placed Successfully",
                            "Your order has been received. Please wait for our confirmation.",
                            "success",
                            () => { this.setView('home'); }
                        );

                    } else { this.showModal("Order Failed", result.message || "Failed to place order. Please try again.", "error"); }
                } catch (error) { this.showModal("Connection Error", "An error occurred. Please check your connection and try again.", "error"); }
            },
            async submitReview() { if (!this.newReview.name.trim() || this.newReview.rating === 0 || !this.newReview.comment.trim()) { this.showModal('Review Error', 'Please fill all fields and provide a rating.', 'error'); return; } const reviewPayload = { action: 'add_review', review: { ...this.newReview, productId: this.selectedProduct.id } }; try { const response = await fetch(`${this.basePath}/api.php`, { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(reviewPayload) }); const result = await response.json(); if(result.success) { const product = this.getProductById(this.selectedProduct.id); if (product) { if(!product.reviews) product.reviews = []; product.reviews.unshift({ ...this.newReview, id: Date.now() }); } this.newReview = { name: '', rating: 0, comment: '' }; this.hoverRating = 0; this.reviewModalOpen = false; this.showModal('Success', 'Thank you for your review!', 'success'); } else { this.showModal('Error', 'Failed to submit review. ' + (result.message || ''), 'error'); } } catch(error) { this.showModal('Error', "An error occurred while submitting your review.", "error"); } },
            async findOrdersByIds(ids) {
                if (!ids || ids.length === 0) { this.isSearchingOrders = false; return; }
                this.isSearchingOrders = true;
                try {
                    const response = await fetch(`${this.basePath}/api.php?action=get_orders_by_ids&ids=${JSON.stringify(ids)}`);
                    const orders = await response.json();
                    if (orders.length > 0) {
                        this.orderHistory = orders.sort((a, b) => b.order_id - a.order_id);
                        this.calculateNotifications();
                    }
                } catch (error) { console.error('Error fetching orders:', error); } finally { this.isSearchingOrders = false; }
            },
            calculateNotifications() { const seenOrders = JSON.parse(localStorage.getItem('submonthSeenOrders') || '{}'); let count = 0; this.orderHistory.forEach(order => { if (!seenOrders[order.order_id] || seenOrders[order.order_id] !== order.status) count++; }); this.newNotificationCount = count; },
            clearNotifications() { this.newNotificationCount = 0; const seenOrders = {}; this.orderHistory.forEach(order => { seenOrders[order.order_id] = order.status; }); localStorage.setItem('submonthSeenOrders', JSON.stringify(seenOrders)); },
            copyToClipboard(text) {
                navigator.clipboard.writeText(text).then(() => {
                    if (this.copySuccess) return;
                    this.copySuccess = true;
                    setTimeout(() => {
                        this.copySuccess = false;
                    }, 1500);
                });
            },
            initSlider() {
                const images = <?= json_encode($hero_banner_paths) ?>;
                if (images.length > 0) { this.heroSlides.hasImages = true; this.heroSlides.slides = images.map(url => ({ url: url })); } 
                else { this.heroSlides.hasImages = false; const placeholders = []; const bgColors = ['bg-violet-500', 'bg-indigo-500', 'bg-sky-500', 'bg-teal-500']; for (let i = 0; i < 4; i++) { placeholders.push({ text: `Banner ${i + 1}`, bgColor: bgColors[i % bgColors.length] }); } this.heroSlides.slides = placeholders; }
                this.startSlider();
            },
            startSlider() { 
                if (this.heroSlides.interval) clearInterval(this.heroSlides.interval);
                if (this.heroSlides.slides.length <= 1) return; 
                this.heroSlides.interval = setInterval(() => { 
                    this.heroSlides.activeSlide = this.heroSlides.activeSlide === this.heroSlides.slides.length - 1 ? 0 : this.heroSlides.activeSlide + 1; 
                }, this.heroSlides.sliderInterval); 
            },
            stopSlider() { clearInterval(this.heroSlides.interval); },
        },
        mounted() {
            this.currency = localStorage.getItem('submonthCurrency') || 'BDT';
            this.cart = JSON.parse(localStorage.getItem('submonthCart') || '[]');
            const savedOrderIds = JSON.parse(localStorage.getItem('submonthOrderIds') || '[]');
            if (savedOrderIds.length > 0) { this.findOrdersByIds(savedOrderIds); }
            const initialParams = <?= json_encode($initial_params) ?>;
            this.changeView(this.currentView, initialParams, true);
            window.addEventListener('popstate', this.handleRouting);
            const yearSpan = document.getElementById('current-year-footer');
            if(yearSpan) { yearSpan.textContent = new Date().getFullYear(); }
            this.initSlider();
            nextTick(() => {
                this.setCategoryScrollerWidth();
                window.addEventListener('resize', this.setCategoryScrollerWidth);
            });
        },
        beforeUnmount() {
            window.removeEventListener('popstate', this.handleRouting);
            this.disconnectResizeObserver();
            window.removeEventListener('resize', this.setCategoryScrollerWidth);
        }
    }).mount('#app');
</script>

<script type="text/javascript" src="//widget.trustpilot.com/bootstrap/v5/tp.widget.bootstrap.min.js" async></script>