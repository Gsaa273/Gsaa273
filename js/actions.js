$(document).ready(function () {
    cathome();
    brand();
    product();
    window.start_load = function (parent = 'body') {
        $(parent).append('<div id="preloader2"></div>');
    }
    window.end_load = function () {
        $('body #preloader2').remove();
    }


    /**
     * Fetch and display homepage categories using AJAX.
     */
    function cathome() {
        $.ajax({
            url: "action.php", method: "POST", data: {categoryhome: 1}, success: function (data) {
                $("#get_category_home").html(data);

            }
        })
    }

    /**
     * Fetch and display available brands using AJAX.
     */
    function brand() {
        $.ajax({
            url: "action.php", method: "POST", data: {brand: 1}, success: function (data) {
                $("#get_brand").html(data);
            }
        })
    }

    /**
     * Fetch and display products based on the search query using AJAX.
     */
    function product() {

        $.ajax({
            url: "action.php",
            method: "POST",
            data: {getProduct: 1, search: $('#search').val()},
            success: function (data) {
                $("#get_product").html(data);
            }
        })
    }


    /*	when page is load successfully then there is a list of categories when user click on category we will get category id and
        according to id we will show products
    */
    $("body").delegate(".categoryhome", "click", function (event) {
        $("#get_product").html("<h3>Loading...</h3>");
        event.preventDefault();
        var cid = $(this).attr('cid');

        $.ajax({
            url: "actions.php", method: "POST", data: {get_seleted_Category: 1, cat_id: cid}, success: function (data) {
                $("#get_product").html(data);
                if ($("body").width() < 480) {
                    $("body").scrollTop(683);
                }
            }
        })

    })

    /*	when page is load successfully then there is a list of brands when user click on brand we will get brand id and
        according to brand id we will show products
    */
    $("body").delegate(".selectBrand", "click", function (event) {
        event.preventDefault();
        $("#get_product").html("<h3>Loading...</h3>");
        var bid = $(this).attr('bid');

        $.ajax({
            url: "action.php", method: "POST", data: {selectBrand: 1, brand_id: bid}, success: function (data) {
                $("#get_product").html(data);
                if ($("body").width() < 480) {
                    $("body").scrollTop(683);
                }
            }
        })

    })
    /*
        At the top of page there is a search box when user put name of product then we will take the user
        given string and with the help of sql query we will match user given string to our database keywords column then matched product
        we will show
    */
    $('#filter-search').submit(function (e) {
        e.preventDefault()
        $("#get_product").html("<h3>Loading...</h3>");
        var keyword = $("#search").val();
        if (keyword != "") {
            location.replace('store.php?filter=' + keyword)
        }
    })
    //end
    /*
        Here #login is the login form's id. This form is available in the header.php file
        and allows users to log in. When the form is submitted, the input data is sent
        to login.php for processing.

        If the server responds with "login_success," the user is successfully logged in,
        and the page reloads to reflect their authenticated state.

        If the user is unconfirmed, the system utilizes AWS Cognito through the CloudServices
        class to resend a confirmation code and display the confirmation code input form.
    */
    $("#login").on("submit", function (event) {
        event.preventDefault(); // Prevent default form submission behavior
        start_load(this); // Show loading animation
        $(this).find('[type="submit"]').first().attr('disabled', true); // Disable submit button to prevent multiple submissions

        // Send login data to the server
        $.ajax({
            url: "login.php", // Server endpoint for login processing
            method: "POST", // HTTP POST request
            data: $("#login").serialize(), // Serialize form data for transmission
            success: function (data) {
                console.log(data); // Log server response for debugging
                try {
                    data = JSON.parse(data); // Attempt to parse JSON response
                } catch (e) {
                    console.error(e); // Log any parsing errors
                }

                // If the user is unconfirmed, show the confirmation code input form
                if (data.confirm) {
                    $.ajax({
                        url: "confirm_code.php?email=" + data.email, // Fetch the confirmation code form
                        success: function (confrm) {
                            console.log(confrm); // Log confirmation form HTML
                            $('#login').replaceWith(confrm); // Replace the login form with the confirmation code form
                        }, error: function (xhr) {
                            console.error(xhr); // Log any errors
                        }
                    });
                } else if (data === "login_success") {
                    // If login is successful, reload the page
                    window.location.reload();
                } else {
                    // Display any error messages from the server
                    $('#msg').html(data);
                }

                $('#login').find('[type="submit"]').first().removeAttr('disabled'); // Re-enable the submit button
                end_load(); // Hide loading animation
            }, error: function (xhr) {
                // Log any errors during the AJAX request
                console.log(xhr);
                $("#msg").html(xhr.responseText || xhr.responseJSON.message);

                $('#login').find('[type="submit"]').first().removeAttr('disabled'); // Re-enable the submit button
                end_load(); // Hide loading animation
            }
        });
    });

    /*
        This function handles the submission of the confirmation code form
        that appears after a user tries to log in but needs to confirm their account.
        The confirmation code is sent to confirm_code.php for validation.
    */
    $('body').delegate('#confirm_code', 'submit', function (event) {
        event.preventDefault(); // Prevent default form submission
        $(this).find('[type="submit"]').first().attr('disabled', true); // Disable the submit button
        start_load(this); // Show loading animation

        // Send confirmation code to the server for validation
        $.ajax({
            url: "confirm_code.php", // Server endpoint for confirmation code validation
            method: "POST", // HTTP POST request
            data: $("#confirm_code").serialize(), // Serialize form data for transmission
            success: function (data) {
                console.log(data); // Log server response
                if (data === 'success') {
                    // On success, reload the page
                    $("#signup_msg,#msg").text("Success");
                    setInterval(location.reload(), 200);
                } else {
                    // Display any error messages
                    $("#signup_msg,#msg").html(data);
                }

                $('#confirm_code').find('[type="submit"]').first().removeAttr('disabled'); // Re-enable the submit button
                end_load(); // Hide loading animation
            }, error: function (xhr) {
                // Log errors and display error messages
                console.error(xhr);
                $("#signup_msg,#msg").html(xhr.responseText || xhr.responseJSON.message);

                $('#confirm_code').find('[type="submit"]').first().removeAttr('disabled'); // Re-enable the submit button
                end_load(); // Hide loading animation
            }
        });
    });

    /*
    Handles the submission of the #signup_form, allowing users to register a new account.
    When the form is submitted, the user-provided data is sent to register.php for processing.

    If registration is successful but the account is unconfirmed, the system sends a confirmation
    code via AWS Cognito through CloudServices and displays a form to verify the code.

    Otherwise, appropriate feedback is provided to the user.
    */
    $("#signup_form").on("submit", function (event) {
        event.preventDefault(); // Prevent the default form submission behavior

        $(this).find('[type="submit"]').first().attr('disabled', true); // Disable the submit button to prevent multiple submissions
        start_load(this); // Display the loading animation

        // Send registration data to the server
        $.ajax({
            url: "register.php", // Server endpoint for processing registration
            method: "POST", // HTTP POST request
            type: "JSON", // Expected response format
            data: $("#signup_form").serialize(), // Serialize form data for transmission
            success: function (data) {
                console.log(data); // Log server response for debugging

                try {
                    data = JSON.parse(data); // Attempt to parse JSON response
                } catch (e) {
                    console.error(e); // Log parsing errors if any
                }

                // If the user needs to confirm their account, fetch the confirmation code form
                if (data.confirm) {
                    $.ajax({
                        url: "confirm_code.php?email=" + data.email, // Provide email to the confirmation form
                        success: function (confrm) {
                            console.log(confrm); // Log the confirmation form HTML
                            $('#signup_form').replaceWith(confrm); // Replace the signup form with the confirmation form
                        }, error: function (xhr) {
                            console.error(xhr); // Log any errors during the AJAX request
                        }
                    });
                } else {
                    // Display server feedback (e.g., success or error messages)
                    $("#signup_msg").html(data);
                }

                end_load(); // Hide the loading animation
                $('#signup_form').find('[type="submit"]').first().removeAttr('disabled'); // Re-enable the submit button
            }, error: function (xhr) {
                // Log errors and display error messages to the user
                console.error(xhr);
                $("#signup_msg").html(xhr.responseText || xhr.responseJSON.message);

                $('#signup_form').find('[type="submit"]').first().removeAttr('disabled'); // Re-enable the submit button
                end_load(); // Hide the loading animation
            }
        });
    });

    //Add Product into Cart
    $("body").delegate("#product", "click", function (event) {
        var pid = $(this).attr("pid");

        event.preventDefault();
        $(".overlay").show();
        $.ajax({
            url: "action.php", method: "POST", data: {addToCart: 1, proId: pid,}, success: function (data) {
                count_item();
                getCartItem();
                $('#product_msg').html(data);
                $('.overlay').hide();
            }
        })
    })
    //Add Product into Cart End Here



    //Count user cart items funtion
    count_item();
    function count_item() {
        $.ajax({
            url: "action.php", method: "POST", data: {count_item: 1}, success: function (data) {
                $(".badge").html(data);
            }
        })
    }

    //Count user cart items funtion end

    //Fetch Cart item from Database to dropdown menu
    getCartItem();
    function getCartItem() {
        $.ajax({
            url: "action.php", method: "POST", data: {Common: 1, getCartItem: 1}, success: function (data) {
                $("#cart_product").html(data);
                net_total();

            }
        })
    }

    //Fetch Cart item from Database to dropdown menu

    /*
        Whenever user change qty we will immediate update their total amount by using keyup funtion
        but whenever user put something(such as ?''"",.()''etc) other than number then we will make qty=1
        if user put qty 0 or less than 0 then we will again make it 1 qty=1
        ('.total').each() this is loop funtion repeat for class .total and in every repetation we will perform sum operation of class .total value
        and then show the result into class .net_total
    */
    $("body").delegate(".qty", "keyup", function (event) {
        event.preventDefault();
        var row = $(this).parent().parent();
        var price = row.find('.price').val();
        var qty = row.find('.qty').val();
        if (isNaN(qty)) {
            qty = 1;
        }
        ;
        if (qty < 1) {
            qty = 1;
        }
        ;var total = price * qty;
        row.find('.total').val(total);
        var net_total = 0;
        $('.total').each(function () {
            net_total += ($(this).val() - 0);
        })
        $('.net_total').html("Total : $ " + net_total);

    })
    //Change Quantity end here

    /*
        whenever user click on .remove class we will take product id of that row
        and send it to action.php to perform product removal operation
    */


    $("body").delegate(".remove", "click", function (event) {
        var remove = $(this).parent().parent().parent();
        var remove_id = remove.find(".remove").attr("remove_id");
        $.ajax({
            url: "action.php", method: "POST", data: {removeItemFromCart: 1, rid: remove_id}, success: function (data) {
                $("#cart_msg").html(data);
                checkOutDetails();
            }
        })
    })


    /*
        whenever user click on .update class we will take product id of that row
        and send it to action.php to perform product qty updation operation
    */
    $("body").delegate(".update", "click", function (event) {
        var update = $(this).parent().parent().parent();
        var update_id = update.find(".update").attr("update_id");
        var qty = update.find(".qty").val();
        $.ajax({
            url: "action.php",
            method: "POST",
            data: {updateCartItem: 1, update_id: update_id, qty: qty},
            success: function (data) {
                $("#cart_msg").html(data);
                checkOutDetails();
            }
        })


    })

    /*
        checkOutDetails() function work for two purposes
        First it will enable php isset($_POST["Common"]) in action.php page and inside that
        there is two isset funtion which is isset($_POST["getCartItem"]) and another one is isset($_POST["checkOutDetials"])
        getCartItem is used to show the cart item into dropdown menu
        checkOutDetails is used to show cart item into Cart.php page
    */
    function checkOutDetails() {
        $('.overlay').show();
        $.ajax({
            url: "action.php", method: "POST", data: {Common: 1, checkOutDetails: 1}, success: function (data) {
                $('.overlay').hide();
                $("#cart_checkout").html(data);
                net_total();
            }
        })
    }
    checkOutDetails();


    /*
        net_total function is used to calcuate total amount of cart item
    */
    function net_total() {
        var net_total = 0;
        $('.qty').each(function () {
            var row = $(this).parent().parent();
            var price = row.find('.price').val();
            var total = price * $(this).val() - 0;
            row.find('.total').val(total);
        })
        $('.total').each(function () {
            net_total += ($(this).val() - 0);
        })
        $('.net_total').html("Total : $ " + net_total);
    }
    net_total();
    page();

    function page() {
        $.ajax({
            url: "action.php", method: "POST", data: {page: 1}, success: function (data) {
                $("#pageno").html(data);
            }
        })
    }

    $("body").delegate("#page", "click", function () {
        var pn = $(this).attr("page");
        $.ajax({
            url: "action.php",
            method: "POST",
            data: {getProduct: 1, setPage: 1, pageNumber: pn},
            success: function (data) {
                $("#get_product").html(data);
            }
        })
    })
})
