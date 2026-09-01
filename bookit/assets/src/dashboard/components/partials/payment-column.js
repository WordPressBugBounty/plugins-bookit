import PaymentMismatchIndicator from '@dashboard-partials/payment-mismatch-indicator';

export default {
  template: `
    <div class="payment-cell">
      <template v-if="row.payment_method">
        <b class="text-capitalize">{{ methodLabel }}</b>
        <span class="status payment" :class="row.payment_status + '-payment'">{{ translations[row.payment_status] }}</span>
        <payment-mismatch-indicator v-if="row.payment_flag_reason" :reason="row.payment_flag_reason"></payment-mismatch-indicator>
      </template>
      <b v-else class="text-capitalize">No payment</b>
    </div>
  `,
  components: {
    PaymentMismatchIndicator
  },
  data: () => ({
    translations: bookit_window.translations
  }),
  props: {
    row: {
      type: Object,
      required: true
    }
  },
  computed: {
    methodLabel() {
      return this.translations[this.row.payment_method] !== undefined ? this.translations[this.row.payment_method] : this.row.payment_method;
    }
  }
}
