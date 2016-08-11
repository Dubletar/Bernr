jQuery.fn.extend({
insertAtCaret: function(myValue){
  return this.each(function(i) {
    if (document.selection) {
      //For browsers like Internet Explorer
      this.focus();
      var sel = document.selection.createRange();
      sel.text = myValue;
      this.focus();
    }
    else if (this.selectionStart || this.selectionStart == '0') {
      //For browsers like Firefox and Webkit based
      var startPos = this.selectionStart;
      var endPos = this.selectionEnd;
      var scrollTop = this.scrollTop;
      
      this.value = this.value.substring(0, startPos)+myValue+this.value.substring(endPos,this.value.length);
      this.focus();
      this.selectionStart = startPos + myValue.length;
      this.selectionEnd = startPos + myValue.length;
      this.scrollTop = scrollTop;
    } else {
      this.value += myValue;
      this.focus();
    }
  });
}
});

function EditorManager()
{
    var self = this;
    
    // Set Headline Variables.
    var defaultText = 'ARTICLE HEADLINE';
    var headline = $( 'input[name="article-headline"]' );
    var italicize = 'italicize';
    var greyOut = 'grey-out';
    var loadedArticleImages = {};
    
    // Misc.
    var articleContainer = document.getElementById("articleContainer");
    var savedRange, isInFocus;
    
    // Headline function.
    this.checkIfHeadlineEmpty = function () {
        
        if ( $.trim( headline.val() ) === '') {
            
            headline.addClass( italicize );
            headline.addClass( greyOut );
            
        }
        
    };
    
    // Headline function.
    this.checkIfHeadlineGrey = function () {
      
        if ( headline.hasClass( italicize ) ) {
            headline.removeClass( italicize );
        }
        
        if ( headline.hasClass( greyOut ) ) {
            headline.removeClass( greyOut );
        }
        
    };
    
    this.addHeadlineListeners = function () {
        
        // Add event listeners for the headline.
        headline.focusout(function () {
            self.checkIfHeadlineEmpty();        
        });
        
        headline.focus(function () {
            self.checkIfHeadlineGrey();        
        });
        // initiate the check for the headline.
        self.checkIfHeadlineEmpty();
        
    };
    
    this.onClickButtonParagraph = function () {
        
        var content = "\
            <div class='added-article-paragraph-container'>\
                <textarea class='added-article-paragraph no-field-border'>Add your text here.</textarea>\
            </div>\
        ";
        
        articleContainer.append( content );
        
    };
    
    this.onClickButtonSection = function () {
        
        var content = "<h3>Replace This Text</h3>";
        
        self.restoreSelection();
        self.pasteHtmlAtCaret( content );
        
    };
    
    this.onClickButtonBold = function () {
        self.restoreSelection();
        var selection = self.getSelectedText();
        var selection_text = selection.toString();

        // How do I add a span around the selected text?

        var span = document.createElement('b');
        span.textContent = selection_text;

        var range = selection.getRangeAt(0);
        range.deleteContents();
        range.insertNode(span);
        
    };
    
    this.onClickButtonPhoto = function () {
        
        var galleryContainer = $( '#photo-gallery-wrapper' );
        var content;
        var url = Routing.generate( 'add-article-photos', { articleId: $( '#save-article' ).data( 'article-id' ) } );
        var uploadButton;
        var form;
        var filesInput;
        
        if ( galleryContainer.length < 1 ) {
            
            content = "\
            <div id='photo-gallery-wrapper' class='background-popup-wrapper background'>\
                <div class='photo-gallery-container drop-shadow'>\
                    <div class='photo-gallery-header'>\
                        <div class='photo-gallery-header-text'>\
                            " + headline.val() + "\
                        </div>\
                    </div>\
                    <div class='photo-gallery-body clearfix'>\
                        <div class='photo-gallery-menu-column float-left'>\
                            <form id='upload-photos-form' method='post' action='" + url + "' enctype='multipart/form-data'>\
                                <input class='upload-gallery-photos-input' type='file' name='upload-gallery-photos[]' multiple=true/>\
                            </form>\
                            <div class='add-photos-box'>\
                                <div class='add-photos-text'>Add Photos</div>\
                            </div>\
                        </div>\
                        <div class='photo-gallery-image-column float-left'>\
                        </div>\
                    </div>\
                </div>\
            </div>\
            ";
            
            // Add content.
            $( document.body ).append( content );
            // Capture the things we need.
            uploadButton = $( '.add-photos-box' );
            form = $( '#upload-photos-form' );
            filesInput = $( '.upload-gallery-photos-input' );
            // Add actions for each thing captured.
            form.submit(  function ( e ) { 
                
                e.preventDefault();
                
                var formData = new FormData( this );

                $.ajax({
                    type:'POST',
                    url: form.attr( 'action' ),
                    data:formData,
                    cache:false,
                    contentType: false,
                    processData: false,
                    success:function(data){
                        self.loadArticleImages();
                    },
                    error: function(data){
                        console.log(data);
                    }
                });
                
            } );
            
            uploadButton.click( function ( e ) {
                    
                e.preventDefault();
                filesInput.click();
                    
            } );
            
            filesInput.on( 'change', function ( e ) {
                
                if ( e.target.files.length > 0 ) {
                    form.submit();
                }
                
            } );
            
            $( '#photo-gallery-wrapper' ).click( function ( e ) {
                
                if ( e.target == this ) {
                    self.closeGallery();
                }
            });
            
            self.loadArticleImages();
        }
        
    };
    
    this.loadArticleImages = function () {
        
        var url = Routing.generate( 'get-article-photos', { articleId: $( '#save-article' ).data( 'article-id' ) } );
        $.ajax(
        {
            url : url,
            type: "GET",
            success:function(data, textStatus, jqXHR) 
            {
                self.processArticleImages(data.data);
            },
            error: function(jqXHR, textStatus, errorThrown) 
            {
                console.log(jqXHR);
                console.log(textStatus);
                console.log(errorThrown);
            }
        });
    };
    
    this.processArticleImages = function (imageData) {
      
        $.each(imageData, function (k, image) {
            
            if ( !( image.id in loadedArticleImages ) ) {
                
                loadedArticleImages[ image.id ] = image;
                self.displayImagesInGallery( image.id );
            }
            
        });
        
    };
    
    this.displayImagesInGallery = function ( id ) {
      
        id = typeof id !== 'undefined' ? id : false;
        
        if ( id ) {
            self.placeImageInGallery( loadedArticleImages[ id ] );
        } else {
            
            $.each( loadedArticleImages, function ( k, imageData ) {
                self.placeImageInGallery( imageData );
            });
        }
        
    };
    
    self.placeImageInGallery = function ( imageData ) {
        
        var imageElement = document.createElement( 'img' );
        var gallery = $( 'div.photo-gallery-image-column' );
        var galleryW = gallery.width() / 3;
        var galleryH = galleryW;
        var sizeMax = 350;
        var html;
        
        if ( galleryW > sizeMax ) {
            galleryH = sizeMax;
            galleryW = sizeMax;
        }
        
        imageElement.onload = function () {
            
            var maxWidth = galleryW; // Max width for the image
            var maxHeight = galleryH;    // Max height for the image
            var width = this.width;    // Current image width
            var height = this.height;  // Current image height
            var ratio = 0;  // Used for aspect ratio
            var imageHtml;
            var container = $( '#image-holder-' + imageData.id );
            
            // Check if the current width is larger than the max
            if( width > height ){
                ratio = width / height;
                $( this ).css( 'width', (maxHeight / height) * width );
                $( this ).css( 'height', maxHeight );
            }

            // Check if current height is larger than max
            if( height > width ){
                ratio = height / width;
                $( this ).css( 'height', (maxWidth / width) * height );
                $( this ).css( 'width', maxWidth );
            }
            
            container.html('');
            container.append( $( this ) );
        };
        
        var html = "\
        <div class='article-gallery-image-parent clearfix' style='width:"+ galleryW +"px; height:"+ galleryW +"px;'>\
            <div class='article-gallery-image-container' id='image-holder-" + imageData.id +"'>\
            </div>\
            <div class='article-gallery-image-menu-parent'>\
                <div class='article-gallery-image-menu-icon-import drop-shadow' data-image='"+ imageData.id +"'></div>\
            </div>\
        </div>\
        ";
        
        $( 'div.photo-gallery-image-column' ).prepend( html );
        imageElement.src = imageData.url;
        
        $( 'div[data-image='+ imageData.id +']' ).click( function () {
            self.importImageToArticle( $( this ).data( 'image' ) );
        });
    };
    
    this.importImageToArticle = function ( imageId ) {
        
        var content = "<div class='relative-background-popup-wrapper background'></div>";
        $( 'div.photo-gallery-container' ).append( content );
        
        /*
         * self.restoreSelection();
        self.pasteHtmlAtCaret( content );
        self.closeGallery();
        */
    };
    
    this.closeGallery = function () {
        
        loadedArticleImages = {};
        $( '#photo-gallery-wrapper' ).remove();
        
    };
    
    this.getSelectedText = function () {
        
        t = (document.all) ? document.selection.createRange().text : document.getSelection();
        return t;
        
    };
    
    this.stripHtmlOnPaste = function ( e ) {

            e.preventDefault();

            var text = (e.originalEvent || e).clipboardData.getData('text/html') || prompt('Paste something..');
            var $result = $('<div></div>').append($(text));

            self.pasteHtmlAtCaret($result.html());

            // replace all styles except bold and italic
            $.each($(this).find("*"), function(idx, val) {

                var $item = $(val);
                if ($item.length > 0){
                   var saveStyle = {
                        'font-weight': $item.css('font-weight'),
                        'font-style': $item.css('font-style')
                    };
                    $item.removeAttr('style')
                         .removeAttr('class')
                         .removeClass();
                         //.css(saveStyle); 
                }
            });

            // remove unnecesary tags (if paste from word)
            $(this).children('style').remove();
            $(this).children('meta').remove()
            $(this).children('link').remove();

    };    
    
    this.pasteHtmlAtCaret = function (html) {
        var sel, range;
        if (window.getSelection) {
            // IE9 and non-IE
            sel = window.getSelection();
            if (sel.getRangeAt && sel.rangeCount) {
                range = sel.getRangeAt(0);
                range.deleteContents();

                // Range.createContextualFragment() would be useful here but is
                // non-standard and not supported in all browsers (IE9, for one)
                var el = document.createElement("div");
                el.innerHTML = html;
                var frag = document.createDocumentFragment(), node, lastNode;
                while ( (node = el.firstChild) ) {
                    lastNode = frag.appendChild(node);
                }
                range.insertNode(frag);

                // Preserve the selection
                if (lastNode) {
                    range = range.cloneRange();
                    range.setStartAfter(lastNode);
                    range.collapse(true);
                    sel.removeAllRanges();
                    sel.addRange(range);
                }
            }
        } else if (document.selection && document.selection.type != "Control") {
            // IE < 9
            document.selection.createRange().pasteHTML(html);
        }
    };
    
    this.addEditorButtonListeners = function () {
      
        // Grab all the buttons
        var buttons = $( '.editor-bar-icon' );
        var buttonId;
        var functionPrefix = 'onClickButton';
        var distinctName;
        var buttonFunctionName;
        
        $.each( buttons, function ( key, button ) {
            
            // Grab the button Id.
            buttonId = $( button ).attr( 'id' );
            // Get the distinct name of the button.
            distinctName = buttonId.split( '-' );
            // Set the function name we'll call.
            buttonFunctionName = functionPrefix
                + distinctName[ 1 ].charAt( 0 ).toUpperCase()
                + distinctName[ 1 ].slice( 1 );
            // Add the eventlistener.
            $( button ).click( self[ buttonFunctionName ] );
            
        });
        
    };
    
    this.addContentAreaListeners = function () {
      
        $( articleContainer ).keyup( self.saveSelection );
        $( articleContainer ).mouseup( self.saveSelection );
        $( articleContainer ).on( 'paste', self.stripHtmlOnPaste );
        
    };
    
    this.restoreSelection = function () {
        
        isInFocus = true;
        articleContainer.focus();
        if (savedRange != null) {
            if (window.getSelection)//non IE and there is already a selection
            {
                var s = window.getSelection();
                if (s.rangeCount > 0) 
                    s.removeAllRanges();
                s.addRange(savedRange);
            }
            else if (document.createRange)//non IE and no selection
            {
                window.getSelection().addRange(savedRange);
            }
            else if (document.selection)//IE
            {
                savedRange.select();
            }
        }
        
    };
    
    this.saveSelection = function () {
        
        if(window.getSelection)//non IE Browsers
        {
            savedRange = window.getSelection().getRangeAt(0);
        }
        else if(document.selection)//IE
        { 
            savedRange = document.selection.createRange();  
        }
        
        console.log(savedRange);
    };
    
    this.getCursorPosition = function ( element ) {
        
        if (document.activeElement === element) {
            var caretOffset = 0;
            var doc = element.ownerDocument || element.document;
            var win = doc.defaultView || doc.parentWindow;
            var sel;
            if (typeof win.getSelection != "undefined") {
                sel = win.getSelection();
                if (sel.rangeCount > 0) {
                    var range = win.getSelection().getRangeAt(0);
                    var preCaretRange = range.cloneRange();
                    preCaretRange.selectNodeContents(element);
                    preCaretRange.setEnd(range.endContainer, range.endOffset);
                    caretOffset = preCaretRange.toString().length;
                }
            } else if ( (sel = doc.selection) && sel.type != "Control") {
                var textRange = sel.createRange();
                var preCaretTextRange = doc.body.createTextRange();
                preCaretTextRange.moveToElementText(element);
                preCaretTextRange.setEndPoint("EndToEnd", textRange);
                caretOffset = preCaretTextRange.text.length;
            }
            return caretOffset;
        } else {
            return cursorPosition;
        }
        
    };
    
    this.setSaveArticleOptions = function () {
      
        var saveButton = $( '#save-article' );
        var content;
        var title;
        var data;
        var articleId;
        
        saveButton.click(function ( e ) {
            
            e.preventDefault();
            title = headline.val();
            content = $( 'div.article-content' ).html();
            articleId = $( this ).data('article-id');
            data = { title: title, content: content }
            
            $.ajax(
            {
                url : Routing.generate( 'save_article_edits', { 'articleId': articleId }),
                type: "POST",
                data : data,
                success:function(data, textStatus, jqXHR) 
                {
                    console.log(data);
                    console.log(textStatus);
                    console.log(jqXHR);
                },
                error: function(jqXHR, textStatus, errorThrown) 
                {
                    console.log(jqXHR);
                    console.log(textStatus);
                    console.log(errorThrown);
                }
            });
        });
    };
    
    this.init = function () {
        
        // Add event listeners
        self.addHeadlineListeners();
        self.addEditorButtonListeners();
        self.addContentAreaListeners();
        self.setSaveArticleOptions();
    };
    
}

$( document ).ready( function () {
    var watcher = new EditorManager();
    watcher.init();
});