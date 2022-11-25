

let app = new Vue({
    el: '#app',
    data: {
        tab:1
    },
    methods: {
        test: function () {
            if(this.tab == 1) {
                this.tab = 2;
            } else {
                this.tab = 1;
            }
        }
    }
})


$(document).ready(function() {

} );

