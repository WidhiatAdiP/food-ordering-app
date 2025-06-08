<template lang="">
  <div>
    <NavBar :name="userName" :role="roleId" />

    <div class="container-fluid mp-5 mt-3">
      <div class="row">
        <!-- Item List -->
        <div class="col-12 col-sm-8 mb-3">
          <!-- Search Box -->
          <div class="col-12">
            <input type="text" v-model="keyword" class="form-control" placeholder="Search Here" :onchange="searchItem()" />
          </div>

          <hr />

          <!-- Item List Box -->
          <div class="col-12">
            <div class="row">
              <div v-for="item in filteredItems" class="col-12 col-sm-6 col-md-4 col-lg-3 mb-3">
                <div class="card">
                  <img class="card-img-top object-fit-cover" height="250px" :src="url+item.image" alt="Card image cap" />
                  <div class="card-body text-center">
                    <h5 class="card-title">{{ item.name }}</h5>
                    <p class="card-text">Rp. {{ item.price }}</p>
                    <p><button class="btn-success" @click="orderItem(item.id)">Order</button></p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- Ordered Items -->
        <div class="col-12 col-sm-4 mb-3 order-box">
          <h3>Order List</h3>
          <div class="mb-5">
            <div>
              <label for="customerName" class="form-label">Customer Name</label>
              <input type="text" id="customerName" class="form-control" v-model="customerName" />
            </div>
            <div>
              <label for="tableNo" class="form-label">Table Number</label>
              <input type="text" id="tableNo" class="form-control" v-model="tableNo" />
            </div>
          </div>
          <hr>
          <div class="item-box">
            <div  v-for="order in orders" class="mb-3">
              <div class="d-flex justify-content-between">
                <span>{{ order.name }} (x{{ order.qty }})</span>
                <span>Rp. {{ order.price }}</span>
              </div>
              <div>
                <span style="font-size: 14px" class="text-muted">@Rp. 20000</span>
                <div>
                  <button class="btn btn-sm btn-outline-info me-2" @click="decreaseItemQty(order)">-</button>
                  <button class="btn btn-sm btn-outline-success me-2" @click="increaseItemQty(order)">+</button>
                  <button class="btn btn-sm btn-outline-danger me-2" @click="deleteItem(order)">Delete</button>
                </div>
              </div>
            </div>
          </div>
          <hr>
          <div class="total-box">
            <div class="d-flex justify-content-between">
              <span>Total</span>
              <span>Rp. {{ total }}</span>
            </div>
          </div>
          <div class="mt-3">
            <button class="btn btn-success form-control" :disabled=processing @click="submitOrder()">{{ processing ? 'Processing Order...' : 'Submit'}}</button>
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
      items: [],
      filteredItems: [],
      keyword: '',
      url: 'http://127.0.0.1:8000/storage/items/',
      orders: [],
      total: 0,
      customerName: '',
      tableNo: '',
      processing: false,
    }
  },
  mounted() {
    this.userName = localStorage.getItem('name')
    this.roleId = localStorage.getItem('role_id')
    if (!this.userName || this.userName == '' || this.userName == null) {
      router.push({ name: 'login' })
    }
    if (this.roleId != 4 && this.roleId !=1) {
      router.push({ name: 'home' })
    }
    this.getItems()
  },
  methods: {
    getItems() {
      axios
        .get('http://127.0.0.1:8000/api/item', {
          headers: {
            Authorization: `Bearer ${localStorage.getItem('token')}`
          },
        })
        .then((response) => {
          this.items = response.data.data
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
    searchItem() {
      this.filteredItems = this.items.filter(item => item.name.toLowerCase().includes(this.keyword.toLowerCase()))
    },
    orderItem(id) {
      let item = this.filteredItems.filter(item => item.id == id)[0]
      let orderItem = Object.assign({}, item)
      orderItem.eachPrice = item.price
      let indexOfArrayItem = this.orders.map(e => e.id).indexOf(orderItem.id)

      if(indexOfArrayItem != -1) { //jika item yg di order ada di array order, maka tambah qty dan kalikan harga
        this.orders[indexOfArrayItem].qty++
        //price 20k
        //20k * 1 = 20k, eachPrice 20k
        //20k * 2 = 40k, eachPrice 20k
        //20k * 3 = 60k, eachPrice 20k, dst
        this.orders[indexOfArrayItem].price = this.orders[indexOfArrayItem].eachPrice * this.orders[indexOfArrayItem].qty
      }
      else { //jika item yg di order tidak ada di array orders, maka push item ke dalam array order dengan qty 1
        orderItem.qty = 1
        this.orders.push(orderItem)
      }

      let orderPrice = this.orders.map(order => order.price)
      let priceTotal = 0

      orderPrice.forEach(price => {
        priceTotal += price
      })

      this.total = priceTotal
    },
    decreaseItemQty(item) {
      if(item.qty <= 1) {
        return
      }

      let indexOfArrayItem = this.orders.map(e => e.id).indexOf(item.id)
      this.orders[indexOfArrayItem].qty--
      this.orders[indexOfArrayItem].price = this.orders[indexOfArrayItem].eachPrice * this.orders[indexOfArrayItem].qty

      let orderPrice = this.orders.map(order => order.price)
      let priceTotal = 0
      orderPrice.forEach(price => {
        priceTotal += price
      })
      this.total = priceTotal
    },
    increaseItemQty(item) {

      let indexOfArrayItem = this.orders.map(e => e.id).indexOf(item.id)
      this.orders[indexOfArrayItem].qty++
      this.orders[indexOfArrayItem].price = this.orders[indexOfArrayItem].eachPrice * this.orders[indexOfArrayItem].qty

      let orderPrice = this.orders.map(order => order.price)
      let priceTotal = 0
      orderPrice.forEach(price => {
        priceTotal += price
      })
      this.total = priceTotal
    },
    deleteItem(item) {
      let indexOfArrayItem = this.orders.map(e => e.id).indexOf(item.id)
      this.orders.splice(indexOfArrayItem, 1)

      let orderPrice = this.orders.map(order => order.price)
      let priceTotal = 0
      orderPrice.forEach(price => {
        priceTotal += price
      })
      this.total = priceTotal
    },
    submitOrder() {
      if(this.customerName == '' || this.tableNo == '')
    {
      alert('Customer Name and Table Number cannot be empty.')
    }

    let item = this.orders.map(item => {
      return {
        'id': item.id,
        'qty': item.qty
      }
    })

    this.processing = true
      axios
        .post('http://127.0.0.1:8000/api/order', {
          'customer_name' : this.customerName,
          'table_no' : this.tableNo,
          'items' : item
        }, {
          headers: {
            Authorization: `Bearer ${localStorage.getItem('token')}`
          },
        })
        .then((response) => {
          console.log(response)
          alert('Order saved Successfully')
          this.orders = []
          this.customerName = ''
          this.tableNo = ''
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
        })
        .finally(() => this.processing = false);
    }
  },
}
</script>

<style>
.order-box {
  border: 1px solid #d1d1d1; /* Warna border soft */
  border-radius: 12px;       /* Sudut membulat smooth */
  padding: 20px;             /* Spasi dalam lebih lega */
  background: linear-gradient(135deg, #f9f9f9, #ffffff); /* Gradasi background */
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1); /* Bayangan halus */
  font-size: 1.2rem;         /* Ukuran teks */
  font-family: 'Poppins', sans-serif; /* Font modern */
  color: #161515;            /* Warna teks */
  text-align: left;        /* Teks rata kiri */
}
.item-box {
  font-size: 20px;
}
.total-box {
  font-size: 22px;
  font-weight: bold;
}
.card {
  border: 1px solid #e0e0e0;  /* Border soft */
  border-radius: 10px;        /* Sudut membulat */
  overflow: hidden;           /* Supaya gambar tidak keluar */
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Bayangan */
  transition: box-shadow 0.2s ease-in-out;  /* Efek hover (opsional) */
}
.card-text {
  font-size: 0.95rem;         /* Ukuran font harga */
  color: #777777;             /* Warna teks harga */
  margin-bottom: 15px;
}
.btn-success {
  background-color: #28a745;  /* Warna tombol hijau */
  border: none;
  color: #ffffff;             /* Warna teks tombol */
  padding: 8px 12px;
  font-size: 0.9rem;
  border-radius: 5px;         /* Membulat */
  cursor: pointer;
}
.btn-success:hover {
  background-color: #218838;  /* Warna hijau lebih gelap saat hover */
}
@media (max-width: 576px) {
  .card {
    margin-bottom: 20px;      /* Jarak antar card lebih besar di mobile */
  }
}
</style>
