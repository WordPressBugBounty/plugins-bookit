export default {
  template: `
    <span class="payment-mismatch" @click.stop @mouseenter="onMouseEnter" @mouseleave="onMouseLeave">
      <button
        type="button"
        class="payment-mismatch-badge"
        :aria-expanded="visible ? 'true' : 'false'"
        :aria-label="tipText"
        @click="toggle"
      >?</button>
      <div v-if="visible" class="payment-mismatch-popover" role="dialog">
        <p>{{ tipText }}</p>
        <a :href="paypalDashboardUrl" target="_blank" rel="noopener noreferrer">{{ translations.view_in_paypal }}</a>
      </div>
    </span>
  `,
  data: () => ({
    translations: bookit_window.translations,
    open: false,
    hovering: false,
    hoverTimeout: null,
  }),
  computed: {
    visible() {
      return this.open || this.hovering;
    },
    // Translation strings come from esc_html__() on the PHP side (HTML-entity
    // encoded, e.g. don&#039;t), which is correct for the raw-HTML contexts
    // elsewhere in this app but renders literally in Vue text bindings.
    tipText() {
      return this.decodeHtmlEntities(this.translations.payment_mismatch_tip);
    },
    paypalDashboardUrl() {
      return bookit_window.paypal_mode === 'live'
        ? 'https://www.paypal.com/myaccount/transactions/'
        : 'https://www.sandbox.paypal.com/myaccount/transactions/';
    }
  },
  methods: {
    toggle() {
      this.open = !this.open;
    },
    onMouseEnter() {
      clearTimeout(this.hoverTimeout);
      this.hovering = true;
    },
    onMouseLeave() {
      clearTimeout(this.hoverTimeout);
      this.hoverTimeout = setTimeout(() => {
        this.hovering = false;
      }, 150);
    },
    handleOutsideClick(event) {
      if (this.open && this.$el && !this.$el.contains(event.target)) {
        this.open = false;
      }
    },
    handleEscape(event) {
      if (this.visible && event.key === 'Escape') {
        this.open = false;
        this.hovering = false;
      }
    },
    decodeHtmlEntities(html) {
      const el = document.createElement('textarea');
      el.innerHTML = html;
      return el.value;
    }
  },
  mounted() {
    document.addEventListener('click', this.handleOutsideClick);
    document.addEventListener('keydown', this.handleEscape);
  },
  beforeDestroy() {
    clearTimeout(this.hoverTimeout);
    document.removeEventListener('click', this.handleOutsideClick);
    document.removeEventListener('keydown', this.handleEscape);
  }
}
