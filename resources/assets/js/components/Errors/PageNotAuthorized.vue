<template>
    <div>
        <el-card class="box-card">
            <div class="text-center">
                <h1>
                    <i class="fas fa-exclamation-triangle text-danger"></i>
                    <div>Not authorized</div>
                </h1>
                <div>
                    You do not have credentials to access this resource
                </div>
            </div>
        </el-card>
    </div>
</template>
<script>
  export default {
    name: "Page404",
    data() {
      return {};
    },
  };
</script>
<style scoped lang="scss"></style>
