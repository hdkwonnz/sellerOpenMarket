<template>
    <div>
        <alert v-model="showAlert" placement="top-right" duration="40000" type="success" width="400px" dismissable>
            <span class="icon-ok-circled alert-icon-float-left"></span>
            <strong>Order Status Updated</strong>
            <p>${{ orderAmount }} has been ordered.</p>
        </alert>
    </div>
</template>

<script>
    import { alert } from 'vue-strap'
    export default {
        components: {
            alert
        },

        props:['user_role'],

        data(){
            return{
                productName: "",
                orderAmount:"",
                showAlert: false,
            }
        },

        methods: {

        },

        created() {


        },

        mounted() {
            // Echo.private('notice-seller')
            // .listen('NoticeToSellerEvent', (product) => {
            //     // console.log(product);
            //     if (this.user_role == 'seller'){
            //         this.productName = product.name;
            //         this.showAlert = true;
            //     }
            // });

            Echo.private('notice-seller')
            .listen('NoticeToSellerEvent', (order) => {
                // console.log(product);
                if (this.user_role == 'seller'){
                    this.orderAmount = order.total_amount;
                    this.showAlert = true;
                }
            });
        }
    }
</script>
