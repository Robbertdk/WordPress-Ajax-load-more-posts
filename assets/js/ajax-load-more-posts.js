(function (){

  var container = document.querySelector(loadMore.containerSelector);

  function init() {
    if(!container) return false;
    createErrorBlock(container);
    on(container, 'click', '.js-load-more', function(event) { 
        getAJAXPosts(event.target.href, '.posts');
        event.preventDefault();
        return true;
    });
  }

  /**
   * Create block for error messages
   * 
   * @param  {Object} container Container to add the error block to
   * @return void
   */
  function createErrorBlock(container) {
    container.insertAdjacentHTML('beforebegin', '<div class="notification notification--error ajax-error" style="display:none"></div>');
    container.errorBlock = container.parentNode.querySelector('.ajax-error'); // Store it for later use.
  }

  /**
   * Insert the response from the server
   * 
   * @param  {object} response Object of Dom Elements
   * @param  {object} request  XMLHttpRequest
   * @return {function} Execute showing errors
   */
  function insertAjaxResponse(response, request) {
    if(!response) return showErrors(response, request);
    container.innerHTML = container.innerHTML + response.innerHTML;
    container.classList.remove('is-waiting');
  }

  /**
   * Insert response errors into the DOM
   * 
   * @param  {object} response Object of Dom Elements
   * @param  {object} request  XMLHttpRequest
   * @return void
   */
  function showErrors(response, request) {
      console.error(request.statusText);
      if (request.timeout === 1) {
        container.errorBlock.innerText = loadMore.timeoutMessage;
      } else {
        container.errorBlock.innerText = loadMore.serverErrorMessage;
      }
      container.errorBlock.style.display = 'block';
      container.errorBlock.scrollIntoView({behavior: "smooth"});
  }

  /**
   * Update url in the browser to the nest page
   *
   * Usefull for sharing a page
   *
   *  @param  {string} url new url
   */
  function updateUrl(url) {
    if (!window.history.pushState) {
      return;
    }
    // Create fake element to get the url path
    var link = document.createElement("a");
    link.href = url;
    // Update the browser url
    window.history.pushState('Blog','', link.pathname);
  }

  /**
   * Remove load more button of loaded page
   */
  function removeLoadMoreButton() {
    var loadMoreButton = container.querySelector('.js-load-more');
    loadMoreButton.parentNode.removeChild(loadMoreButton);
  }

  /**
   * Show the Error Reponse div
   */
  function hideResponseMessage() {
   container.errorBlock.style.display = 'none';
  }

  /**
   * Get new posts via Ajax
   *
   * Retrieve a new set of posts based on the created query
   * 
   * @return string server side generated HTML
   */
  function getAJAXPosts(url, selector) {

    // Hide possible messages from prev response
    hideResponseMessage(container.errorBlock);

    var request = new XMLHttpRequest();
    var finished = false;

    request.open('GET', url, true);
    request.timeout = 10000; // time in milliseconds
    
    request.onreadystatechange = function () {
      if (request.readyState === 4 && !finished) {

        finished = true;
        try { // Try to parse the response
          var parser = new DOMParser();
          var html = parser.parseFromString( request.responseText, "text/html");
          var content = html.querySelector(selector);
          
          // Hide loadMore bUtton from prev page
          removeLoadMoreButton(container);
          insertAjaxResponse(content);
          updateUrl(url);
        } catch (e) {
          insertAjaxResponse(null, e);
        }
      }
    };

    request.ontimeout = request.onabort = request.onerror = function() {
      finished = true;
      insertAjaxResponse(null, request);
    }

    request.send();
  }

  /**
   * Helper function for event delegation
   *
   * To add event listeners on dynamic content, you can add a listener 
   * on thewrapping container, find the dom-node that triggered 
   * the event and check if that node mach our 
   * 
   * @param  NodeElement  el          wrapping element for the dynamic content
   * @param  string       eventName   type of event, e.g. click, mouseenter, etc
   * @param  string       selector    selector criteria of the element where the action should be on
   * @param  Function     fn          callback funciton
   * @return Function     The callback
   */
  function on(el, eventName, selector, fn) {
    var element = el;

    element.addEventListener(eventName, function(event) {
        var possibleTargets = element.querySelectorAll(selector);

        var target = event.target;

        for (var i = 0, l = possibleTargets.length; i < l; i++) {
            var el = target;
            var p = possibleTargets[i];

            while(el && el !== element) {
                if (el === p) {
                    return fn.call(p, event);
                }

                el = el.parentNode;
            }
        }
    });
  }

  init();

}());