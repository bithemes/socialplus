$(document).ready(function() {
    $('.panel-editable').hover(function() {
        $(this).find('.panel-edit').css('display', 'block');
    }, function() {
        $(this).find('.panel-edit').css('display', 'none');
    });

    $('.posts ul li').hover(function() {
        $(this).find('.panel-edit').css('display', 'block');
        $(this).find('.panel-remove').css('display', 'block');
    }, function() {
        $(this).find('.panel-edit').css('display', 'none');
        $(this).find('.panel-remove').css('display', 'none');
    });

    var descField = $('.user-description p[contenteditable="true"]');

    descField.focus(function(e){ check_charcount(e); });
    descField.keyup(function(e){ check_charcount(e); });
    descField.keydown(function(e){ check_charcount(e); });

    function check_charcount(e) {
        if(e.which != 8 && descField.text().length >= 80) e.preventDefault();
        $('.description-characters b').text(descField.text().length + '/80');
    }

    $('.btn-description').click(function() {
        $.ajax({
            data: { description: descField.text() },
            url: Routing.generate('update_description'),
            type: 'post',
            success: function(response) {
                showFlash('success', response);
            },
            error: function(request, status, error) {
                showFlash('danger', request.responseText);
            }
        })
    });

    function showFlash(type, data) {
        var flashOb = '<div class="alert alert-' + type + ' alert-dismissible" role="alert">'+
            '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span>&times;</span></button>'+
            data+
            '</div>';
        $('.last-column').prepend(flashOb);
    }

    $('.autoExpand').autogrow({horizontal: false});

    $('.comment-switch').click(function() {
        var post_id = $(this).data('id');
        $('#post-add-comment-'+post_id).toggle();
        $('#post-add-comment-'+post_id+' textarea').css('height', 'auto');
        $('.commentExpand').autogrow({horizontal: false});
    });

    // Add new comment
    $('.new-comment-send').click(function() {
        $.ajax({
            data: {
                content: $(this).siblings('textarea').val(),
                post_id: $(this).data('id')
            },
            url: Routing.generate('post_comment'),
            type: 'post',
            success: function(response) {
                location.reload();
                showFlash('success', response);
            },
            error: function(request, status, error) {
                showFlash('danger', request.responseText);
            }
        })
    });

    // Add reply comment
    $('.reply-comment-send').click(function() {
        $.ajax({
            data: {
                content: $(this).siblings('textarea').val(),
                post_id: $(this).data('post'),
                comment_id: $(this).data('parent')
            },
            url: Routing.generate('reply_comment'),
            type: 'post',
            success: function(response) {
                location.reload();
                showFlash('success', response);
            },
            error: function(request, status, error) {
                showFlash('danger', request.responseText);
            }
        })
    });

    // Show more comments
    $('.btn-show-comments').click(function() {
        $('#comments-'+$(this).data('id')).find('.each-comment:nth-of-type(n+3)').toggle();
    });

    // Magnific Popup Init
    $('.user-bg, .user-avatar, .post-img').magnificPopup({
        delegate: '', // the selector for gallery item
        type: 'image',
        callbacks: { // bugfix to avoid scrolling to top on gallery launch
            open: function() {
                $('html').css('margin-right', 0).css('overflow', 'visible');
            }
        }
    });

    $('.comment-reply').click(function() {
        $(this).closest('.each-comment').find('.post-reply-comment').toggle();
        $(this).closest('.each-comment').find('.replyExpand').autogrow({ horizontal: false });
    });

    $('.each-msg').click(function() {
        if($(this).find('.msg-hidden').length) {
            $(this).find('.msg-body').text($(this).find('.msg-hidden').text());
        }

        if($(this).hasClass('msg-not-checked')) {
            $.ajax({
                data: {
                    message_id: $(this).data('id')
                },
                url: Routing.generate('message_check'),
                type: 'post',
                success: function(response) {
                    location.reload();
                },
                error: function(request, status, error) {
                    showFlash('danger', request.responseText);
                }
            });
        }
    });

    $('.search-nav').keypress(function(e) {
        if(e.which == 13 && $(this).val()) {
            document.location = Routing.generate('search', { query: $(this).val() });
        }
    });

    $('.each-note').click(function() {
        $.ajax({
            data: {
                id: $(this).data('id')
            },
            url: Routing.generate('note_remove'),
            type: 'post',
            success: function(response) {
                location.reload();
            },
            error: function(request, status, error) {
                console.log(request);
                showFlash('danger', request.responseText);
            }
        })
    });

    $('.show-dashboard-posts').click(function () {
        $.ajax({
            data: {
                last_id: $('.posts ul li.dashboard-post').length
            },
            url: Routing.generate('get_posts'),
            type: 'post',
            success: function(response) {
                $('.posts ul').append(response);
                var total_posts = $('.thumbnail .caption ul li:last-child b').text();
                if($('.posts ul li.dashboard-post').length == total_posts) {
                    $('.show-dashboard-posts').hide();
                }
            },
            error: function(request, status, error) {
                console.log(request);
            }
        })
    });

    $('.show-user-posts').click(function () {
        $.ajax({
            data: {
                last_id: $('.posts ul li.user-post').length,
                target_user: $('.target-user').text().replace('@','')
            },
            url: Routing.generate('get_user_posts'),
            type: 'post',
            success: function(response) {
                $('.posts ul').append(response);
                var total_posts = $('.thumbnail .caption ul li:last-child b').text();
                if($('.posts ul li.user-post').length == total_posts) {
                    $('.show-user-posts').hide();
                }
            },
            error: function(request, status, error) {
                console.log(request);
            }
        })
    });

    $('.show-timeline-posts').click(function () {
        $.ajax({
            data: {
                last_id: $('.posts ul li.timeline-post').length
            },
            url: Routing.generate('get_timeline_posts'),
            type: 'post',
            success: function(response) {
                $('.posts ul').append(response);
                var total_posts = $('.timeline-total-posts').text();
                if($('.posts ul li.timeline-post').length == total_posts) {
                    $('.show-timeline-posts').hide();
                }
            },
            error: function(request, status, error) {
                console.log(request);
            }
        })
    });

    $('.btn-report-post').click(function() {
        $('.btn-report').data('id', $(this).data('id'));
    });

    $('.btn-report').click(function() {
       // ajax
        $.ajax({
            data: {
                post_id: $(this).data('id'),
                reason: $('#report-reason').val()
            },
            url: Routing.generate('report_post'),
            type: 'post',
            success: function(response) {
                showFlash('success', response);
                $('.modal').modal('toggle');
            },
            error: function(request, status, error) {
                showFlash('danger', request.responseText.replace(/\"/g, ''));
                $('.modal').modal('toggle');
            }
        })
    });

});
