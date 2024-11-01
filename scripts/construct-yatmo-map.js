(function () {
  // holds a function queue to call once page is loaded
  function Main() {
    var VERSION = '{{VERSION}}';
    this.VERSION = VERSION;
	
	/**
     * Call a render function, wrapped in a try/catch
     * @param {() => void} fnc
     */
    function callRenderFunction(fnc) {
      try {
        fnc();
      } catch (e) {
        console.log('-- version --', VERSION);
        console.error(e);
      }
    }

    var ready = false;
    var callbacks = []; 

    /**
     * execute all callbacks once page/Yatmo is loaded
     */
    this.init = function () {
      ready = true;
      for (var i = 0, len = callbacks.length; i < len; i++) {
        callRenderFunction(callbacks[i]);
      }
    };

    this.markers = [];
    this.circles = [];
  }

  /**
   * window.WPYatmoMapPlugin can be used, by saving arguments,
   * before it is officially initialized
   *
   * This is used to deal with the potential for deferred scripts
   */
  var original = window.WPYatmoMapPlugin;
  window.WPYatmoMapPlugin = new Main();

  // check for functions to execute
  if (!!original) {
    for (var i = 0, len = original.length; i < len; i++) {
      window.WPYatmoMapPlugin.push(original[i]);
    }

    // empty the array
    original.splice(0);

    // re-add any methods that may have been added to the original
    for (var k in original) {
      if (original.hasOwnProperty(k)) {
        window.WPYatmoMapPlugin[k] = original[k];
      }
    }
  }

  // onload waits for Yatmo to load
  if (window.addEventListener) {
    window.addEventListener('load', window.WPYatmoMapPlugin.init, false);
  } else if (window.attachEvent) {
    window.attachEvent('onload', window.WPYatmoMapPlugin.init);
  }
})();
