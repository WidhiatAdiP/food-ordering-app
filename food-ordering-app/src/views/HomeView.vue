<template lang="">
  <!-- ini navbar komponen -->
   <NavBar :name=userName :role=roleId />
  <div>
    {{ userName }}
  </div>
</template>

<script>
import router from '@/router'
import NavBar from '@/components/NavBar.vue'

export default {
  components: {
    NavBar
  },
  data () {
  return {
    userName: '',
    roleId: ''
    }
  },
  mounted() {
    this.userName = localStorage.getItem('name')
    this.roleId = localStorage.getItem('role_id')
    if(!this.userName || this.userName == '' || this.userName == null) {
      router.push({ name: 'login' })
    }
  },
}
</script>
<style lang="">

</style>
