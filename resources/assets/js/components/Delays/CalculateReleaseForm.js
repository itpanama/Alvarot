import {mapGetters} from 'vuex';

export default {
  name: "calculate-release-form",
  data() {
    return {
      labelPosition: "top",
      roles: [],
      dataForm: {
        bl_number: "",
        discharge_date: "",
        port: "",
        container_size_type: "",
        free_days_demurrage: 0,
        return_date: ""
      },
      defaultDataForm: {},
      rules: {
        bl_number: [
          {
            required: true,
            message: "This field is required.",
            trigger: "blur"
          }
        ],
        discharge_date: [
          {
            required: true,
            message: "This field is required.",
            trigger: "blur"
          }
        ],
        port: [
          {
            required: true,
            message: "This field is required.",
            trigger: "blur"
          }
        ],
        return_date: [
          {
            required: true,
            message: "This field is required.",
            trigger: "blur"
          }
        ]
      }
    };
  },
  computed: {
    ...mapGetters({
      containerSiteTypeOptions: 'getContainerSiteTypeOptions',
    })
  },
  methods: {
    submitForm() {

    },
    resetForm() {
      this.$refs.form.resetFields();
    }
  },
  mounted() {

  }
};
