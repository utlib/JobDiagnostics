jQuery(function() {
    var jqt = jQuery('#test-progress'),
        id = jqt.data('record-id'),
        interval = parseFloat(jqt.data('poll-interval'))*1000,
        timeleft = parseFloat(jqt.data('timeout')),
        pollpath = jqt.data('poll-path');
    setInterval(function() {
        jQuery.ajax({
            url: pollpath,
            success: function(data) {
                if (data.finished || data.error) {
                    window.location.href = "../../..";
                }
            }
        });
        timeleft--;
        jqt.attr('value', parseInt(jqt.attr('value'))+1);
        if (timeleft <= 0) {
            window.location.href = "../../..";
        }
    }, interval);
});
