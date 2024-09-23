import StripeConnectGateway from './components/StripeConnectGateway.js';
import StripeConnectSettings from './components/StripeConnectSettings.js';

export default {
	name: 'stripeConnect',
	template: `
      <div>
        <div :class="['setting-row no-border pb-10 pt-30']">
          <div :class="['form-group small no-margin']">
            <span class="label">{{ this.payment.formatted_name }}</span>
          </div>
          <div :class="['form-group small no-margin']">
            <div class="switcher">
              <div class="bookit-switch">
                <input type="checkbox" v-model="settings_object.payments.stripeConnect.enabled">
                <label></label>
              </div>
            </div>
            <span class="label for-switcher" v-html=" settings_object.payments.stripeConnect.enabled ? translations.enabled : translations.disabled"></span>
          </div>
        </div>
        <template v-if="!gateways.stripeConnect.id">
          <StripeConnectGateway :settings_object="settings_object" :addon="addon" :gateways="gateways"/>
        </template>

        <template v-else>
          <StripeConnectSettings :settings_object="settings_object" :addon="addon" :gateways="gateways"/>
        </template>

      </div>
	`,
	data: () => ( {
		translations: bookit_window.translations,
	} ),
	created() {
		if ( !this.settings_object.payments.stripeConnect ) {
			this.$set( this.settings_object.payments, 'stripeConnect', {
				enabled: false,
				publish_key: '',
				secret_key: ''
			} );
		}
	},
	props: {
		settings_object: {
			type: Object,
			required: true
		},
		payment: {
			type: Object,
			required: true
		},
		addon: {
			type: Object,
			required: true
		},
		gateways: {
			type: Object,
			required: true
		},
	},
	methods: {},
	components: {
		StripeConnectGateway,
		StripeConnectSettings,
	},
}
