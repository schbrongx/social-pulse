jQuery(document).ready(function($) {
    // Show "Settings changed" message when any field changes.
    $('form').on('change', 'input, select, textarea', function() {
        $('.settings-changed').show();
    });
    
    // Test Leader URL AJAX (for Follower mode)
    $('#sp-test-leader').on('click', function() {
        $('#sp-leader-test-result').html('Testing...');
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            dataType: 'json',
            data: { action: 'SOCPUL_test_leader_api' },
            success: function(response) {
                $('#sp-leader-test-result').html(response.message);
            },
            error: function(xhr, status, error) {
                $('#sp-leader-test-result').html('Error: ' + error);
            }
        });
    });
    
    $('#sp-test-youtube').on('click', function() {
        $('#sp-youtube-test-result').html('Testing...');
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            dataType: 'json',
            data: { action: 'SOCPUL_test_youtube_api' },
            success: function(response) {
                $('#sp-youtube-test-result').html(response.message);
                if(response.last_fetch_time) {
                    $('#sp-last-fetch-time').html(response.last_fetch_time);
                }
                if(response.last_fetch_value) {
                    $('#sp-last-fetch-value').html(response.last_fetch_value);
                }
            },
            error: function(xhr, status, error) {
                $('#sp-youtube-test-result').html('Error: ' + error);
            }
        });
    });
    
    $('#sp-test-steam').on('click', function() {
        $('#sp-steam-test-result').html('Testing...');
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            dataType: 'json',
            data: { action: 'SOCPUL_test_steam_api' },
            success: function(response) {
                $('#sp-steam-test-result').html(response.message);
                if(response.last_fetch_time) {
                    $('#sp-steam-last-fetch-time').html(response.last_fetch_time);
                }
                if(response.last_fetch_value) {
                    $('#sp-steam-last-fetch-value').html(response.last_fetch_value);
                }
            },
            error: function(xhr, status, error) {
                $('#sp-steam-test-result').html('Error: ' + error);
            }
        });
    });
    
    $('#sp-test-facebook').on('click', function() {
        $('#sp-facebook-test-result').html('Testing...');
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            dataType: 'json',
            data: { action: 'SOCPUL_test_facebook_api' },
            success: function(response) {
                $('#sp-facebook-test-result').html(response.message);
                if(response.last_fetch_time) {
                    $('#sp-facebook-last-fetch-time').html(response.last_fetch_time);
                }
                if(response.last_fetch_value) {
                    $('#sp-facebook-last-fetch-value').html(response.last_fetch_value);
                }
            },
            error: function(xhr, status, error) {
                $('#sp-facebook-test-result').html('Error: ' + error);
            }
        });
    });
    
    $('#sp-test-x').on('click', function() {
        $('#sp-x-test-result').html('Testing...');
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            dataType: 'json',
            data: { action: 'SOCPUL_test_x_api' },
            success: function(response) {
                $('#sp-x-test-result').html(response.message);
                if(response.last_fetch_time) {
                    $('#sp-x-last-fetch-time').html(response.last_fetch_time);
                }
                if(response.last_fetch_value) {
                    $('#sp-x-last-fetch-value').html(response.last_fetch_value);
                }
                if(response.api_limit_count) {
                    $('#sp-x-api-limit-count').html(response.api_limit_count);
                }
            },
            error: function(xhr, status, error) {
                $('#sp-x-test-result').html('Error: ' + error);
            }
        });
    });
});