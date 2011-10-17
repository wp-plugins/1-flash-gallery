(function($){
 
	var toInt = function() {
		
		return parseInt(this);
	};
	
	var colorFlashToWeb = function() {
		
		return '#' + this.replace(/0x([\w\d]{6})/ig, '$1');
	};
	
	String.prototype.toInt = toInt;
	String.prototype.colorFlashToWeb = colorFlashToWeb;
	Number.prototype.toInt = toInt;

})(jQuery);
