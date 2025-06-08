<template>
  <NavBar :name="userName" :role="roleId" />
  <div class="container">
    <h2 class="my-5">Product List</h2>

    <a href="/product-add" class="btn btn-success mb-3">Add Product</a>

    <div v-if="items.length === 0" class="alert alert-warning">
      No products available. Please add some products.
    </div>

    <table v-else class="modern-table table-striped">
      <thead>
        <tr>
          <th scope="col">#</th>
          <th scope="col">Name</th>
          <th scope="col">Price</th>
          <th scope="col">Image</th>
          <th scope="col">Action</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(item, index) in items" :key="index">
          <td>{{ index + 1 }}</td>
          <td>{{ item.name }}</td>
          <td>{{ `Rp. ${item.price}` }}</td>
          <td>
            <img :src=url+item.image v-if="item.image" class="object-fit-cover" style="width: 100px; height: 100px;" />
            <img v-else src="@/assets/images/not-available.jpg" class="object-fit-cover" style="width: 100px; height: 100px">
          </td>
          <td>
            <RouterLink :to="{ name: 'productUpdate', params: { productId: item.id } }">Update</RouterLink>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script>
import NavBar from '@/components/NavBar.vue';
import router from '@/router';
import axios from 'axios';

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
    };
  },
  computed: {
    fullImageUrl() {
      return (imagePath) => {
        if (!imagePath) return '@/assets/images/not-available.jpg';
        return this.url + imagePath;
      };
    },
  },
  mounted() {
    this.userName = localStorage.getItem('name') || '';
    this.roleId = localStorage.getItem('role_id') || '';

    if (!this.userName) {
      console.error('No userName found in localStorage');
      return router.push({ name: 'login' });
    }

    if (this.roleId !== '4') {
      console.warn('Unauthorized roleId:', this.roleId);
      return router.push({ name: 'home' });
    }

    this.getItems();
  },
  methods: {
    getItems() {
      axios
        .get('http://127.0.0.1:8000/api/item', {
          headers: {
            Authorization: `Bearer ${localStorage.getItem('token')}`,
          },
        })
        .then((response) => {
          console.log('Response:', response.data);
          if (response.data && Array.isArray(response.data.data)) {
            this.items = response.data.data;
          } else {
            console.error('Invalid response structure:', response.data);
            this.items = [];
          }
        })
        .catch(function (error) {
          if(error.response.status == 401) {
            localStorage.removeItem('token')
            localStorage.removeItem('email')
            localStorage.removeItem('name')
            localStorage.removeItem('role_id')

            return router.push({ name: 'login' });
          }
          console.log(error.response.status)
          console.error('Error fetching items:', error);
        });
    },
  },
};
</script>

<style scoped>
.modern-table {
  width: 100%;
  border-collapse: collapse;
  margin: 20px 0;
  font-size: 16px;
  text-align: left;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
  border-radius: 10px;
  overflow: hidden;
  background-color: #ffffff;
}

.modern-table thead {
  background-color: #3b82f6;
  color: #ffffff;
}

.modern-table th,
.modern-table td {
  padding: 14px 16px;
  text-align: center;
  vertical-align: middle;
}

.modern-table tbody tr:nth-child(even) {
  background-color: #f3f4f6;
}

.modern-table tbody tr:nth-child(odd) {
  background-color: #ffffff;
}

.modern-table tbody tr:hover {
  background-color: #e5e7eb;
  cursor: pointer;
  transition: background-color 0.3s ease-in-out;
}

.modern-table image {
  width: 60px;
  height: 60px;
  border-radius: 8px;
  border: 2px solid #d1d5db;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
  object-fit: contain;
  background-color: #f9fafb;
}

.modern-table a {
  color: #2563eb;
  font-weight: bold;
  text-decoration: none;
  padding: 6px 15px;
  border: 1px solid #2563eb;
  border-radius: 5px;
  transition: all 0.3s ease;
}

.modern-table a:hover {
  background-color: #2563eb;
  color: #ffffff;
}
</style>
