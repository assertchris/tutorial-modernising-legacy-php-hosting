$("input").on("click", function() {
    $(this).text("working...")
    $(".input").attr("disabled", "disabled")
})
