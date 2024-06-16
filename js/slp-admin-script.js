jQuery(document).ready(function ($) {
  $("#slp_add_stanza_button").click(function () {
    $("#slp_stanzas_wrapper").append(
      '<textarea style="width:100%; height:100px; margin-bottom:10px;" name="slp_stanzas[]"></textarea>'
    );
  });
});
