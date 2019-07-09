<template>
    <el-container>
        <el-main>
            <div>
                <h1><i class="fas fa-exclamation-triangle"></i> Error Page</h1>
                <el-button type="warning" @click="goToLogin()">Go to login</el-button>

                <form id="logout-form" :action="urlLogout" method="POST" style="display: none;">
                    <input type="hidden" name="_token" :value="token">
                </form>
            </div>
        </el-main>
    </el-container>
</template>
<script>
  export default {
    name: "ErrorPage",
    data() {
      return {
        urlLogout: `${this.$baseUrl}/logout`,
        token: this.$token,
      };
    },
    methods: {
      goToLogin() {
        let loadingInstance = this.$loading();
        document.getElementById("logout-form").submit();
        setTimeout(() => {
          loadingInstance.close();
        }, 500);
      }
    }
  };
</script>
<style scoped lang="scss"></style>
