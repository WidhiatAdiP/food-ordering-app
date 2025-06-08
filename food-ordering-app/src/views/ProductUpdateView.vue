<template lang="">
  <NavBar :name="userName" :role="roleId" />
  <div class="container mt-5">
    <div class="col-12 col-lg-6 mx-auto">
      <div class="card shadow-lg p-4 rounded">
        <h3 class="mb-4 text-center">Update Product</h3>

        <form @submit.prevent="updateProduct()">
          <div class="mb-3">
            <label for="name" class="form-label">Product Name</label>
            <input
              type="text"
              class="form-control form-control-lg"
              v-model="item.name"
              id="name"
              placeholder="Enter product name"
            />
          </div>
          <div class="mb-3">
            <label for="price" class="form-label">Product Price</label>
            <input
              type="number"
              class="form-control form-control-lg"
              v-model="item.price"
              id="price"
              placeholder="Enter product price"
            />
          </div>
          <div class="mb-3">
            <label for="image" class="form-label">Current Image</label> <br />
            <img
              :src="url + item.image"
              v-if="item.image"
              class="object-fit-cover"
              style="width: 100px; height: 100px"
            />
            <img
              v-else
              src="@/assets/images/not-available.jpg"
              class="object-fit-cover"
              style="width: 100px; height: 100px"
            />
          </div>
          <div class="mb-3">
            <label for="image" class="form-label">Product Image</label>
            <input
              type="file"
              class="form-control form-control-lg"
              @change="imageChanged($event)"
              id="image"
            />
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
      productId: '',
      item: '',
      file: '',
    }
  },
  mounted() {
    this.productId = this.$route.params.productId
    this.userName = localStorage.getItem('name')
    this.roleId = localStorage.getItem('role_id')

    if (!this.userName || this.userName == '' || this.userName == null) {
      router.push({ name: 'login' })
    }
    if (this.roleId != 4) {
      router.push({ name: 'home' })
    }

    this.showItem()
  },
  methods: {
    showItem() {
      axios
        .get('http://127.0.0.1:8000/api/item/' + this.productId, {
          headers: {
            'Authorization': `Bearer ${localStorage.getItem('token')}`,
          },
        })
        .then((response) => {
          this.item = response.data.data
        })
        .catch(function (error) {
          console.log(error)
          console.log('error fetch items')
        })
    },
    updateProduct() {
      if (this.item.name == '' || this.item.price == '') {
        alert('Data cannot be empty!')
        return
      }

      let formData = new FormData()
      formData.append('name', this.item.name)
      formData.append('price', this.item.price)
      formData.append('image_file', this.file)
      formData.append('_method', 'patch')

      axios
        .post('http://127.0.0.1:8000/api/item/'+this.productId, formData, {
          headers: {
            'Authorization': `Bearer ${localStorage.getItem('token')}`,
          },
        })
        .then((response) => {
          // Jika update berhasil, redirect ke halaman produk
          console.log('Update successful:', response);
          router.push({ name: 'product' })
        })
        .catch((error) => {
          // Tampilkan error jika update gagal
          alert('Failed to update product. Please try again.')
          console.log('Error details:', error)
        })
    },
    imageChanged(e) {
      let file = e.target.files[0]
      this.file = file
    }
  }, // <-- Ini yang sebelumnya hilang
}
</script>

<style lang=""></style>
