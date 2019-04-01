// Shared JS content
(function($) {

    $.fn.shpResponsiveNavigation = function() {

        return this.each(function() {

            var $this = $(this),   //this = div .responsive-nav
                maxWidth,
                visibleLinks,
                hiddenLinks,
                button;

            maxWidth = $(this).width();

            // check if menu is even visible before start
            if(maxWidth > 0) {

                setup($this);
                update($this);

                function setup(resNavDiv) {
                    visibleLinks = resNavDiv.find('.visible-links');
                    visibleLinks.children('li').each(function() {
                        $(this).attr('data-width', $(this).outerWidth());
                    });

                    //hidden navigation
                    if (!resNavDiv.find('.hidden-links').length) {
                        resNavDiv.append('<button class="navigation-btn"><div class="fas fa-bars"></div></button><ul class="hidden-links hidden"></ul>');
                    }
                    hiddenLinks = resNavDiv.find('.hidden-links');
                    button = resNavDiv.find('button');
                }

                function update(resNavDiv) {
                    maxWidth = resNavDiv.width();
                    var filledSpace = button.outerWidth();

                    if((visibleLinks.outerWidth() + filledSpace) > maxWidth) {
                        // push excess to hidden links
                        visibleLinks.children('li').each(function(index) {
                            filledSpace += $(this).data('width');
                            if (filledSpace >= maxWidth) {
                                $(this).appendTo(hiddenLinks);
                            }
                        });


                    } else {
                        filledSpace += visibleLinks.width();

                        // push missing to visible links
                        hiddenLinks.children('li').each(function(index) {
                            filledSpace += $(this).data('width');
                            if (filledSpace < maxWidth) {
                                $(this).appendTo(visibleLinks);
                            }
                        });
                    }

                    if (hiddenLinks.children('li').length == 0) {
                        button.hide();
                    } else {
                        button.show();
                    }
                }

                $(window).resize(function() {
                    update($this);
                });

                $(button).click(function() {
                    hiddenLinks.toggleClass('hidden');
                });
            }
        });
    };

    $(document).ready(function(){
        /* START RESPONSIVE NAVIGATION */
        $('.responsive-nav').shpResponsiveNavigation();
    });

})(jQuery);


