<template>
    <div>
        <el-card class="box-card mb-1" v-for="(newsletter, index)  in newsletters" :key="index">
            <div slot="header" class="clearfix x">
                <span>{{newsletter.title}}</span>
            </div>
            <iframe v-bind:src="newsletterUrl(newsletter)" width="100%" height="350px">
            </iframe>
        </el-card>
    </div>
</template>

<script src="./Home.js">
</script>

<style scoped lang="scss" src="./Home.scss">
</style>
