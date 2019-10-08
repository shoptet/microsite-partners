import '../scss/main.scss';

// static png
import '../images/shoptetrix-thumb-up.png';
import '../images/shoptetrix-thumb-up-1.png';
import '../images/shoptetrix-thumb-up-2.png';
import '../images/shoptetrix-point-up.png';
import '../images/shoptetrix-meditate.png';
import '../images/shoptetrix-chill.png';
import '../images/shoptet-logo.png';
import '../images/proveren-shoptetem.png';
import '../images/medal-bronze.png';
import '../images/medal-silver.png';
import '../images/medal-gold.png';
import '../images/medal-shoptetrix-bronze.png';
import '../images/medal-shoptetrix-silver.png';
import '../images/medal-shoptetrix-gold.png';
import '../images/envelope-x.png';
import '../images/envelope.png';
import '../images/shoptet-partneri-logo.png';
import '../images/shoptetrix-action-1.png';
import '../images/shoptetrix-action-2.png';
import '../images/shoptetrix-error.png';
import '../images/shoptetrix-success.png';
import '../images/shoptetrix-warning.png';
import '../images/shoptetrix-warning-mail.png';
import '../images/shoptetrix-grimacing-1.png';
import '../images/shoptetrix-grimacing-2.png';

// static jpg
import '../images/placeholder.jpg';

// static svg
import '../images/shoptet-partneri-logo.svg';
import '../images/shoptet-logo.svg';
import '../images/badge.svg';
import '../images/profile-cards.svg';

// Icons
import '../images/icon-ic.svg';
import '../images/icon-dic.svg';
import '../images/bars-solid.svg';
import '../images/star-solid.svg';

// Font Awesome icons
import 'font-awesome-svg-png/black/svg/external-link.svg';
import 'font-awesome-svg-png/black/svg/envelope-o.svg';
import 'font-awesome-svg-png/black/svg/phone.svg';
import 'font-awesome-svg-png/black/svg/facebook-square.svg';
import 'font-awesome-svg-png/black/svg/twitter-square.svg';
import 'font-awesome-svg-png/black/svg/linkedin-square.svg';
import 'font-awesome-svg-png/black/svg/instagram.svg';

$(function () {

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
            resNavDiv.append('<button class="navigation-btn"><svg data-src="./wp-content/themes/shoptet/assets/bars-solid.svg" width="18" height="18" role="img"></svg></button><ul class="hidden-links hidden"></ul>');
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

  // start responsive navigation
  $('.responsive-nav').shpResponsiveNavigation();

  // start bootstrap tooltips
  $('[data-toggle="tooltip"]').tooltip();

  // create svg icons
  new SVGInjector().inject(document.querySelectorAll('svg[data-src]'));

  // Facebook share window
  $('[data-facebook-share]').on('click', function (e) {
    e.preventDefault();
    window.open(
      $(this).attr('href'),
      'fbShareWindow',
      'height=450, width=550, top=' + ($(window).height() / 2 - 275) + ', left=' + ($(window).width() / 2 - 225) + ', toolbar=0, location=0, menubar=0, directories=0, scrollbars=0'
    );
    return false;
  });

  // datalayer push on events
  if($('div.wpcf7-response-output').length > 0) {
    if($('div.wpcf7-mail-sent-ok').closest('form').attr('id') == 'cf7-general-contact') {
      dataLayer.push({'event': 'PartneriKontakt'});
    }
    if($('div.wpcf7-mail-sent-ok').closest('form').attr('id') == 'cf7-partner-contact') {
      dataLayer.push({'event': 'kontaktovatPartnera'});
    }
  }

  $('form.review-form').submit(function(){
    dataLayer.push({'event': 'hodnoceniPartnera'});
  });

});