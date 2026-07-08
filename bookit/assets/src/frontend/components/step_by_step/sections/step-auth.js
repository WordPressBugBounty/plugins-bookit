import AuthPanel from '@components/auth-panel'

/**
 * First step of the step-by-step flow for Registered booking: the visitor must
 * log in or register before any other step becomes reachable.
 */
export default {
  name: 'stepAuth',
  components: { AuthPanel },
  template: `
    <div class="details step-auth">
      <auth-panel @authenticated="onAuthenticated"></auth-panel>
    </div>
  `,
  computed: {
    appointment: {
      get() {
        return this.$store.getters.getAppointment;
      },
      set( appointment ) {
        this.$store.commit('setAppointment', appointment);
      }
    },
    navigation() {
      return this.$store.getters.getStepNavigation;
    },
  },
  methods: {
    onAuthenticated( user ) {
      var appointment       = Object.assign({}, this.appointment);
      appointment.user_id   = user.ID;
      appointment.full_name = ( user.customer && user.customer.full_name ) ? user.customer.full_name : ( appointment.full_name || user.display_name );
      appointment.email     = user.user_email;
      if ( user.customer && user.customer.phone && !appointment.phone ) {
        appointment.phone = user.customer.phone;
      }
      appointment.nonce     = user.nonce;
      this.appointment = appointment;

      /** Move past the gate to the first booking step. **/
      var index = this.navigation.findIndex( step => step.key === 'auth' );
      var next  = this.navigation[ index + 1 ];
      if ( next ) {
        this.$store.commit('setCurrentStepKey', next.key);
      }
    },
  },
}
