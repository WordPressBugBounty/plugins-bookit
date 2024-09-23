import stripeConnect from '@dashboard-addons/payments/stripe-connect';
import paypal from '@dashboard-addons/payments/paypal';
import stripe from '@dashboard-addons/payments/stripe';
import woocommerce from '@dashboard-addons/payments/woocommerce';
import addon_feature from '@dashboard-partials/addon-feature';

import temp_pro from '@dashboard-addons/payments/temp-pro';

export default {
	template: `
      <div class="payments-tabs">
        <div class="setting-row pt-10">
          <div class="form-group small no-margin">
            <span class="label">{{ translations.pay_locally }}</span>
          </div>
          <div class="form-group small no-margin">
            <div class="switcher">
              <div class="bookit-switch">
                <input type="checkbox" v-model="settings_object.payments.locally.enabled">
                <label></label>
              </div>
            </div>
            <span class="label for-switcher" v-html=" settings_object.payments.locally.enabled ? translations.enabled : translations.disabled"></span>
          </div>
        </div>

        <!-- PayPal and StripeConnect components -->
        <component
            v-for="paymentName in ['stripeConnect', 'paypal']"
            :key="paymentName"
            :is="paymentName"
            :addon="proAddons[0].data"
            :payment="findPayment(paymentName)"
            :settings_object="settings_object"
            :gateways="gateways">
        </component>

        <!-- Load bookit payments addon data -->
        <div :class="{'no-addon':!proAddons[0].data.isCanUse }">
          <component
              v-for="payment in proAddons[0].data.settings.payments"
              v-if="payment.name !== 'stripeConnect' && payment.name !== 'paypal'"
              :key="payment.name"
              :is="payment.name"
              :addon="proAddons[0].data"
              :payment="payment"
              :settings_object="settings_object"
              :gateways="gateways">
          </component>

          <!-- IF ADDON NOT INSTALLED -->
          <div class='' v-if="showNotInstalledAddon()">
            <addon_feature :freemius="proAddons[0].freemius" :addon="proAddons[0]" addonSlug="payments" :addonLink="proAddons[0].data.link"></addon_feature>
          </div>
          <!-- IF ADDON NOT INSTALLED END -->
          
          <!-- IF ADDON INSTALLED BUT NO LICENSE-->
          <div class='' v-if="showActivationLink()">
            <div class="addon-feature activation">
            <span class="addon-icon">
              <i :class="proAddons[0].name"></i>
            </span>
              <h2 class="title">{{ proAddons[ 0 ].data.title }}</h2>
              <p class="activation-link" v-html="proAddons[0].data.activationLink"></p>
            </div>
          </div>
          <!-- IF ADDON INSALLED BUT NO LICENSE END-->
        </div>
      </div>
	`,
	components: {
		stripeConnect,
		paypal,
		stripe,
		woocommerce,
		addon_feature,
	},
	data: () => ( {
		translations: bookit_window.translations,
	} ),
	props: {
		proAddons: {
			type: Array,
			required: false,
		},
		settings_object: {
			type: Object,
			required: true
		},
		gateways: {
			type: Object,
			required: true
		},
		pro_installed: {
			type: Boolean,
			required: true
		},
		pro_disabled: {
			type: Boolean,
			required: true
		},
		woocommerce_products: {
			type: Array,
			required: false,
			default: []
		},
		woocommerce_enabled: {
			type: Boolean,
			required: true,
		}
	},
	computed: {
		errors() {
			return this.$store.getters.getErrors;
		},
	},
	created() {
		if ( !this.settings_object.payments.stripeConnect ) {
			this.$set( this.settings_object.payments, 'stripeConnect', {
				enabled: false,
				publish_key: '',
				secret_key: ''
			} );
		}
	},
	methods: {
		showActivationLink() {
			return ( this.proAddons[ 0 ].data.installed && !this.proAddons[ 0 ].data.isCanUse );
		},
		showNotInstalledAddon() {
			return !this.proAddons[ 0 ].data.installed && !this.pro_installed;
		},
		hasStripeConnect( payments ) {
			return payments.some( payment => payment.name === 'stripeConnect' );
		},
		hasPayPalLegacy( payments ) {
			return payments.some( payment => payment.name === 'paypal' );
		},
		findPayment( paymentName ) {
			return this.proAddons[ 0 ].data.settings.payments.find( payment => payment.name === paymentName );
		},
	}
}