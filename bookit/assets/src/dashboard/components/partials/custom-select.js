export default {
  template: `
    <div>
      <div :class="['custom-select', { 'open': isOptionOpen } ]" @click="isOptionOpen = !isOptionOpen;">
        <div class="value">
          {{ selectedOption ? selectedOption.text : 'Select an option' }}
        </div>
        <div :class="['custom-options', { 'open': isOptionOpen } ]">
            <span v-for="option in options" :key="option.value" class="custom-option" :data-value="option.value" @click="selectOption($event, option)">
              {{ option.text }}
            </span>
        </div>
      </div>
      <a v-if="selectedOption" target="_blank" :class="buttonClass" :href="selectedOption.url">
        {{ buttonText }}
      </a>
    </div>
  `,
  data() {
    return {
      translations: bookit_window.translations,
      isOptionOpen: false,
      selectedOption: null,
    };
  },
  props: {
    options: {
      type: Array,
      required: true
    },
    selectedValue: {
      type: String,
      required: false,
    },
    buttonClass: {
      type: String,
      required: true,
    },
    buttonText: {
      type: String,
      required: true,
    }
  },
  created() {
    this.initializeSelectedOption();
  },
  methods: {
    initializeSelectedOption() {
      if (this.options && this.options.length > 0) {
        this.selectedOption = this.options[0];
        this.$emit('selectCallback', this.selectedOption);
      }
    },
    selectOption(event, option) {
      this.selectedOption = option;
      this.$emit('selectCallback', option);
    },
  }
}