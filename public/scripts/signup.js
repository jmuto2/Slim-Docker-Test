
var signup = new Vue({
    el: '#sign_up',
    data: {
        user: {}
    },
    methods: {
        add: function (event) {
            event.preventDefault();

            var url = "{{ path_for('auth.signup') }}";
            var data = this.user;
            $.post(url, data, function(data) {
                if (data.success) {
                    window.location = "/dashboard";
                } else {
                    window.location = "/home";
                }
            }.bind(this), 'json');
        }
    }
});

