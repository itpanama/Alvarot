import {mapGetters} from 'vuex';

export default {
  name: "delay-form",
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
        return_date: "",
        demurrage_amount_to_be_paid: 0.00,
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
    },
    calculateDemurrageAmountToBePaid() {
      let fecha1 = moment(dataForm.discharge_date);
      let fecha2 = moment(dataForm.return_date);

      if (fecha1.isValid() && fecha2.isValid()) {
        console.log(fecha2.diff(fecha1, 'days'), ' horas de diferencia');
      } else {
        dataForm.demurrage_amount_to_be_paid = 0.00;
      }
    }
  },
  mounted() {

  },
  watch: {
    "dataForm.return_date": function () {
      this.calculateDemurrageAmountToBePaid();
    },
    "dataForm.discharge_date": function () {
      this.calculateDemurrageAmountToBePaid();
    },
  }
};
