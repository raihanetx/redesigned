<div v-show="currentView === 'home'">
    <section class="py-16 sm:py-24">
        <div class="max-w-2xl mx-auto text-center px-6">
            <h2 class="text-3xl sm:text-4xl font-bold tracking-tight text-slate-900">
                Your Opinion Matters
            </h2>
            <p class="mt-2 text-base text-slate-600">
                Share your experience on Trustpilot.
            </p>
            <div class="mt-8">
                <div class="trustpilot-widget inline-block" data-locale="en-US" data-template-id="56278e9abfbbba0bdcd568bc" data-businessunit-id="68cd3e85a5e773033d7242cf" data-style-height="52px" data-style-width="100%" data-token="4607939e-09dd-4f65-8fed-06bda9352f4e">
                  <a href="https://www.trustpilot.com/review/submonth.com" target="_blank" rel="noopener">Trustpilot</a>
                </div>
            </div>
        </div>
    </section>
</div>

<div v-show="currentView === 'home'">
    <footer class="bg-slate-900">
        <div class="max-w-4xl mx-auto px-6 sm:px-8 py-12">
            <div class="space-y-8 text-center">
                <div class="space-y-4">
                    <div>
                        <a :href="basePath + '/'" @click.prevent="setView('home')" class="inline-block text-2xl font-bold text-slate-100">Submonth</a>
                        <p class="text-sm text-slate-400 max-w-sm mx-auto">
                            The Digital Product Store
                        </p>
                    </div>
                    <div class="pt-2">
                        <form class="flex gap-2 max-w-md mx-auto">
                            <input type="email" placeholder="Enter your email" class="flex-1 w-full min-w-0 px-3 py-2 bg-slate-800 border border-slate-600 rounded-md text-sm shadow-sm placeholder-slate-400 text-white focus:outline-none focus:border-[var(--primary-color)] focus:ring-1 focus:ring-[var(--primary-color)]" required>
                            <button type="submit" class="bg-[var(--primary-color)] hover:bg-[var(--primary-color-darker)] text-white font-semibold px-3 sm:px-4 py-2 rounded-md text-sm transition-colors duration-300 flex-shrink-0">Subscribe</button>
                        </form>
                    </div>
                </div>

                <nav>
                    <div class="overflow-x-auto no-scrollbar pb-2">
                        <ul class="inline-flex flex-nowrap items-center whitespace-nowrap gap-x-6 sm:gap-x-8 text-sm text-slate-400">
                            <li><a :href="basePath + '/'" @click.prevent="setView('home')" class="hover:text-violet-400 hover:underline">Home</a></li>
                            <li><a :href="basePath + '/about-us'" @click.prevent="setView('aboutUs')" class="hover:text-violet-400 hover:underline">About Us</a></li>
                            <li><a :href="basePath + '/privacy-policy'" @click.prevent="setView('privacyPolicy')" class="hover:text-violet-400 hover:underline">Privacy Policy</a></li>
                            <li><a :href="basePath + '/terms-and-conditions'" @click.prevent="setView('termsAndConditions')" class="hover:text-violet-400 hover:underline">Terms & Conditions</a></li>
                            <li><a :href="basePath + '/refund-policy'" @click.prevent="setView('refundPolicy')" class="hover:text-violet-400 hover:underline">Refund Policy</a></li>
                        </ul>
                    </div>
                </nav>

                <div class="pt-2">
                    <p class="text-xs text-slate-500">&copy; <span id="current-year-footer"></span> Submonth, Inc. All rights reserved.</p>
                </div>
            </div>
        </div>
    </footer>
</div>