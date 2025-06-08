<template lang="">
  <div>
    <NavBar :name="userName" :role="roleId" />

    <div class="container mt-5">
      <h2 class="text-center mb-3">Order List</h2>
      <div class="modern-table-wrapper">
        <table class="table table-bordered table-striped modern-table">
          <thead class="bg-dark text-white">
            <tr>
              <th scope="col">#</th>
              <th scope="col">Customer Name</th>
              <th scope="col">Table Number</th>
              <th scope="col">Order Date</th>
              <th scope="col">Order Time</th>
              <th scope="col">Total</th>
              <th scope="col">Status</th>
              <th scope="col">Witress</th>
              <th scope="col">Cashier</th>
              <th scope="col">Action</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(order, index) in orders" :key="index">
              <td>{{ index + 1 }}</td>
              <td>{{ order.customer_name }}</td>
              <td>{{ order.table_no }}</td>
              <td>{{ order.order_date }}</td>
              <td>{{ order.order_time }}</td>
              <td>{{ order.total }}</td>
              <td class="fw-bold text-center" :class="{ 'text-success': order.status }">
                {{ order.status }}
              </td>
              <td>{{ order.waitress.name }}</td>
              <td>{{ order.cashier ? order.cashier.name : '' }}</td>
              <td>
                <RouterLink :to="{ name: 'orderDetail', params: { orderId: order.id } }"
                  >View Details</RouterLink
                >
              </td>
            </tr>
          </tbody>
        </table>
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
      orders: [],
    }
  },
  mounted() {
    this.userName = localStorage.getItem('name')
    this.roleId = localStorage.getItem('role_id')

    if (!this.userName || this.userName == '' || this.userName == null) {
      router.push({ name: 'login' })
    }
    this.getOrders()
  },
  methods: {
    getOrders() {
      axios
        .get('http://127.0.0.1:8000/api/order', {
          headers: {
            Authorization: `Bearer ${localStorage.getItem('token')}`,
          },
        })
        .then((response) => {
          console.log(response.data.data)
          this.orders = response.data.data
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
<style scoped>
/* Container styling */
.container {
  background-color: #f9f9f9;
  padding: 20px;
  border-radius: 8px;
  box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
}

/* Table styling */
.modern-table {
  border-collapse: collapse;
  width: 100%;
  margin-top: 20px;
}

.modern-table th,
.modern-table td {
  text-align: center;
  padding: 12px 15px;
  border: 1px solid #ddd;
  cursor: default;
}

.view-details-btn {
  background: none;
  border: none;
  color: #007bff;
  font-weight: bold;
  text-decoration: underline;
  cursor: pointer; /* Pointer hanya di sini */
}

.view-details-btn:hover {
  color: #0056b3;
}

.modern-table thead th {
  background-color: #212529;
  color: white;
  font-size: 16px;
}

.modern-table tbody tr:nth-child(even) {
  background-color: #f2f2f2;
}

.modern-table tbody tr:hover {
  background-color: #eaf4ff;
  cursor: pointer;
}

.modern-table td {
  font-size: 14px;
  color: #333;
}

/* Status column styling */
.text-success {
  color: #28a745 !important;
}

.fw-bold {
  font-weight: bold;
}

/* Action button styling */
.modern-table td:last-child {
  color: #007bff;
  font-weight: bold;
  text-decoration: underline;
  cursor: pointer;
}

.modern-table td:last-child:hover {
  color: #0056b3;
}

/* Responsive styles */
@media (max-width: 768px) {
  .container {
    padding: 15px;
  }

  .modern-table th,
  .modern-table td {
    font-size: 12px;
    padding: 8px;
  }

  .modern-table {
    font-size: 14px;
  }

  .modern-table th,
  .modern-table td {
    white-space: nowrap;
  }

  /* Enable horizontal scrolling for smaller screens */
  .modern-table-wrapper {
    overflow-x: auto;
  }
}

@media (max-width: 480px) {
  .container {
    padding: 10px;
  }

  .modern-table {
    font-size: 12px;
  }

  .modern-table th,
  .modern-table td {
    font-size: 11px;
    padding: 6px;
  }
}

/* Wide screens */
@media (min-width: 1200px) {
  .modern-table th,
  .modern-table td {
    font-size: 16px;
  }

  .modern-table {
    width: 80%;
    margin: 0 auto;
  }
}
</style>
