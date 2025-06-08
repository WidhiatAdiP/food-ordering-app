<template lang="">
  <div class="container d-flex justify-content-center align-items-center vh-100">
    <div class="card p-4 shadow" style="width: 400px">
      <h3 class="text-center mb-4">Login</h3>
      <form @submit.prevent="login">
        <!-- Username input -->
        <div class="mb-3">
          <label for="email" class="form-label">Email</label>
          <input
            type="email"
            v-model="email"
            class="form-control"
            id="email"
            placeholder="Enter your Email"
            required
          />
        </div>
        <!-- Password input -->
        <div class="mb-3">
          <label for="password" class="form-label">Password</label>
          <input
            type="password"
            v-model="password"
            class="form-control"
            id="password"
            placeholder="Enter your password"
            required
          />
        </div>
        <!-- Submit Button -->
        <button @click="login()" class="btn btn-success w-100">Login</button>
        <!-- Sign Up Link -->
        <!-- <div class="text-center mt-3">
          <p>Don't have an account? <a href="#" class="text-decoration-none">Sign up</a></p>
        </div> -->
      </form>
    </div>
  </div>
</template>

<script>
import router from '@/router';
import axios from 'axios'

export default {
  data() {
    return {
      email: "",
      password: ""
    }
  },
  methods: {
    login() {
      axios.post('http://127.0.0.1:8000/api/auth/login', {
        email: this.email,
        password: this.password
      })
        .then(function (response) {
          localStorage.setItem('email', response.data.data.email)
          localStorage.setItem('name', response.data.data.name)
          localStorage.setItem('role_id', response.data.data.role_id)
          localStorage.setItem('token', response.data.data.token)
          router.push({ name: 'home' })
        })
        .catch(function (error) {
          console.log(error);
        });
    }
  },
  mounted() {
    this.userName = localStorage.getItem('name')
    if(this.userName) {
      router.push({ name: 'home' })
    }
  },
}
</script>
<style lang=""></style>
