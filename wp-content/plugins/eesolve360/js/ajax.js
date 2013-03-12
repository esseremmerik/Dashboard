
jQuery(function() {
        jQuery.ajax({
            type: "GET",
            data: "v1=",
            url: "/wp-content/plugins/eesolve360/phonelist.php",
              statusCode: {
    404: function() {
      alert("page not found");
    }
  },
            success: function(html) {
                 // html is a string of all output of the server script.
                jQuery("#phonelist").html(html);
                
           }

        });
    setInterval(function() {
        jQuery.ajax({
            type: "GET",
            data: "v1=",
            url: "/wp-content/plugins/eesolve360/phonelist.php",
              statusCode: {
    404: function() {
      alert("page not found");
    }
  },
            success: function(html) {
                 // html is a string of all output of the server script.
                jQuery("#phonelist").html(html);
                
           }

        });
    }, 5000);
});