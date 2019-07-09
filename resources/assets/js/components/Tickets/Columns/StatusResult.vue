<template>
    <div>
        <div v-if="ticket.completed === 1">
            COMPLETED<br>
        </div>
        <div v-else :style="getStyle"></div>
    </div>
</template>

<script>
  export default {
    name: "StatusResult",
    props: ["ticket"],
    computed: {
      getStyle() {
        return {
          backgroundColor: this.ticket.color_hex,
          height: '20px',
          width: '100px'
        }
      }
    },
  }
</script>

<style scoped>

</style>