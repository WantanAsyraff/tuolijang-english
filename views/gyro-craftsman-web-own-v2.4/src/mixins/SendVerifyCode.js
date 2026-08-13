export default {
  data() {
    return {
      disabled: false,
      text: this.$('login.code'),
      _codeTimer: null,
    };
  },
  watch: {
    lang() {
      this.setOptions();
    },
  },
  computed: {
    lang: function () {
      return this.$store.getters.lang;
    },
  },
  beforeDestroy() {
    clearInterval(this._codeTimer);
  },
  methods: {
    setOptions() {
      this.text = this.$('login.code');
    },
    sendCode() {
      if (this.disabled) return;
      this.disabled = true;
      let n = 60;

      const setCountdownText = () => {
        this.text = `${this.$('login.surplus')} ${n}s`;
      };

      setCountdownText();
      clearInterval(this._codeTimer);
      this._codeTimer = setInterval(() => {
        n = n - 1;
        if (n < 0) {
          clearInterval(this._codeTimer);
          this.disabled = false;
          this.text = this.$('login.Recapture');
          return;
        }
        setCountdownText();
      }, 1000);
    },
  },
};
