function DOUBLY_Front(){
	
	var g_objMenuCopy, g_objMenuPaste, g_objMenuCopySection;
	var g_options, g_ajaxUrl, g_nonce = "", g_objBody;
	var g_objLoader, g_objMessageSuccess, g_objMessageError,g_objPanelButtonCopy;
	var g_objInput,g_objPasteInput, g_objInputFile, g_objTopPanelText, g_objTopPanelMiddleText, g_objButtonImport;
	
	var t = this, g_texts;
	var g_showDebug = false;
	
	
	var g_vars = {
		is_admin:false,
		SUCCESS_MESSAGE_TIMEOUT: 2000,
		ERROR_MESSAGE_TIMEOUT: 8000,
		copied_text: "",
		is_file_selected:false,
		STATE_PASTE:"paste_mode",
		STATE_COPY:"copy_mode",
		STATE_COPYING_SECTION:"copying-section",
		STATE_COPYING_SECTION_FRONT:"copying-section-front",
		STATE_SECTION_SELECT:"section-select",
		STATE_PASTING_SECTION:"pasting-section",
		STATE_PASTING_POST:"pasting-post",
		STATE_PASTE_DIALOG:"paste-dialog",
		CLASS_MAIN_LOADING:"doubly-main-loading",
		CLASS_SECTIONS_ASK:"doubly-section-ask-action-mode",
		state:null,
		enable_front_copy:false,
		is_safari:false,
		//copy_timeout:1000,	  //test
		copy_timeout:4700,
		timeout_clear_ask:7000
	};
	
	var g_temp = {
		show_error_trace:false,
		handle:null,
		handle_ask:null
	};
	
	
	function __________GENERAL_FUNCTIONS_____________(){}
	
	
	/**
	 * console log
	 */
	function trace(str){
		console.log(str);
	}
	
	/**
	 * get object property
	 */
	function getVal(obj, name, defaultValue){
		
		if(!defaultValue)
			var defaultValue = "";
		
		var val = "";
		
		if(!obj || typeof obj != "object")
			val = defaultValue;
		else if(obj.hasOwnProperty(name) == false){
			val = defaultValue;
		}else{
			val = obj[name];			
		}
		
		return(val);
	}

	
	
	
	/**
	 * replace all occurances
	 */
	function replaceAll(text, from, to){
		
		return text.split(from).join(to);		
	};
	
	
	/**
	 * get filename from path
	 */
	function getFilenameFromPath(pathFile){
		
		if(!pathFile)
			return(null);
		
		var pathFile = replaceAll(pathFile, "\\","/");		
		var posSlash = pathFile.lastIndexOf("/");
		
		if(posSlash == -1)
			return(null);
		
		var filename = pathFile.substring(posSlash+1);
		
		return(filename);
	}
	
	/**
	 * check if the request timeout ok for clipboard
	 * @param timeDiff
	 */
	function isRequestTimeoutOKforClipboard(requestTime){
		
		if(requestTime > g_vars.copy_timeout)
			return(false);
		
		if(g_vars.is_safari == true)
			return(false);
		
		return(true);
	}
	
	
	function __________AJAX_____________(){}
	
	
	/**
	 * show ajax debug
	 */
	function showAjaxDebug(str){
		
		try{
		
			str = jQuery.trim(str);
			
			if(!str || str.length == 0)
				return(false);
		
			var objStr = jQuery(str);
			
			if(objStr.find("header").length || objStr.find("body").length){
				str = "Wrong ajax response!";
			}
		
		}catch(error){}
		
		
		var objDebug = jQuery("#doubly_front_debug");
		
		if(objDebug.length == 0){
			
			alert(str);
			
			throw new Error("debug not found");
		}
		
		objDebug.show();
		
		str += "<a href='javascript:void(0)' class='doubly-debug-close'>X</a>";
		
		objDebug.html(str);
		
		var objButton = objDebug.find(".doubly-debug-close");
		objButton.on("click", function(){
			objDebug.hide();
		});
		
	}
	
	/**
	 * show error or run function
	 */
	function ajaxShowError(message, onError){
		
		if(onError){
			onError(message);
			return(false);
		}
		
		showErrorMessage(message);
	}
	
	
	
	/**
	 * ajax request file
	 */
	function ajaxRequestFile(action, objData, onSuccess, onError, objFileInput){
				
		var fd = new FormData();
		
		var files = objFileInput[0].files;
		
		if(files.length == 0)
			throw new Error("No file found");
		
		var file = files[0];
		
		fd.append("file", file);
		
		fd.append("action","doubly_ajax_actions");
		fd.append("client_action", action);
		fd.append("nonce", g_nonce);
		
		if(objData){
			
			for(var key in objData){
				
				var value = objData[key];
				
				fd.append(key, value);
			}
			
		}
		
		var ajaxOptions = {
			type:"post",
			url:g_ajaxUrl,
			data: fd,
			contentType: false,
			cache: false,
			processData:false,
			success:function(response){
				ajaxResponseSuccess(response, action, onSuccess, onError);
			},
			error:function(jqXHR, textStatus, errorThrown){
				
				ajaxResponseError(jqXHR, textStatus, onError);
				
			}
		};
			
		jQuery.ajax(ajaxOptions);
		
		
	}
	
	
	/**
	 * ajax response success
	 */
	function ajaxResponseSuccess(response, action, onSuccess, onError){
		
		if(!response){
			throw new Error("Empty ajax response!");
			return(false);					
		}
		
		if(typeof response != "object"){
			
			try{
				
				response = jQuery.parseJSON(response);
				
			}catch(e){
				
				showAjaxDebug(response);
				
				ajaxShowError("Ajax Error!!! not ajax response", onError);
				return(false);
			}
		}
		
		if(response == -1){
			throw new Error("ajax error!!!");
			return(false);
		}
		
		if(response == 0){
			
			ajaxShowError("ajax error, action: <b>"+action+"</b> not found",onError);
			return(false);
		}
		
		if(response.success == undefined){
			
			ajaxShowError("The 'success' param is a must!",onError);
			return(false);
		}
		
		
		if(response.success == false){
			
			ajaxShowError(response.message,onError);
			
			return(false);
		}
							
		if(typeof onSuccess == "function"){
									
			onSuccess(response);
		}
		
	}
	
	/**
	 * on ajax error
	 */
	function ajaxResponseError(jqXHR, textStatus, onError){
		
		switch(textStatus){
			case "parsererror":
			case "error":
				
				ajaxShowError("parse error",onError);
				
				showAjaxDebug(jqXHR.responseText);
				
			break;
		}
		
	}
	
	
	/**
	 * small ajax request
	 */
	function ajaxRequest(action, objData, onSuccess, onError, objFileInput){
		
		if(!objData)
			var objData = {};
		
		if(typeof objData != "object")
			throw new Error("wrong ajax param");
		
		if(objFileInput){		//file ajax send
			
			ajaxRequestFile(action, objData, onSuccess, onError, objFileInput);
			return(false);
		
		}
		
		
		var ajaxData = {};
		ajaxData["action"] = "doubly_ajax_actions";
		ajaxData["client_action"] = action;
		ajaxData["nonce"] = g_nonce;
		
		if(objData)
			ajaxData["data"] = objData;
	
		var ajaxOptions = {
				type:"post",
				url:g_ajaxUrl,
				success:function(response){
					
					ajaxResponseSuccess(response, action, onSuccess, onError);
					
				},
				error:function(jqXHR, textStatus, errorThrown){
					
					ajaxResponseError(jqXHR, textStatus, onError);
					
				}
		}
		
		ajaxOptions.data = ajaxData;
		
		ajaxOptions.dataType = 'json';
		
		jQuery.ajax(ajaxOptions);
		
	}
	
	
	/**
	 * get ajax GET url
	 */
	function getUrlAjax(action, params){
		
		var urlAjax = g_ajaxUrl;
		urlAjax += "?action=doubly_ajax_actions"
		urlAjax += "&nonce="+g_nonce;
		urlAjax += "&client_action="+action;
		
		if(params)
			urlAjax += "&"+params;
		
		return(urlAjax);
	}
	
	
	function __________MESSAGES_____________(){}
	
	
	/**
	 * set popup message content
	 */
	function setPopupMessageContent(objMessage, text){
		
		var objContent = objMessage.find(".doubly-front-popup__content");
		
		if(objContent.length == 0){
			trace(objContent);
			throw new Error("Popup Content Not Found!");
		}
		
		objContent.html(text);
	}
	
	
	/**
	 * show ajax error, should be something visible
	 */
	function showErrorMessage(message, func){
		
		if(typeof(message) == "object" && g_temp.show_error_trace == true){
			trace(message);
		}
					
		hideAllMessages();
		
		setPopupMessageContent(g_objMessageError, message);
		g_objMessageError.show();
		
		setTimeout(function(){
			
			g_objMessageError.hide();
			
			if(func)
				func();
			
		},g_vars.ERROR_MESSAGE_TIMEOUT);
		
	}
	
	
	/**
	 * show loader
	 */
	function showLoader(text){
		
		hideAllMessages();
		
		setPopupMessageContent(g_objLoader, text);
		
		g_objLoader.show();
	}
	
	
	/**
	 * hide the loader
	 */
	function hideLoader(){
		
		g_objLoader.hide();
		
	}
	
	/**
	 * hide all messages
	 */
	function hideAllMessages(){
		
		g_objBody.removeClass(g_vars.CLASS_MAIN_LOADING);
		
		g_objPanelButtonCopy.removeClass("doubly-loading-mode");
		
		g_objLoader.hide();
		
		g_objMessageError.hide();
		
		g_objMessageSuccess.hide();
	}
	
	
	/**
	 * show success message
	 * by default close it after some time
	 */
	function showSuccessMessage(text, noDissapear){
		
		hideAllMessages();
		
		g_objMessageSuccess.show();
		
		setPopupMessageContent(g_objMessageSuccess, text);
		
		if(noDissapear !== true){
			
			setTimeout(function(){
				
				g_objMessageSuccess.hide();
								
			},g_vars.SUCCESS_MESSAGE_TIMEOUT);
			
		}
		
	}
	
	/**
	 * show safari error message
	 */
	function showSafaryErrorMessage(){
		
		showErrorMessage("The copy feature not working in Safari browser right now. Please try from Chrome or Firefox browser");
		
	}
	
	function __________COPY_____________(){}

	/**
	 * tells if it's safari browser or not
	 */	
	function isSafariBrowser(){
						
		if(!navigator.vendor)
			return(false);
				
		var vendor = navigator.vendor;
		
		if(vendor.indexOf('Apple') == -1)
			return(false);
		
		if(!navigator.userAgent)
			return(false);
				
		var userAgent = navigator.userAgent;
				
		var isSafari = userAgent.indexOf('CriOS') == -1 && userAgent.indexOf('FxiOS') == -1;
		
		return(isSafari);
	}
	
	
	/**
	 * copy text to clipboard
	 */
	function copyTextToClipboard(copyText){
		
		g_objInput.val(copyText);
		
		g_objInput.focus();
		
		var input = g_objInput[0];
		
		input.select();
        input.setSelectionRange(0, 99999);
        		
    	document.execCommand("copy");		
    	
	}
	
	/**
	 * on copy button response - close loading state
	 */
	function onCopyButtonResponse(response){
		
		closeCurrentState();
		g_objBody.removeClass(g_vars.CLASS_MAIN_LOADING);
		
		var copyText = response.copy_text;
		
		copyTextToClipboard(copyText);
		
		//close state after finish
		showSuccessMessage(response.message, false, true);
		
	}
	
	/**
	 * clear the copy button
	 */
	function clearTopCopyButton(){
		
		g_objPanelButtonCopy.removeClass("doubly-ask-action-mode");
		g_objPanelButtonCopy.removeData("response");
		
	}
	
	/**
	 * on copy click
	 */
	function onCopyClick(){
		
		try{
			
			var objLinkCopy = jQuery(this);
			
			//copy to clipboard mode
			
			if( objLinkCopy.hasClass("doubly-ask-action-mode") ){
				
				var response = objLinkCopy.data("response");
				
				onCopyButtonResponse(response);
				
				clearTopCopyButton();
				
				return(true);
			}
			
			objLinkCopy.blur();
			
			//avoid double click
			
			if(objLinkCopy.hasClass("doubly-loading-mode"))
				return(true);
			
			//copy the current post
			var postID = getVal(g_options, "postid");
			var copyMode = getVal(g_options, "copymode");
			
			if(!postID)
				throw new Error("Can't copy the post, no post id found");
			
			var postType = getVal(g_options, "posttype");
			
			var objData = {};
			objData["postid"] = postID;
			objData["posttype"] = postType;
			
			if(copyMode)
				objData["copymode"] = copyMode;
			
			g_objBody.addClass(g_vars.CLASS_MAIN_LOADING);
			objLinkCopy.addClass("doubly-loading-mode");
			
			var timeStart = Date.now();
			
			ajaxRequest("copy_post",objData, function(response){
				
				var timeEnd = Date.now();
				var timeDiff = timeEnd - timeStart;
				
				var isTimeoutOK = isRequestTimeoutOKforClipboard(timeDiff);
				
				objLinkCopy.removeClass("doubly-loading-mode");
				
				if(isTimeoutOK == true){
					
					onCopyButtonResponse(response);
					return(true);
				}
								
				objLinkCopy.addClass("doubly-ask-action-mode");
				
				objLinkCopy.data("response", response);
				
				
			});
		
		}catch(e){
			
			showErrorMessage(e);
			
		}
		
		
	}
	
	/**
	 * start copy elementor section
	 */
	function startCopyMode(){
		
		//clear the copy button:
		
		clearTopCopyButton();
		
		t.setState(g_vars.STATE_COPY);
		
		prepareSectionsForCopy();
		
	}
	
	
	function __________COPY_SECTION_____________(){}
	
	/**
	 * clear all sections ask
	 */
	function clearSectionsAsk(){
		
		if(g_temp.handle_ask)			
			clearTimeout(g_temp.handle_ask);
		
		g_temp.handle_ask = null;
		
		var objSections = jQuery("." + g_vars.CLASS_SECTIONS_ASK);
		
		if(objSections.length == 0)
			return(false);
		
		objSections.removeClass(g_vars.CLASS_SECTIONS_ASK);
		
		objSections.removeData("response");
		
		closeCurrentState();
		
	}
	
	
	/**
	 * handle copy section response
	 */
	function handleSectionCopyResponse(objSection, response, isFrontCopy){
		
		var copyText = response.copy_text;
		
		copyTextToClipboard(copyText);
		
		objSection.addClass("doubly-success-mode");
		
		if(isFrontCopy == false)
			showSuccessMessage(g_texts["copy_section_success_message"], true);
		
		var timeout = 1500;
		if(isFrontCopy == true)
			timeout = 800;
		
		setTimeout(function(){
			
			objSection.removeClass("doubly-success-mode");
			
			hideAllMessages();
			closeCurrentState();
			
		}, timeout);
		
		
	}
	
	
	/**
	 * on copy section click
	 */
	function onCopySectionClick(event, isFrontCopy, objCopyButton){
		
		if(!isFrontCopy)
			var isFrontCopy = false;
		
		var objLinkCopy = jQuery(this);
				
		var selectorOverlay = ".doubly-front-copy-section-overlay";
		var setState = g_vars.STATE_COPYING_SECTION;
		
		if(isFrontCopy == true){
			
			selectorOverlay = ".doubly-front-copy-section-button-overlay";
			objLinkCopy = objCopyButton;
			
			setState = g_vars.STATE_COPYING_SECTION_FRONT;
		}
		
		objLinkCopy.blur();
		
		var objWrapper = objLinkCopy.parents(selectorOverlay);
		
		if(objWrapper.length == 0)
			throw new Error("No overlay found");
		
		var objSection = getParentSection(objLinkCopy);
		
		if(objSection.length == 0)
			throw new Error("No section found");

		//copy to clipboard right away (ask mode)
		
		if(objSection.hasClass(g_vars.CLASS_SECTIONS_ASK)){
			
			var response = objSection.data("response");
			
			handleSectionCopyResponse(objSection, response, isFrontCopy);
			
			clearSectionsAsk();
			
			return(false);
		}
		
		
		//click in the middle protection
		switch(g_vars.state){
			case null:
			case g_vars.STATE_COPY:
			break;
			default:
				trace("wrong state: "+g_vars.state);
				return(false);
			break;
		}
		
		
		//copy the current post
		var postID = getSectionLayoutID(objSection);
		var sectionID = objSection.data("id");
		
		if(!postID)
			throw new Error("Can't copy the post, no post id found");
		
		var objData = {};
		objData["postid"] = postID;
		objData["sectionid"] = sectionID;
		
		//add loader class
		
		t.setState(setState);
				
		objSection.addClass("doubly-loading-mode");
		
		var ajaxAction = "copy_elementor_section";
		if(isFrontCopy == true)
			ajaxAction = "copy_elementor_section_front";
		
		var timeStart = Date.now();
		
		ajaxRequest(ajaxAction, objData, function(response){
		
			var timeEnd = Date.now();
			var timeDiff = timeEnd - timeStart;
			
			var isTimeoutOK = isRequestTimeoutOKforClipboard(timeDiff);
			
			objSection.removeClass("doubly-loading-mode");
						
			if(isTimeoutOK == true || isFrontCopy != true){
				
				handleSectionCopyResponse(objSection, response, isFrontCopy);
				return(true);
			}
			
			//show the ask for action
			
			objSection.addClass(g_vars.CLASS_SECTIONS_ASK);
			objSection.data("response", response);
			
			g_temp.handle_ask = setTimeout(clearSectionsAsk, g_vars.timeout_clear_ask);
			
			
		}, function(error){		//on error
			
			showErrorMessage(error);
			
		});
		
	}
	
	/**
	 * export section click = redirect to export url
	 */
	function onExportSectionClick(){
		
		var objButton = jQuery(this);
		
		var objSection = getParentSection(objButton);
		
		if(objSection.length == 0)
			throw new Error("No section found");
		
		var postID = getSectionLayoutID(objSection);
		
		if(!postID)
			throw new Error("Can't copy the post, no post id found");
		
		var sectionID = objSection.data("id");
		
		var params = "sectionid="+sectionID;
		params += "&postid="+postID;
		
		var urlAjax = getUrlAjax("export_elementor_section", params);
		
		location.href=urlAjax;
	}
	
	/**
	 * get elementor main sections 
	 * onlyMiddle - no headers and footers
	 */
	function getElementorMainSections(objTemplate, onlyMiddle){
		
		if(objTemplate)
			var objLayouts = objTemplate;				
		else
			var objLayouts = jQuery("body").find(".elementor");
		
		
		if(objLayouts.length == 0)
			return([]);
				
		var objAllSections = jQuery();
				
		jQuery.each(objLayouts, function(index, layout){
			
			var objLayout = jQuery(layout);
			
			var location = getElementorLayoutLocation(objLayout);
			
			if(onlyMiddle == true && location == "header")
				return(true);
			
			var objElements = objLayout.find(".elementor-element");
			
			if(objElements.length == 0)
				return(true);
			
			//the layout must be parent, not dynamic loop item
			var objParentLayout = objLayout.parents(".elementor").not("body");
			
			
			if(objParentLayout.length != 0)
				return(true);
						
			var objFirstElement = jQuery(objElements[0]);
						
			var objParent = objFirstElement.parent();
						
			var objSections = objParent.children(".elementor-element");
			
			objAllSections = objAllSections.add(objSections);
			
		});
		
		
		return(objAllSections);
	}
	
	
	/**
	 * get section layout id
	 */
	function getSectionLayoutID(objSection){
		
		//find post id
		var objTemplate = objSection.parents(".elementor");
		if(objTemplate.length == 0)
			throw new Error("Section Layout not found");
		
		var postID = objTemplate.data("elementor-id");
		
		return(postID);
	}
	
	
	/**
	 * get element parent section
	 */
	function getParentSection(objElement){
				
		var objSection = objElement.parents(".elementor-element");		
		
		return(objSection);
	}
	
	
	/**
	 * prepare sections for copy
	 */
	function prepareSectionsForCopy(){

		//add overlay to all the sections
		var objTemplate = jQuery("#doubly_copy_section_overlay_template");
		
		var objSections = getElementorMainSections();
		
		if(objSections.length == 0)
			return(false);
		
		jQuery.each(objSections, function(index, section){
			
			var objSection = jQuery(section);
			
			objSection.removeClass("doubly-success-mode");
			
			var isCopyEnabled = objSection.data("doubly_copy_enabled");
			if(isCopyEnabled === true)
				return(true);
			
			objSection.addClass("doubly-copy-enabled");
			
			var objOverlay = objTemplate.clone();
			objOverlay.removeAttr("id");
			
			objSection.append(objOverlay);
			
			objSection.data("doubly_copy_enabled", true);
			
		});
		
	}
	
	/**
	 * check that elementor sections exists
	 */
	function isElementorSectionsExists(){
		
		var objSections = getElementorMainSections();
		
		var isExists = (objSections.length > 0);
		
		return(isExists);
	}
	
	function __________FRONT_COPY_SECTION_____________(){}
	
	/**
	 * get elementor layout location
	 */
	function getElementorLayoutLocation(objLayout){
		
		if(objLayout.hasClass("elementor-location-header"))
			return("header");

		if(objLayout.hasClass("elementor-location-footer"))
			return("header");
		
		return("page");
	}

	
	/**
	 * prepare sections for front copy
	 */
	function prepareSetionForFrontCopy(objFrontOverlay, objSections){
		
		jQuery.each(objSections, function(index, section){
			
			var objSection = jQuery(section);
			
			objSection.removeClass("doubly-success-mode");
			
			var isCopyFrontEnabled = objSection.data("doubly_copy_front_enabled");
			if(isCopyFrontEnabled === true)
				return(true);
						
			objSection.addClass("doubly-copy-front-enabled");
			
			var objOverlay = objFrontOverlay.clone();
			objOverlay.removeAttr("id");
						
			objSection.append(objOverlay);
			
			objSection.data("doubly_copy_front_enabled", true);
		});
		
	}
	
	/**
	 * on front copy click
	 */
	function onCopySectionFrontClick(event){
		
		var objLink = jQuery(this);
		
		var objOverlay = objLink.parents(".doubly-front-copy-section-button-overlay");
		
		objOverlay.removeClass("doubly-show-info");
		
		var objButton = objLink.parent();
		
		onCopySectionClick(event, true, objButton);
		
	}
	
	
	/**
	 * on front icon click, show the info box
	 */
	function onCopySectionFrontIconClick(){
		
		var objLink = jQuery(this);
		var objOverlay = objLink.parents(".doubly-front-copy-section-button-overlay");
		
		objOverlay.toggleClass("doubly-show-info");
		
		
	}
	
	/**
	 * on mouse leave - set timeout to hide the button
	 */
	function onCopySectionFrontMouseLeave(){

		var objButton = jQuery(this);
		var objOverlay = objButton.parents(".doubly-front-copy-section-button-overlay");
		
		if(objOverlay.hasClass("doubly-show-info") == false)
			return(false);
		
		g_temp.handle = setTimeout(function(){ 
			
			objOverlay.removeClass("doubly-show-info");
			
		}, 1000);
		
	}
	
	
	/**
	 * on mouse enter - set the lock for hide the class
	 */
	function onCopySectionFrontMouseEnter(){
				
		var objButton = jQuery(this);
		var objOverlay = objButton.parents(".doubly-front-copy-section-button-overlay");
		
		if(objOverlay.hasClass("doubly-show-info") == false)
			return(false);
		
		if(!g_temp.handle)
			return(false);
		
		clearTimeout(g_temp.handle);
		g_temp.handle = null;
		
	}
	
	/**
	 * clear the remove class timeout
	 */
	function onCopySectionFrontInfoMouseEnter(){
		
		if(!g_temp.handle)
			return(true);
				
		clearTimeout(g_temp.handle);
		g_temp.handle = null;
	}
	
	
	/**
	 * copy section mouse leave - set timeot for close again
	 */
	function onCopySectionFrontInfoMouseLeave(){
		
		var objInfo = jQuery(this);
		
		var objOverlay = objInfo.parents(".doubly-front-copy-section-button-overlay");

		g_temp.handle = setTimeout(function(){ 
						
			objOverlay.removeClass("doubly-show-info");
			
		}, 1000);
		
	}
	
	
	/**
	 * init front copy - if exists
	 */
	function initFrontCopy(skipTimeout){
				
		//a little delay - for device mode to appear
		if(skipTimeout !== true){
			setTimeout(function(){
				initFrontCopy(true);
			},300);
			
			return(false);
		}
		
		if(g_showDebug == true){
			trace("doubly init front copy");
		}
		
		
		var deviceMode = g_objBody.data("elementor-device-mode");
		
		if(deviceMode === "mobile")
			return(false);
		
		var objFrontOverlay = jQuery("#doubly_copy_section_front_overlay_template");
		
		if(objFrontOverlay.length == 0)
			return(false);
		
		var objSections = getElementorMainSections(null, true);
		
		if(objSections.length == 0)
			return(false);
		
		g_vars.enable_front_copy = true;
		
		prepareSetionForFrontCopy(objFrontOverlay, objSections);
		
		initFrontCopyEvents();
	}

	/**
	 * init front copy events
	 */
	function initFrontCopyEvents(){
		
		g_objBody.on("click", ".doubly-button-copy-section-front__text", onCopySectionFrontClick);
		
		g_objBody.on("click", ".doubly-button-copy-section-front__icon", onCopySectionFrontIconClick);
		
		g_objBody.on("mouseleave", ".doubly-button-copy-section-front", onCopySectionFrontMouseLeave);
		g_objBody.on("mouseenter", ".doubly-button-copy-section-front", onCopySectionFrontMouseEnter);
		
		g_objBody.on("mouseenter", ".doubly-button-copy-section-info", onCopySectionFrontInfoMouseEnter);
		
		g_objBody.on("mouseleave", ".doubly-button-copy-section-info", onCopySectionFrontInfoMouseLeave);
		
	}
	
	function __________SECTION_SELECT_____________(){}
	
	/**
	 * get site main div
	 */
	function getSiteMainDiv(){
		
		var objMain = g_objBody.children("main");
		
		if(objMain.length)
			return(objMain);
		
		var objMain = g_objBody.find(".site-main");
		if(objMain.length)
			return(objMain);
		
		//after header
		var objHeader = g_objBody.children("header");
		
		if(objHeader.length){
			var objMain = objHeader.next();
			if(objMain.length)
				return(objMain);
		}
		
		var objContent = jQuery(".page-content");
		if(objContent.length){
			var objMain = objContent.parent();
			
			if(objMain.length)
				return(objMain);
		}
			
		
		return(null);
	}

	/**
	 * prepare empty elementor section in case that no elementor content and elementor page
	 */
	function prepareElementorEmptySection(){
		
		var isPage = g_objBody.hasClass("elementor-page");
		
		if(isPage == false)
			return(false);
		
		//check if there is elementor page wrapper
		var objWrappers = jQuery(".elementor:not(.elementor-location-header,.elementor-location-footer)");
		
		if(objWrappers.length)
			return(false);
		
		//if not, it's empty elementor page add empty page classes
		
		var objMainDiv = getSiteMainDiv();
		
		if(!objMainDiv || objMainDiv.length == 0)
			return(false);
		
		if(objMainDiv.hasClass("doubly-empty-elementor-section"))
			return(false);
		
		objMainDiv.addClass("doubly-empty-elementor-section");
		
	}
	
	
	/**
	 * prepare sections for paste
	 */
	function prepareSectionsForPaste(){
		
		prepareElementorEmptySection();		
		
		var objTemplates = jQuery(".elementor,.doubly-empty-elementor-section");
		
		if(objTemplates.length == 0)
			return(false);
				
		var objOverlay = jQuery("#doubly_paste_section_overlay_template");
		
		if(objOverlay.length == 0)
			throw new Error("doubly_paste_section_overlay_template - Paste Section template not found");
		
		jQuery.each(objTemplates, function(index, template){
			
			var objTemplate = jQuery(template);
			var isEmptySection = false;
			
			if(objTemplate.hasClass("doubly-empty-elementor-section")){
				
				var elementorType = "page";
				var objSections = objTemplate;
				isEmptySection = true;
			}else{
				
				var elementorType = objTemplate.data("elementor-type");
				var objSections = getElementorMainSections(objTemplate);
			}
			
			
			if(objSections.length == 0)
				return(true);
			
			//remove loading state
			
			var objLoadingItems = objSections.find(".doubly-loading");
			if(objLoadingItems.length)
				objLoadingItems.removeClass(".doubly-loading");
			
			jQuery.each(objSections, function(index, section){
				
				var objSection = jQuery(section);
				
				var isPasteEnabled = objSection.data("doubly_paste_enabled");
				if(isPasteEnabled === true)
					return(true);
				
				objSection.addClass("doubly-paste-enabled");
				
				//top overlay
				
				var objTopCloned = objOverlay.clone();
				
				objTopCloned.removeAttr("id");
				objTopCloned.removeAttr("style");
				objTopCloned.addClass("doubly-paste-section-overlay--top");
				objTopCloned.data("position","before");
				
				//bottom overlay
				
				var objBottomCloned = objOverlay.clone();
				
				objBottomCloned.removeAttr("id");
				objBottomCloned.removeAttr("style");
				objBottomCloned.data("position","after");
				
				
				switch(elementorType){
					case "header":
						
						objTopCloned.addClass("doubly-type-header");
						objBottomCloned.addClass("doubly-type-header");
						
					break;
					case "footer":
						
						objTopCloned.addClass("doubly-type-footer");
						objBottomCloned.addClass("doubly-type-footer");
						
					break;
				}
				
				objSection.append(objTopCloned);
				
				//check position
				var pos = objSection.position();
				
				if(pos.top <= 40)
					objTopCloned.addClass("uc-strong-zindex");
				
				if(isEmptySection == false){
					objSection.append(objBottomCloned);
				}
				
				objSection.data("doubly_paste_enabled", true);
				
			});
			
		});
		
		
		
	}
	
	
	/**
	 * on paste section click - paste the section
	 */
	function onPasteSectionClick(){
			
			var objButton = jQuery(this);
			
			t.setState(g_vars.STATE_PASTING_SECTION);
			
			var objOverlay = objButton.parents(".doubly-paste-section-overlay");
			
			objOverlay.addClass("doubly-loading");
			
			//prepare data
			
			var position = objOverlay.data("position");
			
			var objSection = objOverlay.parent();
			
			if(objSection.hasClass("doubly-empty-elementor-section")){
				
				var sectionID = "new";
				var postID = getVal(g_options, "postid");
								
			}else{
				
				var sectionID = objSection.data("id");
				
				//find post id
				var objTemplate = objSection.parents(".elementor");
				if(objTemplate.length == 0)
					throw new Error("Section Template not found");
				
				var postID = objTemplate.data("elementor-id");
			}
			
			if(!postID)
				throw new Error("Post id of the section not found");
				
			var objData = {};
			objData.postid = postID;
			objData.sectionid = sectionID;
			objData.position = position;
			
			var objFile = null;
			
			if(g_vars.is_file_selected == true){
				
				objFile = g_objInputFile;
			}else{
				
				objData.copy_text = g_vars.copied_text;
			}
			
			var action = "paste_elementor_section";
			if(objFile)
				action = "import_elementor_section";
			
			
			// send request
			
			ajaxRequest(action, objData, function(response){
				
				objOverlay.removeClass("doubly-loading");
				
				showSuccessMessage(response.message, true);
				
				location.reload();
				
			}, function(error){		//on error
				
				objOverlay.removeClass("doubly-loading");
				
				closeCurrentState();
				
				showErrorMessage(error);
			
			}, objFile);
			
		
	}
	
	
	/**
	 * start section select mode
	 */
	function startSectionSelectMode(){
		
		if(g_vars.state == g_vars.STATE_SECTION_SELECT)
			return(false);
				
		t.setState(g_vars.STATE_SECTION_SELECT);
		
		prepareSectionsForPaste();
		
	}
	
	
	function __________PASTE_____________(){}
	
	/**
	 * on paste click
	 */
	function onPasteClick(){
		
		try{
			
			var objLinkPaste = jQuery(this);
			objLinkPaste.blur();
			
			g_objCopyPasteFront.setState("paste_mode");
			
			
			/*
			doublyGetTextFromClipboard(function(text, t){
				
				if(!text)
					return(false);
				
				var isNoValidText = pasteStep2_handleText(text, true);
				
				//if the text not valid, start paste mode
				if(isNoValidText === true)
					g_objCopyPasteFront.setState("paste_mode");
				
			});
			*/
			
		}catch(error){
			showErrorMessage(error);
		}
		
	}
	
	
	/**
	 * paste - step 2, start section import or post import dialog
	 */
	function pasteStep2_handleText(text, isReturn){
		
		try{
			
			//clear paste input
			g_objPasteInput.val("");
			
			if(!text){
				
				if(isReturn)
					return(true);
				
				throw new Error("No paste content found");
			}
			
			if(text.indexOf("doubly_") == -1){
				
				if(isReturn)
					return(true);
				
				throw new Error("No copied content detected, please try again, or export/import zip functionality instead.");
			}
			
			g_vars.copied_text = text;
			g_vars.is_file_selected = false;
			
			var dataType = "single";
			if(text.indexOf("doubly_section_") != -1)
				dataType = "section";
			
			if(text.indexOf("doubly_multiple") != -1)
				dataType = "multiple";
						
			if(dataType == "section"){
				
				startSectionSelectMode();
				return(false);
			}
			
			//start the paste dialog mode
			
			startImportPost(dataType);
						
		}catch(e){
			
			//clear 
			setTimeout(function(){
				
				clearPasteInput();
				
			},500);
			
			showErrorMessage(e);
			
		}
		
	}
	
	/**
	 * send import request
	 */
	function pasteStep3_onDialogImportClick(){
				
		//clear paste input
		g_objPasteInput.val("");
		
		var objRadioOverwrite = jQuery("#doubly_dialog_import_radio_overwrite");
		
		var isOverwrite = objRadioOverwrite.is(":checked");
		
		sendPasteRequest(isOverwrite);
		
	}
	
	
	/**
	 * send paste request
	 */
	function sendPasteRequest(isOverwrite){
		
		try{
			
			var postID = getVal(g_options, "postid");
			
			if(!postID || isOverwrite == false)
				postID = "new";
			
			var copyMode = getVal(g_options, "copymode");
						
			var objData = {};
			objData.postid = postID;
			objData.isadmin = g_vars.is_admin;
			
			if(copyMode)
				objData.paste_mode = copyMode;
			
			t.setState(g_vars.STATE_PASTING_POST);
			showLoader(g_texts.loader_pasting_post_text);
			
			var objFile = null;
			
			if(g_vars.is_file_selected == true){
				
				objFile = g_objInputFile;
			}else{
				objData.copy_text = g_vars.copied_text;
			}
			
			var action = "paste_post";
			if(objFile)
				action = "import_post";
			
			ajaxRequest(action, objData, function(response){
				
				hideLoader();
				
				showSuccessMessage(response.message, true);
				
				var urlImportedPost = response.url_post;
				
				if(urlImportedPost){
					location.href = urlImportedPost;
				}else
					location.reload();
				
			},
			function(error){		//on error
			
				closeCurrentState();
				
				showErrorMessage(error);
				
			}, objFile);			
			
		}catch(e){
			
			showErrorMessage(e);
			
		}
			
		
	}
	
	
	
	/**
	 * clear paste input
	 */
	function clearPasteInput(){
		
		g_objPasteInput.val("");
		
		setTimeout(function(){
			g_objPasteInput.focus();
		},100);
		
		
	}
	
	
	/**
	 * on paste code change
	 */
	function onPasteCodeChange(){
						
		if(g_vars.state == null)
			return(false);
			
		var text = g_objPasteInput.val();
		
		if(!text)
			return(false);
		
		pasteStep2_handleText(text);
		
	}
	
	/**
	 * on key up, check escape key and check for paste request
	 */
	function onPasteInputKeyUp(event){
		
		if(event.keyCode == 27 && g_vars.state != null){
						
			g_objPasteInput.blur();
			closeCurrentState();
			return(true);
		}
		
		onPasteCodeChange();
	}
	
	/**
	 * start import post dialog, or proceed to import post
	 */
	function startImportPost(dataType){
				
		var postID = getVal(g_options, "postid");
		
		var isStartDialog = false;
		
		if(postID)
			isStartDialog = true;
		
		if(dataType == "multiple")
			isStartDialog = false;
		
		if(isStartDialog == true)
			t.setState(g_vars.STATE_PASTE_DIALOG);
		else
			sendPasteRequest();
	}
	
	
	function __________UPLOAD_____________(){}
	
	
	/**
	 * on upload button click
	 */
	function onButtonUploadClick(){
		
		g_objInputFile.trigger("click");
		
	}
	
		
	
	/**
	 * validate upload file
	 */
	function validateUploadFile(pathFile){
		
		var filename = getFilenameFromPath(pathFile);
		
		if(!filename)
			throw new Error("Wrong File");
		
		var arrFile = filename.split(".");

				
		if(arrFile.length < 2)
			throw new Error("Wrong import file given");
		
		var name = arrFile[0];
		
		//get extension
		
		var ext = arrFile[arrFile.length-1];
		
		if(name.indexOf("doubly_") == -1)
			throw new Error("Wrong import file");
		
		ext = ext.toLowerCase();
		
		if(ext != "zip")
			throw new Error("The import file has to be zip");
		
		return(name);
	}
	
	
	/**
	 * import the file
	 */
	function importFile(){
		
		try{
		
			//validate file
			var pathFile = g_objInputFile.val();
			
			var filename = validateUploadFile(pathFile);
			
			var isSection = false;
			if(filename.indexOf("doubly_section_") != -1)
				isSection = true;
			
			g_vars.is_file_selected = true;
			
			if(isSection == true)
				startSectionSelectMode();
			else
				startImportPost();
			
		}catch(error){
			
			showErrorMessage(error);
			
		}
		
	}

	function __________BULK_POSTS_____________(){}
	
	/**
	 * get checked post id's from edit posts view
	 */
	function getSelectedPostsCheckboxes(){
		
		var objList = jQuery("#the-list");
		if(objList.length == 0)
			return(null);
		
		var objCheckboxes = objList.find("input[type='checkbox']:checked");
		
		return(objCheckboxes);
	}
	
	/**
	 * get post ids from checkboxes
	 */
	function getPostIDsFromChecks(objPostsChecks){
		
		var arrIDs = [];
		
		jQuery.each(objPostsChecks, function(index, check){
			
			var objCheck = jQuery(check);
			var postID = objCheck.val();
			
			arrIDs.push(postID);
		});
		
		return(arrIDs);
	}
	
	
	/**
	 * copy posts or post
	 */
	function ajaxCopyPosts(mixed){
		
		var objData = {};
		objData["postid"] = mixed;
		
		var postType = getVal(g_options,"posttype");
		objData["posttype"] = postType;
		
		var copyMode = getVal(g_options, "copymode");
		if(copyMode)
			objData["copymode"] = copyMode;
		
		
		showLoader("Copying...");
		
		ajaxRequest("copy_post",objData, function(response){
			
			hideLoader();
			
			var copyText = response.copy_text;
			
			copyTextToClipboard(copyText);
			
			showSuccessMessage(response.message);
			
		});
		
	}
	
	
	/**
	 * copy selected posts from the list
	 * action - copy / export
	 */
	function bulkPostsAction(isExport){
		
		var objPostsChecks = getSelectedPostsCheckboxes();
		
		var operationName = "Copy";
		if(isExport)
			operationName = "Export Zip";
		
		if(!objPostsChecks || objPostsChecks.length == 0)
			throw new Error("Bulk "+operationName+" - No Posts Selected");
		
		var arrPostIDs = getPostIDsFromChecks(objPostsChecks);
		
		if(isExport == false){
			ajaxCopyPosts(arrPostIDs);
			return(false);
		}
		
		var copyMode = getVal(g_options, "copymode");
		
		var strPostIDs = arrPostIDs.join(",");
		
		if(copyMode){
			
			var action = "export_object";
			var params = "objtype="+copyMode+"&id="+strPostIDs; 
			
		}else{
			
			var action = "export_post";
			var params = "type=posts&postid="+strPostIDs; 
			
		}
		
		var url = getUrlAjax(action, params);
		
		location.href = url;
		
	}
	
	
	/**
	 * on bulk actions click
	 */
	function onBulkActionApplyClick(event){
		try{
			
			var objBulkSelect = jQuery("#bulk-action-selector-top");
			
			var bulkType = objBulkSelect.val();
			
			var isExport = false;
			
			switch(bulkType){
				case "doubly_copy":
				break;
				case "doubly_export":
					isExport = true;
				break;
				default:
					return(true);
				break;
			}
						
			event.preventDefault();
			objBulkSelect.val("-1");
			
			bulkPostsAction(isExport);
						
		}catch(e){
			showErrorMessage(e);
		}
	}
	
	
	/**
	 * init bulk actions
	 */
	function initBulkActions(){
		
		var objBulkSelect = jQuery("#bulk-action-selector-top");
		
		if(objBulkSelect.length == 0)
			return(false);
		
		var objOptionCopy = objBulkSelect.find("option[value=doubly_copy]");
		
		if(objOptionCopy.length == 0)
			return(false);
		
		//show the hidden setting if available
		
		var isHidden = objBulkSelect.is(":hidden");
		
		if(isHidden == true){
			objBulkSelect.show();
			objBulkSelect.parent().show();
		}
		
		var objButtonApply = jQuery("#doaction");
		
		objButtonApply.on("click", onBulkActionApplyClick);
		
	}
	
	
	function __________INIT_____________(){}
	
	
	/**
	 * on body key up
	 * close state on escape
	 */
	function onBodyKeyUp(event){
		
		if(event.keyCode == 27 && g_vars.state != null){
			
			hideAllMessages();
			
			closeCurrentState();
		}
		
	}
	
	
	/**
	 * init button events
	 */
	function initButtonEvents(objButton, func){
		
		var objLink = objButton.children("a.ab-item");
		if(objLink.length == 0){
			trace(objButton);
			trace("doubly error - button not found");
			return(false);
		}
		
		objLink.attr("href","javascript:void(0)");
		objLink.on("click", func);
		
	}
	
	/**
	 * init floating buttons events
	 */
	function initFloatingButtonsEvents(){
		
		var objFloatingWrapper = jQuery("#doubly_floating_buttons_wrapper");
		
		if(objFloatingWrapper.length == 0)
			return(false);
		
		
		var objButtonCopy = objFloatingWrapper.find(".doubly-floating-button__copy");
		var objButtonPaste = objFloatingWrapper.find(".doubly-floating-button__paste");
		
		if(objButtonCopy.length)
			objButtonCopy.on("click", startCopyMode);
		
		if(objButtonPaste.length)
			objButtonPaste.on("click", onPasteClick);
		
	}
	
	
	
	/**
	 * init events
	 */
	function initEvents(){
		
		//init menu buttons
		if(g_objMenuCopy)
			initButtonEvents(g_objMenuCopy, startCopyMode);
		
		//init paste related events
		if(g_objMenuPaste)
			initButtonEvents(g_objMenuPaste, onPasteClick);
		
		//top panel buttons	
		if(g_objPanelButtonCopy.length)
			g_objPanelButtonCopy.on("click", onCopyClick);
		
		//close current state buttons
		
		var objCloseButton = jQuery("#doubly_top_panel_button_close");
		
		objCloseButton.on("click", function(){
			
			g_objBody.removeClass(g_vars.CLASS_MAIN_LOADING);
			closeCurrentState();
			
		});
		
		var buttonDialogImport = jQuery("#doubly_button_dialog_import");
		
		buttonDialogImport.on("click", pasteStep3_onDialogImportClick);
		
		g_objBody.on("click", ".doubly-button-copy-section", onCopySectionClick);
		
		g_objBody.on("click",".doubly-paste-section-overlay__button", onPasteSectionClick);
		
		g_objBody.on("click",".doubly-button-export-section", onExportSectionClick);
		
		//body button
		
		g_objBody.on("keyup",onBodyKeyUp);
		
		//paste input
		
		g_objPasteInput.on("paste", function(event){
			
			setTimeout(function(){
				g_objPasteInput.blur();
			},100);
			
		});
		
		g_objPasteInput.on("change", onPasteCodeChange);
		g_objPasteInput.on("keyup", onPasteInputKeyUp);
		
		//init upload form
		g_objButtonImport.on("click", onButtonUploadClick);
		
		//file input
		
		g_objInputFile.on("change", importFile);
		
		//front floating buttons
		
		initFloatingButtonsEvents();
				
		
	}
	
	
	/**
	 * show error message
	 */
	this.showErrorMessage = function(text){
				
		showErrorMessage(text);
	}
	
	
	/**
	 * get state class
	 */
	function getStateClass(state){
		
		var objClasses = {};
		objClasses[g_vars.STATE_COPY] = "doubly-copy-mode";
		objClasses[g_vars.STATE_COPYING_SECTION] = "doubly-copying-section-mode";
		objClasses[g_vars.STATE_COPYING_SECTION_FRONT] = "doubly-copying-section-front-mode";
		objClasses[g_vars.STATE_PASTE] = "doubly-paste-mode";
		objClasses[g_vars.STATE_SECTION_SELECT] = "doubly-select-section-mode";
		objClasses[g_vars.STATE_PASTING_SECTION] = "doubly-pasting-section-mode";
		objClasses[g_vars.STATE_PASTING_POST] = "doubly-pasting-post-mode";
		objClasses[g_vars.STATE_PASTE_DIALOG] = "doubly-paste-dialog-mode";
		
		var className = getVal(objClasses, state);
		
		if(!className)
			throw new Error("getStateClass Wrong state: "+state);
		
		
		return(className);
	}
	
	
	/**
	 * set state
	 */
	this.setState = function(state){
		
		if(g_vars.state == state)
			return(false);
		
		var className = getStateClass(state);
		
		hideAllMessages();
		
		closeCurrentState();
		
		g_vars.state = state;
		
		switch(state){
			case g_vars.STATE_COPY:
				
				var instructions = g_texts.instructions_copy;
				var isSectionsExist = isElementorSectionsExists();
				
				if(isSectionsExist == true)
					instructions += g_texts.instructions_copy_elementor_section;
								
				g_objTopPanelText.html(instructions);
				
			break;
			case g_vars.STATE_PASTE:
				
				g_objTopPanelText.html(g_texts.instructions_paste);
				
				clearPasteInput();
								
			break;
			case g_vars.STATE_SECTION_SELECT:
				
				g_objTopPanelMiddleText.html(g_texts.middle_text_paste);
												
			break;
			case g_vars.STATE_PASTING_SECTION:
				
				g_objTopPanelMiddleText.html(g_texts.middle_text_pasting_section);
												
			break;
			case g_vars.STATE_COPYING_SECTION:
				
				g_objTopPanelMiddleText.html(g_texts.middle_text_copying_section);
				
			break;
			case g_vars.STATE_COPYING_SECTION_FRONT:
				
				//do nothing for now
				
			break;
			case g_vars.STATE_PASTING_POST:
				
				g_objTopPanelMiddleText.html(g_texts.middle_text_pasting_post);
				
			break;
			case g_vars.STATE_PASTE_DIALOG:
				
				g_objTopPanelMiddleText.html(g_texts.middle_text_paste_dialog);
				
				//set radio to new
				jQuery("#doubly_dialog_import_radio_new").trigger("click");
				
			break;
			default:
				throw new Error("setState function, Wrong state: "+state);
			break;
		}
		
		g_objBody.addClass(className);
		
	}
	
	
	/**
	 * close current state
	 */
	function closeCurrentState(){
		
		if(g_vars.state == null)
			return(false);
		
		var className = getStateClass(g_vars.state);
	
		g_objBody.removeClass(className);
		
		g_vars.state = null;
	}
	
	
	
	/**
	 * init the front object
	 */
	this.init = function(counter){
				
		if(counter && counter > 2)
			return(false);
				
		//protection if inside some page builder frame
		if(window !== window.parent){
			return(false);
		}
		
		if(g_showDebug == true){
			trace("doubly init!");
		}
		
		g_objMenuCopy = jQuery("#wp-admin-bar-doubly_copy");
		if(g_objMenuCopy.length == 0)
			g_objMenuCopy = null;
		
		g_objMenuPaste = jQuery("#wp-admin-bar-doubly_paste");
		if(g_objMenuPaste.length == 0)
			g_objMenuPaste = null;
				
		//init texts, if not - try to init later (optimization maybe)
		
		if(typeof g_doublyTexts == "undefined"){
			
			if(!counter)
				counter = 0;
			
			if(g_showDebug == true){
				trace("no options, will try again...");
			}
			
			setTimeout(function(){
				t.init(counter+1);
			}, 1000);
			
			return(false);
		}
		
		g_texts = JSON.parse(g_doublyTexts);
		
		//init options
		if(typeof g_strDOUBLYOptions == "undefined")
			return(false);
		
		
		g_objInput = jQuery("#doubly_copy_input");
		
		g_objLoader = jQuery("#doubly_front_loading");
		g_objMessageSuccess = jQuery("#doubly_front_success");
		g_objMessageError = jQuery("#doubly_front_error");
		
		g_options = JSON.parse(g_strDOUBLYOptions);
				
		if(!g_options)
			return(false);
		
		if(jQuery.isEmptyObject(g_options))
			return(false);
		
		g_vars.is_admin = getVal(g_options, "isadmin");
		
		g_ajaxUrl = getVal(g_options, "ajaxurl");
		
		if(!g_ajaxUrl)
			throw new Error("no ajax url found");
		
		g_nonce = getVal(g_options,"nonce");
		if(!g_nonce)
			throw new Error("no nonce found");
		
		g_vars.is_safari = isSafariBrowser();
		
		g_objTopPanelText = jQuery("#doubly_top_panel_text");;
		
		g_objTopPanelMiddleText = jQuery("#doubly_top_panel_middle_text");
		
		g_objPasteInput = jQuery("#doubly_top_panel_paste_input");
		
		g_objBody = jQuery("body");
		
		g_objInputFile = jQuery("#doubly_top_panel_input_import");
		
		g_objPanelButtonCopy = jQuery("#doubly_top_panel_button_copy");
		
		g_objButtonImport = jQuery("#doubly_top_panel_button_import");
		
		if(g_vars.is_admin == false)
			initFrontCopy();
		
		initEvents();
		
		if(g_vars.is_admin)
			initBulkActions();
				
	}

	
}

/**
 * get text from clipboard */
async function doublyGetTextFromClipboard(onReturn){
	
	var text = "";
		
	try {
	    
		var text = await navigator.clipboard.readText();
	    
		onReturn(text);
		
	  } catch (err) {
		  
		  g_objCopyPasteFront.setState("paste_mode");
		  
	 }
	  
}	

var g_objCopyPasteFront;

jQuery(document).ready(function(){
	
	g_objCopyPasteFront = new DOUBLY_Front();
	g_objCopyPasteFront.init();
	
});
