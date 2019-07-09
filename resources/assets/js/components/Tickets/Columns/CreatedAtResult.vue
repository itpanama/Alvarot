<template>
    <div class="created-at-result">

        <div v-show="!ticket.completed">
            <el-tooltip placement="top-start">
                <div slot="content">Created at: {{ticket.ticket_created_format}}</div>
                <i class="el-icon-info"></i>
            </el-tooltip>
            <span v-if="ticket.created_at_days">{{ticket.created_at_days}} day(s), {{ticket.created_at_hours}} hour(s), {{ticket.created_at_minutes}} minute(s)</span>
            <span v-else-if="ticket.created_at_hours">{{ticket.created_at_hours}} hour(s), {{ticket.created_at_minutes}} minute(s)</span>
            <span v-else-if="ticket.created_at_minutes">{{ticket.created_at_minutes}} minute(s) ago</span>
            <span v-else>Just Now</span>
        </div>
        <div v-show="ticket.completed">
            {{ticket.ticket_created_format}}
        </div>
    </div>
</template>

<script>
  export default {
    name: "CreatedAtResult",
    props: ["ticket"],
  }
</script>

<style scoped>
    div /deep/ .created-at-result {
        font-size: 9px !important;
    }
</style>