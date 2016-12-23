$(document).ready(function () {
    var sizesDropdown = $('#lovethatfit_supportbundle_evaluationdefaultproductstype_product_sizes');
    var chosenContainer = $('.chosen-container');
    $("#lovethatfit_supportbundle_evaluationdefaultproductstype_product_id").change(function () {
        $('.load-sizes-loader').css('display', 'inline-block');
        chosenContainer.css('display', 'none');
        var selectedProduct = $(this).val();
        if (isNaN(selectedProduct) || selectedProduct === "") {
            sizesDropdown.html('');
            $('.load-sizes-loader').css('display', 'none');
            chosenContainer.css('display', 'inline-block');
            return false;
        }
        $.ajax({
            method: "POST",
            url: url,
            data: {id: selectedProduct},
            dataType: "html"
        }).done(function (sizes) {
            $('.load-sizes-loader').css('display', 'none');
            chosenContainer.css('display', 'inline-block');
            if (sizes != "") {
                sizesDropdown.html(sizes);
                sizesDropdown.trigger('chosen:updated');
            }
        }).fail(function () {
            $('.load-sizes-loader').css('display', 'none');
            chosenContainer.css('display', 'inline-block');
            alert("$.get failed!");
        }).error(function () {
            $('.load-sizes-loader').css('display', 'none');
            chosenContainer.css('display', 'inline-block');
            alert("Handler for .error() called.")
        });
    });
});