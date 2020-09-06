<template>
    <li class="nav-item dropdown">
        <a href="#" id="navbarDropdown" class="nav-link dropdown-toggle" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <span class="text-danger">
                <i class="fa fa-bell"></i>
            </span>
            <span class="notification-count label label-danger" v-if="notifications.length > 0">{{ notifications.length }}</span>
            <span class="caret"></span>
        </a>
        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
            <div v-for="notification in notifications" :key="notification.index">
                <a :href="notification.url" target="_blank">
                    <div>
                        <i class="fa fa-exclamation-circle fa-fw"></i> {{notification.description}}
                        <span class="pull-right text-muted small"><timeago :datetime="notification.time" :auto-update="60"></timeago></span>
                    </div>
                </a>
                <div class="divider"></div>
            </div>
            <div class="text-center see-all-notifications">
                <div v-if="notifications.length < 1">No notifications</div>
            </div>
        </div>
    </li>
</template>

<script>
    import VueTimeago from 'vue-timeago'

    Vue.use(VueTimeago, {
        name: 'Timeago', // Component name, `Timeago` by default
        locale: 'en', // Default locale
        // We use `date-fns` under the hood
        // So you can use all locales from it
        locales: {
            'zh-CN': require('date-fns/locale/zh_cn'),
            ja: require('date-fns/locale/ja')
        }
    })

    export default {
        props:['user_role'],

        data(){
            return{
                 notifications: []
            }
        },

        mounted() {
            // Echo.channel('pizza-tracker')
            // .listen('OrderStatusChanged', (order) => {
            //     if (this.user_id == order.user_id) {
            //         this.notifications.unshift({
            //             description: 'Order ID: ' + order.id + ' updated',
            //             url: '/orders/' + order.id,
            //             time: new Date()
            //         })
            //     }
            // });

            Echo.private('notice-seller')
            .listen('NoticeToSellerEvent', (product) => {
                console.log(product);
                if (this.user_role == 'seller'){
                    this.notifications.unshift({
                        description: 'Product Name: ' + product.name + ' updated',
                        url: '/seller/product/showProductById/' + product.id,
                        time: new Date()
                    })
                }
            });
        }
    }
</script>
