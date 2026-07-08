/**
 * Login / Register gate shared by both themes. Delegates auth to WordPress
 * (bookit_login / bookit_register) and emits `authenticated` with the user on
 * success so the booking flow can continue in place.
 */
export default {
  name: 'authPanel',
  template: `
    <div class="bookit-auth">
      <div class="bookit-auth-nav">
        <button type="button" :class="['bookit-auth-tab', { active: authTab === 'login' }]" @click="setTab('login')">{{ translations.login }}</button>
        <button type="button" :class="['bookit-auth-tab', { active: authTab === 'register' }]" @click="setTab('register')">{{ translations.register }}</button>
      </div>

      <div v-if="message" class="bookit-auth-message">{{ message }}</div>

      <div v-if="authTab === 'register'" class="bookit-auth-field">
        <input type="text" v-model="full_name" :placeholder="translations.full_name">
        <span class="bookit-auth-error" v-if="errors.full_name">{{ errors.full_name }}</span>
      </div>

      <div class="bookit-auth-field">
        <input type="email" v-model="email" :placeholder="translations.email">
        <span class="bookit-auth-error" v-if="errors.email">{{ errors.email }}</span>
      </div>

      <div class="bookit-auth-field">
        <input type="password" v-model="password" :placeholder="translations.password" @keyup.enter="submit">
        <span class="bookit-auth-error" v-if="errors.password">{{ errors.password }}</span>
      </div>

      <div v-if="authTab === 'register'" class="bookit-auth-field">
        <input type="password" v-model="password_confirmation" :placeholder="translations.password_confirmation" @keyup.enter="submit">
        <span class="bookit-auth-error" v-if="errors.password_confirmation">{{ errors.password_confirmation }}</span>
      </div>

      <div class="bookit-auth-action">
        <button type="button" class="bookit-auth-submit" :disabled="loading" @click="submit">
          {{ authTab === 'login' ? translations.login : translations.register }}
        </button>
      </div>
    </div>
  `,
  data: () => ({
    translations: bookit_window.translations,
    full_name: null,
    email: null,
    password: null,
    password_confirmation: null,
    errors: {},
    message: null,
    loading: false,
  }),
  computed: {
    authTab() {
      return this.$store.getters.getAuthTab;
    },
  },
  methods: {
    setTab( tab ) {
      this.errors = {};
      this.message = null;
      this.$store.commit('setAuthTab', tab);
    },
    validate() {
      let errors = {};
      if ( this.authTab === 'register' && ( !this.full_name || this.full_name.length < 3 || this.full_name.length > 25 ) ) {
        errors.full_name = bookit_window.translations.full_name_wrong_length;
      }
      if ( !this.validEmail( this.email ) ) {
        errors.email = bookit_window.translations.invalid_email;
      }
      if ( !this.password ) {
        errors.password = bookit_window.translations.required_field;
      }
      if ( this.authTab === 'register' && this.password !== this.password_confirmation ) {
        errors.password_confirmation = bookit_window.translations.confirmation_mismatched;
      }
      this.errors = errors;
      return Object.keys( errors ).length === 0;
    },
    async submit() {
      this.message = null;
      if ( !this.validate() ) {
        return;
      }
      this.loading = true;
      let action = this.authTab === 'login' ? 'bookit_login' : 'bookit_register';
      let data = ( this.authTab === 'login' )
        ? { email: this.email, password: this.password }
        : { email: this.email, password: this.password, password_confirmation: this.password_confirmation, full_name: this.full_name };

      let response = await this.authRequest( action, data );
      this.loading = false;

      if ( response && response.success ) {
        this.applyAuthSession( response.data );
        this.$emit( 'authenticated', response.data.user );
      } else if ( response && response.data && response.data.errors ) {
        this.errors = response.data.errors;
      } else {
        this.message = ( response && response.data && response.data.message ) ? response.data.message : bookit_window.translations.login_failed;
      }
    },
  },
}
