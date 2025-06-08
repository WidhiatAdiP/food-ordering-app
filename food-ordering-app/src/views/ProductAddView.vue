<template lang="html">
  <NavBar :name="userName" :role="roleId" />

  <div class="container mt-5">
    <div class="col-12 col-lg-6 mx-auto">
      <div class="card shadow-lg p-4 rounded">
        <h3 class="mb-4 text-center">Add New Product</h3>

        <form @submit.prevent="createProduct">
          <div class="mb-4">
            <label for="name" class="form-label">Product Name</label>
            <input type="text" class="form-control form-control-lg modern-input" v-model="name" id="name"
              placeholder="Enter product name" required />
          </div>

          <div class="mb-4">
            <label for="price" class="form-label">Product Price</label>
            <input type="number" class="form-control form-control-lg modern-input" v-model="price" id="price"
              placeholder="Enter product price" required />
          </div>

          <div class="mb-4">
            <label for="image" class="form-label">Product Image</label>
            <input type="file" class="form-control form-control-lg modern-input" @change="imageChanged($event)"
              id="image" />
          </div>

          <div class="mb-4 text-center">
            <button class="btn btn-success btn-lg w-100" type="submit">Save Product</button>
          </div>
          <div class="mb-4 text-center">
            <router-link to="/product" class="btn btn-danger w-100">Cancel</router-link>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import NavBar from '@/components/NavBar.vue'
import router from '@/router'
import axios from 'axios'

export default {
  components: {
    NavBar,
  },
  data() {
    return {
      userName: '',
      roleId: '',
      items: [],
      url: 'http://127.0.0.1:8000/storage/items/',
      name: '',
      price: '',
      file: '',
    }
  },
  mounted() {
    this.userName = localStorage.getItem('name')
    this.roleId = localStorage.getItem('role_id')

    if (!this.userName || this.userName == '' || this.userName == null) {
      router.push({ name: 'login' })
    }
    if (this.roleId != 4) {
      router.push({ name: 'home' })
    }
  },
  methods: {
    createProduct() {
      if (this.name == '' || this.price == '') {
        alert('Data cannot be empty!')
        return
      }

      let formData = new FormData()
      formData.append('name', this.name)
      formData.append('price', this.price)
      formData.append('image_file', this.file)

      axios
        .post('http://127.0.0.1:8000/api/item', formData, {
          headers: {
            Authorization: `Bearer ${localStorage.getItem('token')}`,
            // 'content-type' : 'multipart/form-data'
          },
        })
        .then((response) => {
          console.log('Product created:', response.data)
          router.push({ name: 'product' })
        })
        .catch((error) => {
          console.log(error)
          alert('Failed to create product. Please try again.')
        })
    },
    imageChanged(e) {
      let file = e.target.files[0]
      this.file = file
    }
  },
}
</script>

<style scoped>
.modern-input {
  padding: 14px 16px;
  font-size: 16px;
  border-radius: 8px;
  border: 1px solid #d1d5db;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
  transition: all 0.3s ease;
}

.modern-input:focus {
  border-color: #3b82f6;
  box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.3);
}

.modern-btn {
  background-color: #3b82f6;
  color: white;
  border-radius: 8px;
  padding: 12px;
  font-size: 18px;
  transition: all 0.3s ease;
}

.modern-btn:hover {
  background-color: #2563eb;
  cursor: pointer;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
}
</style>
