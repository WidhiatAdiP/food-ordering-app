<template lang="">
  <header v-if="$route.name != 'login'">
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
  <div class="container-fluid">
    <a class="navbar-brand fw-bold text-primary" href="#">Nusa Restaurant</a>
    <button
      class="navbar-toggler"
      type="button"
      data-bs-toggle="collapse"
      data-bs-target="#navbarSupportedContent"
      aria-controls="navbarSupportedContent"
      aria-expanded="false"
      aria-label="Toggle navigation"
    >
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <RouterLink to="/" class="nav-link">Home</RouterLink>
        </li>
        <li v-if="role == 4 || role == 1" class="nav-item">
          <RouterLink to="/order" class="nav-link">Order</RouterLink>
        </li>
        <li class="nav-item">
          <RouterLink to="/order-list" class="nav-link">Order List</RouterLink>
        </li>
        <li v-if="role == 4" class="nav-item">
          <RouterLink to="/order-report" class="nav-link">Order Report</RouterLink>
        </li>
        <li v-if="role == 4" class="nav-item">
          <RouterLink to="/product" class="nav-link">Product</RouterLink>
        </li>
      </ul>
      <ul class="navbar-nav ms-auto">
        <li class="nav-item">
          <RouterLink to="#" class="nav-link text-danger" @click="logout">Logout</RouterLink>
        </li>
        <li class="nav-item d-flex align-items-center ms-3">
          <span class="user-greeting">Hi, {{ name }}!</span>
        </li>
      </ul>
    </div>
  </div>
</nav>
  </header>
</template>

<script>
import axios from 'axios'
import router from '@/router'

export default {
  props: ['name', 'role'],
  methods: {
    async logout() {
      try {
        // Logout API request
        axios.get('http://127.0.0.1:8000/api/auth/logout', {
          headers: {
            "Authorization": `Bearer ${localStorage.getItem('token')}`,
          },
        });
        // Clear localStorage setelah logout
        localStorage.removeItem('email');
        localStorage.removeItem('name');
        localStorage.removeItem('role_id');
        localStorage.removeItem('token');
        // Redirect ke halaman login
        router.push({ name: 'login' });
      } catch (error) {
        console.log(error);
      }
    }
  },
}
</script>
<style scoped>
.navbar {
  background-color: #ffffff; /* Clean white background */
  padding: 10px 20px; /* Padding for a spacious feel */
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); /* Subtle shadow for depth */
}

.navbar-brand {
  font-size: 1.75rem; /* Larger brand font */
  color: #007bff; /* Primary color */
}

.navbar-toggler {
  border: none; /* Remove border */
}

.nav-link {
  color: #333; /* Darker text color for better readability */
  padding: 10px 15px; /* Padding for better spacing */
  transition: color 0.3s ease; /* Smooth transition for hover effect */
}

.nav-link:hover {
  color: #007bff; /* Change to primary color on hover */
  text-decoration: underline; /* Underline on hover */
}

.text-danger {
  color: #dc3545; /* Danger color for logout */
}

.user-greeting {
  color: #333; /* Match user greeting color with nav link */
  font-weight: 500; /* Medium weight for visibility */
  margin-left: 15px; /* Spacing from the logout link */
}
</style>
