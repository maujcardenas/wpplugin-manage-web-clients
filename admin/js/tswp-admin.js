jQuery(document).ready(function($) {
    'use strict';

    // Initialize datepickers
    if ($.fn.datepicker) {
        $('.tswp-datepicker').datepicker({
            dateFormat: 'yy-mm-dd'
        });
    }

    // Client information page functionality
    var clientInfoContainer = $('#tswp-client-info');
    if (clientInfoContainer.length) {
        $('#client-selector').on('change', function() {
            var clientId = $(this).val();
            if (clientId) {
                loadClientInformation(clientId);
            } else {
                clientInfoContainer.html('');
            }
        });
    }

    // Load client information via AJAX
    function loadClientInformation(clientId) {
        $.ajax({
            url: tswpAjax.ajax_url,
            type: 'POST',
            data: {
                action: 'get_client_info',
                nonce: tswpAjax.nonce,
                client_id: clientId
            },
            beforeSend: function() {
                clientInfoContainer.html('<div class="tswp-loading">Loading...</div>');
            },
            success: function(response) {
                if (response.success) {
                    displayClientInformation(response.data);
                } else {
                    clientInfoContainer.html('<div class="tswp-alert tswp-alert-error">Error loading client information</div>');
                }
            },
            error: function() {
                clientInfoContainer.html('<div class="tswp-alert tswp-alert-error">Error loading client information</div>');
            }
        });
    }

    // Display client information in the UI
    function displayClientInformation(clientData) {
        var html = '<div class="tswp-client-info">';
        
        // Basic Information
        html += '<div class="tswp-info-section">';
        html += '<h3>Basic Information</h3>';
        html += '<p><strong>Name:</strong> ' + clientData.first_name + ' ' + clientData.last_name + '</p>';
        html += '<p><strong>Email:</strong> ' + clientData.email + '</p>';
        html += '<p><strong>Phone:</strong> ' + clientData.cell_number + '</p>';
        html += '</div>';

        // Domains
        if (clientData.domains && clientData.domains.length) {
            html += '<div class="tswp-info-section">';
            html += '<h3>Domains</h3>';
            html += '<table class="tswp-table">';
            html += '<tr><th>Domain</th><th>Expiration</th></tr>';
            clientData.domains.forEach(function(domain) {
                html += '<tr>';
                html += '<td>' + domain.domain_name + '</td>';
                html += '<td>' + domain.expiration_date + '</td>';
                html += '</tr>';
            });
            html += '</table>';
            html += '</div>';
        }

        // Applications
        if (clientData.applications && clientData.applications.length) {
            html += '<div class="tswp-info-section">';
            html += '<h3>Applications</h3>';
            html += '<table class="tswp-table">';
            html += '<tr><th>Name</th><th>Hosting Plan</th><th>Expiration</th></tr>';
            clientData.applications.forEach(function(app) {
                html += '<tr>';
                html += '<td>' + app.application_name + '</td>';
                html += '<td>' + app.hosting_plan + '</td>';
                html += '<td>' + app.expiration_date + '</td>';
                html += '</tr>';
            });
            html += '</table>';
            html += '</div>';
        }

        // Emails
        if (clientData.emails && clientData.emails.length) {
            html += '<div class="tswp-info-section">';
            html += '<h3>Email Accounts</h3>';
            html += '<table class="tswp-table">';
            html += '<tr><th>Email</th><th>Plan</th><th>Expiration</th></tr>';
            clientData.emails.forEach(function(email) {
                html += '<tr>';
                html += '<td>' + email.email_address + '</td>';
                html += '<td>' + email.email_plan + '</td>';
                html += '<td>' + email.expiration_date + '</td>';
                html += '</tr>';
            });
            html += '</table>';
            html += '</div>';
        }

        // Payments
        if (clientData.payments && clientData.payments.length) {
            html += '<div class="tswp-info-section">';
            html += '<h3>Payment History</h3>';
            html += '<table class="tswp-table">';
            html += '<tr><th>Date</th><th>Service</th><th>Status</th></tr>';
            clientData.payments.forEach(function(payment) {
                html += '<tr>';
                html += '<td>' + payment.payment_date + '</td>';
                html += '<td>' + payment.service_title + '</td>';
                html += '<td>' + payment.status + '</td>';
                html += '</tr>';
            });
            html += '</table>';
            html += '</div>';
        }

        html += '</div>';
        clientInfoContainer.html(html);
    }

    // Form validation
    $('.tswp-form').on('submit', function(e) {
        var form = $(this);
        var valid = true;
        
        // Clear previous error messages
        form.find('.tswp-error').remove();

        // Validate required fields
        form.find('[required]').each(function() {
            if (!$(this).val()) {
                valid = false;
                $(this).after('<span class="tswp-error">This field is required</span>');
            }
        });

        // Validate email fields
        form.find('input[type="email"]').each(function() {
            var email = $(this).val();
            if (email && !isValidEmail(email)) {
                valid = false;
                $(this).after('<span class="tswp-error">Please enter a valid email address</span>');
            }
        });

        if (!valid) {
            e.preventDefault();
        }
    });

    // Email validation helper
    function isValidEmail(email) {
        var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        return re.test(email);
    }

    // Delete confirmation
    $('.tswp-delete-button').on('click', function(e) {
        if (!confirm('Are you sure you want to delete this item? This action cannot be undone.')) {
            e.preventDefault();
        }
    });

    // Expiration date warnings
    function highlightExpiringItems() {
        $('.expiration-date').each(function() {
            var expirationDate = new Date($(this).data('date'));
            var today = new Date();
            var daysUntilExpiration = Math.ceil((expirationDate - today) / (1000 * 60 * 60 * 24));

            if (daysUntilExpiration <= 30) {
                $(this).addClass('tswp-expiring-soon');
                if (daysUntilExpiration <= 7) {
                    $(this).addClass('tswp-expiring-critical');
                }
            }
        });
    }

    highlightExpiringItems();

    // Dynamic search functionality
    var searchTimeout;
    $('.tswp-search-input').on('keyup', function() {
        var searchInput = $(this);
        var searchTerm = searchInput.val();
        
        clearTimeout(searchTimeout);
        
        searchTimeout = setTimeout(function() {
            if (searchTerm.length >= 2) {
                performSearch(searchTerm, searchInput.data('type'));
            }
        }, 500);
    });

    function performSearch(term, type) {
        $.ajax({
            url: tswpAjax.ajax_url,
            type: 'POST',
            data: {
                action: 'tswp_search',
                nonce: tswpAjax.nonce,
                term: term,
                type: type
            },
            success: function(response) {
                if (response.success) {
                    updateSearchResults(response.data);
                }
            }
        });
    }

    function updateSearchResults(results) {
        var resultsContainer = $('#tswp-search-results');
        var html = '';

        if (results.length) {
            results.forEach(function(item) {
                html += '<div class="tswp-search-result">';
                html += '<a href="' + item.edit_url + '">' + item.title + '</a>';
                html += '</div>';
            });
        } else {
            html = '<div class="tswp-no-results">No results found</div>';
        }

        resultsContainer.html(html);
    }
});