(function ($) {
    "use strict";
    $(window).load(function () {
		$('.classes .our-class-main').each(function(){
        var $container = $(this).parent();
		var $isotope = $container.find('.classes-content section,section.our-trainers');

		var $filter = $container.find('button.filter');
		var $loadmore = $container.find('button.load-more');
        $isotope.isotope({
            itemSelector: '.element-item'
        });
		
        $filter.click(function () {
            var selector = $(this).attr('data-filter');
            $filter.removeClass('is-checked');
            $(this).addClass('is-checked');
            $isotope.isotope({filter: selector});
            return false;
        });
        $loadmore.click(function () {
            var itemTarget = $(this);
            if ($(itemTarget).hasClass('all-loaded')) {
                return;
            }
            var pages = $container.find('.post-pagination a');
            var listPageUrl = new Array(), endPage = false;
            pages.each(function () {
                if ($(this).hasClass('loaded')) {
                } else {
                    listPageUrl.push($(this));
                }
            });
            if (listPageUrl.length === 2) {
                endPage = true;
            }
            var pageLoad = listPageUrl[0];
            $.ajax({
                type: "GET",
                url: $(pageLoad).attr('href'),
                cache: false,
                beforeSend: function (xhr) {
                    itemTarget.find('.ajax-loading-icon').show();
                },
                success: function (transport) {
                    var html = $(transport).find('div .element-item');
                    html.each(function () {
						var item = $(this);
						var img = new Image();
						img.src = $('img', item).attr('src');
						img.onload = function () {
							$isotope.append(item).isotope('appended', item);
						};
					});
                    $(pageLoad).addClass('loaded');
                    itemTarget.find('.ajax-loading-icon').hide();
                    if (endPage) {
                        itemTarget.addClass('all-loaded').html('All Loaded');
                    }
                }, complete: function (data) {
                    $isotope.isotope('layout');
                }
            });
        });
		});
    });
})(jQuery);