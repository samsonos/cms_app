/** Loader initing routine */
var Loader = function( parentBlock, position )
{	
	// Generate loader id
	this.id = 'canvas-loader_'+Math.ceil( 10000 * Math.random());
	
	// Create DOM objects
	var loaderDOM = s('<div class="__loader_bg"><div class="__loader_middle"><div id="'+this.id+'" class="__loader_canvas"></div><div class="text"></div></div>');
		
	// Canvas loader
	var cl = null;
	
	/** Initialize loader */
	this.init = function(parentBlock)	{
		if(loaderDOM) loaderDOM.hide();

		// If no parent block - set body
		if (!parentBlock) {
            // Remove all full screen loaders
            s('body > .__loader_bg').remove();
            loaderDOM.addClass('__fullscreen');
            parentBlock = s(document.body);
		}

        // Append loader to parent DOM element
        this.parentBlock = parentBlock.append( loaderDOM );
		
		// Create canvas loader
		cl = new CanvasLoader( this.id );
		cl.setColor('#0bba17'); // default is '#000000'
		cl.setShape('square'); // default is 'oval'
		cl.setDiameter(72); // default is 40
		cl.setDensity(63); // default is 40
		cl.setRange(0.6); // default is 1.3
		cl.setSpeed(1); // default is 2
		cl.show(); // Hidden by default	
	};
	
	/** Show loader */
	this.show = function(text, showBG) {

		// Get parentBlock position
		var of = this.parentBlock.offset();
		var w = this.parentBlock.width();
		var h = this.parentBlock.height();
		
		if( showBG ) loaderDOM.css('background-color','rgba(0,0,0,0.6)');
		
                if (!position) {
                        position = {
                                type: 'fixed',
                                left: of.left + 1,
                                top: of.top + 1
                        }
                } else {
                        position.type = (position.type) ? position.type : 'fixed';
                        position.left = (position.left) ? position.left : of.left + 1;
                        position.top = (position.top) ? position.top : of.top + 1;
                }

		loaderDOM.css('position', position.type);
		loaderDOM.left(position.left);
		loaderDOM.top(position.top);
		loaderDOM.width( w );
		loaderDOM.height( h );		
		
		// Set loader text if present
		if( text ) s('.text',loaderDOM).html( text );		
		
		// Calculate loader image height depending on parent block size
		var mHeight = 125;		
		if( mHeight > this.parentBlock.height() ) mHeight = this.parentBlock.height() * 0.9;
			
		// Set loader diameter
		cl.setDiameter( mHeight );
		
		var centerDOM = s('.__loader_middle', loaderDOM );		
		centerDOM.top( (h/2) - mHeight/2 );
			
		// Show loader
		loaderDOM.show();		
	};
	
	/** Hide loader */
	this.hide = function()
	{		
		loaderDOM.hide();
	};
	
	/** Hide loader */
	this.remove = function()
	{		
		loaderDOM.remove();
	};
	
	/** Init loader */
	this.init( parentBlock );
};