(function ( $ ) {
	var $window = $(window);
	var windowHeight = $window.height();
	
	$window.resize(function() {
		windowHeight = $window.height();
	});

	$.fn.parallax = function(type, pos, speed) {
		var $this = $(this);

		function update() {
			var scrollPos = $window.scrollTop();

			$this.each(function() {
				var $element = $(this);
				if(type == 'backgroundPosition') {
					$this.css(type, "50% " + Math.round((pos - scrollPos) * speed) + 'px');
				} 
				else {
					$this.css(type, (Math.round(pos - (scrollPos * speed))) + 'px');
				}
			});
		}
		$window.bind('scroll', update).resize(update);
		update();
	};
})(jQuery);