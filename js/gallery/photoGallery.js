(function($) {
	$.fn.photoGallery = function(options) {
		
		var head = $('head');
		
		$('<script>')
			.prop('type', 'text/javascript')
			.prop('src', FGallery.url+'/js/gallery/pixastic.custom.js')
			.appendTo(head);
		
		$('<script>')
			.prop('type', 'text/javascript')
			.prop('src', FGallery.url+'/js/gallery/jquery.routines.js')
			.appendTo(head);
		
		$('<script>')
			.prop('type', 'text/javascript')
			.prop('src', FGallery.url+'/js/gallery/jquery.xml2json.js')
			.appendTo(head);
		
		var options = $.extend({
			prefix: 'pg-',
			sources: {
				css: {
					gallery: FGallery.url+'/css/gallery/gallery.css'
				},
				xml: {
					settings: FGallery.config_url+'&gall_id='+options.gall_id,
					images: FGallery.images_url+'&js=1'+'&gall_id='+options.gall_id
				}
			}
		}, options);
		
		var settings = {};
		var prefix = options.prefix;
		var blocks = this;
		
		$('<link>')
			.prop('type', 'text/css')
			.prop('rel', 'stylesheet')
			.prop('href', options.sources.css.gallery)
			.appendTo(head);
		
		$.get(options.sources.xml.settings, function(xml) {
			settings = $.xml2json(xml);
			
			$.get(options.sources.xml.images, function(xml) {
				var images = $.xml2json(xml).folder.img;
                                   $.each(blocks, function() {
                                    this.settings = settings;
                                    var block = $(this);

                                    $.extend(block, {
                                            indexCurrent: 0,
                                            init: function() {

                                                    block.html('');

                                                    block.css({
                                                            position: 'relative',
                                                            overflow: 'hidden'
                                                    });

                                                    block.image = $('<div>').prop('class', prefix + 'image').appendTo(block);
                                                    block.image.items = {};

                                                    for (var i = 0; i < images.length; i++) {

                                                            block.image.items[i] = $('<div>')
                                                                    .prop({
                                                                            'class': prefix + 'image-item'
                                                                    })
                                                                    .appendTo(block.image);

                                                            var blockWith = block.width();
                                                            var blockHeight = block.height();
                                                            var imgWith = 'auto';
                                                            var imgHeight = 'auto';

                                                            switch (settings.image.scaleMode) {
                                                                    case 'fit':
                                                                            var ratio = blockWith / blockHeight;

                                                                            if (blockWith < blockHeight) {

                                                                                    imgWith = blockWith;
                                                                            }
                                                                            else {

                                                                                    imgHeight = blockHeight;
                                                                            }
                                                                            break;

                                                                    case 'fill':
                                                                            imgWith = blockWith;
                                                                            imgHeight = blockHeight;
                                                                            break;

                                                                    case 'noscale':
                                                                    default:
                                                                            break;
                                                            }

                                                            block.image.items[i].image = $('<img>')
                                                                    .prop({
                                                                            'src': images[i].file_b,
                                                                            'id': prefix + 'image-' + i
                                                                    })
                                                                    .css({
                                                                            height: imgHeight,
                                                                            width: imgWith,
                                                                            opacity: 0
                                                                    })
                                                                    .appendTo(block.image.items[i])
                                                                    .load(function() {

                                                                            var image = $(this);
                                                                            var n = image.parent().index().toInt();

                                                                            image.css({
                                                                                    marginLeft: (blockWith - image.width()) / 2,
                                                                                    marginTop: (blockHeight - image.height()) / 2
                                                                            });

                                                                            if (n) {

                                                                                    block.image.items[n].css({
                                                                                            display: 'none'
                                                                                    });

                                                                                    image.css({
                                                                                            opacity: 1
                                                                                    });
                                                                            }
                                                                            else {

                                                                                    image.css({
                                                                                            opacity: 1
                                                                                    });
                                                                            }
                                                                    });

                                                            block.image.items[i].image.text = images[i].text;
                                                    }

                                                    block.setScroller(block);
                                                    block.setCaption(block);
                                                    block.setSlideshow();
                                            },
                                            transition: function(indexNext) {

                                                    var block = this;
                                                    var params = {
                                                                    animate: {
                                                                            durations: {
                                                                                    items: 300,
                                                                                    images: settings.image.transitionDuration * 1000
                                                                            }
                                                                    }
                                                    };

                                                    var horizontal = settings.scroller.direction == 'horizontal';

                                                    var indexCurrent = block.indexCurrent;
                                                    var scrollItemNext = block.scrollerItems.items[indexNext];
                                                    var topItem = horizontal ? scrollItemNext.offset().left : scrollItemNext.offset().top;
                                                    var topBlock = horizontal ? block.offset().left : block.offset().top;
                                                    var deltaTop = topItem - topBlock;
                                                    var bottomItem = topItem + (horizontal ? scrollItemNext.width() : scrollItemNext.height());
                                                    var bottomBlock = topBlock + (horizontal ? block.width() : block.height());
                                                    var deltaBottom = bottomItem - bottomBlock;
                                                    var delta = (deltaTop < 0) ? deltaTop : deltaBottom;

                                                    if (deltaTop < 0 || deltaBottom > 0) {

                                                            var scroller = $('.' + prefix + 'scroller');

                                                            scroller.clearQueue().stop();

                                                            if (horizontal) {

                                                                    scroller.animate({
                                                                            left: scroller.position().left - delta
                                                                    }, 500);
                                                            }
                                                            else {

                                                                    scroller.animate({
                                                                            top: scroller.position().top - delta
                                                                    }, 500);
                                                            }
                                                    }

                                                    $('.' + prefix + 'scroller-item-disabler', block.scrollerItems.items[block.indexCurrent]).css({
                                                            display: 'block',
                                                            opacity: 0
                                                    }).animate({
                                                            opacity: 0.6
                                                    }, params.animate.durations.icons);

                                                    $('.' + prefix + 'scroller-item-disabler', block.scrollerItems.items[indexNext]).css({
                                                            opacity: 0.6
                                                    }).animate({
                                                            opacity: 0
                                                    }, params.animate.durations.icons, function() {
                                                            $(this).css({
                                                                    display: 'none'
                                                            });

                                                            block.indexCurrent = indexNext;

                                                            if (block.captionText) {

                                                                    block.captionText.html(block.image.items[indexNext].image.text).find('a').css('color', settings.caption.fontColor.colorFlashToWeb());
                                                            }
                                                    });

                                                    switch (settings.image.transitionEffect) {
                                                            case 'alpha':
                                                                    block.image.items[indexCurrent].fadeOut(params.animate.durations.images);
                                                                    block.image.items[indexNext].fadeIn(params.animate.durations.images);
                                                                    break;

                                                            case 'blur':
                                                                    var count = 3;
                                                                    var i = 1;
                                                                    var img = $('img', block.image.items[indexCurrent]);
                                                                    var id = img.prop('id');
                                                                    var intetval = params.animate.durations.images / count;

                                                                    Pixastic.process(document.getElementById(id), 'blurfast', {
                                                                            amount: 0.5
                                                                    }, function(){

                                                                            block.image.items[indexCurrent].fadeOut(params.animate.durations.images, function() {

                                                                                    Pixastic.revert(document.getElementById(id));
                                                                            });
                                                                            block.image.items[indexNext].fadeIn(params.animate.durations.images);
                                                                    });

                                                                    /*img.pixastic('blurfast', {
                                                                            amount: i / count
                                                                    });

                                                                    blur = setInterval(function() {

                                                                            img.pixastic('blurfast', {
                                                                                    amount: ++i / count
                                                                            });

                                                                            if (i >= count) {
                                                                                    clearInterval(blur);
                                                                            }
                                                                    }, intetval);

                                                                    block.image.items[indexCurrent].fadeOut(params.animate.durations.images, function() {

                                                                            clearInterval(blur);
                                                                            Pixastic.revert(document.getElementById(img.prop('id')));
                                                                    });
                                                                    block.image.items[indexNext].fadeIn(params.animate.durations.images);*/
                                                                    break;

                                                            case 'none':
                                                            default:
                                                                    block.image.items[indexCurrent].hide();
                                                                    block.image.items[indexNext].show();
                                                                    break;
                                                    }

                                            },
                                            setCaption: function() {

                                                    if (!settings.caption.enable.toInt()) {

                                                            return;
                                                    }

                                                    var block = this;
                                                    var widthBlock = block.innerWidth();
                                                    var widthScroller = block.scroller.outerWidth();
                                                    var widthScrollerImage = block.scrollerItems.items[0].outerWidth();

                                                    block.caption = $('<div>').prop('class', prefix + 'caption').appendTo(block);
                                                    block.captionText = $('<div>')
                                                                                            .prop('class', prefix + 'caption-text')
                                                                                            .appendTo(block)
                                                                                            .html(block.image.items[0].image.text);

                                                    var width = settings.scroller.direction == 'horizontal' 
                                                                    ? widthBlock
                                                                    : widthBlock
                                                                    - Math.max(widthScroller, widthScrollerImage) 
                                                                    - block.scroller.css('left').toInt()
                                                                    - Math.min(0, block.scrollerItems.items[0].css('marginLeft').toInt());

                                                    var cssText = {
                                                            color: settings.caption.fontColor.colorFlashToWeb(),
                                                            fontSize: settings.caption.fontSize + 'px'
                                                    };

                                                    var cssCap = {
                                                            backgroundColor: settings.caption.backgroundColor.colorFlashToWeb(),
                                                            opacity: settings.caption.backgroundAlpha
                                                    };

                                                    var caption = $('.' + prefix + 'caption, .' + prefix + 'caption-text');

                                                    switch (settings.caption.align) {
                                                            case 'bottom':
                                                                    cssText.bottom = 0;
                                                                    cssCap.bottom = 0;
                                                                    break;

                                                            case 'top':
                                                            default:
                                                                    cssText.top = 0;
                                                                    cssCap.top = 0;
                                                                    break;
                                                    }

                                                    switch (settings.caption.visible) {
                                                            case 'onHover':
                                                                    caption.css('visibility', 'hidden');

                                                                    var setOpacity = function() {

                                                                            block.captionText.css('opacity', 1);
                                                                            block.caption.css('opacity', settings.caption.backgroundAlpha);
                                                                    };

                                                                    block.bind({
                                                                            mouseenter: function() {

                                                                                    caption.hide().css('visibility', 'visible');
                                                                                    caption.clearQueue().stop();
                                                                                    setOpacity();
                                                                                    caption.fadeIn();
                                                                            },
                                                                            mouseleave: function() {

                                                                                    caption.clearQueue().stop();
                                                                                    setOpacity();
                                                                                    caption.fadeOut();
                                                                            }
                                                                    });
                                                                    break;

                                                            case 'always':
                                                            default:
                                                                    break;
                                                    }

                                                    $('a', block.captionText.width(width).css(cssText)).css(cssText);
                                                    block.caption
                                                            .width(width)
                                                            .height(block.captionText.height())
                                                            .css(cssCap);
                                            },
                                            setSlideshow: function() {

                                                    if (settings.slideshow.autostart == 0) {
                                                            return;
                                                    }

                                                    var block = this;

                                                    block.slideShow = setInterval(function() {

                                                            block.transition((block.indexCurrent + 1) % images.length);
                                                    }, (settings.slideshow.delay.toInt() + settings.image.transitionDuration.toInt()) * 1000);
                                            },
                                            setScroller: function() {

                                                    var block = this;
                                                    var params = {
                                                            scroller: {
                                                                    width: settings.scroller.size.toInt(),
                                                                    distance: settings.scroller.itemDistance.toInt(),
                                                                    left: settings.scroller.distanceFromBorder.toInt(),
                                                                    itemMarginFull: settings.scroller.borderWidth.toInt(),
                                                                    itemMargin: Math.round(settings.scroller.borderWidth / 2),
                                                                    itemHeight: settings.scrollerItem.height.toInt(),
                                                                    itemWidth: settings.scrollerItem.width.toInt()
                                                            }
                                                    };

                                                    var horizontal = settings.scroller.direction == 'horizontal';

                                                    block.scroller = $('<div>').prop('class', prefix + 'scroller').appendTo(block);
                                                    block.scrollerBg = $('<div>').prop('class', prefix + 'scroller-bg').appendTo(block.scroller);
                                                    block.scrollerItems = $('<div>').prop('class', prefix + 'scroller-items').appendTo(block.scroller);
                                                    block.scrollerItems.items = {};

                                                    var k = 0;

                                                    for (var i = 0; i < images.length; i++) {

                                                            var cssItem = {
                                                                    borderStyle: 'solid',
                                                                    borderWidth: settings.scrollerItem.borderWidth + 'px',
                                                                    borderColor: settings.scrollerItem.borderColor.colorFlashToWeb(),
                                                                    height: params.scroller.itemHeight + params.scroller.itemMargin,
                                                                    width: params.scroller.itemWidth + params.scroller.itemMargin
                                                            };

                                                            if (horizontal) {

                                                                    cssItem.marginLeft = i ? params.scroller.distance : 0;
                                                            }
                                                            else {
                                                                    cssItem.marginTop = i ? params.scroller.distance : 0;
                                                            }

                                                            block.scrollerItems.items[i] = $('<div>')
                                                                    .prop('class', prefix + 'scroller-item')
                                                                    .css(cssItem)
                                                                    .appendTo(block.scrollerItems)
                                                                    .bind('click', function() {

                                                                            if (block.slideShow) {
                                                                                    clearInterval(block.slideShow);
                                                                            }
                                                                            block.transition($(this).index());
                                                                    });

                                                            if (horizontal) {

                                                                    block.scrollerItems.items[i].css({
                                                                            marginTop: (params.scroller.width - block.scrollerItems.items[i].outerHeight()) / 2
                                                                    });
                                                            }
                                                            else {

                                                                    block.scrollerItems.items[i].css({
                                                                            marginLeft: (params.scroller.width - block.scrollerItems.items[i].outerWidth()) / 2
                                                                    });
                                                            }

                                                            block.scrollerItems.items[i].image = $('<img>')
                                                                    .prop({
                                                                            'src': images[i].file
                                                                    })
                                                                    .css({
                                                                            height: params.scroller.itemHeight - params.scroller.itemMargin,
                                                                            width: params.scroller.itemWidth - params.scroller.itemMargin,
                                                                            top: params.scroller.itemMargin + 'px',
                                                                            left: params.scroller.itemMargin + 'px'
                                                                    })
                                                                    .appendTo(block.scrollerItems.items[i])
                                                                    .load(function() {

                                                                            if (++k >= images.length) {

                                                                                    block.scroller.css({
                                                                                            visibility: settings.scroller.enable.toInt() ? 'visible' : 'hidden'
                                                                                    });
                                                                            }
                                                                    });

                                                            block.scrollerItems.items[i].bg = $('<div>')
                                                                    .prop('class', prefix + 'scroller-item-bg')
                                                                    .css({
                                                                            opacity: settings.scrollerItem.alpha,
                                                                            background: settings.scrollerItem.color.colorFlashToWeb()
                                                                    })
                                                                    .appendTo(block.scrollerItems.items[i]);

                                                            block.scrollerItems.items[i].disabler = $('<div>')
                                                                    .prop('class', prefix + 'scroller-item-disabler')
                                                                    .css({
                                                                            display: i ? 'block' : 'none'
                                                                    })
                                                                    .appendTo(block.scrollerItems.items[i]);

                                                    }

                                                    if ($.browser.msie && $.browser.version.toInt() < 9) {

                                                            var cssScroller = {
                                                                            visibility: settings.scroller.enable.toInt() ? 'visible' : 'hidden'
                                                            };
                                                    }
                                                    else {

                                                            var cssScroller = {
                                                                            visibility: 'hidden'
                                                            };
                                                    }

                                                    if (horizontal) {

                                                            block.scrollerItems.css({
                                                                    width: (block.scrollerItems.items[0].outerWidth() + params.scroller.distance) * images.length - params.scroller.distance
                                                            });

                                                            cssScroller.top = params.scroller.left;

                                                            switch (settings.scroller.align) {
                                                                    case 'bottom':
                                                                            cssScroller.top = block.height() - block.scrollerItems.height() - cssScroller.top;
                                                                            break;

                                                                    case 'top':
                                                                    default:
                                                                            break;
                                                            }

                                                            block.scrollerBg.css({
                                                                    background: settings.scroller.color.colorFlashToWeb(),
                                                                    width: horizontal ? block.scrollerItems.width() : params.scroller.width,
                                                                    height: horizontal ? params.scroller.width : block.scrollerItems.height()
                                                            });
                                                    }
                                                    else {

                                                            block.scrollerBg.css({
                                                                    background: settings.scroller.color.colorFlashToWeb(),
                                                                    width: horizontal ? block.scrollerItems.width() : params.scroller.width,
                                                                    height: horizontal ? params.scroller.width : block.scrollerItems.height()
                                                            });

                                                            block.scroller.css({
                                                                    height: block.scrollerItems.height()
                                                            });

                                                            cssScroller.left = params.scroller.left;

                                                            switch (settings.scroller.align) {
                                                                    case 'bottom':
                                                                            cssScroller.top = block.height() - block.scrollerBg.height();
                                                                            break;

                                                                    case 'top':
                                                                    default:
                                                                            cssScroller.top = 0;
                                                                            break;
                                                            }
                                                    }

                                                    block.scroller.css(cssScroller);

                                                    var heightBlock = horizontal ? block.width() : block.height();
                                                    var widthBlock = horizontal ? block.scrollerBg.height() : block.scrollerBg.width();
                                                    var widthItem = horizontal ? block.scrollerItems.items[0].height() : block.scrollerItems.items[0].width();
                                                    var heightScroller = horizontal ? block.scrollerBg.width() : block.scrollerBg.height();
                                                    var deltaHeight = heightScroller - heightBlock;
                                                    var deltaWidth = widthItem - widthBlock;

                                                    if (deltaHeight > 0) {

                                                            var offset = block.scrollerBg.offset();

                                                            var min = horizontal 
                                                                            ? offset.top - (deltaWidth > 0 ? deltaWidth / 2 : 0)
                                                                            : offset.left - (deltaWidth > 0 ? deltaWidth / 2 : 0);
                                                            var max = horizontal 
                                                                            ? offset.top + widthBlock + (deltaWidth > 0 ? deltaWidth / 2 : 0)
                                                                            : offset.left + widthBlock + (deltaWidth > 0 ? deltaWidth / 2 : 0);

                                                            var durMax = 3;
                                                            var durMin = 0.1;
                                                            var duration = (durMax - (durMax - durMin) * parseFloat(settings.scroller.speed)) * 1000;

                                                            block.mousemove(function(e) {

                                                                    var top = 0;
                                                                    var move = false;
                                                                    var direction = 0;

                                                                    block.scroller.clearQueue().stop();

                                                                    if ((horizontal ? e.pageY : e.pageX) >= min && (horizontal ? e.pageY : e.pageX) <= max) {

                                                                            if ((horizontal ? e.pageX : e.pageY) > heightBlock * 3 / 4) {

                                                                                    move = true;
                                                                                    top = -deltaHeight;
                                                                            }

                                                                            if ((horizontal ? e.pageX : e.pageY) < heightBlock * 1 / 4) {

                                                                                    move = true;
                                                                            }

                                                                            if (move) {

                                                                                    if (horizontal) {

                                                                                            block.scroller.animate({
                                                                                                    left: top
                                                                                            }, duration.toInt());
                                                                                    }
                                                                                    else {

                                                                                            block.scroller.animate({
                                                                                                    top: top
                                                                                            }, duration.toInt());
                                                                                    }

                                                                            }
                                                                    }
                                                            });
                                                    }
                                            }
                                    });

                                    $.extend(this, {
                                            render: block.init
                                    });

                                    block.init();
                            });
			});
		});
		
		return;
	};
	
	$.fn.setGalleryParam = function(param, value) {
		
		var blocks = this;
		
		$.each(blocks, function() {
			
			param = param.split('.');
			this.settings[param[0]][param[1]] = value;
			
			this.render();
		});
		
		return;
	};
})(jQuery);