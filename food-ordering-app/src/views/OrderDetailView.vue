<template lang="">
  <div>
    <NavBar :name="userName" :role="roleId" />

    <div class="container mt-5">
      <div class="card shadow-sm border-0">
        <div class="card-header">
          <h4 class="fw-bold mb-0">Detail Pesanan</h4>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table order-details-table text-center align-middle">
              <tbody>
                <tr>
                  <td><strong>Nama Pelanggan:</strong> {{ order.customer_name }}</td>
                  <td><strong>Nomor Meja:</strong> {{ order.table_no }}</td>
                  <td><strong>Tanggal Pesanan:</strong> {{ order.order_date }}</td>
                  <td><strong>Status:</strong> {{ order.status }}</td>
                </tr>
                <tr>
                  <td>
                    <strong>Pelayan:</strong> {{ order.waitress ? order.waitress.name : '-' }}
                  </td>
                  <td><strong>Kasir:</strong> {{ order.cashier ? order.cashier.name : '-' }}</td>
                  <td><strong>Waktu Pesanan:</strong> {{ order.order_time }}</td>
                  <td><strong>Total:</strong> Rp. {{ order.total }}</td>
                </tr>
              </tbody>
            </table>
          </div>

          <hr />

          <div class="card shadow-sm border-0">
            <div class="card-header">
              <h4 class="fw-bold mb-0">Detail Item</h4>
            </div>
            <table class="table item-details-table">
              <thead>
                <tr>
                  <td><strong>#</strong></td>
                  <td><strong>Nama Item</strong></td>
                  <td><strong>Harga</strong></td>
                  <td><strong>Qty</strong></td>
                  <td><strong>Total</strong></td>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(item, index) in order.order_detail" :key="index">
                  <td>{{ index + 1 }}</td>
                  <td>{{ item.item ? item.item.name : '-' }}</td>
                  <td>Rp. {{ item.price }}</td>
                  <td>{{ item.qty }}</td>
                  <td>Rp. {{ item.price * item.qty }}</td>
                </tr>
              </tbody>
            </table>
            <div class="mt-3">
              <button
                v-if="order.status == 'ordered' && roleId == 2"
                class="btn btn-primary"
                @click="setAsDone(order.id)"
              >
                Selesai
              </button>
              <button
                v-if="order.status == 'done' && (roleId == 3 || roleId == 4)"
                class="btn btn-success"
                @click="setAsPaid(order.id)"
              >
                Dibayar
              </button>
            </div>
            <br>
            <div class="mb-4 text-center">
              <router-link to="/order-list" class="btn btn-secondary w-100">Kembali</router-link>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import router from '@/router'
import NavBar from '@/components/NavBar.vue'
import axios from 'axios'

