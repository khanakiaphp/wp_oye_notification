var module_notificationBar = {
    init: function() {
        var _this = this;
        this.el = (".notification-bar");
        this.$el = jQuery(this.el);
        this.$elCloseButton = jQuery(".notification-bar__inner__closebtn");
        this.$elCloseButton.click(function() {
            jQuery( this).parents(_this.el).fadeOut( "slow", function() {
                _this.setBodyMargin();
            });
            return false;
        });
        
        this.setBodyMargin();
    },

    setBodyMargin: function() {
        var numItems = this.$el.filter(":visible").length;
        if (numItems == 0){
            jQuery("body").css("margin-top", "");
        } else {
            jQuery("body").css("margin-top", "50px");
        }
    }
}

jQuery(document).ready(function(){
	module_notificationBar.init();
});
