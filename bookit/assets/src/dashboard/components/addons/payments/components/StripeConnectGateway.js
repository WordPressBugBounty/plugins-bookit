export default {
	template: `
      <div v-if="settings_object.payments.stripeConnect.enabled" :class="['setting-row pt-10']">
        <div class="bookit_admin-settings-payments-gateway">
          <div id="bookit_admin-settings-payments-gateway-connect" class="bookit_admin-settings-payments-gateway-connect">
            <h2 class="bookit_admin-settings-payments-gateway-title">
              {{ translations.stripe_connect_title }}
            </h2>
            <div class="bookit_admin-settings-payments-gateway-description">
              <p class="bookit_admin-settings-payments-gateway-description-text">
                {{ translations.stripe_connect_start_selling }}<br>
              </p>
              <div class="bookit_admin-settings-payments-gateway-signup-links">
                <div class="bookit_admin-settings-payments-gateway-signup-settings">
                  <div class="bookit_admin-settings-payments-gateway-connect-button">
                    <a 
                    	data-gateway-onboard-complete="tecTicketsCommerceGatewayStripeSignupCallback" 
                    	:href="gateways.stripeConnect.authorize_link" 
                    	data-gateway-button="true" 
                    	id="connect_to_stripe" 
                    	class="bookit_admin-settings-payments-gateway-connect-button-link"
                    >
                      {{ translations.stripe_connect_get_connected }} <i> {{ translations.stripe_connect_get_connected_name }}</i> 
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
}