export default {
  components: {
    NavBar,
  },
  data() {
    return {
      userName: '',
      roleId: '',
      order: '',
    }
  },
  mounted() {
    this.userName = localStorage.getItem('name')
    this.roleId = localStorage.getItem('role_id')

    if (!this.userName || this.userName == '' || this.userName == null) {
      router.push({ name: 'login' })
    }
    this.getOrder()
  },
  methods: {
    getOrder() {
      axios
        .get('http://127.0.0.1:8000/api/order/' + this.$route.params.orderId, {
          headers: {
            Authorization: `Bearer ${localStorage.getItem('token')}`,
          },
        })
        .then((response) => {
          console.log(response.data.data)
          this.order = response.data.data
        })
        .catch(function (error) {
          if (error.response.status == 401) {
            localStorage.removeItem('token')
            localStorage.removeItem('email')
            localStorage.removeItem('name')
            localStorage.removeItem('role_id')

            return router.push({ name: 'login' })
          }
          console.log(error.response.status)
          console.error('Error fetching items:', error)
        })
    },
    setAsDone(orderId) {
      axios
        .get('http://127.0.0.1:8000/api/order/' + orderId + '/set-as-done', {
          headers: {
            Authorization: `Bearer ${localStorage.getItem('token')}`,
          },
        })
        .then((response) => {
          if (response.status == 200) {
            alert('Order status change to DONE successfully.')
          }
          this.order = response.data.data
        })
        .catch(function (error) {
          if (error.response.status == 401) {
            localStorage.removeItem('token')
            localStorage.removeItem('email')
            localStorage.removeItem('name')
            localStorage.removeItem('role_id')

            return router.push({ name: 'login' })
          }
          console.log(error.response.status)
          console.error('Error fetching items:', error)
        })
    },
    setAsPaid(orderId) {
      axios
        .get('http://127.0.0.1:8000/api/order/' + orderId + '/payment', {
          headers: {
            Authorization: `Bearer ${localStorage.getItem('token')}`,
          },
        })
        .then((response) => {
          if (response.status == 200) {
            alert('Order status change to PAID successfully.')
          }
          this.order = response.data.data
        })
        .catch(function (error) {
          if (error.response.status == 401) {
            localStorage.removeItem('token')
            localStorage.removeItem('email')
            localStorage.removeItem('name')
            localStorage.removeItem('role_id')

            return router.push({ name: 'login' })
          }
          console.log(error.response.status)
          console.error('Error fetching items:', error)
        })
    },
  },
}
</script>

<style>
body {
  font-family: 'Arial', sans-serif; /* Gunakan font yang lebih modern */
  background-color: #f8f9fa; /* Warna latar belakang yang lebih cerah */
  color: #333; /* Warna teks yang lebih gelap untuk kontras */
}

.card {
  border: none;
  border-radius: 12px;
  box-shadow: 0 8px 30px rgba(0, 0, 0, 0.2);
  overflow: hidden;
  transition:
    transform 0.3s,
    box-shadow 0.3s;
}

.card:hover {
  transform: translateY(-5px);
  box-shadow: 0 12px 40px rgba(0, 0, 0, 0.3);
}

.card-header {
  background: linear-gradient(90deg, #007bff, #0056b3);
  color: white;
  padding: 20px;
  font-size: 1.5em;
  text-align: center;
  border-bottom: 2px solid #28a745; /* Garis bawah untuk penekanan */
}

.table {
  margin-bottom: 0;
  font-size: 16px;
  width: 100%;
  border-collapse: collapse;
}

.table th {
  background-color: #343a40;
  color: #ffffff;
  padding: 15px;
  text-align: left;
  font-weight: bold;
}

.table td {
  color: #555;
  padding: 15px;
  border-bottom: 1px solid #e0e0e0;
  transition: background-color 0.3s;
}

.table tbody tr:hover {
  background-color: rgba(0, 123, 255, 0.1);
}

.table tbody tr:nth-child(even) {
  background-color: rgba(240, 240, 240, 0.5);
}

.btn {
  border-radius: 5px;
  transition:
    background-color 0.3s,
    transform 0.3s;
  padding: 10px 20px; /* Tambahkan padding untuk ukuran tombol yang lebih baik */
}

.btn-primary {
  background-color: #007bff;
  color: #ffffff;
}

.btn-primary:hover {
  background-color: #0056b3;
  transform: scale(1.05);
}

.btn-success {
  background-color: #28a745;
  color: #ffffff;
}

.btn-success:hover {
  background-color: #218838;
  transform: scale(1.05);
}

.btn-secondary {
  background-color: #6c757d;
  color: #ffffff;
}

.btn-secondary:hover {
  background-color: #5a6268;
  transform: scale(1.05);
}

/* Tambahan gaya untuk spasi yang lebih baik */
.container {
  padding: 20px; /* Tambahkan padding pada kontainer */
}

.mt-5 {
  margin-top: 3rem; /* Sesuaikan margin untuk spasi yang lebih baik */
}

.mb-4 {
  margin-bottom: 2rem; /* Sesuaikan margin untuk spasi yang lebih baik */
}
</style>
