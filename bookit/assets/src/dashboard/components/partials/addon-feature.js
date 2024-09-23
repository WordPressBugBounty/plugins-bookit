import customSelect from '@dashboard-partials/custom-select';

export default {
  template: `
    <div class="addon-feature">
      <span class="addon-icon">
        <i :class="addonSlug"></i>
      </span>
      <h2 class="title">{{ freemiusTitle }}</h2>
      <div class="price" v-if="selectedPlan">
        <p v-if="freemiusPlan" :class="[ selectedPlan.licenses, 'active' ]">
          <span class="plan-price">$ {{ selectedPlan.annual_price }}</span>
          <span class="plan-period">/{{ translations.per_year }}</span>
        </p>
      </div>
      <p class="info">{{ freemiusDescriptions }} <a target="_blank" href="https://bookitwp.com/">{{ translations.learn_more }}</a></p>

      <div class="developer-info" v-if="freemiusLatest">
        <span class="version-label">{{ translations.version }}</span>
        <span>{{ freemiusLatest.version }}
          <a href="https://bookitwp.com/" target="_blank">{{ translations.view_changelog }}</a>
        </span>
      </div>
      
      <div class="action">
        <customSelect 
          @selectCallback="selectCallback" 
          :options="planOptions" 
          buttonClass="buy" 
          :buttonText="translations.buy"
        />
      </div>
    </div>
  `,
  data() {
    return {
      translations: bookit_window.translations,
      selectedPlan: null,
    };
  },
  components: {
    customSelect,
  },
  props: {
    addon: {
      type: Object,
      required: true
    },
    addonLink: {
      type: String,
      required: true
    },
    addonSlug: {
      type: String,
      required: true
    },
    freemius: {
      type: [Object, Array],
      required: true
    }
  },
  computed: {
    title() {
      return this.translations.addon_feature.replace('%s', this.addon.title);
    },
    freemiusTitle() {
      return this.freemius && this.freemius.title ? this.freemius.title : '';
    },
    freemiusDescriptions() {
      return this.freemius && this.freemius.descriptions ? this.freemius.descriptions : '';
    },
    freemiusLatest() {
      return this.freemius && this.freemius.latest ? this.freemius.latest : null;
    },
    freemiusPlan() {
      return this.freemius && this.freemius.plan ? this.freemius.plan : null;
    },
    planOptions() {
      let options = [];
      if (this.freemiusPlan && typeof this.freemiusPlan === 'object') {
        Object.keys(this.freemiusPlan).forEach((key) => {
          options.push({
            'id': this.freemiusPlan[key].id,
            'value': this.freemiusPlan[key].licenses,
            'text': this.freemiusPlan[key].data.text,
            'url': this.freemiusPlan[key].url
          });
        });
      }
      return options;
    },
  },
  methods: {
    selectCallback(selectedOption) {
      if (selectedOption && this.freemiusPlan && typeof this.freemiusPlan === 'object') {
        const selectedPlanKey = Object.keys(this.freemiusPlan).find((planKey) => {
          return this.freemiusPlan[planKey].id == selectedOption.id;
        });
        if (selectedPlanKey) {
          this.selectedPlan = this.freemiusPlan[selectedPlanKey];
        }
      }
    },
  }
}