import { defineStore } from 'pinia';

export const useUiStore = defineStore('ui', {
    state: () => ({
        isAuthOpen: false,
        authMode: 'login',
        isCartOpen: false,
        cartToastMessage: 'Item added to cart!',
        isCartToastOpen: false,
        authToastMessage: '',
        authToastType: 'login',
        isAuthToastOpen: false,
    }),
    actions: {
        openAuthModal(mode = 'login') {
            this.authMode = mode;
            this.isAuthOpen = true;
        },
        closeAuthModal() {
            this.isAuthOpen = false;
        },
        setAuthMode(mode) {
            this.authMode = mode;
        },
        openCartModal() {
            this.isCartOpen = true;
        },
        closeCartModal() {
            this.isCartOpen = false;
        },
        showCartToast(message = 'Item added to cart!') {
            this.cartToastMessage = message;
            this.isCartToastOpen = true;
            setTimeout(() => {
                this.isCartToastOpen = false;
            }, 2000); // 2 second duration
        },
        showAuthToast(message, type = 'login', duration = 2500) {
            this.authToastMessage = message;
            this.authToastType = type;
            this.isAuthToastOpen = true;
            setTimeout(() => {
                this.isAuthToastOpen = false;
            }, duration);
        }
    }
});
