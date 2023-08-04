$(function () {

    $("#music_band_form_city_id_country").on("change", function () {
        $("#music_band_form_city_name").val("");
    });

    (function () {
        var deleteBtns = $(".js-delete-btn"),
            deleteConfirmationModal = $("#delete-confirmation-dialog");

        if (!deleteBtns.length) {
            return;
        }

        deleteConfirmationModal.on("show.bs.modal", function (e) {
            var btnClicked = $(e.relatedTarget);

            $(".js-band-name", deleteConfirmationModal).text(btnClicked.closest("tr").find(".js-name:first").text());

            $(".js-ok-btn", deleteConfirmationModal).on("click", function () {
                // prevent double clicking, and hence, double submitting
                $(this).prop("disabled", true).attr("disabled", "disabled");

                // submit to form, to delete the band
                btnClicked.closest("form").submit();
            });
        });

        deleteConfirmationModal.on("hide.bs.modal", function (e) {
            // remove previously attached event handler,
            // so the wrong form doesn't get submitted.
            $(".js-ok-btn", deleteConfirmationModal).off();
        });
    }());

});
