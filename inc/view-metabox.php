<div id="postp-metabox-wrap">
    <div id="credit-bal">You have 3 credits available</div>
    <p>Would you like this post in audio format as well?</p>
    <div id="credit-charge-wrap">
        <div id="charge-display-oval">1</div>
    </div>
    <h1 id="credits-spend">Credits</h1>
    <h4 id="pp-words-count">160 Words</h4>

    <div id="postp-switch-wrap">
        <div id="postp-switch">
            <div class="switch-split switch-yes ">Yes</div>
            <div class="switch-split switch-no active">No</div>
            <input type="hidden" name="postplay-send" id="postplay-send" value="0">
        </div>
    </div>
</div>

<script>
    function toggleSendValue() {
        var theVal = jQuery("#postplay-send").val();
        if (theVal == '0') {
            jQuery("#postplay-send").val('1');
        } else {
            jQuery("#postplay-send").val('0');
        }

    }
    jQuery(document).on('click', '.switch-split', function () {
        jQuery('.switch-split').toggleClass('active');
        toggleSendValue();
    });
</script>