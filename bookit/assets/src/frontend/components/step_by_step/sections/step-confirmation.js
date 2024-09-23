import DOMPurify from 'dompurify';

export default {
  name: 'stepConfirmation',
  template: `
    <div class="confirmation">
      <div v-if="Object.keys(errors).length > 0" class="errors">
        <div class="error" v-for="(error, index) in errors">
          <span>{{ error }}</span> <i @click="deleteError(index)" class="close-icon"></i>
        </div>
      </div>
      <p class="date" >
        {{ moment.unix(appointment.date_timestamp).format('DD MMMM YYYY') }}
      </p>
      <p class="time">{{ moment.unix(appointment.start_time).format( wpTimeFormat ) }} — {{ moment.unix(appointment.end_time).format( wpTimeFormat  ) }}</p>
      
      <div class="appointment-info">
        <div class="appointment-detail">
          <div class="detail-icon">
            <span class="icon service"><i></i></span>
          </div>
          <div class="info">
            <span class="title">{{ translations.service }}:</span>
            <span class="value">{{  sanitizeInput( selectedService.title ) }}</span>
          </div>
        </div>
        <div class="appointment-detail">
          <div class="detail-icon">
            <span class="icon user"><i></i></span>
          </div>
          <div class="info">
            <span class="title">{{ translations.employee }}:</span>
            <span class="value">{{  sanitizeInput( selectedStaff.full_name ) }}</span>
          </div>
        </div>
        <div class="appointment-detail">
          <div class="detail-icon">
            <span class="icon user"><i></i></span>
          </div>
          <div class="info">
            <span class="title">{{ translations.client_name }}:</span>
            <span class="value" v-html="sanitizeInput( appointment.full_name )"></span>
          </div>
        </div>

        <div class="appointment-detail" v-if="parseFloat(clearPrice) > 0">
          <div class="detail-icon">
            <span class="icon price"><i></i></span>
          </div>
          <div class="info">
            <span class="title">{{ translations.price }}:</span>
            <span class="value">{{ staffPrice }}</span>
          </div>
        </div>
        <div class="appointment-detail" v-if="parseFloat(clearPrice) > 0">
          <div class="detail-icon">
            <span class="icon payment"><i></i></span>
          </div>
          <div class="info">
            <span class="title">{{ translations.payment_method }}:</span>
            <span class="value">{{ appointment.payment_method }}</span>
          </div>
        </div>
      </div>
      <div class="total">
        <label>{{ translations.total }}:</label>
        <span>{{ staffPrice }}</span>
      </div>
    </div>
    `,
  components: {},
  data: () => ({
    selectedPayment: 0,
    stripe: {
      stripe: '',
      elements: '',
      card: '',
      client_secret: ''
    },
    stripeConnect: {
      stripe: '',
      elements: '',
      card: '',
      client_secret: ''
    },
    translations: bookit_window.translations
  }),
  computed: {
    appointment: {
      get() {
        return this.$store.getters.getAppointment;
      },
      set( appointment ) {
        this.$store.commit('setAppointment', appointment);
      }
    },
    clearPrice() {
      return this.selectedStaff.staff_services.find(staff_service => staff_service.id == this.selectedService.id).price;
    },
    errors: {
      get() {
        return this.$store.getters.getErrors;
      },
      set( errors ) {
        this.$store.commit('setErrors', errors);
      }
    },
    selectedStaff() {
        return this.$store.getters.getSelectedStaff;
    },
    selectedService () {
      return this.$store.getters.getSelectedService;
    },
    settings () {
      return this.$store.getters.getSettings;
    },
    staffPrice() {
      return this.getStaffPrice(this.selectedStaff, this.selectedService, this.settings);
    },
    wpTimeFormat() {
      return this.getWPSettingsTimeFormat();
    },
  },
  created() {
  },
  methods: {
    sanitizeInput( input ) {
      const sanitizedInput = DOMPurify.sanitize( input );

      // Validate phone number.
      if ( this.appointment.customer_phone === input ) {
        /**
         * Regular expression pattern for phone number validation.
         *
         * Allows phone numbers containing any combination of the following characters:
         * - Plus sign (+)
         * - Parentheses ()
         * - Hyphen (-)
         * - Dot (.)
         * - Spaces
         * - Digits (0-9)
         *
         * The characters and digits can appear in any order.
         *
         * @type {RegExp}
         */
        const phoneRegex = /^[\+\(\)\-\.\s\d]+$/;
        if ( phoneRegex.test( sanitizedInput ) ) {
          return '';
        }
      }

      return sanitizedInput;
    },
    deleteError( errorIndex ) {
      var errors = Object.assign( {}, this.errors );
      delete errors[ errorIndex ];

      this.errors = errors;
    }
  },
}