export default {
	template: `
      <div v-if="settings_object.payments.stripeConnect.enabled" :class="['setting-row pt-10']">
        <div class="bookit_admin-settings-payments-gateway bookit_admin-settings-payments-gateway--connected">
          <div
              id="bookit_admin-settings-payments-gateway-connect"
              class="bookit_admin-settings-payments-gateway-connect"
          >
            <div class="bookit_admin-settings-payments-gateway-connected">
              <div class="bookit_admin-settings-payments-gateway-connected-row">
                <div class="bookit_admin-settings-payments-gateway-connected-col2">
                  <h2 class="tribe-modal__title tribe-common-h5 tribe-modal__title--gateway-connected">
                    <span v-html="translations.stripe_connect_connected_title"></span>
                  </h2>

                  <div v-if="settings_object.payments.stripeConnect.test_mode" class="bookit_admin-settings-payments-stripe-modal-content-section">
                    <strong>{{ translations.stripe_connect_active_message }}</strong>
                  </div>

                  <div class="switcher">
                    <div class="bookit-switch">
                      <input type="checkbox" v-model="settings_object.payments.stripeConnect.test_mode" checked>
                      <label></label>
                    </div>
                    <span class="label for-switcher">{{ translations.stripe_connect_test_mode_label }}</span>
                    <div 
                    	class="bookit_admin-settings-payments-stripe-modal-content-section"
	                    v-html="translations.stripe_connect_test_mode_description"
                    >
                    </div>
                  </div>

                  <div class="bookit_admin-settings-payments-stripe-modal-content-section">
                    <strong>{{ translations.stripe_connect_currency_header }}</strong> — {{ translations.stripe_connect_currency_text }}
                  </div>

<!--
                  <div class="bookit_admin-settings-payments-stripe-modal-content-section">
                    <strong>{{ translations.stripe_connect_payment_methods_header }}</strong> — {{ translations.stripe_connect_payment_methods_text }}
                    <a
                        href="https://dashboard.stripe.com/settings/payment_methods"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="tribe-common-anchor-alt"
                    >
                      {{ translations.stripe_connect_payment_methods_link }}
                    </a>.
                  </div>
-->

<!--
                  <div class="bookit_admin-settings-payments-stripe-modal-content-section">
                    <strong>{{ translations.stripe_connect_webhooks_header }}</strong> — {{ translations.stripe_connect_webhooks_text }}
                    <a href="https://evnt.is/1b3p" target="_blank" rel="noopener noreferrer" class="tribe-common-anchor-alt">{{ translations.stripe_connect_webhooks_link }}</a>.
                  </div>
-->

                  <div class="bookit_admin-settings-payments-stripe-modal-content-section">
                    <strong> {{ translations.stripe_connect_pci_compliance_header }}</strong> — {{ translations.stripe_connect_pci_compliance_text }}
                    <a href="https://theeventscalendar.com/knowledgebase/k/pci-compliance/" target="_blank" rel="noopener noreferrer" class="tribe-common-anchor-alt">{{ translations.stripe_connect_pci_compliance_link }}</a>.
                  </div>
                </div>
              </div>
              <div class="bookit_admin-settings-payments-gateway-connected-row">
                <div class="bookit_admin-settings-payments-gateway-connected-col1">
                  {{ translations.stripe_connect_connected_as }}:
                </div>
                <div class="bookit_admin-settings-payments-gateway-connected-col2">
	                <span class="bookit_admin-settings-payments-gateway-connected-text-name">
	                    \t\t\t {{ gateways.stripeConnect.id }} \t\t
	                </span>
                  <a
                      :href="gateways.stripeConnect.disconnect_link"
                      class="bookit_admin-settings-payments-gateway-connected-text-disconnect-link"
                  >
                    {{ translations.stripe_connect_disconnect_button_text }}:
                  </a>
                </div>
              </div>
              <div class="bookit_admin-settings-payments-gateway-connected-col2">
                <ul>
                  <li v-for="(status, method) in gateways.stripeConnect.payment_methods" :key="method">
                    <span
                        :class="['dashicons', status === 'active' ? 'dashicons-yes bookit_admin-settings-payments-gateway-capability--yes' : 'dashicons-no bookit_admin-settings-payments-gateway-capability--no']"
                        :title="status === 'active' ? 'Enabled' : 'Disabled'"
                    ></span>
                    {{ formatCapabilitiesName( method ) }}
                  </li>
                </ul>
              </div>
              <div class="bookit_admin-settings-payments-gateway-connected-row">
                <div class="bookit_admin-settings-payments-gateway-connected-col1">
                  {{ translations.stripe_connect_currency_title }}:
                </div>
                <div class="bookit_admin-settings-payments-gateway-connected-col2">
                  <div class="bookit_admin-settings-payments-gateway-currency">
                    {{ gateways.stripeConnect.currency }}
                  </div>
                  <div class="bookit_admin-settings-payments-gateway-currency-message">
                    <span class="dashicons dashicons-info-outline"></span>
                    <span v-if="gateways.stripeConnect.currency_code !== gateways.stripeConnect.stripe_currency_code">
                      {{ formatCurrencyMessage( translations.stripe_connect_currency_not_match_message, gateways.stripeConnect.stripe_currency_code, gateways.stripeConnect.currency_code ) }}
                    </span>
                    <span v-if="gateways.stripeConnect.currency_code === gateways.stripeConnect.stripe_currency_code">
                      {{ translations.stripe_connect_currency_message }}
                    </span>
                    <a href="https://dashboard.stripe.com/settings/payment_methods" target="_blank" rel="noopener noreferrer">
                      {{ translations.stripe_connect_currency_message_link_text }}
                    </a>.
                  </div>
                </div>
              </div>

              <div class="bookit_admin-settings-payments-gateway-connected-row">
                <div class="bookit_admin-settings-payments-gateway-connected-col1"></div>
                <div class="bookit_admin-settings-payments-gateway-connected-col2">
                  <div class="bookit_admin-settings-payments-gateways-item-button">
                    <a
                        class="bookit_admin-settings-payments-gateways-item-button-link"
                        href="https://dashboard.stripe.com/settings"
                        target="_blank"
                        rel="noopener noreferrer"
                    >
                      {{ translations.stripe_connect_settings_link_text }}
                    </a>
                  </div>
                </div>
              </div>
              <div class="bookit_admin-settings-payments-gateway-help-links">
                <div class="bookit_admin-settings-payments-gateway-help-link">
                  <svg class="tribe-common-c-svgicon" width="16" height="23" fill="none" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                    <path fill="url(#a)" d="M0 .682h16v22H0z"></path>
                    <defs>
                      <pattern id="a" patternContentUnits="objectBoundingBox" width="1" height="1">
                        <use xlink:href="#b" transform="scale(.03125 .02273)"></use>
                      </pattern>
                      <image id="b" width="32" height="44" xlink:href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAsCAYAAAAEuLqPAAAC6klEQVRYCdVY7XHbMAz1z/6oKYzgESBNkBG6QbNBu0GyQbxBu4G7QUbwCB7BI7j3KICCKYqEYuV69R3P/AAeQBAAIe52/8vvC/EhEP/oaDh11F866m+mYfweaDjuib9tuqevxE8AN8Ks4KX+JdDwi4gPH1aGiCnQ8GYEXwMxQJ8BjHUFl/FTIH7tiM/3PMOL0rn/AWjMfCXiVyuwBQT+QPx7UmQ4ufkz4ReMWwKX1mEts5GzS4nVDEvSZT7b0HuVPNDwImZ7aOe5EKtEIP6Zr8exEEWvfsTsRfDdbkdjNAEfPpUcONEH6qPTwNPT5MYdySE3RMsMGprB/I/sHrucAZuJRSsge41nz3UnMWB5F/7TUgA8mtTuaAPxEQoUTZNLKoxH5/UpPyarKOuYoIpapdV6RyPnbkcVFnMM50SmsY8LJ006Oioc/A7ySGKibeKR2L95QUBnhN/2xN/X8M7kzSYaaFb4mt0r7EyeCcF5glAu+c+EI7HEWkDqgbeMvDgsKRALjBWOhOv4oIlFAF3pm4hZ6Ccn/EgYIp2q5SS9uhxYbkiE/J9kHhMa5TydKKeOAmE3XsuBW0N+5ri64E1GSk9Lt9uka+pNGbefH5exQnNHGsuolBJ6oyM86mtlPgCKg+AoeAkTNycRT6l0iVDmrfCO+sn5Snwk17I4VlEJeH+JtzSXCZ+bvsw01gYlJWAZgJb48rlMOGpCF1/E8VgiF2jHEqbxzGH2YhVkGUp9owRM18ySFsNkzNW8FifFrTc8lVlv2JozK23134TntUpoFuVzDvdE3eMNT7Wru/HWC5pw7tJtVUJjca0CmqY3q7BVAXye74mfW23zEl8VkCwZP148/c0t0BGfANpqelFtroDXCT/NB/65AriE8PTSap92BB7HszSb+wDCEOfbavo8s6UC8eHJW/vhEQKW2EwB/aCUKhhPdrWWXslmhWcj4y4u4ypWs9ozrvXX1IuLgvMFVDQ4hkZDxeSuHf4CwwK1/5LnQpQAAAAASUVORK5CYII="></image>
                    </defs>
                  </svg>
                  <a
                      :href="gateways.stripeConnect.configuring_link"
                      target="_blank"
                      rel="noopener noreferrer"
                      class="bookit_admin-settings-payments-gateway-help-link-url"
                  >
                    {{ translations.stripe_connect_configured_link_text }}
                  </a>
                </div>
                <div class="bookit_admin-settings-payments-gateway-help-link">
                  <svg class="tribe-common-c-svgicon" width="16" height="23" fill="none" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                    <path fill="url(#a)" d="M0 .682h16v22H0z"></path>
                    <defs>
                      <pattern id="a" patternContentUnits="objectBoundingBox" width="1" height="1">
                        <use xlink:href="#b" transform="scale(.03125 .02273)"></use>
                      </pattern>
                      <image id="b" width="32" height="44" xlink:href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAsCAYAAAAEuLqPAAAC6klEQVRYCdVY7XHbMAz1z/6oKYzgESBNkBG6QbNBu0GyQbxBu4G7QUbwCB7BI7j3KICCKYqEYuV69R3P/AAeQBAAIe52/8vvC/EhEP/oaDh11F866m+mYfweaDjuib9tuqevxE8AN8Ks4KX+JdDwi4gPH1aGiCnQ8GYEXwMxQJ8BjHUFl/FTIH7tiM/3PMOL0rn/AWjMfCXiVyuwBQT+QPx7UmQ4ufkz4ReMWwKX1mEts5GzS4nVDEvSZT7b0HuVPNDwImZ7aOe5EKtEIP6Zr8exEEWvfsTsRfDdbkdjNAEfPpUcONEH6qPTwNPT5MYdySE3RMsMGprB/I/sHrucAZuJRSsge41nz3UnMWB5F/7TUgA8mtTuaAPxEQoUTZNLKoxH5/UpPyarKOuYoIpapdV6RyPnbkcVFnMM50SmsY8LJ006Oioc/A7ySGKibeKR2L95QUBnhN/2xN/X8M7kzSYaaFb4mt0r7EyeCcF5glAu+c+EI7HEWkDqgbeMvDgsKRALjBWOhOv4oIlFAF3pm4hZ6Ccn/EgYIp2q5SS9uhxYbkiE/J9kHhMa5TydKKeOAmE3XsuBW0N+5ri64E1GSk9Lt9uka+pNGbefH5exQnNHGsuolBJ6oyM86mtlPgCKg+AoeAkTNycRT6l0iVDmrfCO+sn5Snwk17I4VlEJeH+JtzSXCZ+bvsw01gYlJWAZgJb48rlMOGpCF1/E8VgiF2jHEqbxzGH2YhVkGUp9owRM18ySFsNkzNW8FifFrTc8lVlv2JozK23134TntUpoFuVzDvdE3eMNT7Wru/HWC5pw7tJtVUJjca0CmqY3q7BVAXye74mfW23zEl8VkCwZP148/c0t0BGfANpqelFtroDXCT/NB/65AriE8PTSap92BB7HszSb+wDCEOfbavo8s6UC8eHJW/vhEQKW2EwB/aCUKhhPdrWWXslmhWcj4y4u4ypWs9ozrvXX1IuLgvMFVDQ4hkZDxeSuHf4CwwK1/5LnQpQAAAAASUVORK5CYII="></image>
                    </defs>
                  </svg>
                  <a :href="gateways.stripeConnect.troubleshooting_link" target="_blank" rel="noopener noreferrer" class="bookit_admin-settings-payments-gateway-help-link-url">
                    {{ translations.stripe_connect_get_troubleshoot_help }}
                  </a>
                </div>
              </div>
            </div>
          </div>
          <div class="bookit_admin-settings-payments-gateway-logo">
            <img
                width="200"
                :src="plugin_url + 'assets/images/stripe/stripe-logo.png'"
                alt="Stripe Logo Image"
                class="bookit_admin-settings-payments-gateway-logo-image bookit_admin-settings-payments-gateway-logo-image--stripe"
            >
            <ul>
              <li>
                {{ translations.stripe_connect_marketing_1 }}
              </li>
              <li>
                {{ translations.stripe_connect_marketing_2 }}
              </li>
              <li>
                {{ translations.stripe_connect_marketing_3 }}
              </li>
              <li>
                {{ translations.stripe_connect_marketing_4 }}
              </li>
            </ul>
          </div>
        </div>
      </div>
	`,
	data: () => ( {
		translations: bookit_window.translations,
		plugin_url: bookit_window.plugin_url,
	} ),
	props: {
		settings_object: {
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
	methods: {
		formatCapabilitiesName( method ) {
			// Remove '*payments' from the end of the method name
			return method.replace( /_?payments$/, '' );
		},
		formatCurrencyMessage( message, currency1, currency2 ) {
			return message.replace( /%1\$s/g, currency1 ).replace( /%2\$s/g, currency2 );
		}
	}
}