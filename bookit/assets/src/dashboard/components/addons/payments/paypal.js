export default {
	name: 'paypal',
	template: `
      <div>
        <div :class="['setting-row no-border pt-30 pb-10']">
          <div :class="['form-group small no-margin']">
            <span class="label">{{ this.payment.formatted_name }}</span>
          </div>
          <div :class="['form-group small no-margin']">
            <div class="switcher">
              <div class="bookit-switch">
                <input type="checkbox" v-model="settings_object.payments.paypal.enabled">
                <label></label>
              </div>
              <span class="label for-switcher" v-html="translations.enabled"></span>
            </div>
          </div>
        </div>

        <div :class="['setting-row no-border pt-10 pb-10']">
          <div :class="['form-group medium']">
            <div class="copy-block">
              <label>{{ translations.paypal_ipn_setup }}</label>
              <div>
                {{ translations.paypal_ipn_pre_link }}
                <a ref="ipnLink" href="https://developer.paypal.com/docs/api-basics/notifications/ipn/IPNSetup/" target="_blank">
                  {{ translations.paypal_ipn_link_txt }}
                </a>
              </div>
              <div class="code">
                <code>{{ ipn_url }}</code>
                <button class="button-copy" type="button" @click="copyURL()">
                  <i class="copy-icon"></i>{{ translations.copy }}
                </button>
                <div class="help" v-if="linkCopy">
                  <div class="help-tip" v-html="translations.url_copied">
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div :class="['setting-row pt-10']">
          <div class="form-group small">
            <label>{{ translations.paypal_email }}</label>
            <input type="email" v-model="settings_object.payments.paypal.email" :class="{'error': errors.paypal_email}">
            <span class="error-message" v-if="errors.paypal_email">{{ errors.paypal_email }}</span>
          </div>
          <div class="form-group small">
            <label>{{ translations.paypal_mode }}</label>
            <select v-model="settings_object.payments.paypal.mode" :class="{'error': errors.paypal_mode}">
              <option value="live">{{ translations.paypal_mode_live }}</option>
              <option value="sandbox">{{ translations.paypal_mode_sandbox }}</option>
            </select>
            <span class="error-message" v-if="errors.paypal_mode">{{ errors.paypal_mode }}</span>
          </div>
        </div>
      </div>
	`,
	data: () => ( {
		linkCopy: false,
		translations: bookit_window.translations,
		ipn_url: `${ bookit_window.site_url }/?stm_bookit_check_ipn=1`,
	} ),
	created() {
	},
	computed: {
		errors() {
			return this.$store.getters.getErrors;
		},
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
	},
	methods: {
		copyURL() {
			var input = document.body.appendChild( document.createElement( "input" ) );
			input.value = this.$refs.ipnLink.href;
			;
			input.select();
			document.execCommand( 'copy' );
			input.parentNode.removeChild( input );

			this.linkCopy = true;
			setTimeout( () => {
				this.linkCopy = false;
			}, 1000 );
		}
	}
}